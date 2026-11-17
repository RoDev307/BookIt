import { defineConfig, devices } from '@playwright/test';
export default defineConfig({
  testDir: './e2e', testMatch: 'admin-scope.spec.ts', workers: 1, reporter: 'list',
  use: { baseURL: process.env.BOOKIT_TEST_URL || 'http://127.0.0.1:8114', ...devices['Pixel 7'], channel: 'chrome' },
});
