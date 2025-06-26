const { test, expect } = require('@playwright/test');

// TODO refactor duplication for narrow versions -- extract function with a
// conditional for the 'Show Search Filters'?

// Click on "Latin" language facet and verify fewer results, then remove facet and verify more results.
// Verify Japanese language facet is hidden before, and visible after,
// expanding the language facet list.
test('Facets (non-mobile/fullwidth)', async ({ page, isMobile }) => {
  test.skip(isMobile === true, 'immobile-only test');
  await page.goto('/Search/Home?lookfor=journal');
  // Filter on Latin to get fewer results
  await page.getByRole('button', { name: "Language" }).click();
  await page.getByRole('link', { name: "Latin" }).click();
  // TODO ideally should get the original # of results and assert this is fewer
  await expect(page.getByRole('heading', { name: /3 results/i })).toBeVisible();
  // Facet is part of URL
  await expect(page).toHaveURL(/filter%5B%5D=language%3ALatin/);
  // Remove facet in active-filters-list to get full number of results
  await page.getByRole('link', { name: 'Remove filter Language: Latin' }).click();
  await expect(page.getByRole('heading', { name: /174 results/i })).toBeVisible();
  // Japanese is down in the mud
  await page.getByRole('button', { name: "Language" }).click();
  await expect(page.getByRole('link', { name: "Japanese" })).not.toBeVisible();
  // "Show all" to make all language facets visible
  await page.getByRole('button', { name: /Show all \d+ language filters/i }).click();
  await expect(page.getByRole('link', { name: "Japanese" })).toBeVisible();
});

// This is essentially the same test as above but with "Options/Filters" toggled when necessary.
test('Facets (mobile/width-shrink)', async ({ page }) => {
  page.setViewportSize({ width: 800, height: 1500 });
  await page.goto('/Search/Home?lookfor=journal');
  // Expand filters
  await page.getByRole('button', { name: "Show Search Filters" }).click();
  // Filter on Latin to get fewer results
  await page.getByRole('button', { name: "Language" }).click();
  await page.getByRole('link', { name: "Latin" }).click();
  // TODO ideally should get the original # of results and assert this is fewer
  await expect(page.getByRole('heading', { name: /3 results/i })).toBeVisible();
  // Expand filters (again)
  await page.getByRole('button', { name: "Show Search Filters" }).click();
  // Facet is part of URL
  await expect(page).toHaveURL(/filter%5B%5D=language%3ALatin/);
  // Remove facet in active-filters-list to get full number of results
  await page.getByRole('link', { name: 'Remove filter Language: Latin' }).click();
  await expect(page.getByRole('heading', { name: /174 results/i })).toBeVisible();
  // Expand filters (yet again)
  await page.getByRole('button', { name: "Show Search Filters" }).click();
  await page.getByRole('button', { name: "Language" }).click();
  // Japanese is down in the mud
  await expect(page.getByRole('link', { name: "Japanese" })).not.toBeVisible();
  // "Show all" to make all language facets visible
  await page.getByRole('button', { name: /Show all \d+ language filters/i }).click();
  await expect(page.getByRole('link', { name: "Japanese" })).toBeVisible();
});

// Back button and "Clear filters" button remove facets
// Note here we're using a wildcard search term so initial results should be unfaceted.
test('Clear facets (non-mobile/fullwidth)', async ({ page, isMobile }) => {
  test.skip(isMobile === true, 'immobile-only test');
  await page.goto('/Search/Home?lookfor=*');
  // No filters to be found
  await expect(page.getByRole('heading', { name: 'Current filters' })).not.toBeVisible();
  // Add language filter from the top options
  await page.getByRole('button', { name: "Language" }).click();
  await page.getByRole('link', { name: "English" }).click();
  // Now we have filters
  await expect(page.getByRole('heading', { name: 'Current filters' })).toBeVisible();
  // Clear filters
  await page.getByRole('link', { name: 'Clear filters' }).click();
  await expect(page.getByRole('heading', { name: 'Current filters' })).not.toBeVisible();
  // Return to filters
  await page.goBack();
  // Return to original search results
  await page.goBack();
  // Still no filters
  await expect(page.getByRole('heading', { name: 'Current filters' })).not.toBeVisible();
});

// This is essentially the same test as above but with "Options/Filters" toggled when necessary.
test('Clear facets (mobile/width-shrink)', async ({ page }) => {
  page.setViewportSize({ width: 800, height: 1500 });
  await page.goto('/Search/Home?lookfor=*');
  // No filters to be found
  await expect(page.getByRole('heading', { name: 'Current filters' })).not.toBeVisible();
  // Expand filters
  await page.getByRole('button', { name: "Show Search Filters" }).click();
  await page.getByRole('button', { name: "Language" }).click();
  // Add language filter from the top options
  await page.getByRole('link', { name: "English" }).click();
  // Expand filters (again)
  await page.getByRole('button', { name: "Show Search Filters" }).click();
  // Now we have filters
  await expect(page.getByRole('heading', { name: 'Current filters' })).toBeVisible();
  // Clear filters
  await page.getByRole('link', { name: 'Clear filters' }).click();
  await expect(page.getByRole('heading', { name: 'Current filters' })).not.toBeVisible();
  // Return to filters
  await page.goBack();
  // Return to original search results
  await page.goBack();
  // Still no filters
  await expect(page.getByRole('heading', { name: 'Current filters' })).not.toBeVisible();
});
