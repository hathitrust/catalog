const { test, expect } = require('@playwright/test');

// Searching for a bogus value results in "no results" and no hits.
test('Failed search', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=xxzzxx');
  await expect(page.getByText('No results matched your search.')).toBeVisible();
  const articles = await page.getByRole('article');
  const count = await articles.count();
  expect(count).toEqual(0);
});

// Check up to 20 results and make sure:
//   - Cover image is present in non-mobile scenarios
//   - Title, published date, and author are present
//   - Link to catalog record is present, but we don't simulate following it here.
//   - 
test('Article required elements', async ({ page, isMobile }) => {
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  const articles = await page.getByRole('article');
  let count = await articles.count();
  expect(count).toBeGreaterThan(0);

  // with firebird pages have 100 results, don't need to check all of them -- 20 is enough
  if (count > 20) { count = 20; }

  for (let i = 0; i < count; i++) {
    let article = await articles.nth(i);
    await expect(article).toBeVisible();
    // Covers won't properly load in dev since we don't have all the items in
    // the sample catalog in the repository. We could mock this out, perhaps.
    //
    // if (false === isMobile) {
    //   let image = await article.locator('div.cover');
    //   await expect(image).toBeVisible();
    // }
    expect(await article.locator('span.title')).toBeVisible();
    expect(await article.getByRole('link', { name: 'Catalog Record' } )).toBeVisible();
    // Check for "Limited (Access Permitted)", "Full View" link, "Temporary Access",
    // "Limited (search only)" link, or "Multiple Items"
    const limited_access_permitted_link = article.getByRole('link', { name: 'Limited (Access Permitted)' });
    const full_view_link = article.getByRole('link', { name: 'Full view' });
    const limited_search_only_link = article.getByRole('link', { name: 'Limited (search only)' });
    const view_record_text = article.getByRole('link', {name: 'Multiple Items'})
    await expect(limited_access_permitted_link
      .or(full_view_link)
      .or(limited_search_only_link)
      .or(view_record_text))
      .toBeVisible();
  }
});

// Make sure "Catalog Record" link goes to a "Record/nnnnnnnnn" page.
test('Follow link to catalog record', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  await page.getByRole('link', { name: 'Catalog Record' } ).first().click();
  await expect(page).toHaveURL(/Record\/\d{9}/);
});


// Make sure selecting each of the "Sort by" options results in the appropriate sort value.
// (Not sure how to verify that the records are actually sorted in the manner specified.)
test('Sort by', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('false');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Date (newest first)');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('year');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Date (oldest first)');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('yearup');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Author');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('author');
  await page.getByRole('combobox', { name: 'Sort by' }).selectOption('Title');
  await expect(page.getByRole('combobox', { name: 'Sort by' })).toHaveValue('title');
  
});

// Check that next and previous pagination links go where they are supposed to
// (based on  "n - m of x Catalog results" header)
test('Pagination', async ({ page }) => {
  await page.goto('/Search/Home?lookfor=journal');
  await page.getByRole('button', { name: 'Allow all cookies' }).click();
  // Go forward
  await page.getByRole('link', { name: 'Next' }).click();
  await expect(page.getByRole('heading', { name: /101 to \d+ of \d+ results/i })).toBeVisible();
  // This sometimes fails just by being slow in github..
  //  // Go back
  //  await page.getByRole('link', { name: 'Previous' }).click();
  //  await expect(page.getByRole('heading', { name: /1 to 100 of \d+ results/i })).toBeVisible();
});
