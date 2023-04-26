import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    // Run all tests in parallel.
    fullyParallel: true,

    // Retry on CI only.
    retries: process.env.CI ? 2 : 0,

    // Opt out of parallel tests on CI.
    workers: process.env.CI ? 1 : undefined,

    use: {
	// Change baseURL to run against e.g. production:
	// baseURL: 'https://catalog.hathitrust.org',
	baseURL: 'http://nginx:8080',
	headless: true,
	// Collect trace when retrying the failed test.
	trace: 'on-first-retry',
	video: 'off',
    },

    // Configure  browsers here.
    projects: [
	{
	    name: 'chromium', // chrome & edge
	    use: { ...devices['Desktop Chrome'] },
	},
    ],
})
