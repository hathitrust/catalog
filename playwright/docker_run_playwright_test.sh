#!/bin/bash

# This is started when you do e.g.
# `docker compose run --rm playwright`

npm config set update-notifier false
npm install

run_playwright_test(){
    speed=$1
    echo "running ${speed} playwright tests"
    npx -y playwright test --config=test/${speed}/playwright.config.ts test/${speed} --trace on
}

npm config set update-notifier false
# Run tests.
run_playwright_test "fast" && run_playwright_test "slow"
