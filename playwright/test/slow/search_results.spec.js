const { test, expect } = require('@playwright/test');

// Searching for a bogus value results in "no results" and no hits.
test('Failed search', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=xxzzxx');
  await expect(page.getByRole('heading', { name: 'No results matched your search.' })).toBeVisible();
  const articles = await page.getByRole('article');
  const count = await articles.count();
  expect(count).toEqual(0);
});

// Check all 20 (by default) results and make sure:
//   - Cover image is present in non-mobile scenarios
//   - Title, published date, and author are present
//   - Link to catalog record is present, but we don't simulate following it here.
//   - 
test('Article required elements', async ({ page, isMobile }) => {
  await page.goto('/Search/Home?lookfor=journal');
  const articles = await page.getByRole('article');
  const count = await articles.count();
  expect(count).toBeGreaterThan(0);
  for (let i = 0; i < count; i++) {
    let article = await articles.nth(i);
    await expect(article).toBeVisible();
    if (false === isMobile) {
      let image = await article.locator('div.cover');
      await expect(image).toBeVisible();
    }
    expect(await article.locator('span.title')).toBeVisible();
    expect(await article.getByText('Published').count()).toBeLessThanOrEqual(1);
    expect(await article.getByText('Author').count()).toBeLessThanOrEqual(1);
    expect(await article.getByRole('link', { name: 'Catalog Record' } )).toBeVisible();
    // Check for "Limited (Access Permitted)", "Full View" link, "Temporary Access",
    // "Limited (search only)" link, or "(view record to see multiple volumes)"
    const limited_access_permitted_link = article.getByRole('link', { name: 'Limited (Access Permitted)' });
    const full_view_link = article.getByRole('link', { name: 'Full view' });
    const temporary_access_link = article.getByRole('link', { name: 'Temporary Access' });
    const limited_search_only_link = article.getByRole('link', { name: 'Limited (search only)' });
    const view_record_text = article.getByText('(view record to see multiple volumes)');
    await expect(limited_access_permitted_link
      .or(full_view_link)
      .or(temporary_access_link)
      .or(limited_search_only_link)
      .or(view_record_text))
      .toBeVisible();
  }
});

// Make sure "Catalog Record" link goes to a "Record/nnnnnnnnn" page.
test('Follow link to catalog record', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('link', { name: 'Catalog Record' } ).first().click();
  await expect(page).toHaveURL(/Record\/\d{9}/);
});

// These tend to time out if the timeout is not boosted for the larger per page values,
// For some reason these are glacially slow on webkit and mobile Safari.
// especially on webkit and mobile Safari.
// For each of the values of the "results per page" menu:
//   - Make sure the "1 - X of Y Catalog results" header is present
//   - Make sure there are at least X items
test('Results per page', async ({ page }) => {
  test.setTimeout(60_000);
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('combobox', { name: 'Items per page' }).selectOption('20');
  await expect(page.getByRole('heading', { name: '1 - 20' })).toBeVisible();
  expect(await page.getByRole('article').count()).toEqual(20);
  await page.getByRole('combobox', { name: 'Items per page' }).selectOption('50');
  await expect(page.getByRole('heading', { name: '1 - 50', timeout: 50_000 } )).toBeVisible({ timeout: 50_000 });
  expect(await page.getByRole('article').count()).toBeGreaterThan(20);
  await page.getByRole('combobox', { name: 'Items per page' }).selectOption('100');
  await expect(page.getByRole('heading', { name: '1 - 100', timeout: 50_000 })).toBeVisible({ timeout: 50_000 });
  expect(await page.getByRole('article').count()).toBeGreaterThan(50);
});

// Make sure selecting each of the "Sort by" options results in the appropriate sort value.
// (Not sure how to verify that the records are actually sorted in the manner specified.)
test('Sort by', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=journal');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Date (newest first)');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('year');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Date (oldest first)');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('yearup');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Author');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('author');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Title');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('title');
  
});

// Check that next, previous, and numbered pagination links go where they are supposed to
// (based on  "n - m of x Catalog results" header)
test('Pagination', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=journal');
  // Go forward
  await page.getByRole('link', { name: 'Next Page' }).click();
  await expect(page.getByRole('heading', { name: /21 \- 40 of \d+ Catalog results/i })).toBeVisible();
  // Go back
  await page.getByRole('link', { name: 'Previous Page' }).click();
  await expect(page.getByRole('heading', { name: /1 \- 20 of \d+ Catalog results/i })).toBeVisible();
  // Go somewhere in the middle
  await page.getByRole('link', { name: 'Results Page 3' }).click();
  await expect(page.getByRole('heading', { name: /41 \- 60 of \d+ Catalog results/i })).toBeVisible();
});



  


