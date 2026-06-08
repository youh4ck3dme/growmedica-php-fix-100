#!/bin/bash
set -euo pipefail

# Push Shopify web-bot-auth signature env vars to Vercel (API, non-interactive).
# Usage:
#   cd storefront
#   VERCEL_TOKEN=xxx yarn push:signature-env
# Or after `vercel login` locally (reads ~/.local/share/com.vercel.cli/auth.json):
#   yarn push:signature-env
#
# Optional:
#   ENV_FILE=.env.local VERCEL_SCOPE=h4ck3d VERCEL_PROJECT=growmedicanextjs

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
STOREFRONT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
ENV_FILE="${ENV_FILE:-$STOREFRONT_DIR/.env.local}"
VERCEL_SCOPE="${VERCEL_SCOPE:-h4ck3d}"
VERCEL_PROJECT="${VERCEL_PROJECT:-growmedicanextjs}"

echo "===================================================="
echo "Push Shopify signature env vars → Vercel (API)"
echo "Project: ${VERCEL_SCOPE}/${VERCEL_PROJECT}"
echo "Source:  ${ENV_FILE}"
echo "===================================================="

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ERROR: Env file not found: $ENV_FILE"
  exit 1
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

REQUIRED_VARS=(
  SHOPIFY_SIGNATURE_NAME
  SHOPIFY_STORE_DOMAIN
  SHOPIFY_SIGNATURE
  SHOPIFY_SIGNATURE_INPUT
  SHOPIFY_SIGNATURE_AGENT
  SHOPIFY_SIGNATURE_EXPIRES
)

for var in "${REQUIRED_VARS[@]}"; do
  if [[ -z "${!var:-}" ]]; then
    echo "ERROR: Missing required variable in $ENV_FILE: $var"
    exit 1
  fi
done

cd "$STOREFRONT_DIR"

export VERCEL_TOKEN="${VERCEL_TOKEN:-}"
export VERCEL_SCOPE
export VERCEL_PROJECT
export VERCEL_ORG_ID="${VERCEL_ORG_ID:-}"
export VERCEL_PROJECT_ID="${VERCEL_PROJECT_ID:-}"

node <<'NODE'
const fs = require('fs');
const os = require('os');
const path = require('path');

function readToken() {
  if (process.env.VERCEL_TOKEN) return process.env.VERCEL_TOKEN;
  const authPath = path.join(os.homedir(), '.local/share/com.vercel.cli/auth.json');
  if (fs.existsSync(authPath)) {
    return JSON.parse(fs.readFileSync(authPath, 'utf8')).token;
  }
  throw new Error(
    [
      'Missing Vercel auth.',
      'Set VERCEL_TOKEN (https://vercel.com/account/tokens) or run: vercel login',
      'Then: cd storefront && yarn push:signature-env',
    ].join(' '),
  );
}

async function api(token, url, options = {}) {
  const res = await fetch(url, {
    ...options,
    headers: {
      Authorization: `Bearer ${token}`,
      'Content-Type': 'application/json',
      ...(options.headers || {}),
    },
  });
  const body = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(`${options.method || 'GET'} ${url} → ${res.status} ${JSON.stringify(body)}`);
  }
  return body;
}

async function resolveProject(token) {
  const linkedPath = '.vercel/project.json';
  if (fs.existsSync(linkedPath)) {
    const linked = JSON.parse(fs.readFileSync(linkedPath, 'utf8'));
    return { teamId: linked.orgId, projectId: linked.projectId };
  }

  if (process.env.VERCEL_ORG_ID && process.env.VERCEL_PROJECT_ID) {
    return {
      teamId: process.env.VERCEL_ORG_ID,
      projectId: process.env.VERCEL_PROJECT_ID,
    };
  }

  const scope = process.env.VERCEL_SCOPE || 'h4ck3d';
  const projectName = process.env.VERCEL_PROJECT || 'growmedicanextjs';

  const teams = await api(token, 'https://api.vercel.com/v2/teams');
  const team = (teams.teams || []).find((entry) => entry.slug === scope || entry.name === scope);
  if (!team) {
    throw new Error(`Vercel team not found: ${scope}. Set VERCEL_ORG_ID explicitly.`);
  }

  const project = await api(
    token,
    `https://api.vercel.com/v9/projects/${encodeURIComponent(projectName)}?teamId=${team.id}`,
  );

  fs.mkdirSync('.vercel', { recursive: true });
  fs.writeFileSync(
    linkedPath,
    JSON.stringify({ orgId: team.id, projectId: project.id }, null, 2) + '\n',
  );
  console.log(`Linked ${scope}/${projectName} → ${project.id}`);

  return { teamId: team.id, projectId: project.id };
}

const vars = [
  ['SHOPIFY_SIGNATURE_NAME', process.env.SHOPIFY_SIGNATURE_NAME],
  ['SHOPIFY_STORE_DOMAIN', process.env.SHOPIFY_STORE_DOMAIN],
  ['SHOPIFY_SIGNATURE', process.env.SHOPIFY_SIGNATURE],
  ['SHOPIFY_SIGNATURE_INPUT', process.env.SHOPIFY_SIGNATURE_INPUT],
  ['SHOPIFY_SIGNATURE_AGENT', process.env.SHOPIFY_SIGNATURE_AGENT],
  ['SHOPIFY_SIGNATURE_EXPIRES', process.env.SHOPIFY_SIGNATURE_EXPIRES],
];

const targets = ['production', 'preview', 'development'];

async function upsert(token, teamId, projectId, key, value) {
  const list = await api(
    token,
    `https://api.vercel.com/v9/projects/${projectId}/env?teamId=${teamId}`,
  );
  const stale = (list.envs || []).filter((entry) => entry.key === key);
  for (const entry of stale) {
    await api(
      token,
      `https://api.vercel.com/v9/projects/${projectId}/env/${entry.id}?teamId=${teamId}`,
      { method: 'DELETE' },
    );
  }

  await api(token, `https://api.vercel.com/v10/projects/${projectId}/env?teamId=${teamId}`, {
    method: 'POST',
    body: JSON.stringify({
      key,
      value,
      type: 'encrypted',
      target: targets,
    }),
  });

  console.log(`  ✓ ${key} → production, preview, development`);
}

(async () => {
  const token = readToken();
  const { teamId, projectId } = await resolveProject(token);

  for (const [key, value] of vars) {
    if (!value) throw new Error(`Missing value for ${key}`);
    await upsert(token, teamId, projectId, key, value);
  }

  console.log('\nDone. Redeploy production/preview so new env vars apply.');
  console.log('  npx vercel redeploy growmedicanextjs.vercel.app --scope h4ck3d --prod');
})().catch((error) => {
  console.error(`ERROR: ${error.message}`);
  process.exit(1);
});
NODE

echo "===================================================="
