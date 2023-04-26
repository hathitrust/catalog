# Overview

This project has a service called `playwright` that can access localhost (nginx) and run `Playwright` tests against the (local) catalog.

## Run

`Playwright` tests can be run locally with: 

`$ docker-compose up`

... or:

`$ docker-compose run --rm playwright`

... or more interactively:

```
$ docker-compose run --rm playwright /bin/bash
root@b33fb015:/usr/src/app# bash docker_run_playwright_test.sh
...
```

`docker_run_playwright_test.sh` is just a wrapper, which runs all fast tests and (if all success) all slow tests.
If you want to run specific tests with specific configurations, look inside `docker_run_playwright_test.sh` for inspiration.

Tests also run as a GitHub action, see `.github/workflows/playwright.yml`.

## Traces

A trace is a file that describes what happened in a conducted test, and can be important for figuring out why a particular test failed.
A test only leaves a trace when you tell it to, which can be done as commandline args, `ENV` vars or config vars.
A common trick is to have traces off, but rerunning failing tests with trace on.
To view traces you should have `Node.js` and `Playwright` installed locally.
To avoid kerfuffle, try to keep your local versions as close to the versions in this project as possible.

### Local traces

Run tests with traces on, e.g. while developing:

```
$ docker-compose run --rm playwright npx -y playwright test --config=test/fast/playwright.config.ts --trace on
$ ...
$ npx playwright show-trace playwright/test-results/<test_name>/trace.zip
```

(You can also mess with the config file to get traces to always be on.)

### Github traces

A Github action runs the tests `on push`.
If any test failed, failed tests will be run again with trace on, generating an artifact under https://github.com/hathitrust/catalog/actions
Download your `trace.zip` file from Github, and do:

```
$ npx playwright show-trace /path/to/trace.zip
```

## Recording tests

Sometimes it's easier to record the tests than writing them from scratch. To do this, again, have `Node.js` installed locally, and run:

```
$ npx playwright codegen localhost:8080
```

... and follow instructions from e.g. https://playwright.dev/docs/codegen#generate-tests-with-the-playwright-inspector
