const { test, expect } = require('@playwright/test');

// TODO refactor duplication for narrow versions -- extract function with a
// conditional for the 'Show Search Filters'?

// Click on "Latin" language facet and verify fewer results, then remove facet and verify more results.
// Verify Japanese language facet is hidden before, and visible after,
// expanding the language facet list.
test('Facets (non-mobile/fullwidth)', async ({ page, isMobile }) => {
  test.skip(isMobile === true, 'immobile-only test');
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
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
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
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
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
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
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
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

// Advanced Search adv=1 flag persistence
test('Advanced Search adv=1 is retained when selecting a facet', async ({ page, isMobile }) => {
  test.skip(isMobile === true, 'immobile-only test');
  await page.goto('/Search/Home?adv=1&lookfor=journal');
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  // Filter on Latin
  await page.getByRole('button', { name: "Language" }).click();
  await page.getByRole('link', { name: "Latin" }).click();
  // adv=1 is still in the URL
  await expect(page).toHaveURL(/adv=1/);
  // "Revise this advanced search" button is present
  await expect(page.getByRole('link', { name: 'Revise this advanced search' })).toBeVisible();
  // Remove the facet we just added
  await page.getByRole('link', { name: 'Remove filter Language: Latin' }).click();
  // adv=1 is still in the URL
  await expect(page).toHaveURL(/adv=1/);
  // "Revise this advanced search" button is still present
  await expect(page.getByRole('link', { name: 'Revise this advanced search' })).toBeVisible();
});

test('Advanced Search adv=1 is retained when selecting viewability facets', async ({ page, isMobile }) => {
  test.skip(isMobile === true, 'immobile-only test');
  await page.goto('/Search/Home?adv=1&setft=true&ft=ft&lookfor[]=turner&type[]=author');
  //await page.getByRole('button', { name: 'Allow all cookies' }).click();
  // Initial page of results is Full View
  // Filter on All Items
  await page.getByRole('link', { name: "All Items" }).click();
  // adv=1 is still in the URL
  await expect(page).toHaveURL(/adv=1/);
  // "Revise this advanced search" button is still present
  await expect(page.getByRole('link', { name: 'Revise this advanced search' })).toBeVisible();
  // Filter on Full View
  await page.getByRole('link', { name: "Full View" }).click();
  // adv=1 is still in the URL
  await expect(page).toHaveURL(/adv=1/);
  // "Revise this advanced search" button is still present
  await expect(page.getByRole('link', { name: 'Revise this advanced search' })).toBeVisible();
});

test('Advanced Search adv=1 is removed when removing advanced search terms', async ({ page, isMobile }) => {
  test.skip(isMobile === true, 'immobile-only test');
  await page.goto('/Search/Home?adv=1&setft=true&ft=ft&lookfor[]=jones&type[]=author');
  //await page.getByRole('button', { name: 'Allow all cookies' }).click();
  // Add another filter so there's still a filter in the list after we remove the advanced one.
  // This is so we can double check adv=1 is gone by looking for the "Revise..." button.
  await page.getByRole('button', { name: "Language" }).click();
  await page.getByRole('link', { name: "English" }).click();
  // Remove the author filter
  await page.getByRole('link', { name: 'Remove filter Author: jones' }).click();
  // adv=1 is no longer in the URL
  await expect(page).not.toHaveURL(/adv=1/);
  // "Revise this advanced search" button is no longer present
  await expect(page.locator('span:has-text("Revise this advanced search")')).toHaveCount(0);
});
