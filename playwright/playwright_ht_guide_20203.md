# Playwright Guide for HathiTrust catalog, May 2023

Playwright is a browser testing tool. It runs simulated browsers, in which it executes
tests, which generates a report.

If you are working with Playwright you are eventually going to need a local installation
of Node.js and the node-package. We're not going into detail how to do that here. In fact,
don't worry about it yet. We'll let you know when it is time to install Playwright.

# Playwright at Hathitrust, infrastructure

At the time of writing Playwright has been added as a component to one repository: the
catalog. This can probably be done in multiple ways, but here is how it was done.
There is more detail than can be covered, but here are some of the main broad strokes.

## Add a `playwright` service to `docker-compose` file

```yaml
services:
  ...
  playwright:
    image: mcr.microsoft.com/playwright:v1.32.0-focal
    volumes:
      - ./playwright:/usr/src/app
      - node_modules:/usr/src/app/node_modules
    working_dir: /usr/src/app
    depends_on:
      nginx:
        condition: service_healthy
    command: /bin/sh /usr/src/app/docker_run_playwright_test.sh
    profiles:
      - playwright
  ...
```

This uses MicroSoft's image (they are behind Playwright),
tells the service to wait for the `nginx` service to display signs of health,
and when ready to execute the bash script `docker_run_playwright_test.sh`.

Note that this `playwright` service and the `playwright` image are distinct from
your local Playwright installation. The the image runs the tests inside the service,
inside the Docker container.

We're also setting up a persistant volume to keep node modules, so that Node.js can
cache some files there:

```yaml
...
volumes:
  node_modules:
...
```

## The `playwright` directory

In the `playwright` directory:

```shell
$ ls -w1 catalog/playwright

docker_run_playwright_test.sh # runs the tests
node_modules/                 # cache for Node.js
package.json                  # metadata for Node.js
package-lock.json             # metadata for Node.js
playwright_ht_guide_20203.md  # you are looking at it
README.md                     # more specific info
test/                         # where the test files and test conf files are
test-results/                 # where results end up

```

### `docker_run_playwright_test.sh`

Runs the tests. Currently there are tests at 2 "speeds", fast and slow.
"Fast" tests only run a single browser and are meant to be more general,
and should ideally test more vital functions (like page is loading, logo is visible,
button is clickable) and simple workflows (enter text, hit submit, expect x, y and z).
"Slow" tests run the full set of browsers specified in the slow conf file
(see `projects:[...]` in `catalog/playwright/test/slow/playwright.config.ts`).
Slow tests are meant to test things that may differ between browsers. They are
more resource intensive and will only start running if the fast tests finished OK:

```shell
run_playwright_test "fast" && run_playwright_test "slow"
```

### `README.md`

A more detailed document on how to run Playwright locally for dev purposes.

### `test/`

The test directory is currently subdivided into `fast` and `slow`.
Each "speed" has its own configuration file, e.g.:

```shell
catalog$ ls -w1 playwright/test/*/playwright.config.ts
playwright/test/fast/playwright.config.ts
playwright/test/slow/playwright.config.ts

```

These config files are written in TypeScript. They can be in either JavaScript or
TypeScript, but almost all documentation uses TypeScript and this author does not
know either TypeScript or post-2010 JavaScript well enough to convert them on the fly,
nor does Playwright give the best warning messages when you mess up the conf files.
All this to say: yes, it is confusing that they are in TypeScript when nothing else is.

The main difference in the conf files is that the slow conf uses more browsers.

A future goal could be for all tests to inherit the same conf, and then just specify
the differences.

### Tests

Playwright will happily run all the tests it recursively finds under a given directory,
so if you have a lot of tests, or wildly different types of tests, it may make sense to
structure them into a directory tree. The catalog repo uses the following structure for
fast tests:

```shell
bugfix/      # test that a specific bug was fixed
emergency/   # check temporary error messages, ETAS
forms/       # form validation and submission
navigation/  # traditional end-to-end navigation tests
```

A test file can contain any number of tests. A file follows the template:

A test file starts with the boilerplate line:

```javascript
import { test, expect } from '@playwright/test';

```

... and then any number of uniquely named tests:

```javascript
test('UNIQUE_TEST_NAME', async ({ page, baseURL }) => {
    ...
});
```

A test generally consists, minimally, of 3 parts: go somewhere, look at something
and expect something. For navigation tests it makes sense to try and do as much of
the navigation in the test as possible, e.g.:

start at homepage, go to menu, click on link, expect something, etc. 

For other tests it makes more sense to go straight to the feature under test, e.g.:

```
test('norfolk', async ({ page, baseURL }) => {
    // go directly to search results and expect something
    await page.goto('/Search/Home?lookfor="norfolk');
    await expect(page.getByText('norfolk')).toBeVisible();
});

```
