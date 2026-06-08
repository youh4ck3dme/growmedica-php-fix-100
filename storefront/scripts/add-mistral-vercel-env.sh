#!/bin/bash
set -euo pipefail

# Push Mistral AI env vars to Vercel (never commit real values).
# Usage:
#   MISTRAL_API_KEY=... MISTRAL_API_KEY_BACKUP=... ./scripts/add-mistral-vercel-env.sh
# Optional:
#   VERCEL_SCOPE=h4ck3d VERCEL_PROJECT=growmedicanextjs VERCEL_TOKEN=...

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
STOREFRONT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
VERCEL_SCOPE="${VERCEL_SCOPE:-h4ck3d}"
VERCEL_PROJECT="${VERCEL_PROJECT:-growmedicanextjs}"
ENVIRONMENTS=(production preview development)

MISTRAL_API_KEY="${MISTRAL_API_KEY:-}"
MISTRAL_API_KEY_BACKUP="${MISTRAL_API_KEY_BACKUP:-}"
MISTRAL_MODEL="${MISTRAL_MODEL:-mistral-large-latest}"

echo "===================================================="
echo "Configure Mistral env vars on Vercel"
echo "Project: ${VERCEL_SCOPE}/${VERCEL_PROJECT}"
echo "===================================================="

if ! command -v vercel >/dev/null 2>&1; then
  echo "ERROR: Vercel CLI is not installed."
  echo "Install with: npm install -g vercel"
  exit 1
fi

if [[ -z "$MISTRAL_API_KEY" ]]; then
  echo "ERROR: Missing MISTRAL_API_KEY"
  exit 1
fi

if [[ -z "$MISTRAL_API_KEY_BACKUP" ]]; then
  echo "ERROR: Missing MISTRAL_API_KEY_BACKUP"
  exit 1
fi

cd "$STOREFRONT_DIR"

vercel_args=(--scope "$VERCEL_SCOPE")
if [[ -n "${VERCEL_TOKEN:-}" ]]; then
  vercel_args+=(--token "$VERCEL_TOKEN")
fi

if [[ ! -f .vercel/project.json ]]; then
  vercel link --yes --project "$VERCEL_PROJECT" "${vercel_args[@]}"
fi

remove_env_var() {
  local name=$1
  local target=$2

  if vercel env rm "$name" "$target" --yes "${vercel_args[@]}" 2>/dev/null; then
    echo "  - Removed stale $name from $target"
  fi
}

upsert_env_var() {
  local name=$1
  local value=$2

  echo "Setting $name..."
  for target in "${ENVIRONMENTS[@]}"; do
    remove_env_var "$name" "$target"
    if printf '%s' "$value" | vercel env add "$name" "$target" "${vercel_args[@]}"; then
      echo "  - Added to $target"
    else
      echo "  - ERROR: failed to add $name to $target"
      exit 1
    fi
  done
}

disable_mock_mode() {
  echo "Disabling MISTRAL_MOCK_MODE..."
  for target in "${ENVIRONMENTS[@]}"; do
    remove_env_var "MISTRAL_MOCK_MODE" "$target"
  done
}

upsert_env_var "MISTRAL_API_KEY" "$MISTRAL_API_KEY"
upsert_env_var "MISTRAL_API_KEY_BACKUP" "$MISTRAL_API_KEY_BACKUP"
upsert_env_var "MISTRAL_MODEL" "$MISTRAL_MODEL"
disable_mock_mode

echo "===================================================="
echo "Done. Verify with:"
echo "  vercel env ls production --scope ${VERCEL_SCOPE}"
echo "Then redeploy preview/production so new env vars apply."
echo "===================================================="
