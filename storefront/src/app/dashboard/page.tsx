import DashboardFrame from '@/components/dashboard/DashboardFrame'
import { getDashboardUrl } from '@/lib/dashboard'

export default function DashboardPage() {
  const dashboardUrl = getDashboardUrl()

  if (!dashboardUrl) {
    return (
      <div
        className="flex h-dvh w-full flex-col items-center justify-center gap-3 bg-(--color-surface) px-6 text-center"
        data-testid="dashboard-unconfigured"
      >
        <h1 className="text-xl font-semibold text-(--color-text)">Dashboard nie je nakonfigurovaný</h1>
        <p className="max-w-md text-sm text-(--color-text-muted)">
          Nastavte premennú{' '}
          <code className="rounded bg-(--color-border)/40 px-1.5 py-0.5 text-xs">
            NEXT_PUBLIC_DASHBOARD_URL
          </code>{' '}
          vo Vercel projekte alebo v <code className="rounded bg-(--color-border)/40 px-1.5 py-0.5 text-xs">.env.local</code>.
        </p>
      </div>
    )
  }

  return <DashboardFrame src={dashboardUrl} />
}
