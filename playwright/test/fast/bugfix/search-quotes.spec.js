import { test, expect } from '@playwright/test';

test('smartquote_problem_talking_catalog', async ({ page, baseURL }) => {
    const warning = 'There was a problem talking to the catalog.'
    
    await page.goto('/Search/Home?lookfor=smart'); // no smart quote, no problem, no warning
    await expect(page.getByText(warning)).not.toBeVisible();

    await page.goto('/Search/Home?lookfor=“smart'); // smart quote, problem, warning
    await expect(page.getByText(warning)).toBeVisible();
});

test('unbalanced_quotes', async ({ page, baseURL }) => {
    /* 
       If an odd number of quotes are given in the search string, we remove the last one
       and display a little warning to that effect, and proceed as if there were an even
       number of quotes, i.e. run query and show results.
     */
    const warning = 'Your query contained ambiguous quotes and was modified by removing the last one.'
    const chaucer = 'Chaucer, a Norfolk man'

    await page.goto('/Search/Home?lookfor=norfolk');         // 0 quotes, balanced:
    await expect(page.getByText(warning)).not.toBeVisible(); // no warn
    await expect(page.getByText(chaucer)).toBeVisible();     // results visible
    
    await page.goto('/Search/Home?lookfor="norfolk');        // 1 quote, unbalanced:
    await expect(page.getByText(warning)).toBeVisible();     // expect warn
    await expect(page.getByText(chaucer)).toBeVisible();     // results visible
    
    await page.goto('/Search/Home?lookfor="norfolk"');       // 2 quotes, balanced:
    await expect(page.getByText(warning)).not.toBeVisible(); // no warn
    await expect(page.getByText(chaucer)).toBeVisible();     // results visible
    
    await page.goto('/Search/Home?lookfor="norfolk""');      // 3 quotes, unbalanced:
    await expect(page.getByText(warning)).toBeVisible();     // expect warn
    await expect(page.getByText(chaucer)).toBeVisible();     // results visible
    // etc
});
