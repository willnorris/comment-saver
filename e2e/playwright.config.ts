import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: 'tests',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: 'list',
    use: {
        // Ideally, it should be retrieved from an .env file
        // https://playwright.dev/docs/test-parameterize#env-files
        baseURL: 'http://wordpress',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
