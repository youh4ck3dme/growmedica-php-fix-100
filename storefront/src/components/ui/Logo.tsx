interface LogoProps {
  variant?: 'light' | 'dark'
  showTagline?: boolean
  iconSize?: number
  className?: string
}

export function LogoIcon({ size = 36 }: { size?: number }) {
  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 48 48"
      fill="none"
      aria-hidden="true"
    >
      <defs>
        <linearGradient id="crossMetal" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#E8ECF0" />
          <stop offset="50%" stopColor="#B8C4CE" />
          <stop offset="100%" stopColor="#8A9BAA" />
        </linearGradient>
        <linearGradient id="leafGreen" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#6DD4A8" />
          <stop offset="100%" stopColor="#35C79A" />
        </linearGradient>
      </defs>
      {/* Medical cross */}
      <rect x="18" y="8" width="12" height="32" rx="3" fill="url(#crossMetal)" />
      <rect x="8" y="18" width="32" height="12" rx="3" fill="url(#crossMetal)" />
      {/* Leaf overlay bottom-right */}
      <path
        d="M30 28C30 28 38 26 40 32C42 38 36 42 32 40C28 38 26 34 28 30C29 28.5 30 28 30 28Z"
        fill="url(#leafGreen)"
      />
      <path
        d="M32 30C32 30 34 34 33 37"
        stroke="white"
        strokeWidth="0.8"
        strokeLinecap="round"
        opacity="0.7"
      />
    </svg>
  )
}

export default function Logo({
  variant = 'light',
  showTagline = false,
  iconSize = 36,
  className = '',
}: LogoProps) {
  const growColor = variant === 'dark' ? '#FFFFFF' : '#101615'
  const medicaColor = '#35C79A'
  const skColor = variant === 'dark' ? '#D1D5DB' : '#6B7280'
  const taglineColor = variant === 'dark' ? '#9CA3AF' : '#9CA3AF'

  return (
    <div className={`flex items-center gap-2.5 ${className}`}>
      <LogoIcon size={iconSize} />
      <div className="flex flex-col leading-none">
        <span
          className="text-lg sm:text-xl font-extrabold tracking-tight whitespace-nowrap"
          style={{ fontFamily: 'Montserrat, sans-serif' }}
        >
          <span style={{ color: growColor }}>Grow</span>
          <span style={{ color: medicaColor }}>Medica</span>
          <span style={{ color: skColor }}>.sk</span>
        </span>
        {showTagline && (
          <div className="flex items-center gap-2 mt-1">
            <span className="h-px w-3 bg-current opacity-30" style={{ color: taglineColor }} />
            <span
              className="text-[0.55rem] font-semibold uppercase tracking-[0.14em]"
              style={{ color: taglineColor, fontFamily: 'Montserrat, sans-serif' }}
            >
              Premium Medical E-shop
            </span>
            <span className="h-px w-3 bg-current opacity-30" style={{ color: taglineColor }} />
          </div>
        )}
      </div>
    </div>
  )
}
