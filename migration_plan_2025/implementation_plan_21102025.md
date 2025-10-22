# HathiTrust Catalog: PHP 7.4 → 8.2 Migration Plan

**Document Version:** 1.0
**Date:** October 21, 2025
**Status:** Planning Phase
**Strategy:** PEAR-First → PHP Upgrade → Composer Migration

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Strategic Approach](#strategic-approach)
3. [Dependency Analysis](#dependency-analysis)
4. [Phase 0: PEAR Dependency Updates](#phase-0-pear-dependency-updates-week-1---php-74)
5. [Phase 1: Critical PHP Code Fixes](#phase-1-critical-php-code-fixes-week-2---still-php-74)
6. [Phase 2: Upgrade to PHP 8.0](#phase-2-upgrade-to-php-80-week-3-4)
7. [Phase 3: Upgrade to PHP 8.1](#phase-3-upgrade-to-php-81-week-5)
8. [Phase 4: Upgrade to PHP 8.2](#phase-4-upgrade-to-php-82-week-6)
9. [Phase 5: Comprehensive Testing](#phase-5-comprehensive-testing--hardening-week-7-8)
10. [Phase 6: Migrate to Composer](#phase-6-migrate-to-composer-week-9-10)
11. [Timeline Summary](#timeline-summary)
12. [Risk Mitigation](#risk-mitigation--rollback)
13. [Monitoring & Validation](#monitoring--validation)

---

## Executive Summary

### Project Overview

The HathiTrust Catalog is a PHP application that requires migration from PHP 7.4 to PHP 8.2 to maintain security, performance, and compatibility with modern infrastructure.

### Current State

- **PHP Version:** 7.4
- **Base Image:** Debian Bullseye
- **Dependency Manager:** PEAR (deprecated)
- **Architecture:** Mixed procedural/OOP, no namespaces
- **Test Coverage:** 5 PHPUnit tests, 17+ Playwright browser tests
- **Key Components:**
  - 23 system classes in `sys/`
  - 37 service controllers in `services/`
  - Smarty template engine
  - Apache Solr search integration
  - MARC record processing

### Target State

- **PHP Version:** 8.2
- **Base Image:** Debian Bookworm
- **Dependency Manager:** Composer
- **Architecture:** Modern autoloading, updated dependencies
- **Test Coverage:** Expanded unit tests, all integration tests passing

### Migration Philosophy

**Minimize disruption by upgrading dependencies before changing the dependency management system.**

1. Update PEAR packages to latest versions (still using PEAR)
2. Fix critical PHP code compatibility issues
3. Incrementally upgrade PHP versions (7.4 → 8.0 → 8.1 → 8.2)
4. Migrate from PEAR to Composer (final modernization step)

---

## Strategic Approach

### Why PEAR-First?

Traditional approach: "Switch to Composer first, then upgrade PHP"

**Our approach:** "Update PEAR dependencies → Upgrade PHP → Then migrate to Composer"

#### Benefits of PEAR-First Strategy:

1. **Reduced Complexity:** Each phase has one major change (dependencies OR PHP version OR package manager)
2. **Better Rollback:** Easier to identify what broke when changes are isolated
3. **Risk Mitigation:** PEAR packages are already working; update them before introducing Composer
4. **Incremental Testing:** Test each change independently
5. **Team Familiarity:** Developers can work with known PEAR setup during PHP upgrades

#### Migration Sequence:

```
PHP 7.4 + PEAR (old packages)
  ↓ Phase 0
PHP 7.4 + PEAR (updated packages) ← Update dependencies
  ↓ Phase 1
PHP 7.4 + PEAR (updated packages + fixed code) ← Fix compatibility issues
  ↓ Phase 2-4
PHP 8.2 + PEAR (updated packages + fixed code) ← Upgrade PHP incrementally
  ↓ Phase 5
PHP 8.2 + PEAR (tested & stable) ← Comprehensive testing
  ↓ Phase 6
PHP 8.2 + Composer ← Modernize dependency management
```

---

## Dependency Analysis

### Current PEAR Dependencies

From `Dockerfile` lines 29-49:

| Package | Current Status | Migration Path | PHP 8.2 Compatible? | Code Impact |
|---------|---------------|----------------|---------------------|-------------|
| **Auth_SASL** | ⚠️ Deprecated | → Auth_SASL2 | ✅ Yes | ⚠️ No code found using it |
| **HTTP_Request** | ⚠️ Deprecated | → HTTP_Request2 only | ❌ Remove | ⚠️ No direct usage found |
| **HTTP_Request2** | ✅ Current | Keep for now | ✅ Yes | ✅ **Used in 6 files** |
| **PhpDocumentor** | ⚠️ Deprecated | → phpdoc/phpdocumentor | N/A (dev tool) | ⚠️ No code usage found |
| DB | ✅ Maintained | Keep | ✅ Yes | Unknown (need to check) |
| DB_DataObject | ⚠️ Legacy | Keep for now | ⚠️ Risky | Unknown |
| File_CSV | ✅ Maintained | Keep | ✅ Yes | Unknown |
| File_MARC | ✅ Maintained | Keep | ✅ Yes | Unknown |
| HTTP_Session2-beta | ⚠️ Beta/Old | Keep for now | ⚠️ Risky | Unknown |
| Log | ✅ Maintained | Keep | ✅ Yes | Unknown |
| Mail | ✅ Maintained | Keep | ✅ Yes | Unknown |
| Net_SMTP | ✅ Maintained | Keep | ✅ Yes | Unknown |
| Net_URL_Mapper-beta | ⚠️ Beta/Old | Keep for now | ⚠️ Risky | Unknown |
| Pager | ✅ Maintained | Keep | ✅ Yes | Unknown |
| PHP_Compat | ⚠️ Obsolete | **Remove** | ❌ No | Legacy compatibility layer |
| Structures_DataGrid-beta | ⚠️ Beta/Old | Keep for now | ⚠️ Risky | ✅ **Used in sys/Datagrid.php** |
| Structures_LinkedList-beta | ⚠️ Beta/Old | Keep for now | ⚠️ Risky | Unknown |
| XML_Parser | ✅ Maintained | Keep | ✅ Yes | Unknown |
| XML_Beautifier | ✅ Maintained | Keep | ✅ Yes | Unknown |
| XML_Serializer-beta | ⚠️ Beta/Old | Keep for now | ⚠️ Risky | Unknown |

### HTTP_Request2 Usage Analysis

**Files using HTTP_Request2:**

1. `sys/SolrConnection.php:3,83` - Solr API client (core functionality)
2. `sys/Solr.php:23` - Solr wrapper
3. `sys/Zebra.php:23,78` - Zebra Z39.50 client
4. `services/Record/AJAX.php:55` - SFX API integration
5. `services/Record/Export.php:62` - RefWorks export
6. `bookcover.php:127,134,173` - Google Books API, Amazon API

**Good news:** Already using HTTP_Request2 (the newer version), not the deprecated HTTP_Request.

### Critical PHP Code Issues Found

| Issue | Location | Severity | PHP Version |
|-------|----------|----------|-------------|
| **Old-style constructors** | `sys/Datagrid.php:25`, `sys/Interface.php:26` | **CRITICAL** | Removed in PHP 8.0 |
| **Non-static method called statically** | `sys/AuthSpecs.php:12`, `sys/DSession.php:18`, `sys/VFUser.php:33`, `sys/VFSession.php:28` | **CRITICAL** | Deprecated 7.0, Error in 8.0 |
| **`each()` function** | `feedcreator/include/feedcreator.class.php:1426` | **HIGH** | Removed in PHP 7.2/8.0 |
| **Undefined array keys** | Multiple locations | **HIGH** | Warnings in 8.0+ |
| **Smarty version unknown** | `sys/Interface.php` | **MEDIUM** | Smarty 2 incompatible with PHP 8+ |

---

## Phase 0: PEAR Dependency Updates (Week 1) - PHP 7.4

### Objective

Update deprecated PEAR packages to their modern equivalents while remaining on PHP 7.4 and PEAR.

### Changes Required

#### 1. Update Dockerfile PEAR Dependencies

**File:** `Dockerfile` lines 29-49

```dockerfile
# BEFORE (Current - lines 29-49)
RUN pear channel-update pear.php.net && pear install \
      Auth_SASL \
      DB \
      DB_DataObject \
      File_CSV \
      File_MARC \
      HTTP_Request \
      HTTP_Request2 \
      HTTP_Session2-beta \
      Log \
      Mail \
      Net_SMTP \
      Net_URL_Mapper-beta \
      Pager \
      PhpDocumentor \
      PHP_Compat \
      Structures_DataGrid-beta \
      Structures_LinkedList-beta \
      XML_Parser \
      XML_Beautifier \
      XML_Serializer-beta

# AFTER (Phase 0 - Updated PEAR packages, still PHP 7.4)
RUN pear channel-update pear.php.net && pear install \
      Auth_SASL2 \
      DB \
      DB_DataObject \
      File_CSV \
      File_MARC \
      HTTP_Request2 \
      HTTP_Session2-beta \
      Log \
      Mail \
      Net_SMTP \
      Net_URL_Mapper-beta \
      Pager \
      Structures_DataGrid-beta \
      Structures_LinkedList-beta \
      XML_Parser \
      XML_Beautifier \
      XML_Serializer-beta
```

**Changes Summary:**
- ✅ `Auth_SASL` → `Auth_SASL2` (addresses deprecation warning)
- ❌ **Removed** `HTTP_Request` (deprecated, not used)
- ❌ **Removed** `PhpDocumentor` (deprecated, dev tool only)
- ❌ **Removed** `PHP_Compat` (obsolete for PHP 7.4+)

#### 2. Verify No Code Changes Needed

**Verification Results:**

```bash
# Auth_SASL usage check
grep -r "Auth_SASL" --include="*.php" .
# Result: No code uses Auth_SASL directly

# HTTP_Request usage check
grep -r "require.*HTTP/Request.php" --include="*.php" .
# Result: No code uses HTTP_Request (only HTTP_Request2)

# PhpDocumentor usage check
grep -r "PhpDocumentor" --include="*.php" .
# Result: No code uses PhpDocumentor (it's a CLI tool)
```

**Conclusion:** Phase 0 requires **ZERO code changes** - only Dockerfile updates.

### Testing Procedures

```bash
# 1. Build new Docker image with updated PEAR packages
docker compose build vufind

# 2. Start application
docker compose up -d

# 3. Verify Solr connectivity (uses HTTP_Request2)
curl "http://localhost:8080/Search/Home?lookfor=test"

# 4. Run PHPUnit tests
docker compose run phpunit

# 5. Run Playwright browser tests
docker compose run playwright

# 6. Check for errors in logs
docker compose logs vufind | grep -i "error\|warning\|fatal"
```

### Success Criteria

- ✅ Docker build completes without errors
- ✅ Application starts successfully
- ✅ All 5+ PHPUnit tests pass
- ✅ All 17+ Playwright tests pass
- ✅ No PHP errors/warnings in logs
- ✅ Search functionality works
- ✅ Record display works
- ✅ Export functionality works

### Deliverables

- [ ] Updated `Dockerfile` with modern PEAR packages
- [ ] Documentation in `docs/pear-dependencies.md`
- [ ] Test results report
- [ ] Git commit: "Phase 0: Update deprecated PEAR packages"

### Phase 0 Checklist

- [ ] Update Dockerfile: `Auth_SASL` → `Auth_SASL2`
- [ ] Remove `HTTP_Request` from Dockerfile
- [ ] Remove `PhpDocumentor` from Dockerfile
- [ ] Remove `PHP_Compat` from Dockerfile
- [ ] Build Docker image
- [ ] Test application startup
- [ ] Run PHPUnit tests
- [ ] Run Playwright tests
- [ ] Check error logs
- [ ] Create `docs/pear-dependencies.md`
- [ ] Commit changes
- [ ] Create git tag: `migration-phase0-complete`

---

## Phase 1: Critical PHP Code Fixes (Week 2) - Still PHP 7.4

### Objective

Fix code issues that will cause fatal errors in PHP 8.0+ while remaining on PHP 7.4.

### Code Changes Required

#### 1. Fix Old-Style Constructors ⚠️ CRITICAL

**File:** `sys/Datagrid.php` (lines 25-27)

```php
// BEFORE
class DataGrid extends Structures_DataGrid {
    function DataGrid($limit = null, $page = 1)
    {
        parent::Structures_DataGrid($limit, $page);

// AFTER
class DataGrid extends Structures_DataGrid {
    function __construct($limit = null, $page = 1)
    {
        parent::__construct($limit, $page);
```

**File:** `sys/Interface.php` (lines 26-91)

```php
// BEFORE (line 26)
class UInterface extends Smarty
{
    function UInterface()
    {

// AFTER
class UInterface extends Smarty
{
    function __construct()
    {
```

#### 2. Fix Non-Static Methods Called Statically ⚠️ CRITICAL

**File:** `sys/AuthSpecs.php` (line 12)

```php
// BEFORE
class AuthSpecs
{
  private static $instance = false;
  private static $data;

  private function __construct($file) {
    self::$data = yaml_parse_file($file);
  }

  public function singleton($file = 'config/authspecs.yaml') {

// AFTER
class AuthSpecs
{
  private static $instance = false;
  private static $data;

  private function __construct($file) {
    self::$data = yaml_parse_file($file);
  }

  public static function singleton($file = 'config/authspecs.yaml') {
```

**File:** `sys/DSession.php` (line 18)

```php
// BEFORE
protected function singleton($cookiename, $cookieargs = false, $dargs=false)

// AFTER
protected static function singleton($cookiename, $cookieargs = false, $dargs=false)
```

**File:** `sys/VFSession.php` (line 28)

```php
// BEFORE
function instance($confdir = false)

// AFTER
static function instance($confdir = false)
```

**File:** `sys/VFUser.php` - **RECOMMEND DELETION**

This file contains the comment: `/* Not used for HathiTrust. */`

```bash
# Verify it's not referenced anywhere
grep -r "VFUser" --include="*.php" --exclude="sys/VFUser.php" .

# If no results (except the file itself), safe to delete
git rm sys/VFUser.php
```

#### 3. Fix `each()` Function ⚠️ HIGH PRIORITY

**File:** `feedcreator/include/feedcreator.class.php` (line 1426)

```php
// BEFORE
while( list(, $line) = each($lines) ) {

// AFTER
foreach($lines as $line) {
```

**Note:** The `feedcreator` library is very old and should be replaced in Phase 6 with a modern RSS library.

#### 4. Fix Undefined Array Key Access ⚠️ MEDIUM PRIORITY

**Strategy:** Use PHPStan to identify all instances, then fix systematically.

**Common Pattern:**

```php
// BEFORE (causes "Undefined array key" warning in PHP 8.0+)
$value = $_GET['key'];
$value = $_REQUEST['param'];
$value = $array['index'];

// AFTER - Option 1: Null coalescing operator (PHP 7.0+)
$value = $_GET['key'] ?? null;
$value = $_REQUEST['param'] ?? '';
$value = $array['index'] ?? 'default';

// AFTER - Option 2: isset() check
$value = isset($_GET['key']) ? $_GET['key'] : null;
```

**Implementation:**

```bash
# Install PHPStan via PHAR (before Composer migration)
curl -L -o phpstan.phar https://github.com/phpstan/phpstan/releases/download/1.10.50/phpstan.phar
chmod +x phpstan.phar

# Run analysis to find undefined array key issues
./phpstan.phar analyse --level=0 sys/ services/ static/ > phpstan-report.txt

# Review report and fix issues systematically
```

**Example locations to check:**
- `$_GET`, `$_POST`, `$_REQUEST` access
- `$_SERVER` access (especially `HTTP_REFERER`, `REMOTE_ADDR`)
- `$_COOKIE` access
- Configuration array access (`$configArray['key']`)
- Method parameters that are arrays

### Testing Procedures

```bash
# 1. Rebuild Docker image (still PHP 7.4)
docker compose build

# 2. Start application
docker compose up -d

# 3. Run PHPUnit tests
docker compose run phpunit

# 4. Run Playwright tests
docker compose run playwright

# 5. Check for PHP notices/warnings
docker compose logs vufind | grep -i "deprecated\|notice\|warning"

# 6. Manual smoke tests
curl "http://localhost:8080/Search/Home"
curl "http://localhost:8080/Record/000000001"
curl "http://localhost:8080/Record/000000001.json"
```

### Success Criteria

- ✅ All old-style constructors converted
- ✅ All singleton methods are static
- ✅ No `each()` function usage
- ✅ Reduced/eliminated undefined array key warnings
- ✅ All tests pass
- ✅ No new errors introduced

### Deliverables

- [ ] Updated PHP files with fixes
- [ ] PHPStan analysis report
- [ ] List of undefined array key fixes
- [ ] Test results
- [ ] Git commit: "Phase 1: Fix PHP 8.0 compatibility issues"

### Phase 1 Checklist

- [ ] Fix `sys/Datagrid.php` constructor
- [ ] Fix `sys/Interface.php` constructor
- [ ] Fix `sys/AuthSpecs.php` static method
- [ ] Fix `sys/DSession.php` static method
- [ ] Fix `sys/VFSession.php` static method
- [ ] Verify `VFUser.php` is unused
- [ ] Delete `sys/VFUser.php` (if unused)
- [ ] Fix `feedcreator/include/feedcreator.class.php` each() usage
- [ ] Install PHPStan (PHAR version)
- [ ] Run PHPStan analysis
- [ ] Fix undefined array key warnings (prioritize high-traffic code)
- [ ] Test all changes
- [ ] Run full test suite
- [ ] Commit changes
- [ ] Create git tag: `migration-phase1-complete`

---

## Phase 2: Upgrade to PHP 8.0 (Week 3-4)

### Objective

Upgrade from PHP 7.4 to PHP 8.0 while maintaining PEAR dependencies.

### Infrastructure Changes

#### 1. Update Dockerfile

**File:** `Dockerfile`

```dockerfile
# Line 2 - Keep Debian Bullseye (supports both 7.4 and 8.0)
FROM debian:bullseye

# Lines 6-22 - Update package names from 7.4 to 8.0
RUN apt-get update && apt-get install -y \
      curl \
      msmtp-mta \
      bsd-mailx \
      php8.0-curl \
      php8.0-fpm \
      php8.0-gd \
      php8.0-http \
      php8.0-ldap \
      php8.0-mysql \
      php8.0-mdb2 \
      php8.0-mdb2-driver-mysql \
      php8.0-xdebug \
      php8.0-xsl \
      php8.0-mbstring \
      pear-channels \
      php-yaml

# Line 26-27 - Update PHPUnit version
RUN curl -O https://phar.phpunit.de/phpunit-9.6.11.phar
RUN chmod +x phpunit-9.6.11.phar && mv phpunit-9.6.11.phar /usr/local/bin/phpunit

# Line 71 - Update PHP-FPM path
COPY ./docker/php_pool.conf /etc/php/8.0/fpm/pool.d/www.conf
```

#### 2. Update docker/run_catalog.sh

```bash
#!/bin/bash

# Change from php-fpm7.4 to php-fpm8.0
/usr/sbin/php-fpm8.0 --nodaemonize --fpm-config /etc/php/8.0/fpm/php-fpm.conf
```

### PHP 8.0 Breaking Changes to Monitor

1. **Named arguments** - New feature, shouldn't affect existing code
2. **Attributes (annotations)** - New feature, won't break existing code
3. **Match expressions** - New feature
4. **Stricter type checking** - May expose bugs
5. **String/number handling** - Non-numeric strings cause TypeError
6. **Error handling** - Some warnings promoted to Error exceptions

### Testing Procedures

```bash
# 1. Build Docker image with PHP 8.0
docker compose build vufind

# 2. Start application and monitor logs
docker compose up -d
docker compose logs -f vufind

# 3. Check PHP version
docker compose exec vufind php -v
# Expected: PHP 8.0.x

# 4. Run PHPUnit tests
docker compose run phpunit

# 5. Run Playwright tests
docker compose run playwright

# 6. Check for PHP 8.0 specific errors
docker compose logs vufind | grep -i "fatal\|error\|warning"

# 7. Manual testing
curl "http://localhost:8080/Search/Home?lookfor=test"
curl "http://localhost:8080/Record/000000001"
curl "http://localhost:8080/Record/000000001.marc"
curl "http://localhost:8080/Record/000000001.json"

# 8. Test exports
curl "http://localhost:8080/Record/Export/Home?id=000000001&style=bibtex"

# 9. Test API endpoints
curl "http://localhost:8080/static/api/volumes.php?id=000000001"
```

### Common Issues to Watch For

1. **Type juggling changes**
   - Non-numeric string + number = TypeError
   - Check arithmetic operations

2. **Null handling**
   - Functions that previously accepted null may throw TypeError
   - Check function parameters

3. **String handling**
   - Empty string access (`$str[0]` where `$str = ""`) returns null instead of false

### Success Criteria

- ✅ Docker image builds successfully with PHP 8.0
- ✅ Application starts without fatal errors
- ✅ PHP version is 8.0.x
- ✅ All PHPUnit tests pass
- ✅ All Playwright tests pass
- ✅ No PHP 8.0 fatal errors in logs
- ✅ Search functionality works
- ✅ Record display works (all formats)
- ✅ Export functionality works

### Deliverables

- [ ] Updated `Dockerfile` for PHP 8.0
- [ ] Updated `docker/run_catalog.sh`
- [ ] Test results report
- [ ] List of any issues found and resolved
- [ ] Performance comparison with PHP 7.4
- [ ] Git commit: "Phase 2: Upgrade to PHP 8.0"

### Phase 2 Checklist

- [ ] Update Dockerfile packages to `php8.0-*`
- [ ] Update `docker/run_catalog.sh` to `php-fpm8.0`
- [ ] Update `docker/php_pool.conf` path to `/etc/php/8.0/`
- [ ] Build Docker image
- [ ] Verify PHP version
- [ ] Test application startup
- [ ] Check error logs
- [ ] Run PHPUnit tests
- [ ] Run Playwright tests
- [ ] Manual smoke testing (search, records, exports)
- [ ] Performance testing
- [ ] Document any issues
- [ ] Commit changes
- [ ] Create git tag: `migration-phase2-php80`

---

## Phase 3: Upgrade to PHP 8.1 (Week 5)

### Objective

Upgrade from PHP 8.0 to PHP 8.1.

### Infrastructure Changes

#### 1. Update to Debian Bookworm

**File:** `Dockerfile`

```dockerfile
# Line 2 - Upgrade to Bookworm (has PHP 8.1)
FROM debian:bookworm

# Lines 6-22 - Update to PHP 8.1 packages
RUN apt-get update && apt-get install -y \
      curl \
      msmtp-mta \
      bsd-mailx \
      php8.1-curl \
      php8.1-fpm \
      php8.1-gd \
      php8.1-http \
      php8.1-ldap \
      php8.1-mysql \
      php8.1-xdebug \
      php8.1-xsl \
      php8.1-mbstring \
      pear-channels \
      php-yaml

# Note: php-mdb2 packages may need verification on Bookworm

# Line 71 - Update PHP-FPM path
COPY ./docker/php_pool.conf /etc/php/8.1/fpm/pool.d/www.conf
```

#### 2. Update docker/run_catalog.sh

```bash
#!/bin/bash

# Change to php-fpm8.1
/usr/sbin/php-fpm8.1 --nodaemonize --fpm-config /etc/php/8.1/fpm/php-fpm.conf
```

### PHP 8.1 Changes to Address

#### 1. Passing null to Non-Nullable Internal Function Parameters

**Issue:** PHP 8.1 deprecates passing `null` to internal functions that don't accept nullable parameters.

**Example:**

```php
// BEFORE (causes deprecation warning in PHP 8.1)
strlen(null);
htmlspecialchars(null);

// AFTER
strlen($var ?? '');
htmlspecialchars($var ?? '');
```

**Action:**

```bash
# Find potential issues
grep -rn "strlen\|htmlspecialchars\|trim\|explode" --include="*.php" sys/ services/

# Review and fix cases where null might be passed
```

#### 2. Deprecated ${} String Interpolation

**Issue:** `${var}` and `${expr}` are deprecated in favor of `{$var}`.

**Example:**

```php
// BEFORE (deprecated in PHP 8.1)
$str = "Hello ${name}";
$str = "Value: ${getValue()}";

// AFTER
$str = "Hello {$name}";
$str = "Value: " . getValue();
```

**Action:**

```bash
# Find ${} usage
grep -rn '\${' --include="*.php" .

# Replace with {$}
# Review each instance and update
```

#### 3. Serializable Interface Deprecation

**Issue:** Old `Serializable` interface is deprecated in favor of `__serialize()` and `__unserialize()`.

**Action:**

```bash
# Check if any classes implement Serializable
grep -rn "implements.*Serializable" --include="*.php" .

# If found, update to use magic methods
```

### Testing Procedures

```bash
# 1. Build Docker image with PHP 8.1
docker compose build vufind

# 2. Start and monitor
docker compose up -d
docker compose logs -f vufind

# 3. Check PHP version
docker compose exec vufind php -v
# Expected: PHP 8.1.x

# 4. Look for deprecation warnings
docker compose logs vufind | grep -i "deprecated"

# 5. Run full test suite
docker compose run phpunit
docker compose run playwright

# 6. Manual testing
curl "http://localhost:8080/Search/Home?lookfor=test"
curl "http://localhost:8080/Record/000000001"
```

### Success Criteria

- ✅ Docker image builds with PHP 8.1
- ✅ Application starts successfully
- ✅ PHP version is 8.1.x
- ✅ All tests pass
- ✅ Minimal deprecation warnings
- ✅ No fatal errors

### Deliverables

- [ ] Updated `Dockerfile` for PHP 8.1
- [ ] Updated `docker/run_catalog.sh`
- [ ] Fixes for null parameter issues (if any)
- [ ] Fixes for ${} string interpolation (if any)
- [ ] Test results
- [ ] Git commit: "Phase 3: Upgrade to PHP 8.1"

### Phase 3 Checklist

- [ ] Update Dockerfile to Debian Bookworm
- [ ] Update packages to `php8.1-*`
- [ ] Update `docker/run_catalog.sh` to `php-fpm8.1`
- [ ] Update PHP-FPM config path to `/etc/php/8.1/`
- [ ] Build Docker image
- [ ] Scan for `${var}` syntax
- [ ] Fix string interpolation issues (if found)
- [ ] Check for null parameter issues
- [ ] Fix null parameter warnings (if found)
- [ ] Run PHPUnit tests
- [ ] Run Playwright tests
- [ ] Check deprecation warnings
- [ ] Manual testing
- [ ] Commit changes
- [ ] Create git tag: `migration-phase3-php81`

---

## Phase 4: Upgrade to PHP 8.2 (Week 6)

### Objective

Upgrade from PHP 8.1 to PHP 8.2 (final target version).

### Infrastructure Changes

#### 1. Update Dockerfile

**File:** `Dockerfile`

```dockerfile
# Line 2 - Stay on Bookworm
FROM debian:bookworm

# Lines 6-23 - Update to PHP 8.2 + add php-raphf
RUN apt-get update && apt-get install -y \
      curl \
      msmtp-mta \
      bsd-mailx \
      php8.2-curl \
      php8.2-fpm \
      php8.2-gd \
      php8.2-http \
      php8.2-ldap \
      php8.2-mysql \
      php8.2-xdebug \
      php8.2-xsl \
      php8.2-mbstring \
      php8.2-xml \
      php-raphf \
      pear-channels \
      php-yaml \
      && rm -rf /var/lib/apt/lists/*

# Line 71 - Update PHP-FPM path
COPY ./docker/php_pool.conf /etc/php/8.2/fpm/pool.d/www.conf
```

**Key changes:**
- Updated all packages to `php8.2-*`
- Added `php-raphf` dependency (as you specified)
- Added `php8.2-xml` (explicit dependency)
- Added cleanup to reduce image size

#### 2. Update docker/run_catalog.sh

```bash
#!/bin/bash

# Change to php-fpm8.2
/usr/sbin/php-fpm8.2 --nodaemonize --fpm-config /etc/php/8.2/fpm/php-fpm.conf
```

### PHP 8.2 Changes to Address

#### 1. Dynamic Properties Deprecated

**Issue:** Creating properties on an object that weren't declared is deprecated.

**Example of the warning:**
```
Deprecated: Creation of dynamic property MyClass::$undeclaredProperty is deprecated
```

**Solution Option 1:** Add attribute to allow dynamic properties (quick fix)

```php
#[AllowDynamicProperties]
class MyClass {
    // existing code
}
```

**Solution Option 2:** Declare all properties explicitly (preferred)

```php
class MyClass {
    public $property1;
    public $property2;
    private $internalProperty;

    // existing code
}
```

**Implementation:**

```bash
# 1. Run application and capture warnings
docker compose up -d
docker compose logs vufind 2>&1 | grep "Creation of dynamic property" > dynamic-props.txt

# 2. Review dynamic-props.txt and fix each class
# For each class, either:
#   - Add #[AllowDynamicProperties] attribute (temporary)
#   - Declare properties explicitly (permanent)

# 3. Common locations to check:
# - sys/*.php classes
# - services/**/*.php classes
# - Any classes extending Smarty, PEAR classes, etc.
```

#### 2. utf8_encode() / utf8_decode() Deprecated

**Issue:** These functions are deprecated in favor of `mb_convert_encoding()`.

**Example:**

```php
// BEFORE
$encoded = utf8_encode($str);
$decoded = utf8_decode($str);

// AFTER
$encoded = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
$decoded = mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
```

**Action:**

```bash
# Find usage
grep -rn "utf8_encode\|utf8_decode" --include="*.php" .

# Replace with mb_convert_encoding
```

#### 3. Other Deprecations

- Callables with `${var}` in strings
- Partial datetime format strings
- `libxml_disable_entity_loader()` (no-op)

### Testing Procedures

```bash
# 1. Build with PHP 8.2
docker compose build vufind

# 2. Start and monitor for deprecation warnings
docker compose up -d
docker compose logs -f vufind | tee php82-startup.log

# 3. Verify PHP version
docker compose exec vufind php -v
# Expected: PHP 8.2.x

# 4. Check for dynamic property warnings
docker compose logs vufind 2>&1 | grep "dynamic property"

# 5. Run full test suite
docker compose run phpunit
docker compose run playwright

# 6. Comprehensive manual testing
./test-critical-paths.sh  # Create this script
```

### Success Criteria

- ✅ Docker image builds with PHP 8.2
- ✅ php-raphf dependency installed
- ✅ Application starts successfully
- ✅ PHP version is 8.2.x
- ✅ All tests pass
- ✅ Dynamic property warnings addressed
- ✅ No fatal errors
- ✅ Performance is acceptable

### Deliverables

- [ ] Updated `Dockerfile` for PHP 8.2 with php-raphf
- [ ] Updated `docker/run_catalog.sh`
- [ ] Fixed dynamic property issues
- [ ] Fixed utf8_encode/decode usage (if any)
- [ ] Test results
- [ ] Performance comparison
- [ ] Git commit: "Phase 4: Upgrade to PHP 8.2"

### Phase 4 Checklist

- [ ] Update Dockerfile packages to `php8.2-*`
- [ ] Add `php-raphf` to Dockerfile
- [ ] Add `php8.2-xml` to Dockerfile
- [ ] Update `docker/run_catalog.sh` to `php-fpm8.2`
- [ ] Update PHP-FPM config path to `/etc/php/8.2/`
- [ ] Build Docker image
- [ ] Verify PHP version is 8.2
- [ ] Run application and capture dynamic property warnings
- [ ] Fix dynamic property issues
- [ ] Check for utf8_encode/decode usage
- [ ] Fix utf8 encoding issues (if found)
- [ ] Run PHPUnit tests
- [ ] Run Playwright tests
- [ ] Check all deprecation warnings
- [ ] Manual comprehensive testing
- [ ] Performance testing
- [ ] Commit changes
- [ ] Create git tag: `migration-phase4-php82`

---

## Phase 5: Comprehensive Testing & Hardening (Week 7-8)

### Objective

Ensure the application is stable, performant, and production-ready on PHP 8.2 before migrating to Composer.

### Testing Matrix

| Test Type | Tool | Target | Success Criteria |
|-----------|------|--------|------------------|
| **Unit Tests** | PHPUnit | Core logic | All tests pass, coverage report |
| **Integration Tests** | Playwright | UI/UX | All 17+ tests pass |
| **Smoke Tests** | Manual/Script | Critical paths | All core functionality works |
| **Performance Tests** | Apache Bench | Response times | No degradation >10% from PHP 7.4 |
| **Load Tests** | Apache Bench | Concurrent users | Handle expected load |
| **Error Monitoring** | Docker logs | Runtime errors | No warnings/errors during normal use |
| **Memory Tests** | PHP profiling | Memory usage | No memory leaks |
| **Security Tests** | Manual | Input validation | No new vulnerabilities |

### Testing Procedures

#### 1. Unit Testing

```bash
# Run PHPUnit with coverage
docker compose run phpunit --coverage-html coverage

# Review coverage report
open coverage/index.html

# Ensure coverage doesn't decrease from baseline
```

**Expand test coverage** (if time permits):

Create tests for:
- Session management (`sys/DSession.php`, `sys/VFSession.php`)
- Search logic (`services/Search/`)
- Record handling (`services/Record/`)
- Solr integration (`sys/SolrConnection.php`)

#### 2. Integration Testing

```bash
# Run all Playwright tests
docker compose run playwright

# Run with reporter for detailed output
docker compose run -e CI=true playwright --reporter=html

# Review test results
open playwright-report/index.html
```

#### 3. Smoke Testing

Create `scripts/smoke-test.sh`:

```bash
#!/bin/bash
set -e

BASE_URL="http://localhost:8080"
PASS=0
FAIL=0

echo "=== HathiTrust Catalog Smoke Tests ==="
echo "Base URL: $BASE_URL"
echo ""

# Test 1: Homepage
echo -n "Testing homepage... "
if curl -sf "$BASE_URL/" | grep -q "HathiTrust"; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

# Test 2: Search
echo -n "Testing search... "
if curl -sf "$BASE_URL/Search/Home?lookfor=test&type=all" | grep -q "results"; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

# Test 3: Record view
echo -n "Testing record view... "
if curl -sf "$BASE_URL/Record/000000001" | grep -q "Record"; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

# Test 4: MARC export
echo -n "Testing MARC export... "
if curl -sf "$BASE_URL/Record/000000001.marc" | head -c 5 | grep -q "00"; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

# Test 5: JSON export
echo -n "Testing JSON export... "
if curl -sf "$BASE_URL/Record/000000001.json" | python3 -m json.tool > /dev/null 2>&1; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

# Test 6: XML export
echo -n "Testing XML export... "
if curl -sf "$BASE_URL/Record/000000001.xml" | grep -q "<?xml"; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

# Test 7: API endpoint
echo -n "Testing API... "
if curl -sf "$BASE_URL/static/api/volumes.php?id=000000001" | python3 -m json.tool > /dev/null 2>&1; then
    echo "✅ PASS"
    ((PASS++))
else
    echo "❌ FAIL"
    ((FAIL++))
fi

echo ""
echo "=== Results ==="
echo "Passed: $PASS"
echo "Failed: $FAIL"

if [ $FAIL -eq 0 ]; then
    echo "✅ All smoke tests passed!"
    exit 0
else
    echo "❌ Some tests failed"
    exit 1
fi
```

```bash
chmod +x scripts/smoke-test.sh
./scripts/smoke-test.sh
```

#### 4. Performance Testing

Create `scripts/performance-test.sh`:

```bash
#!/bin/bash

BASE_URL="http://localhost:8080"
CONCURRENCY=10
REQUESTS=1000

echo "=== Performance Testing ==="
echo "Installing apache2-utils..."
docker compose exec vufind apt-get update -qq
docker compose exec vufind apt-get install -y apache2-utils

echo ""
echo "Test 1: Homepage"
ab -n $REQUESTS -c $CONCURRENCY "$BASE_URL/" | grep "Requests per second\|Time per request"

echo ""
echo "Test 2: Search"
ab -n $REQUESTS -c $CONCURRENCY "$BASE_URL/Search/Home?lookfor=test&type=all" | grep "Requests per second\|Time per request"

echo ""
echo "Test 3: Record View"
ab -n $REQUESTS -c $CONCURRENCY "$BASE_URL/Record/000000001" | grep "Requests per second\|Time per request"

echo ""
echo "Test 4: JSON Export"
ab -n $REQUESTS -c $CONCURRENCY "$BASE_URL/Record/000000001.json" | grep "Requests per second\|Time per request"
```

**Create baseline and compare:**

```bash
# Save PHP 7.4 baseline (if you have it)
./scripts/performance-test.sh > baseline-php74-performance.txt

# Test PHP 8.2
./scripts/performance-test.sh > php82-performance.txt

# Compare
diff baseline-php74-performance.txt php82-performance.txt
```

#### 5. Error Monitoring

```bash
# Monitor logs in real-time
docker compose logs -f vufind | grep -i "error\|warning\|fatal\|deprecated"

# Check logs for specific period
docker compose logs --since 1h vufind | grep -i "error\|warning" > error-log.txt

# Analyze error patterns
cat error-log.txt | sort | uniq -c | sort -rn
```

#### 6. Memory Profiling

```bash
# Check memory usage
docker compose exec vufind php -r "echo 'Memory limit: ' . ini_get('memory_limit') . PHP_EOL;"

# Monitor memory during load test
docker stats vufind --no-stream
```

### Regression Testing Checklist

- [ ] Homepage loads correctly
- [ ] Search returns results
- [ ] Faceted search works
- [ ] All facet filters work
- [ ] Pagination works
- [ ] Record pages display correctly
- [ ] All record formats work (HTML, MARC, XML, JSON)
- [ ] Export functions work (BibTeX, EndNote, RIS, RefWorks)
- [ ] Citations display correctly
- [ ] API endpoints respond correctly
- [ ] Book cover images load
- [ ] Advanced search works (if enabled)
- [ ] Institution-based access works (ETAS)
- [ ] Session management works
- [ ] GeoIP filtering works (if enabled)
- [ ] RSS feeds work (if used)
- [ ] Email functionality works (if used)

### Create Baseline Metrics Document

Create `docs/baseline-php82-pear.md`:

```markdown
# PHP 8.2 + PEAR Baseline Metrics

**Date:** [Current Date]
**PHP Version:** 8.2.x
**Dependency Manager:** PEAR

## Test Results

### Unit Tests (PHPUnit)
- Tests: X passed, 0 failed
- Coverage: X%

### Integration Tests (Playwright)
- Tests: X passed, 0 failed

### Performance
- Homepage: X requests/sec
- Search: X requests/sec
- Record View: X requests/sec
- JSON Export: X requests/sec

### Resource Usage
- Memory Limit: 256M
- Average Memory Usage: XMB
- Peak Memory Usage: XMB

## Issues Found

[Document any issues discovered during testing]

## Notes

[Any other relevant information]
```

### Success Criteria

- ✅ All PHPUnit tests pass (5+ tests)
- ✅ All Playwright tests pass (17+ tests)
- ✅ All smoke tests pass
- ✅ Performance is within 10% of baseline
- ✅ No errors/warnings in logs during normal operation
- ✅ Memory usage is stable
- ✅ All regression tests pass
- ✅ Stakeholder approval for Composer migration

### Deliverables

- [ ] Complete test results report
- [ ] Performance comparison document
- [ ] Baseline metrics document
- [ ] List of any remaining issues
- [ ] Sign-off from stakeholders
- [ ] Git commit: "Phase 5: Comprehensive testing complete"

### Phase 5 Checklist

- [ ] Create test scripts (`smoke-test.sh`, `performance-test.sh`)
- [ ] Run PHPUnit with coverage report
- [ ] Run Playwright tests
- [ ] Run smoke tests
- [ ] Run performance tests
- [ ] Create performance baseline
- [ ] Monitor error logs
- [ ] Memory profiling
- [ ] Complete regression testing checklist
- [ ] Document baseline metrics
- [ ] Review and document any issues
- [ ] Get stakeholder approval
- [ ] Commit test artifacts
- [ ] Create git tag: `migration-phase5-ready-for-composer`

---

## Phase 6: Migrate to Composer (Week 9-10)

### Objective

Replace PEAR with Composer for modern dependency management.

### Why Composer?

- **Modern standard:** Industry-standard for PHP dependencies
- **Better dependency resolution:** Handles conflicts automatically
- **Version locking:** `composer.lock` ensures reproducible builds
- **Autoloading:** PSR-4 autoloading eliminates manual `require_once`
- **Development tools:** Easy integration with PHPStan, Rector, PHPCS
- **Active ecosystem:** Access to 300,000+ packages on Packagist

### Step 1: Create composer.json

Create `composer.json` in project root:

```json
{
    "name": "hathitrust/catalog",
    "description": "HathiTrust Catalog Front-End",
    "type": "project",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": "^8.2",
        "ext-pdo": "*",
        "ext-mbstring": "*",
        "ext-yaml": "*",
        "ext-gd": "*",
        "ext-ldap": "*",
        "ext-xsl": "*",

        "pear/auth_sasl2": "^1.1",
        "pear/db": "^1.11",
        "pear/db_dataobject": "^1.12",
        "pear/file_marc": "^1.2",
        "pear/http_request2": "^2.5",
        "pear/log": "^1.13",
        "pear/mail": "^1.5",
        "pear/net_smtp": "^1.10",
        "pear/pager": "^2.5",
        "pear/structures_datagrid": "^0.9",
        "pear/xml_parser": "^1.3",
        "pear/xml_beautifier": "^1.3",
        "pear/xml_serializer": "^0.22",

        "smarty/smarty": "^4.3",
        "suin/php-rss-writer": "^1.8"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.6",
        "phpstan/phpstan": "^1.10",
        "rector/rector": "^0.18",
        "squizlabs/php_codesniffer": "^3.7",
        "phpcompatibility/php-compatibility": "^9.3"
    },
    "autoload": {
        "psr-0": {
            "": "."
        },
        "files": []
    },
    "scripts": {
        "test": "phpunit",
        "test:coverage": "phpunit --coverage-html coverage",
        "stan": "phpstan analyse",
        "stan:baseline": "phpstan analyse --generate-baseline",
        "rector": "rector process",
        "rector:dry": "rector process --dry-run",
        "cs:check": "phpcs",
        "cs:fix": "phpcbf"
    },
    "config": {
        "allow-plugins": {
            "pear/pear_exception": true
        },
        "optimize-autoloader": true,
        "sort-packages": true
    }
}
```

### Step 2: Install Dependencies

```bash
# Install Composer (if not already installed)
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Install dependencies
composer install

# This creates:
# - vendor/ directory with all packages
# - composer.lock with locked versions
# - vendor/autoload.php for autoloading
```

### Step 3: Update Dockerfile to Use Composer

Replace PEAR installation with Composer:

```dockerfile
# syntax=docker/dockerfile:1.3-labs
FROM debian:bookworm
LABEL org.opencontainers.image.source https://github.com/hathitrust/catalog

# Install base packages (no PEAR packages)
RUN apt-get update && apt-get install -y \
      curl \
      git \
      unzip \
      msmtp-mta \
      bsd-mailx \
      php8.2-curl \
      php8.2-fpm \
      php8.2-gd \
      php8.2-http \
      php8.2-ldap \
      php8.2-mysql \
      php8.2-xdebug \
      php8.2-xsl \
      php8.2-mbstring \
      php8.2-xml \
      php8.2-yaml \
      php-raphf \
      && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set up application directory
WORKDIR /app

# Copy composer files first (for Docker layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# PHP-FPM configuration
RUN mkdir -p /run/php
COPY ./docker/php_pool.conf /etc/php/8.2/fpm/pool.d/www.conf

STOPSIGNAL SIGQUIT

# GeoIP database
WORKDIR /usr/share/GeoIP
ADD --chmod=644 https://github.com/maxmind/MaxMind-DB/blob/main/test-data/GeoIP2-Country-Test.mmdb?raw=true GeoIP2-Country.mmdb

EXPOSE 9000
WORKDIR /app
CMD ["/app/docker/run_catalog.sh"]
```

**Key changes:**
- Removed PEAR installation
- Removed `pear-channels` package
- Added Composer binary from official image
- Copy `composer.json` and `composer.lock` before code (layer caching)
- Run `composer install` instead of `pear install`

### Step 4: Update index.php Autoloader

**File:** `index.php`

```php
<?php
// BEFORE (lines ~20-23)
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';
}
spl_autoload_register('sample_autoloader');

// AFTER
// Composer autoloader (handles PEAR packages and PSR-4)
require_once __DIR__ . '/vendor/autoload.php';

// Legacy autoloader for classes not managed by Composer
spl_autoload_register(function($class) {
  $file = str_replace('_', '/', $class) . '.php';
  if (file_exists($file)) {
    require $file;
  }
});
```

### Step 5: Remove PEAR require_once Statements

**Files to update:**

```bash
# Find all PEAR require statements
grep -rn "require_once.*HTTP/Request2" --include="*.php" sys/ services/ static/ bookcover.php

# Example files to update:
# - sys/SolrConnection.php:3
# - sys/Solr.php:23
# - sys/Zebra.php:23
# - bookcover.php:127,161
# - services/Record/AJAX.php (if it has require)
# - services/Record/Export.php (if it has require)
```

**Remove these lines:**

```php
// REMOVE
require_once 'HTTP/Request2.php';
require_once 'Structures/DataGrid.php';
// etc.

// Composer autoloader handles these automatically
```

### Step 6: Upgrade Smarty to Version 4

**File:** `sys/Interface.php` (lines 52-53)

```php
// BEFORE
$this->register_function('translate', 'translate');
$this->register_function('char', 'char');

// AFTER
$this->registerPlugin('function', 'translate', 'translate');
$this->registerPlugin('function', 'char', 'char');
```

**Check for other Smarty 2/3 methods:**

```bash
# Find Smarty method calls
grep -rn "register_\|register_function\|register_modifier" --include="*.php" .

# Common methods changed in Smarty 4:
# register_function()    → registerPlugin('function', ...)
# register_modifier()    → registerPlugin('modifier', ...)
# register_block()       → registerPlugin('block', ...)
# register_resource()    → registerResource()
```

### Step 7: Replace feedcreator Library

The `feedcreator` library uses deprecated `each()` function. Replace with modern RSS library.

**Install replacement:**

```bash
composer require suin/php-rss-writer
```

**Find feedcreator usage:**

```bash
grep -rn "feedcreator\|FeedCreator" --include="*.php" .
```

**Example refactoring:**

```php
// BEFORE (old feedcreator)
require_once 'feedcreator/include/feedcreator.class.php';

$rss = new UniversalFeedCreator();
$rss->title = "HathiTrust Search Results";
$rss->description = "Search results feed";
$rss->link = "https://catalog.hathitrust.org";

$item = new FeedItem();
$item->title = "Item Title";
$item->link = "https://catalog.hathitrust.org/Record/123";
$item->description = "Item description";
$rss->addItem($item);

echo $rss->createFeed("RSS2.0");

// AFTER (modern library)
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Item;

$feed = new Feed();

$channel = new Channel();
$channel
    ->title("HathiTrust Search Results")
    ->description("Search results feed")
    ->url("https://catalog.hathitrust.org")
    ->appendTo($feed);

$item = new Item();
$item
    ->title("Item Title")
    ->url("https://catalog.hathitrust.org/Record/123")
    ->description("Item description")
    ->appendTo($channel);

echo $feed;
```

### Step 8: Configure PHPStan

Create `phpstan.neon`:

```neon
parameters:
    level: 6
    paths:
        - sys
        - services
        - static
        - interface/plugins
        - Apache
        - Crypt
        - File
        - lib
    excludePaths:
        - feedcreator/*
        - catalog/*
        - static/jquery.fancybox/*
        - static/MTagger/*
    bootstrapFiles:
        - vendor/autoload.php
    ignoreErrors:
        # Gradually remove these
        - '#Access to an undefined property#'
        - '#Call to an undefined method PEAR_Error#'
    checkMissingIterableValueType: false
    checkGenericClassInNonGenericObjectType: false
```

### Step 9: Configure Rector

Create `rector.php`:

```php
<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/sys',
        __DIR__ . '/services',
        __DIR__ . '/static',
        __DIR__ . '/interface/plugins',
        __DIR__ . '/Apache',
        __DIR__ . '/Crypt',
        __DIR__ . '/File',
        __DIR__ . '/lib',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/feedcreator',
        __DIR__ . '/catalog',
        __DIR__ . '/static/jquery.fancybox',
        __DIR__ . '/static/MTagger',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ]);
};
```

### Step 10: Update .gitignore

Add Composer-related entries:

```
# Composer
/vendor/
composer.lock

# PHPUnit
/coverage/
.phpunit.result.cache

# PHPStan
phpstan.neon.local
/phpstan-baseline.neon

# IDE
.idea/
.vscode/
*.swp
*.swo
```

### Step 11: Update docker-compose.yml

Update PHPUnit service to use Composer:

```yaml
  phpunit:
    build: .
    volumes:
      - .:/app
      - ./conf/authspecs-sample.yaml:/app/conf/authspecs.yaml
    working_dir: /app
    depends_on:
      nginx: *healthy
    command: vendor/bin/phpunit --coverage-html coverage
    environment:
      - XDEBUG_MODE=coverage
```

### Testing Composer Migration

```bash
# 1. Install dependencies locally
composer install

# 2. Build Docker image
docker compose build vufind

# 3. Run PHPUnit tests
docker compose run phpunit

# 4. Run Playwright tests
docker compose run playwright

# 5. Smoke tests
./scripts/smoke-test.sh

# 6. Performance comparison
./scripts/performance-test.sh > php82-composer-performance.txt
diff php82-performance.txt php82-composer-performance.txt

# 7. Run PHPStan
docker compose exec vufind composer stan

# 8. Run Rector (dry run first)
docker compose exec vufind composer rector:dry
```

### Migration Validation Checklist

- [ ] `composer install` succeeds without errors
- [ ] `vendor/` directory created with all packages
- [ ] `composer.lock` file created
- [ ] Docker image builds successfully
- [ ] Application starts without errors
- [ ] No "Class not found" errors
- [ ] All PHPUnit tests pass
- [ ] All Playwright tests pass
- [ ] Smoke tests pass
- [ ] Performance is comparable to PEAR version
- [ ] PHPStan analysis runs successfully
- [ ] Rector dry run completes
- [ ] All require_once statements removed or justified

### Rollback Plan

If Composer migration fails, revert to PEAR:

```bash
# Checkout previous tag
git checkout migration-phase5-ready-for-composer

# Rebuild
docker compose build
docker compose up -d
```

### Success Criteria

- ✅ Composer installed and configured
- ✅ All dependencies managed by Composer
- ✅ PEAR completely removed from Dockerfile
- ✅ Autoloading works via Composer
- ✅ All tests pass
- ✅ Performance is equivalent or better
- ✅ Smarty 4 working correctly
- ✅ feedcreator replaced (if used)
- ✅ Static analysis tools integrated
- ✅ No PEAR require_once statements remain

### Deliverables

- [ ] `composer.json` and `composer.lock`
- [ ] Updated `Dockerfile` without PEAR
- [ ] Updated `index.php` with Composer autoloader
- [ ] Removed PEAR require statements
- [ ] Updated Smarty code for version 4
- [ ] Replaced feedcreator (if used)
- [ ] `phpstan.neon` configuration
- [ ] `rector.php` configuration
- [ ] Updated `.gitignore`
- [ ] Test results comparing PEAR vs Composer
- [ ] Performance comparison
- [ ] Git commit: "Phase 6: Migrate to Composer"

### Phase 6 Checklist

- [ ] Create `composer.json`
- [ ] Run `composer install` locally
- [ ] Create `composer.lock`
- [ ] Update `Dockerfile` to use Composer
- [ ] Remove PEAR installation from Dockerfile
- [ ] Update `index.php` autoloader
- [ ] Find and remove PEAR require_once statements
- [ ] Update `sys/Interface.php` for Smarty 4
- [ ] Check for other Smarty deprecated methods
- [ ] Find feedcreator usage
- [ ] Replace feedcreator with modern library (if used)
- [ ] Create `phpstan.neon`
- [ ] Create `rector.php`
- [ ] Update `.gitignore`
- [ ] Update `docker-compose.yml` PHPUnit service
- [ ] Build Docker image
- [ ] Test Composer autoloading
- [ ] Run PHPUnit tests
- [ ] Run Playwright tests
- [ ] Run smoke tests
- [ ] Performance testing
- [ ] Run PHPStan analysis
- [ ] Run Rector dry run
- [ ] Document any issues
- [ ] Commit changes
- [ ] Create git tag: `migration-phase6-composer-complete`
- [ ] Create final release tag: `v2.0.0-php82-composer`

---

## Timeline Summary

| Phase | Duration | PHP Version | Package Manager | Status | Deliverables |
|-------|----------|-------------|-----------------|--------|--------------|
| **Phase 0** | 1 week | 7.4 | PEAR (updated) | 🟡 Planned | Updated PEAR packages, zero code changes |
| **Phase 1** | 1 week | 7.4 | PEAR | 🟡 Planned | Fixed constructors, static methods, each() |
| **Phase 2** | 2 weeks | 8.0 | PEAR | 🟡 Planned | PHP 8.0 compatible, all tests pass |
| **Phase 3** | 1 week | 8.1 | PEAR | 🟡 Planned | PHP 8.1 compatible, fixed deprecations |
| **Phase 4** | 1 week | 8.2 | PEAR | 🟡 Planned | PHP 8.2 compatible, php-raphf added |
| **Phase 5** | 2 weeks | 8.2 | PEAR | 🟡 Planned | Comprehensive testing, baseline metrics |
| **Phase 6** | 2 weeks | 8.2 | Composer | 🟡 Planned | Modern dependency management |
| **TOTAL** | **10 weeks** | **8.2** | **Composer** | 🟡 In Progress | **Production-ready PHP 8.2 application** |

### Milestone Dates

Assuming start date of **[INSERT START DATE]**:

- **Week 1 (End of Phase 0):** PEAR packages updated
- **Week 2 (End of Phase 1):** Code fixes complete
- **Week 4 (End of Phase 2):** PHP 8.0 stable
- **Week 5 (End of Phase 3):** PHP 8.1 stable
- **Week 6 (End of Phase 4):** PHP 8.2 stable
- **Week 8 (End of Phase 5):** Testing complete, ready for Composer
- **Week 10 (End of Phase 6):** Migration complete, ready for production

---

## Risk Mitigation & Rollback

### Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Breaking changes in PHP 8.x | Medium | High | Incremental upgrades, comprehensive testing |
| PEAR packages incompatible with PHP 8.2 | Low | High | Verify compatibility in Phase 0-4 |
| Performance degradation | Low | Medium | Baseline metrics, performance testing |
| Test failures | Medium | Medium | Fix issues incrementally, don't skip phases |
| Composer migration issues | Medium | High | Test thoroughly in Phase 5, clear rollback plan |
| Production downtime | Low | Critical | Parallel deployment, gradual rollout |
| Dynamic property issues | High | Low | PHPStan detection, systematic fixes |
| Memory leaks | Low | Medium | Memory profiling, load testing |

### Rollback Strategy

#### Per-Phase Rollback

Each phase creates a git tag for easy rollback:

```bash
# List available tags
git tag -l "migration-phase*"

# Rollback to specific phase
git checkout migration-phase4-php82

# Rebuild
docker compose build
docker compose up -d
```

#### Multi-Version Dockerfile (Advanced)

Keep multiple PHP versions available during migration:

```dockerfile
FROM debian:bookworm

# Install all PHP versions
RUN apt-get install -y \
    php8.0-fpm php8.0-cli \
    php8.1-fpm php8.1-cli \
    php8.2-fpm php8.2-cli

# Use environment variable to select version
ARG PHP_VERSION=8.2
RUN update-alternatives --set php /usr/bin/php${PHP_VERSION}
RUN update-alternatives --set php-fpm /usr/sbin/php-fpm${PHP_VERSION}
```

#### Parallel Deployment

Test both versions side-by-side:

```yaml
# docker-compose.override.yml
services:
  vufind-php74:
    build:
      context: .
      dockerfile: Dockerfile.php74
    ports:
      - "8074:8080"

  vufind-php82:
    build:
      context: .
      dockerfile: Dockerfile.php82
    ports:
      - "8082:8080"
```

### Feature Flags

Add environment variable for gradual rollout:

```yaml
# docker-compose.yml
services:
  vufind:
    environment:
      - PHP_VERSION=8.2
      - USE_COMPOSER=true
      - ENABLE_NEW_FEATURES=false
```

### Database Compatibility

Ensure database schemas remain compatible:

```sql
-- Check for breaking changes in queries
-- Ensure sessions table works with both versions
```

### Monitoring During Migration

```bash
# Real-time error monitoring
docker compose logs -f vufind | grep -i "fatal\|error" | tee migration-errors.log

# Performance monitoring
watch -n 5 'docker stats vufind --no-stream'

# Health check script
while true; do
  curl -sf http://localhost:8080/ > /dev/null && echo "✅ OK" || echo "❌ DOWN"
  sleep 10
done
```

---

## Monitoring & Validation

### Health Checks

#### Application Health Check

Add to `docker-compose.yml`:

```yaml
services:
  vufind:
    healthcheck:
      test: ["CMD", "php", "-v"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
```

#### Endpoint Health Check

```bash
#!/bin/bash
# scripts/health-check.sh

ENDPOINTS=(
  "/"
  "/Search/Home"
  "/Record/000000001"
  "/Record/000000001.json"
)

for endpoint in "${ENDPOINTS[@]}"; do
  status=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$endpoint")
  if [ "$status" -eq 200 ]; then
    echo "✅ $endpoint: $status"
  else
    echo "❌ $endpoint: $status"
  fi
done
```

### Error Tracking

#### Log Monitoring Script

```bash
#!/bin/bash
# scripts/monitor-logs.sh

docker compose logs -f vufind | while read line; do
  if echo "$line" | grep -qi "fatal\|error"; then
    echo "❌ ERROR: $line" | tee -a critical-errors.log
    # Optional: Send alert (email, Slack, etc.)
  elif echo "$line" | grep -qi "warning"; then
    echo "⚠️  WARNING: $line" | tee -a warnings.log
  fi
done
```

#### Error Summary

```bash
# scripts/error-summary.sh
#!/bin/bash

echo "=== Error Summary ==="
echo ""
echo "Fatal Errors:"
docker compose logs vufind | grep -i "fatal" | wc -l
echo ""
echo "Errors:"
docker compose logs vufind | grep -i "error" | wc -l
echo ""
echo "Warnings:"
docker compose logs vufind | grep -i "warning" | wc -l
echo ""
echo "Deprecated:"
docker compose logs vufind | grep -i "deprecated" | wc -l
```

### Performance Monitoring

#### Response Time Tracking

```bash
#!/bin/bash
# scripts/response-time.sh

ENDPOINTS=(
  "/Search/Home?lookfor=test"
  "/Record/000000001"
  "/Record/000000001.json"
)

for endpoint in "${ENDPOINTS[@]}"; do
  time=$(curl -s -o /dev/null -w "%{time_total}" "http://localhost:8080$endpoint")
  echo "$endpoint: ${time}s"
done
```

#### Continuous Performance Monitoring

```bash
#!/bin/bash
# scripts/continuous-perf-monitor.sh

while true; do
  timestamp=$(date +"%Y-%m-%d %H:%M:%S")
  response_time=$(curl -s -o /dev/null -w "%{time_total}" "http://localhost:8080/Search/Home")
  echo "$timestamp,$response_time" >> performance-log.csv
  sleep 60
done
```

### Resource Monitoring

```bash
# CPU and Memory usage
docker stats vufind --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}"

# Disk usage
docker system df

# Network traffic
docker stats vufind --no-stream --format "table {{.Name}}\t{{.NetIO}}"
```

### Alerting

Create `scripts/alert.sh`:

```bash
#!/bin/bash

SLACK_WEBHOOK="https://hooks.slack.com/services/YOUR/WEBHOOK/URL"

function send_alert() {
  local message="$1"
  curl -X POST -H 'Content-type: application/json' \
    --data "{\"text\":\"🚨 HathiTrust Catalog Alert: $message\"}" \
    "$SLACK_WEBHOOK"
}

# Example usage
if ! curl -sf http://localhost:8080/ > /dev/null; then
  send_alert "Application is DOWN!"
fi
```

---

## Appendix A: File Change Summary

### Files Modified by Phase

#### Phase 0
- `Dockerfile` - Update PEAR packages

#### Phase 1
- `sys/Datagrid.php` - Fix constructor
- `sys/Interface.php` - Fix constructor
- `sys/AuthSpecs.php` - Make singleton static
- `sys/DSession.php` - Make singleton static
- `sys/VFSession.php` - Make instance static
- `sys/VFUser.php` - Delete (if unused)
- `feedcreator/include/feedcreator.class.php` - Fix each()
- Multiple files - Fix undefined array keys

#### Phase 2
- `Dockerfile` - Update to PHP 8.0
- `docker/run_catalog.sh` - Update to php-fpm8.0

#### Phase 3
- `Dockerfile` - Update to Debian Bookworm, PHP 8.1
- `docker/run_catalog.sh` - Update to php-fpm8.1
- Multiple files - Fix ${var} interpolation (if found)

#### Phase 4
- `Dockerfile` - Update to PHP 8.2, add php-raphf
- `docker/run_catalog.sh` - Update to php-fpm8.2
- Multiple files - Fix dynamic properties

#### Phase 6
- `composer.json` - Create
- `Dockerfile` - Replace PEAR with Composer
- `index.php` - Update autoloader
- `sys/Interface.php` - Update Smarty 4 methods
- `sys/SolrConnection.php` - Remove require_once
- `sys/Solr.php` - Remove require_once
- `sys/Zebra.php` - Remove require_once
- `bookcover.php` - Remove require_once
- `phpstan.neon` - Create
- `rector.php` - Create
- `.gitignore` - Update
- `docker-compose.yml` - Update PHPUnit service

---

## Appendix B: Testing Scripts

All testing scripts should be placed in `scripts/` directory.

### scripts/smoke-test.sh
(See Phase 5 for full script)

### scripts/performance-test.sh
(See Phase 5 for full script)

### scripts/health-check.sh
(See Monitoring section for full script)

### scripts/monitor-logs.sh
(See Monitoring section for full script)

### scripts/error-summary.sh
(See Monitoring section for full script)

---

## Appendix C: Useful Commands

### Docker Commands

```bash
# Build without cache
docker compose build --no-cache vufind

# View logs
docker compose logs -f vufind

# Execute command in container
docker compose exec vufind php -v

# Restart service
docker compose restart vufind

# Clean up
docker compose down
docker system prune -a
```

### Composer Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Show installed packages
composer show

# Validate composer.json
composer validate

# Check for security vulnerabilities
composer audit

# Run scripts
composer test
composer stan
composer rector:dry
```

### Git Commands

```bash
# Create tag
git tag -a migration-phase1-complete -m "Phase 1: Code fixes complete"

# Push tag
git push origin migration-phase1-complete

# List tags
git tag -l "migration-*"

# Checkout tag
git checkout migration-phase1-complete
```

### PHPStan Commands

```bash
# Run analysis
./vendor/bin/phpstan analyse

# Generate baseline
./vendor/bin/phpstan analyse --generate-baseline

# Clear cache
./vendor/bin/phpstan clear-result-cache
```

### Rector Commands

```bash
# Dry run
./vendor/bin/rector process --dry-run

# Apply changes
./vendor/bin/rector process

# Process specific file
./vendor/bin/rector process sys/Interface.php
```

---

## Appendix D: Additional Resources

### PHP 8.0 Migration Guides
- [Official PHP 8.0 Migration Guide](https://www.php.net/manual/en/migration80.php)
- [PHP 8.0 Backward Incompatible Changes](https://www.php.net/manual/en/migration80.incompatible.php)

### PHP 8.1 Migration Guides
- [Official PHP 8.1 Migration Guide](https://www.php.net/manual/en/migration81.php)

### PHP 8.2 Migration Guides
- [Official PHP 8.2 Migration Guide](https://www.php.net/manual/en/migration82.php)
- [PHP 8.2 Deprecated Features](https://www.php.net/manual/en/migration82.deprecated.php)

### Tools Documentation
- [Composer Documentation](https://getcomposer.org/doc/)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Rector Documentation](https://getrector.com/documentation)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Smarty 4 Documentation](https://www.smarty.net/docs/en/)

### PEAR Package Information
- [PEAR Channel](https://pear.php.net/)
- [Auth_SASL2](https://pear.php.net/package/Auth_SASL2/)
- [HTTP_Request2](https://pear.php.net/package/HTTP_Request2/)

---

## Document Change Log

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2025-10-21 | Migration Team | Initial plan created |

---

**END OF MIGRATION PLAN**
