import { defineConfig, devices } from '@playwright/test';

const isPwaProductionTest = !!process.env.PWA_PRODUCTION_TEST;
const shopifyTestEnv = {
  SHOPIFY_MOCK_MODE: process.env.SHOPIFY_MOCK_MODE ?? '1',
  SHOPIFY_STORE_DOMAIN: process.env.SHOPIFY_STORE_DOMAIN ?? 'mock-store.myshopify.com',
  SHOPIFY_STOREFRONT_ACCESS_TOKEN:
    process.env.SHOPIFY_STOREFRONT_ACCESS_TOKEN ?? 'mock-storefront-token',
  SHOPIFY_API_VERSION: process.env.SHOPIFY_API_VERSION ?? '2025-01',
};

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
        env: shopifyTestEnv,
      }
    : {
        command: 'yarn dev',
        url: 'http://localhost:5555',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        env: shopifyTestEnv,
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
