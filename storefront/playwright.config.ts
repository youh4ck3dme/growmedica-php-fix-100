import { defineConfig, devices } from '@playwright/test';

const isPwaProductionTest = !!process.env.PWA_PRODUCTION_TEST;

export default defineConfig({
  testDir: './tests',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: [['list'], ['html', { open: 'never' }]],
  webServer: isPwaProductionTest
    ? {
        command: 'yarn start --port 5556',
        url: 'http://localhost:5556',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
      }
    : {
        command: 'yarn dev',
        url: 'http://localhost:5555',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
      },
  use: {
    baseURL: isPwaProductionTest
      ? 'http://localhost:5556'
      : process.env.BASE_URL || 'http://localhost:5555',
    trace: 'on-first-retry',
  },

  projects: [
    {
      name: 'integrity',
      testMatch: /integrity\/.*\.spec\.ts/,
      testIgnore: '**/pwa.spec.ts',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'pwa',
      testMatch: /integrity\/pwa\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'e2e-chromium',
      testMatch: /e2e\/.*\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'e2e-mobile',
      testMatch: /e2e\/.*\.spec\.ts/,
      use: { ...devices['Pixel 5'] },
    },
  ],
});
