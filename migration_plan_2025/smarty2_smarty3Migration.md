# HathiTrust Catalog: Smarty 2 → Smarty 3 Migration Plan

**Document Version:** 1.0
**Date:** November 17, 2025
**Status:** Ready for Implementation
**Target:** Complete Smarty 2 → Smarty 3 migration
**Next Phase:** Smarty 3 → Smarty 4 (separate task)

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Current State Analysis](#current-state-analysis)
3. [Root Cause Analysis](#root-cause-analysis)
4. [Migration Strategy](#migration-strategy)
5. [Phase-by-Phase Implementation](#phase-by-phase-implementation)
6. [Testing Procedures](#testing-procedures)
7. [Rollback Plan](#rollback-plan)
8. [Troubleshooting Guide](#troubleshooting-guide)
9. [Post-Migration Validation](#post-migration-validation)

---

## Executive Summary

### Current Problem

The HathiTrust Catalog is in a **critical hybrid state** between Smarty 2 and Smarty 3:

- **Code expects:** Smarty 3 (extends `SmartyBC`)
- **Runtime uses:** Smarty 2.6.31 (evidence from compiled templates)
- **Docker installs:** Smarty 3 (smarty3 Debian package)

### Root Cause

The PEAR-style autoloader in `index.php` intercepts Smarty 3's attempts to load its internal classes (`Smarty_Internal_*`), causing a silent fallback to Smarty 2.6.31.

**Solution:** Remove the autoloader entirely (it's only used for one class: `XML_Unserializer`) and add explicit `require_once` statements instead. This simplifies the codebase and eliminates the conflict.

### Migration Goal

Complete the Smarty 2 → Smarty 3 migration to prepare for PHP 8 compatibility and eventual upgrade to Smarty 4/5.

### Key Deliverables

1. ✅ Fix autoloader conflict
2. ✅ Remove deprecated Smarty 2 method calls
3. ✅ Update all custom plugins to Smarty 3 API
4. ✅ Remove alicorn theme
5. ✅ Ensure all templates compile with Smarty 3
6. ✅ Fix PHP 8.1+ compatibility issues
7. ✅ Comprehensive testing and validation

---

## Current State Analysis

### What We Found

#### 1. Code Structure (Smarty 3)

**File:** `sys/Interface.php`
```php
require_once 'smarty3/SmartyBC.class.php';  // ✓ Smarty 3 path

class UInterface extends SmartyBC {          // ✓ Smarty 3 BC class
    function __construct() {
        parent::__construct();               // ✓ Correct parent call

        // ❌ Deprecated Smarty 2 methods
        $this->register_function('translate', 'translate');
        $this->register_function('char', 'char');
    }
}
```

**Status:** Code structure is Smarty 3-ready but uses deprecated methods

#### 2. Compiled Templates (Smarty 2)

**File:** `interface/compile/firebird/%%82^823^823B39A1%%layout.tpl.php`
```php
<?php /* Smarty version 2.6.31, created on 2025-10-20 16:58:38 */  // ❌ Smarty 2!
```

**Status:** Templates are being compiled by Smarty 2.6.31, NOT Smarty 3

#### 3. Autoloader Conflict

**File:** `index.php` (lines 42-45)
```php
function sample_autoloader($class) {
  require str_replace('_', '/', $class) . '.php';  // ❌ Intercepts Smarty_Internal_*
}
spl_autoload_register('sample_autoloader');
```

**Impact:** When Smarty 3 tries to load `Smarty_Internal_Template`, the autoloader looks for `Smarty/Internal/Template.php` which doesn't exist, causing silent failure.

#### 4. Mixed Smarty Versions

| Component | Version | File | Line |
|-----------|---------|------|------|
| Interface.php | Smarty 3 | `sys/Interface.php` | 21 |
| volumes.php | Smarty 2 | `static/api/volumes.php` | 410 |
| Compiled templates | Smarty 2.6.31 | `interface/compile/` | All files |

### Inventory: Files Using Smarty

#### Core Files (3)
1. **`sys/Interface.php`** - Main template engine wrapper (Smarty 3)
2. **`static/api/volumes.php`** - API endpoint (Smarty 2) ⚠️
3. **`index.php`** - Application entry point (autoloader conflict) ⚠️

#### Template Files (41 total)

**Firebird Theme (22 files):**
- Layout: `layout.tpl`, `header.tpl`, `footer.tpl`
- Search: `Search/list.tpl`, `Search/list-list.tpl`, `Search/advanced.tpl`, `Search/home.tpl`
- Record: `Record/view.tpl`, `Record/view.summary.tpl`, `Record/cite.tpl`, `Record/marc_pretty.tpl`
- Other: 13 additional template files

**Alicorn Theme (18 files):** ❌ TO BE DELETED

**API Template (1 file):**
- `static/api/templates/volumes/volumes.xml.tpl`

#### Custom Smarty Plugins (13 files)

**Functions (4):**
1. `function.css_link.php` - Cache-busting CSS
2. `function.firebird_manifest.php` - Asset manifest ⚠️ (uses deprecated method)
3. `function.js_link.php` - Cache-busting JS
4. `function.record_author_display.php` - MARC author display

**Modifiers (9):**
1. `modifier.arrayutils.php` - Array implode
2. `modifier.dateadd.php` - Date arithmetic ⚠️ (uses `strftime()` - PHP 8.1 incompatible)
3. `modifier.escape_quotes.php` - Quote escaping
4. `modifier.formatISBN.php` - ISBN formatting
5. `modifier.getvalue.php` - MARC subfield extraction
6. `modifier.highlight.php` - Search term highlighting
7. `modifier.printms.php` - Millisecond formatting
8. `modifier.remove_url_param.php` - URL parameter removal
9. `modifier.substr.php` - String truncation

### Template Syntax Analysis

#### Smarty 2 Syntax Still in Use

**1. Built-in `truncate` Modifier (8 occurrences)**
```smarty
{$pageTitle|truncate:64:"..."}
```
**Status:** ✓ Compatible with both Smarty 2 and 3 (built-in modifier)

**2. `$smarty.foreach` Special Variables (20+ occurrences)**
```smarty
{$smarty.foreach.loop.first}
{$smarty.foreach.loop.last}
{$smarty.foreach.loop.iteration}
```
**Status:** ✓ Still supported in Smarty 3 (but `@` syntax preferred)
**Action:** No immediate change needed (can migrate to `@first`, `@last` in future)

**3. Custom Modifiers**
All custom modifiers properly use standard naming conventions and are compatible with both versions.

---

## Root Cause Analysis

### The Autoloader Problem

**What's Happening:**

1. Application loads `smarty3/SmartyBC.class.php` ✓
2. SmartyBC needs to load internal classes like `Smarty_Internal_Template`
3. The `sample_autoloader` function intercepts the class load request
4. It looks for `Smarty/Internal/Template.php` (PEAR-style path conversion)
5. File doesn't exist in include path → **Load fails**
6. Smarty 3 falls back to bundled Smarty 2.6.31 (or finds it elsewhere)
7. Templates compile with Smarty 2.6.31 instead of Smarty 3

**Evidence:**

```php
// What Smarty 3 tries to do:
spl_autoload_call('Smarty_Internal_Template');

// What sample_autoloader does:
str_replace('_', '/', 'Smarty_Internal_Template') . '.php'
→ 'Smarty/Internal/Template.php'

// File doesn't exist at:
// /usr/share/php/Smarty/Internal/Template.php ❌

// Smarty 3's files are actually at:
// /usr/share/php/smarty3/sysplugins/smarty_internal_template.php ✓
```

### Why This Went Unnoticed

1. **SmartyBC provides backward compatibility** - Many Smarty 2 methods still work
2. **No fatal errors** - The fallback to Smarty 2 is silent
3. **Templates render correctly** - Smarty 2 and 3 template syntax is largely compatible
4. **Compiled templates work** - The `.php` files generated by Smarty 2 execute fine

### Why It Matters Now

1. **PHP 8 compatibility** - Smarty 2 is incompatible with PHP 8+
2. **Future Smarty 4/5 migration** - Need clean Smarty 3 base
3. **Deprecated methods** - Smarty 3 deprecations will break in Smarty 4
4. **Maintenance burden** - Hybrid state is confusing and error-prone

---

## Migration Strategy

### Principles

1. **Fix the root cause first** (autoloader) before touching templates
2. **One change at a time** with testing after each phase
3. **Maintain backward compatibility** during migration
4. **Comprehensive testing** at each step
5. **Easy rollback** if issues arise

### Phased Approach

```
Current State:           Target State:
Code: Smarty 3    ──┐    Code: Smarty 3
Runtime: Smarty 2   │ →  Runtime: Smarty 3
Docker: Smarty 3  ──┘    Docker: Smarty 3
```

### Migration Sequence

```
Phase 1: Preparation
  ↓
Phase 2: Remove Alicorn Theme (reduce scope)
  ↓
Phase 3: Fix Autoloader (CRITICAL - enables Smarty 3)
  ↓
Phase 4: Clean Compiled Templates (force recompilation)
  ↓
Phase 5: Update Interface.php (Smarty 3 API)
  ↓
Phase 6: Fix volumes.php (consistency)
  ↓
Phase 7: Update Custom Plugins (compatibility)
  ↓
Phase 8: Comprehensive Testing (validation)
  ↓
Phase 9: Documentation & Completion
```

---

## Phase-by-Phase Implementation

### Phase 1: Pre-Migration Preparation

**Objective:** Create safety net for rollback and baseline for comparison

#### Steps:

1. **Create new git branch**
   ```bash
   git checkout -b smarty-3-migration
   ```

2. **Create rollback tag**
   ```bash
   git tag -a pre-smarty3-migration -m "Before Smarty 2 to 3 migration"
   git push origin pre-smarty3-migration
   ```

3. **Take visual screenshots**
   ```bash
   # Start application
   docker compose up -d

   # Screenshot key pages:
   # - Homepage (/)
   # - Search results (/Search/Home?lookfor=test)
   # - Record view (/Record/[any-id])
   # - Advanced search (/Search/Advanced)
   # - Record citation (/Record/[any-id]/Cite)
   ```

4. **Document current Smarty version**
   ```bash
   # Check compiled template header
   head -1 interface/compile/firebird/*.php
   # Should show: /* Smarty version 2.6.31 ... */
   ```

5. **Run baseline tests**
   ```bash
   docker compose run phpunit
   docker compose run playwright
   ```

#### Success Criteria:
- ✅ Branch created
- ✅ Rollback tag created
- ✅ Screenshots saved
- ✅ Baseline test results documented

#### Time Estimate: 15 minutes

---

### Phase 2: Remove Alicorn Theme

**Objective:** Simplify migration by removing unused alternative theme

**Decision:** Team decided to remove alicorn theme entirely rather than migrate it.

#### Steps:

1. **Delete alicorn theme directory**
   ```bash
   rm -rf interface/themes/alicorn/
   ```

   **Files removed (18):**
   - layout.tpl, header.tpl, footer.tpl
   - Search templates (list.tpl, list-list.tpl, advanced.tpl, home.tpl)
   - Record templates (view.tpl, view.summary.tpl, cite.tpl, etc.)
   - All other alicorn-specific templates

2. **Delete alicorn compiled templates**
   ```bash
   rm -rf interface/compile/alicorn/
   ```

3. **Remove alicorn theme from configuration** (if present)
   ```bash
   # Check if alicorn is referenced in config
   grep -r "alicorn" conf/

   # If found in config.ini, remove those lines
   ```

4. **Remove alicorn theme override code**
   ```bash
   # Check index.php for alicorn references
   grep -n "alicorn" index.php
   ```

   **File:** `index.php` (lines 73-75)
   ```php
   // BEFORE:
   if (isset($_GET['skin']) && ($_GET['skin'] == 'alicorn')) {
     $configArray['Site']['theme'] = 'alicorn';
   }

   // AFTER: Remove this block entirely
   ```

5. **Commit changes**
   ```bash
   git add interface/themes/ interface/compile/ index.php
   git commit -m "Remove alicorn theme - unused, simplifies Smarty 3 migration"
   ```

#### Success Criteria:
- ✅ `interface/themes/alicorn/` deleted
- ✅ `interface/compile/alicorn/` deleted
- ✅ No alicorn references in code
- ✅ Application still starts (uses firebird theme)

#### Time Estimate: 10 minutes

---

### Phase 3: Remove Autoloader and Add Explicit Requires (CRITICAL)

**Objective:** Remove PEAR autoloader entirely and make dependencies explicit

**This is the most critical fix** - it eliminates the root cause of the Smarty 3 conflict and reduces code complexity.

#### Problem:

The PEAR-style autoloader (lines 42-45 in index.php) intercepts Smarty 3's internal class loading, causing silent fallback to Smarty 2.6.31.

#### Analysis:

Through codebase analysis, we found that the autoloader is only used for **one class**:
- `XML_Unserializer` - used in 2 files without explicit `require_once`

All other classes (application classes, controllers, PEAR libraries) are already explicitly loaded with `require_once` statements.

#### Solution:

**Remove the autoloader entirely** and add explicit requires for `XML_Unserializer`.

#### Steps:

1. **Add explicit requires for XML_Unserializer**

   **File:** `services/Search/Browse.php`

   Add after line 21 (`require_once 'Action.php';`):
   ```php
   require_once 'Action.php';
   require_once 'XML/Unserializer.php';  // Add this line
   ```

   **File:** `services/Record/Record.php`

   Add after line 30 (`require_once 'services/Record/RecordUtils.php';`):
   ```php
   require_once 'services/Record/RecordUtils.php';
   require_once 'XML/Unserializer.php';  // Add this line
   ```

2. **Remove the autoloader from index.php**

   **File:** `index.php` (lines 42-45)

   **BEFORE:**
   ```php
   // Set up for autoload
   function sample_autoloader($class) {
     require str_replace('_', '/', $class) . '.php';
   }
   spl_autoload_register('sample_autoloader');
   ```

   **AFTER:**
   ```php
   // Autoloader removed - all dependencies now explicitly required
   // This eliminates the conflict with Smarty 3's internal autoloader
   ```

3. **Verify no other autoloaded classes exist**
   ```bash
   # Search for PEAR-style class usage without requires
   docker compose exec vufind bash -c "grep -r 'new [A-Z][a-z]*_[A-Z]' services/ bookcover.php | grep -v '//' | grep -v require"
   # Expected: Only XML_Unserializer instances we already fixed
   ```

4. **Test application starts correctly**
   ```bash
   # Rebuild and start application
   docker compose build vufind
   docker compose up -d

   # Check for any class loading errors
   docker compose logs vufind | grep -i "class.*not found\|failed opening"
   # Expected: No errors
   ```

5. **Verify Smarty 3 loads correctly**
   ```bash
   # Check application homepage
   curl -s http://localhost:8080/ | grep -i "smarty"
   # Expected: No Smarty errors

   # Check error logs
   docker compose logs vufind | grep -i "smarty"
   # Expected: No "Failed opening required 'Smarty/Internal/...'" errors
   ```

6. **Commit the changes**
   ```bash
   git add index.php services/Search/Browse.php services/Record/Record.php
   git commit -m "Remove PEAR autoloader, add explicit requires

   - Remove sample_autoloader entirely from index.php
   - Add explicit require_once for XML/Unserializer.php in Browse.php
   - Add explicit require_once for XML/Unserializer.php in Record.php
   - Eliminates Smarty 3 class loading conflict
   - Reduces complexity and makes dependencies explicit
   - Critical fix for Smarty 2 to 3 migration"
   ```

#### Success Criteria:
- ✅ Autoloader completely removed from index.php
- ✅ Explicit requires added for XML_Unserializer (2 files)
- ✅ Application starts without errors
- ✅ No "Class not found" errors in logs
- ✅ No "Failed opening required 'Smarty/Internal/...'" errors
- ✅ Browse page works (uses XML_Unserializer)
- ✅ Homepage renders correctly

#### Verification Commands:

```bash
# Verify autoloader is gone
grep -n "sample_autoloader" index.php
# Expected: No results (or only in comments)

# Verify explicit requires added
grep -n "XML/Unserializer" services/Search/Browse.php
grep -n "XML/Unserializer" services/Record/Record.php
# Expected: require_once statements in both files

# Test XML_Unserializer works
curl -s "http://localhost:8080/Search/Browse" | grep -i "error"
# Expected: No PHP errors
```

#### Benefits:

1. **Eliminates Smarty 3 conflict** - Smarty can now load its internal classes
2. **Reduces complexity** - No autoloader magic, all dependencies explicit
3. **Improves performance** - No autoloader function calls
4. **Better debugging** - Clear dependency chain
5. **Easier maintenance** - Developers can see what classes each file uses

#### Time Estimate: 20 minutes

---

### Phase 4: Clean Compiled Templates

**Objective:** Force templates to recompile with Smarty 3

**Why:** Old compiled templates have Smarty 2 code baked in. We need fresh compilation with Smarty 3.

#### Steps:

1. **Delete all compiled Smarty templates**
   ```bash
   # Firebird theme (main theme)
   rm -f interface/compile/firebird/*.php

   # If any other compiled files exist
   find interface/compile/ -name "*.php" -type f -delete
   ```

2. **Verify deletion**
   ```bash
   ls -la interface/compile/firebird/
   # Expected: Empty directory or only .gitkeep
   ```

3. **Restart application to trigger recompilation**
   ```bash
   docker compose restart vufind
   ```

4. **Access pages to trigger template compilation**
   ```bash
   # Homepage
   curl -s http://localhost:8080/ > /dev/null

   # Search
   curl -s "http://localhost:8080/Search/Home?lookfor=test" > /dev/null

   # Record view (use any valid record ID)
   curl -s "http://localhost:8080/Record/000000001" > /dev/null
   ```

5. **Verify Smarty 3 compilation**
   ```bash
   # Check first compiled template
   head -1 interface/compile/firebird/*.php | head -5

   # BEFORE (Smarty 2):
   # /* Smarty version 2.6.31, created on 2025-10-20 16:58:38 */

   # AFTER (Smarty 3):
   # /* Smarty version 3.1.39, created on 2025-11-17 ... */
   # (or whatever Smarty 3 version is installed)
   ```

6. **Check for compilation errors**
   ```bash
   docker compose logs vufind | grep -i "smarty.*error\|compile.*error"
   # Expected: No errors
   ```

7. **Commit the cleanup** (optional - compiled templates usually in .gitignore)
   ```bash
   git status
   # If compiled templates are tracked:
   git add interface/compile/
   git commit -m "Clear Smarty 2 compiled templates, force Smarty 3 recompilation"
   ```

#### Success Criteria:
- ✅ All `.php` files deleted from `interface/compile/firebird/`
- ✅ New compiled templates created on page access
- ✅ New compiled templates show Smarty 3.x version header
- ✅ No compilation errors in logs
- ✅ Pages render correctly

#### Verification Script:

```bash
#!/bin/bash
# verify_smarty3_compilation.sh

echo "Checking compiled template versions..."

COMPILED_DIR="interface/compile/firebird"

if [ ! -d "$COMPILED_DIR" ]; then
  echo "❌ Compiled directory not found: $COMPILED_DIR"
  exit 1
fi

# Check for any compiled templates
COUNT=$(find "$COMPILED_DIR" -name "*.php" | wc -l)

if [ "$COUNT" -eq 0 ]; then
  echo "⚠️  No compiled templates found - access pages to trigger compilation"
  exit 0
fi

echo "Found $COUNT compiled templates"
echo ""

# Check versions
echo "Template versions:"
grep -h "Smarty version" "$COMPILED_DIR"/*.php | sort -u

# Check for Smarty 2
SMARTY2_COUNT=$(grep -l "Smarty version 2" "$COMPILED_DIR"/*.php 2>/dev/null | wc -l)

if [ "$SMARTY2_COUNT" -gt 0 ]; then
  echo ""
  echo "❌ Found $SMARTY2_COUNT templates compiled with Smarty 2!"
  echo "   Autoloader fix may not be working correctly"
  exit 1
else
  echo ""
  echo "✅ All templates compiled with Smarty 3"
  exit 0
fi
```

#### Time Estimate: 10 minutes

---

### Phase 5: Update Interface.php to Smarty 3 API

**Objective:** Replace deprecated Smarty 2 methods with Smarty 3 equivalents

**File:** `sys/Interface.php`

#### Changes Required:

1. **Replace `register_function()` with `registerPlugin()`**
2. **Replace direct `plugins_dir` assignment with `addPluginsDir()`**
3. **Verify all property assignments use correct Smarty 3 syntax**

#### Steps:

1. **Update custom function registration (Lines 53-54)**

   **BEFORE:**
   ```php
   $this->register_function('translate', 'translate');
   $this->register_function('char', 'char');
   ```

   **AFTER:**
   ```php
   $this->registerPlugin('function', 'translate', 'translate');
   $this->registerPlugin('function', 'char', 'char');
   ```

2. **Update plugins directory configuration (Line 46)**

   **BEFORE:**
   ```php
   $this->plugins_dir   = array('/usr/share/php/smarty3/plugins', "$local/interface/plugins");
   ```

   **AFTER:**
   ```php
   // Smarty 3 already has default plugins dir, just add custom plugins
   $this->addPluginsDir("$local/interface/plugins");
   ```

   **Note:** Smarty 3 automatically includes `/usr/share/php/smarty3/plugins/` so we only need to add our custom directory.

3. **Full updated constructor**

   **File:** `sys/Interface.php` (lines 26-92)

   ```php
   function __construct()
   {
       parent::__construct();  // Call SmartyBC constructor

       global $configArray;
       $local = $configArray['Site']['local'];
       $theme = $configArray['Site']['theme'];

       // Template directories
       $this->template_dir  = "$local/interface/themes/$theme";

       // Compile directory (auto-creates if missing)
       $comp = "$local/interface/compile/$theme";
       if (!is_dir($comp)) {
         mkdir($comp, 0777);
         chmod($comp, 0777);
       }

       $this->compile_dir   = $comp;
       $this->cache_dir     = "$local/interface/cache";

       // Add custom plugins directory (Smarty 3 method)
       $this->addPluginsDir("$local/interface/plugins");

       $this->caching       = false;
       $this->debug         = true;
       $this->compile_check = true;

       unset($local);

       // Register custom functions (Smarty 3 method)
       $this->registerPlugin('function', 'translate', 'translate');
       $this->registerPlugin('function', 'char', 'char');

       // ... rest of template assignments unchanged ...
   }
   ```

4. **Test the changes**
   ```bash
   # Rebuild
   docker compose build vufind
   docker compose up -d

   # Test homepage
   curl -s http://localhost:8080/ | grep -i "hathitrust"
   # Expected: Page loads successfully

   # Check for errors
   docker compose logs vufind | grep -i "error\|warning" | grep -i "smarty\|plugin"
   # Expected: No plugin-related errors

   # Test translate function works
   curl -s http://localhost:8080/ | grep -q "Search"
   # Expected: Translated text appears (translate function working)
   ```

5. **Run PHPUnit tests**
   ```bash
   docker compose run phpunit
   # Expected: All 5 tests pass, 18 assertions
   ```

6. **Commit the changes**
   ```bash
   git add sys/Interface.php
   git commit -m "Update Interface.php to use Smarty 3 API methods

   - Replace register_function() with registerPlugin()
   - Replace plugins_dir property with addPluginsDir() method
   - Both deprecated Smarty 2 methods replaced with Smarty 3 equivalents
   - Maintains backward compatibility via SmartyBC"
   ```

#### Success Criteria:
- ✅ No deprecation warnings about `register_function()`
- ✅ Custom plugins still load correctly
- ✅ `translate` and `char` functions work in templates
- ✅ All PHPUnit tests pass
- ✅ No Smarty errors in logs

#### Verification:

Check that custom plugins load:
```bash
# Check if translate function is available
docker compose exec vufind php -r "
require 'sys/Interface.php';
\$i = new UInterface();
var_dump(\$i->getRegisteredObject('translate'));
"
# Expected: Shows the registered function callback
```

#### Time Estimate: 30 minutes

---

### Phase 6: Fix volumes.php API Endpoint

**Objective:** Migrate volumes.php from Smarty 2 to Smarty 3

**File:** `static/api/volumes.php`

**Why:** Consistency across the application - all components should use Smarty 3

#### Current Issue:

**Lines 410-411:**
```php
require_once 'Smarty/Smarty.class.php';  // ❌ Smarty 2 path
$interface = new Smarty();                // ❌ Pure Smarty, not SmartyBC
```

#### Steps:

1. **Update Smarty require path**

   **BEFORE:**
   ```php
   require_once 'Smarty/Smarty.class.php';
   ```

   **AFTER:**
   ```php
   require_once 'smarty3/Smarty.class.php';
   ```

2. **Keep using base Smarty class** (no need for SmartyBC in this simple case)

   The volumes.php endpoint doesn't use any Smarty 2 specific methods, so we can use the base `Smarty` class from Smarty 3.

   ```php
   $interface = new Smarty();  // ✓ Smarty 3 base class is fine here
   ```

3. **Full context of the change**

   **File:** `static/api/volumes.php` (around lines 410-420)

   **BEFORE:**
   ```php
   require_once 'Smarty/Smarty.class.php';
   $interface = new Smarty();
   $interface->compile_dir = $compdir;
   $interface->template_dir = TMPLTDIR;
   $interface->assign( 'gbs_info', $gbs_info );
   $interface->assign( 'hathitrust_info', $hathitrust_info );
   $interface->display('volumes.xml.tpl');
   ```

   **AFTER:**
   ```php
   require_once 'smarty3/Smarty.class.php';
   $interface = new Smarty();
   $interface->compile_dir = $compdir;
   $interface->template_dir = TMPLTDIR;
   $interface->assign( 'gbs_info', $gbs_info );
   $interface->assign( 'hathitrust_info', $hathitrust_info );
   $interface->display('volumes.xml.tpl');
   ```

   **Note:** Only line 410 changes - everything else stays the same.

4. **Test the API endpoint**
   ```bash
   # Test volumes API with a sample ID
   curl -s "http://localhost:8080/static/api/volumes.php?id=mdp.39015012668484" | head -20

   # Expected: Valid XML output starting with:
   # <?xml version="1.0" encoding="UTF-8"?>
   # <volumes>...</volumes>
   ```

5. **Check for Smarty errors**
   ```bash
   # Access API endpoint and check logs
   curl -s "http://localhost:8080/static/api/volumes.php?id=mdp.39015012668484" > /dev/null
   docker compose logs vufind | tail -20 | grep -i "smarty\|error"

   # Expected: No errors
   ```

6. **Verify template compilation**
   ```bash
   # Check if volumes template compiled with Smarty 3
   find static/api/compile/ -name "*.php" -exec head -1 {} \;

   # Expected: Smarty version 3.x
   ```

7. **Commit the changes**
   ```bash
   git add static/api/volumes.php
   git commit -m "Migrate volumes.php API endpoint to Smarty 3

   - Change require path from Smarty/Smarty.class.php to smarty3/Smarty.class.php
   - Maintains same Smarty base class (no backward compat needed)
   - Ensures consistency: all components now use Smarty 3"
   ```

#### Success Criteria:
- ✅ API endpoint returns valid XML
- ✅ No Smarty errors in logs
- ✅ Template compiles with Smarty 3.x
- ✅ No functionality change (output identical)

#### Alternative (if issues arise):

If using base `Smarty` class causes issues, switch to `SmartyBC`:

```php
require_once 'smarty3/SmartyBC.class.php';
$interface = new SmartyBC();
```

#### Time Estimate: 20 minutes

---

### Phase 7: Update Custom Plugins

**Objective:** Fix deprecated method calls and PHP 8.1+ compatibility in custom Smarty plugins

#### Plugins to Update:

1. **function.firebird_manifest.php** - Deprecated `get_template_vars()` method
2. **modifier.dateadd.php** - PHP 8.1 incompatible `strftime()` function

#### Fix 1: function.firebird_manifest.php

**File:** `interface/plugins/function.firebird_manifest.php`

**Issue:** Line 13 uses deprecated `get_template_vars()` method

**BEFORE:**
```php
function smarty_function_firebird_manifest($params, &$smarty) {
  // ... code ...

  $manifest = $smarty->get_template_vars('firebird_manifest');  // ❌ Smarty 2 method

  // ... rest of function ...
}
```

**AFTER:**
```php
function smarty_function_firebird_manifest($params, &$smarty) {
  // ... code ...

  $manifest = $smarty->getTemplateVars('firebird_manifest');  // ✓ Smarty 3 method

  // ... rest of function ...
}
```

**Steps:**

1. Edit the file:
   ```bash
   # Change line 13:
   # FROM: $manifest = $smarty->get_template_vars('firebird_manifest');
   # TO:   $manifest = $smarty->getTemplateVars('firebird_manifest');
   ```

2. Test the function:
   ```bash
   # Access a page that uses firebird_manifest
   curl -s http://localhost:8080/ | grep -i "firebird"
   # Expected: No errors, assets load correctly
   ```

#### Fix 2: modifier.dateadd.php

**File:** `interface/plugins/modifier.dateadd.php`

**Issue:** Uses `strftime()` which is deprecated in PHP 8.1 and removed in PHP 8.2

**BEFORE:**
```php
<?php
/**
 * Smarty modifier to add days to a date
 *
 * Type:     modifier<br>
 * Name:     dateadd<br>
 * Purpose:  Add days to a date and format it
 *
 * @param string $dt The input date
 * @param int $days Number of days to add
 * @param string $format strftime format string
 * @return string The formatted date
 */
function smarty_modifier_dateadd($dt, $days, $format='%Y-%m-%d') {
  return strftime($format, strtotime("$dt + $days days"));  // ❌ strftime() deprecated
}
?>
```

**AFTER:**
```php
<?php
/**
 * Smarty modifier to add days to a date
 *
 * Type:     modifier<br>
 * Name:     dateadd<br>
 * Purpose:  Add days to a date and format it
 *
 * @param string $dt The input date
 * @param int $days Number of days to add
 * @param string $format DateTime format string (not strftime format!)
 * @return string The formatted date
 */
function smarty_modifier_dateadd($dt, $days, $format='Y-m-d') {
  // Use DateTime for PHP 8.1+ compatibility
  try {
    $date = new DateTime($dt);
    $date->modify("+{$days} days");
    return $date->format($format);
  } catch (Exception $e) {
    // Fallback: return original date on error
    error_log("dateadd modifier error: " . $e->getMessage());
    return $dt;
  }
}
?>
```

**IMPORTANT FORMAT CHANGE:**

| strftime format | DateTime format | Meaning |
|-----------------|-----------------|---------|
| `%Y-%m-%d` | `Y-m-d` | 2025-11-17 |
| `%Y` | `Y` | 2025 |
| `%m` | `m` | 11 |
| `%d` | `d` | 17 |
| `%B` | `F` | November |
| `%b` | `M` | Nov |
| `%A` | `l` | Sunday |
| `%a` | `D` | Sun |

**Search for usage in templates:**
```bash
# Find all uses of dateadd modifier
grep -rn "dateadd" interface/themes/firebird/

# Check what format strings are used
# Update template calls if they use strftime format
```

**Steps:**

1. Edit modifier.dateadd.php with the new code

2. **Check for template usage:**
   ```bash
   grep -rn "|dateadd" interface/themes/firebird/
   ```

   If the modifier is used with strftime format strings (containing `%`), you'll need to convert them:
   ```smarty
   {* BEFORE (strftime format) *}
   {$date|dateadd:7:"%Y-%m-%d"}

   {* AFTER (DateTime format) *}
   {$date|dateadd:7:"Y-m-d"}
   ```

3. Test the modifier:
   ```bash
   # Test via PHP
   docker compose exec vufind php -r "
   require 'interface/plugins/modifier.dateadd.php';
   echo smarty_modifier_dateadd('2025-11-17', 7, 'Y-m-d') . PHP_EOL;
   "
   # Expected: 2025-11-24
   ```

4. Test in application:
   ```bash
   # Access pages that might use dateadd
   curl -s http://localhost:8080/Search/Home?lookfor=test > /dev/null

   # Check for errors
   docker compose logs vufind | grep -i "dateadd"
   # Expected: No errors
   ```

#### Commit Both Plugin Fixes:

```bash
git add interface/plugins/function.firebird_manifest.php \
        interface/plugins/modifier.dateadd.php

git commit -m "Update custom Smarty plugins for Smarty 3 and PHP 8.1+

- function.firebird_manifest.php: get_template_vars() → getTemplateVars()
- modifier.dateadd.php: Replace strftime() with DateTime::format()

PHP 8.1+ compatibility: strftime() is deprecated
Smarty 3 compatibility: Use camelCase method names"
```

#### Success Criteria:
- ✅ firebird_manifest function works (assets load)
- ✅ dateadd modifier works (if used in templates)
- ✅ No deprecation warnings in logs
- ✅ PHP 8.1+ compatible (no strftime)
- ✅ All custom plugins load without errors

#### Testing All Custom Plugins:

```bash
# Verify all 13 custom plugins load
docker compose exec vufind php << 'EOF'
<?php
require_once 'sys/Interface.php';
$interface = new UInterface();

$plugins = glob('interface/plugins/*.php');
echo "Testing " . count($plugins) . " custom plugins:\n";

foreach ($plugins as $plugin) {
  $name = basename($plugin, '.php');
  echo "  - $name: ";

  // Try to load plugin
  require_once $plugin;
  echo "✓ loaded\n";
}
echo "\nAll plugins loaded successfully!\n";
?>
EOF
```

#### Time Estimate: 30 minutes

---

### Phase 8: Comprehensive Testing

**Objective:** Validate entire migration with all testing methods

#### Testing Matrix:

| Test Type | Tool | When | Success Criteria |
|-----------|------|------|------------------|
| Unit Tests | PHPUnit | After each phase | 5 tests, 18 assertions pass |
| Integration Tests | Playwright | After Phase 8 | 17+ browser tests pass |
| Smoke Tests | Manual/curl | After each phase | Key pages load correctly |
| Visual Regression | Screenshots | After Phase 8 | No visual differences |
| Error Monitoring | Docker logs | Continuous | No Smarty errors |
| Compilation Check | Script | After Phase 4 | Smarty 3.x version in compiled templates |

#### Test Procedures:

##### 1. PHPUnit Tests

```bash
docker compose run phpunit

# Expected output:
# PHPUnit 9.6.11 by Sebastian Bergmann and contributors.
#
# Runtime:       PHP 7.4.33
# Configuration: /app/phpunit.xml
#
# .....                                              5 / 5 (100%)
#
# Time: 00:00.XXX, Memory: XX.XX MB
#
# OK (5 tests, 18 assertions)
```

**If tests fail:**
- Check which test failed
- Review error message
- Check if Smarty-related (template rendering, etc.)
- Fix issue and re-run

##### 2. Playwright Tests

```bash
docker compose run playwright

# Expected:
# All 17+ tests pass
# No Smarty-related failures
```

**Key tests to watch:**
- Homepage rendering
- Search results display
- Record view rendering
- Advanced search form
- Citation display

##### 3. Manual Smoke Tests

**Test Script:**
```bash
#!/bin/bash
# smoke_test_smarty3.sh

BASE_URL="http://localhost:8080"
PASS=0
FAIL=0

echo "=== Smarty 3 Migration Smoke Tests ==="
echo ""

# Test 1: Homepage
echo -n "1. Homepage loads: "
if curl -sf "$BASE_URL/" | grep -q "HathiTrust"; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "❌ FAIL"
  ((FAIL++))
fi

# Test 2: Search results
echo -n "2. Search results render: "
if curl -sf "$BASE_URL/Search/Home?lookfor=test" | grep -q "results"; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "❌ FAIL"
  ((FAIL++))
fi

# Test 3: Record view
echo -n "3. Record view renders: "
if curl -sf "$BASE_URL/Record/000000001" 2>/dev/null | grep -q "record"; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "⚠️  SKIP (no test record)"
fi

# Test 4: Advanced search
echo -n "4. Advanced search form: "
if curl -sf "$BASE_URL/Search/Advanced" | grep -q "advanced"; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "❌ FAIL"
  ((FAIL++))
fi

# Test 5: API endpoint (volumes.php)
echo -n "5. Volumes API works: "
if curl -sf "$BASE_URL/static/api/volumes.php?id=test" 2>/dev/null | grep -q "<?xml"; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "⚠️  SKIP (may need valid ID)"
fi

# Test 6: Smarty 3 compilation
echo -n "6. Templates use Smarty 3: "
if head -1 interface/compile/firebird/*.php 2>/dev/null | grep -q "Smarty version 3"; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "❌ FAIL - Still using Smarty 2!"
  ((FAIL++))
fi

# Test 7: No Smarty errors in logs
echo -n "7. No Smarty errors in logs: "
ERROR_COUNT=$(docker compose logs vufind --since 10m 2>&1 | grep -i "smarty.*error\|smarty.*fatal" | wc -l)
if [ "$ERROR_COUNT" -eq 0 ]; then
  echo "✅ PASS"
  ((PASS++))
else
  echo "❌ FAIL ($ERROR_COUNT errors)"
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
  echo "❌ Some tests failed - check logs"
  exit 1
fi
```

**Run it:**
```bash
chmod +x smoke_test_smarty3.sh
./smoke_test_smarty3.sh
```

##### 4. Visual Regression Testing

**Process:**

1. **Load before screenshots** (taken in Phase 1)

2. **Take new screenshots** of same pages:
   ```bash
   # Homepage
   # Search results
   # Record view
   # Advanced search
   # Citation view
   ```

3. **Compare visually:**
   - Check layout is identical
   - Check no missing elements
   - Check no broken CSS
   - Check images load
   - Check JavaScript works

4. **Differences to expect:**
   - None! Visual output should be identical
   - If differences exist, investigate cause

**Tools:**
- Browser screenshot tools
- `diff` for HTML source comparison
- Visual diff tools (optional)

##### 5. Error Log Analysis

```bash
# Check last 100 lines for Smarty issues
docker compose logs vufind --tail 100 | grep -i smarty

# Check for specific error patterns
docker compose logs vufind --since 1h | grep -E "error|warning|fatal" | grep -i smarty

# Expected: No errors
```

**Good log output:**
```
(No Smarty-related errors or warnings)
```

**Bad log output (examples to watch for):**
```
PHP Notice: Undefined property: UInterface::$debug
PHP Warning: Smarty error: unable to read resource
PHP Fatal error: Call to undefined method Smarty::register_function()
```

##### 6. Template Compilation Verification

**Script:**
```bash
#!/bin/bash
# verify_smarty3_templates.sh

COMPILED_DIR="interface/compile/firebird"

echo "=== Smarty 3 Template Compilation Check ==="
echo ""

# Count compiled templates
COUNT=$(find "$COMPILED_DIR" -name "*.php" 2>/dev/null | wc -l)
echo "Compiled templates found: $COUNT"

if [ "$COUNT" -eq 0 ]; then
  echo "⚠️  No compiled templates - access some pages first"
  exit 0
fi

# Check versions
echo ""
echo "Smarty versions in compiled templates:"
grep -h "Smarty version" "$COMPILED_DIR"/*.php 2>/dev/null | sort -u
echo ""

# Check for Smarty 2
SMARTY2=$(grep -l "Smarty version 2" "$COMPILED_DIR"/*.php 2>/dev/null | wc -l)
SMARTY3=$(grep -l "Smarty version 3" "$COMPILED_DIR"/*.php 2>/dev/null | wc -l)

echo "Smarty 2 templates: $SMARTY2"
echo "Smarty 3 templates: $SMARTY3"
echo ""

if [ "$SMARTY2" -gt 0 ]; then
  echo "❌ FAIL: Still have Smarty 2 compiled templates!"
  echo "   This means Smarty 3 is not being used for compilation"
  echo "   Check autoloader fix in index.php"
  exit 1
fi

if [ "$SMARTY3" -gt 0 ]; then
  echo "✅ PASS: All templates compiled with Smarty 3"
  exit 0
fi

echo "⚠️  Unable to determine Smarty version"
exit 1
```

**Run it:**
```bash
chmod +x verify_smarty3_templates.sh
./verify_smarty3_templates.sh
```

#### Comprehensive Test Checklist:

**Before declaring success, verify:**

- [ ] ✅ All 5 PHPUnit tests pass
- [ ] ✅ All 17+ Playwright tests pass
- [ ] ✅ Homepage renders correctly
- [ ] ✅ Search results display correctly
- [ ] ✅ Individual record pages render
- [ ] ✅ Advanced search form works
- [ ] ✅ Record citations display
- [ ] ✅ Export formats work (MARC, BibTeX, etc.)
- [ ] ✅ API endpoint (volumes.php) returns valid XML
- [ ] ✅ All compiled templates show Smarty 3.x version
- [ ] ✅ No Smarty errors in logs
- [ ] ✅ No deprecation warnings in logs
- [ ] ✅ Custom plugins work (translate, highlight, etc.)
- [ ] ✅ Visual appearance matches pre-migration screenshots
- [ ] ✅ JavaScript functionality works
- [ ] ✅ CSS styling correct
- [ ] ✅ Images load correctly

#### Time Estimate: 60 minutes

---

### Phase 9: Documentation & Completion

**Objective:** Document the migration and create completion markers

#### Steps:

1. **Update this migration plan** with actual results
   ```markdown
   ## Migration Results

   **Date Completed:** [DATE]
   **Completed By:** [NAME]
   **Branch:** smarty-3-migration
   **Commits:** [LIST KEY COMMITS]

   ### Issues Encountered:
   - [Any issues and how they were resolved]

   ### Deviations from Plan:
   - [Any changes to the planned approach]

   ### Final Test Results:
   - PHPUnit: 5/5 tests passed
   - Playwright: 17/17 tests passed
   - Smoke tests: All passed
   - Visual regression: No differences
   ```

2. **Create completion tag**
   ```bash
   git tag -a smarty3-migration-complete -m "Smarty 2 to 3 migration complete

   All changes:
   - Removed alicorn theme
   - Fixed autoloader to allow Smarty 3 class loading
   - Cleaned compiled templates (now use Smarty 3.x)
   - Updated Interface.php to Smarty 3 API
   - Migrated volumes.php to Smarty 3
   - Updated custom plugins for Smarty 3 and PHP 8.1+

   Testing:
   - All PHPUnit tests pass
   - All Playwright tests pass
   - All smoke tests pass
   - Visual regression: no differences
   - No Smarty errors in logs

   Next: Ready for Smarty 3 to 4 migration (separate task)"

   git push origin smarty3-migration-complete
   ```

3. **Update developer documentation**

   Create or update `docs/SMARTY_USAGE.md`:
   ```markdown
   # Smarty Template Usage in HathiTrust Catalog

   ## Current Version

   **Smarty 3.1.x** (via Debian smarty3 package)

   ## Migration History

   - **Pre-2025**: Smarty 2.6.31
   - **Nov 2025**: Migrated to Smarty 3.1.x
   - **Future**: Plan to migrate to Smarty 4/5

   ## Key Differences from Smarty 2

   ### Method Names (camelCase in Smarty 3)

   | Smarty 2 | Smarty 3 |
   |----------|----------|
   | `register_function()` | `registerPlugin('function', ...)` |
   | `register_modifier()` | `registerPlugin('modifier', ...)` |
   | `get_template_vars()` | `getTemplateVars()` |
   | `assign()` | `assign()` (unchanged) |
   | `display()` | `display()` (unchanged) |

   ### Creating Custom Plugins

   Place custom plugins in: `interface/plugins/`

   **Function example:**
   ```php
   <?php
   function smarty_function_my_function($params, &$smarty) {
       return "output";
   }
   ?>
   ```

   **Modifier example:**
   ```php
   <?php
   function smarty_modifier_my_modifier($input, $param) {
       return modified($input, $param);
   }
   ?>
   ```

   ## Template Syntax

   Smarty 2 and 3 template syntax is mostly compatible.

   ### Recommended Modern Syntax

   ```smarty
   {* Use @ for foreach properties *}
   {foreach $items as $item}
     {if $item@first}First item{/if}
     {if $item@last}Last item{/if}
     {$item@iteration}
   {/foreach}

   {* Instead of old style: *}
   {* $smarty.foreach.loop.first *}
   {* $smarty.foreach.loop.last *}
   ```

   ## Notes for Developers

   - Always use SmartyBC for backward compatibility
   - Use Smarty 3 method names (camelCase)
   - Test custom plugins with Smarty 3
   - No need to update template syntax (both work)
   ```

4. **Document lessons learned**

   Add to this migration plan:
   ```markdown
   ## Lessons Learned

   1. **Autoloader conflicts are silent** - The autoloader issue caused a silent fallback to Smarty 2, which was hard to diagnose.

   2. **Check compiled templates** - The compiled template headers were the key evidence that Smarty 2 was still being used.

   3. **Test incrementally** - Testing after each phase made it easy to identify issues.

   4. **Visual regression is important** - Screenshots helped verify no rendering changes.

   5. **Custom plugins need updates** - Don't forget to check custom plugins for deprecated methods.
   ```

5. **Prepare for next migration (Smarty 3 → 4)**

   Create placeholder: `migration_plan_2025/smarty3_smarty4Migration.md`
   ```markdown
   # Smarty 3 → Smarty 4 Migration Plan

   **Status:** Not yet started
   **Prerequisite:** Smarty 2 → 3 migration must be complete

   ## Key Changes in Smarty 4

   - PHP 7.1+ required (we have 7.4 ✓)
   - Removed `SmartyBC` class (must use pure Smarty 4)
   - Changed plugin registration
   - New template syntax features

   ## To Be Determined

   - Full compatibility analysis
   - Testing strategy
   - Migration timeline

   **Note:** This is a future task, separate from the current Smarty 3 migration.
   ```

6. **Final commit**
   ```bash
   git add docs/ migration_plan_2025/
   git commit -m "Document Smarty 2 to 3 migration completion

   - Update migration plan with results
   - Add Smarty usage documentation for developers
   - Create placeholder for future Smarty 3 to 4 migration
   - Document lessons learned"
   ```

#### Success Criteria:
- ✅ Migration plan updated with results
- ✅ Completion tag created
- ✅ Developer documentation updated
- ✅ Lessons learned documented
- ✅ Future migration placeholder created

#### Time Estimate: 15 minutes

---

## Testing Procedures

### Test Levels

#### Level 1: Quick Smoke Test (after each phase)
```bash
# Start app
docker compose up -d

# Check homepage
curl -s http://localhost:8080/ | grep -q "HathiTrust" && echo "✓ Homepage OK"

# Check logs
docker compose logs vufind --tail 50 | grep -i "error\|fatal" | grep -i smarty
# Expected: no output
```

#### Level 2: PHPUnit Tests (after major changes)
```bash
docker compose run phpunit
# Expected: OK (5 tests, 18 assertions)
```

#### Level 3: Full Validation (Phase 8)
```bash
# Run all test scripts
./smoke_test_smarty3.sh
./verify_smarty3_templates.sh

# Run Playwright
docker compose run playwright

# Visual comparison
# Compare screenshots manually
```

---

## Rollback Plan

### If Issues Arise During Migration

#### Option 1: Rollback Last Phase
```bash
# See what changed
git log --oneline -5

# Revert last commit
git revert HEAD

# Or reset to previous commit
git reset --hard HEAD~1

# Rebuild
docker compose build vufind
docker compose up -d
```

#### Option 2: Complete Rollback to Pre-Migration State
```bash
# Return to pre-migration tag
git checkout pre-smarty3-migration

# Or if on branch, reset to tag
git reset --hard pre-smarty3-migration

# Clean up
rm -rf interface/compile/firebird/*.php

# Rebuild
docker compose build vufind
docker compose up -d

# Verify Smarty 2 is back
head -1 interface/compile/firebird/*.php
# Expected: Smarty version 2.6.31
```

#### Option 3: Restore from Backup (if database affected)
```bash
# If session data was corrupted (unlikely)
# Restore database backup (if taken)
docker compose exec -T db mysql -u root -p catalog < sessions-backup.sql
```

### Rollback Decision Tree

```
Issue detected
    ↓
Is it critical? (site down, data loss, etc.)
    ↓
YES → Complete rollback (Option 2)
NO  → Can we fix quickly? (< 30 min)
    ↓
YES → Fix and continue
NO  → Rollback last phase (Option 1), investigate
```

---

## Troubleshooting Guide

### Issue 1: Templates Still Compile with Smarty 2

**Symptom:**
```bash
head -1 interface/compile/firebird/*.php
# Shows: /* Smarty version 2.6.31 ... */
```

**Diagnosis:**
The autoloader wasn't fully removed or Smarty 3 isn't loading correctly.

**Solutions:**

1. **Verify autoloader is completely removed:**
   ```bash
   grep -n "sample_autoloader" index.php
   # Should show: No results (or only in comments about removal)

   # Verify the lines where autoloader was are now comments or removed
   sed -n '42,45p' index.php
   # Should show: Comments about autoloader removal, not the function
   ```

2. **Check Smarty 3 is installed:**
   ```bash
   docker compose exec vufind ls -la /usr/share/php/smarty3/
   # Should show: SmartyBC.class.php, Smarty.class.php, etc.
   ```

3. **Verify Interface.php loads Smarty 3:**
   ```bash
   grep "require_once" sys/Interface.php | grep -i smarty
   # Should show: require_once 'smarty3/SmartyBC.class.php';
   ```

4. **Force delete and recompile:**
   ```bash
   rm -f interface/compile/firebird/*.php
   docker compose restart vufind
   curl http://localhost:8080/
   head -1 interface/compile/firebird/*.php
   ```

5. **Check for multiple Smarty installations:**
   ```bash
   docker compose exec vufind find /usr -name "Smarty.class.php" 2>/dev/null
   # Should only show: /usr/share/php/smarty3/Smarty.class.php
   ```

---

### Issue 2: "Undefined property" Warnings

**Symptom:**
```
PHP Notice: Undefined property: UInterface::$debug in /usr/share/php/smarty3/Smarty.class.php
```

**Diagnosis:**
Setting Smarty properties before parent constructor is called.

**Solution:**
Ensure `parent::__construct()` is the **first line** in `UInterface::__construct()`:

```php
function __construct()
{
    parent::__construct();  // ← Must be FIRST
    global $configArray;
    // ... rest of code
}
```

---

### Issue 3: "Call to undefined method register_function()"

**Symptom:**
```
PHP Fatal error: Call to undefined method Smarty::register_function()
```

**Diagnosis:**
Using pure `Smarty` class instead of `SmartyBC`, or `registerPlugin()` change not applied.

**Solutions:**

1. **Use SmartyBC class:**
   ```php
   // In Interface.php
   class UInterface extends SmartyBC {  // Not just Smarty
   ```

2. **Update method calls:**
   ```php
   // Change from:
   $this->register_function('translate', 'translate');

   // To:
   $this->registerPlugin('function', 'translate', 'translate');
   ```

---

### Issue 4: "Unknown modifier 'truncate'"

**Symptom:**
```
Smarty Compiler: unknown modifier 'truncate'
```

**Diagnosis:**
Smarty's default plugins directory not loaded.

**Solutions:**

1. **Check plugins_dir configuration:**
   ```php
   // In Interface.php, should be:
   $this->addPluginsDir("$local/interface/plugins");

   // NOT:
   $this->plugins_dir = array(...);  // This overwrites default!
   ```

2. **Verify Smarty 3 plugins exist:**
   ```bash
   docker compose exec vufind ls /usr/share/php/smarty3/plugins/modifier.truncate.php
   # Should exist
   ```

3. **Check plugin loading:**
   ```bash
   docker compose exec vufind php << 'EOF'
   <?php
   require 'sys/Interface.php';
   $i = new UInterface();
   print_r($i->getPluginsDir());
   ?>
   EOF
   # Should show both /usr/share/php/smarty3/plugins and interface/plugins
   ```

---

### Issue 5: "Failed opening required 'Smarty/Internal/...'"

**Symptom:**
```
PHP Fatal error: Failed opening required 'Smarty/Internal/Compile/Firebird/Manifest.php'
```

**Diagnosis:**
Autoloader is still present and intercepting Smarty internal classes.

**Solution:**
The autoloader should be completely removed. Verify:

```bash
grep -n "sample_autoloader" index.php
```

Should show:
```
# No results (or only in comments about removal)
```

If the autoloader is still there, remove it completely as described in Phase 3:
- Delete the `function sample_autoloader` definition (lines 42-45)
- Delete the `spl_autoload_register('sample_autoloader');` call
- Add explicit `require_once 'XML/Unserializer.php';` in Browse.php and Record.php

---

### Issue 6: Custom Plugins Don't Load

**Symptom:**
```
Smarty error: unable to read resource: "function:my_custom_function"
```

**Diagnosis:**
Custom plugins directory not added or plugins have incorrect naming.

**Solutions:**

1. **Verify plugins directory added:**
   ```php
   // In Interface.php constructor:
   $this->addPluginsDir("$local/interface/plugins");
   ```

2. **Check plugin file naming:**
   ```
   Correct: function.my_function.php
   Wrong:   my_function.php

   Correct: modifier.my_modifier.php
   Wrong:   my_modifier_modifier.php
   ```

3. **Check plugin function naming:**
   ```php
   // For function.my_function.php:
   function smarty_function_my_function($params, &$smarty) { }

   // For modifier.my_modifier.php:
   function smarty_modifier_my_modifier($value, $param) { }
   ```

4. **Test plugin loading:**
   ```bash
   docker compose exec vufind php << 'EOF'
   <?php
   require 'interface/plugins/function.css_link.php';
   echo "✓ Plugin file loads\n";

   if (function_exists('smarty_function_css_link')) {
     echo "✓ Plugin function exists\n";
   }
   ?>
   EOF
   ```

---

### Issue 7: Visual Differences After Migration

**Symptom:**
Pages look different than before migration.

**Diagnosis:**
Template rendering changed or CSS not loading.

**Investigation:**

1. **Compare HTML source:**
   ```bash
   # Before migration (from screenshots or saved HTML)
   diff before.html after.html
   ```

2. **Check CSS loads:**
   ```bash
   curl -s http://localhost:8080/ | grep "\.css"
   # Should show CSS links
   ```

3. **Check JavaScript loads:**
   ```bash
   curl -s http://localhost:8080/ | grep "\.js"
   # Should show JS links
   ```

4. **Check browser console:**
   Open browser dev tools, look for:
   - 404 errors (missing assets)
   - JavaScript errors
   - CSS syntax errors

5. **Check custom plugins:**
   If css_link or js_link plugins changed, check their output:
   ```smarty
   {* In template: *}
   {css_link href="/css/main.css"}

   {* Should output: *}
   <link rel="stylesheet" href="/css/main.css?v=timestamp">
   ```

---

### Issue 8: Volumes API Returns Errors

**Symptom:**
```
curl http://localhost:8080/static/api/volumes.php?id=test
# Returns: PHP errors or invalid XML
```

**Diagnosis:**
volumes.php not updated to Smarty 3 or template compilation issue.

**Solutions:**

1. **Verify Smarty 3 path:**
   ```bash
   grep "require_once.*Smarty" static/api/volumes.php
   # Should show: require_once 'smarty3/Smarty.class.php';
   ```

2. **Check template compilation directory:**
   ```bash
   ls -la static/api/compile/
   # Check permissions, check for .php files
   ```

3. **Test template directly:**
   ```bash
   docker compose exec vufind php << 'EOF'
   <?php
   define('TMPLTDIR', '/app/static/api/templates/volumes');
   require_once 'smarty3/Smarty.class.php';
   $s = new Smarty();
   $s->template_dir = TMPLTDIR;
   $s->compile_dir = '/tmp';
   $s->assign('gbs_info', array());
   $s->assign('hathitrust_info', array());
   $s->display('volumes.xml.tpl');
   ?>
   EOF
   ```

---

### Issue 9: PHPUnit Tests Fail

**Symptom:**
```bash
docker compose run phpunit
# Shows: FAILURES!
```

**Investigation:**

1. **Check which test failed:**
   Look at the test name and error message.

2. **Check if Smarty-related:**
   ```
   Test: testSearchStructure
   Error: Smarty error: unable to read resource
   → Smarty template issue
   ```

3. **Run single test:**
   ```bash
   docker compose run phpunit --filter testSearchStructure
   ```

4. **Check test dependencies:**
   - Do tests need specific templates?
   - Do tests mock Smarty correctly?
   - Do tests use deprecated methods?

5. **Check test fixtures:**
   ```bash
   ls -la test/fixtures/
   # Verify test data exists
   ```

---

### Issue 10: Performance Degradation

**Symptom:**
Pages load slower after migration.

**Investigation:**

1. **Check if compiling on every request:**
   ```php
   // In Interface.php, should have:
   $this->compile_check = true;  // OK for development
   // For production:
   $this->compile_check = false;  // Faster (don't recompile)
   ```

2. **Check compiled template count:**
   ```bash
   ls -1 interface/compile/firebird/*.php | wc -l
   # Should match template count (41 templates = ~41 compiled files)
   ```

3. **Check file permissions:**
   ```bash
   ls -la interface/compile/firebird/
   # Should be writable by PHP-FPM user
   ```

4. **Profile template rendering:**
   ```bash
   # Enable Smarty debugging
   # In Interface.php:
   $this->debugging = true;

   # Reload page, check for debug output
   ```

---

## Post-Migration Validation

### Final Validation Checklist

Run this checklist before declaring migration complete:

#### Code Validation

- [ ] ✅ `index.php` autoloader excludes Smarty classes
- [ ] ✅ `sys/Interface.php` uses `registerPlugin()` not `register_function()`
- [ ] ✅ `sys/Interface.php` uses `addPluginsDir()` not direct property
- [ ] ✅ `static/api/volumes.php` uses `smarty3/Smarty.class.php` path
- [ ] ✅ `interface/plugins/function.firebird_manifest.php` uses `getTemplateVars()`
- [ ] ✅ `interface/plugins/modifier.dateadd.php` uses `DateTime::format()` not `strftime()`
- [ ] ✅ `interface/themes/alicorn/` directory deleted
- [ ] ✅ No files with deprecated Smarty 2 method calls

#### Runtime Validation

- [ ] ✅ All compiled templates show Smarty 3.x version
- [ ] ✅ No Smarty errors in application logs
- [ ] ✅ No deprecation warnings in logs
- [ ] ✅ PHPUnit: 5/5 tests pass
- [ ] ✅ Playwright: 17+/17+ tests pass
- [ ] ✅ Homepage renders correctly
- [ ] ✅ Search results render correctly
- [ ] ✅ Record pages render correctly
- [ ] ✅ Advanced search works
- [ ] ✅ Citations display correctly
- [ ] ✅ Export formats work
- [ ] ✅ API endpoint returns valid XML
- [ ] ✅ Custom plugins work (translate, highlight, etc.)
- [ ] ✅ CSS loads correctly
- [ ] ✅ JavaScript works correctly
- [ ] ✅ Images load correctly

#### Documentation Validation

- [ ] ✅ Migration plan updated with results
- [ ] ✅ Git tag `smarty3-migration-complete` created
- [ ] ✅ Developer documentation updated
- [ ] ✅ Lessons learned documented

### Success Metrics

| Metric | Target | Status |
|--------|--------|--------|
| Compiled template version | Smarty 3.x | ✅ / ❌ |
| PHPUnit tests passing | 5/5 | ✅ / ❌ |
| Playwright tests passing | 17+/17+ | ✅ / ❌ |
| Smarty errors in logs | 0 | ✅ / ❌ |
| Deprecation warnings | 0 | ✅ / ❌ |
| Visual regression issues | 0 | ✅ / ❌ |
| API endpoint functional | Yes | ✅ / ❌ |
| Performance impact | < 5% slower | ✅ / ❌ |

---

## Next Steps: Smarty 3 → Smarty 4 Migration

**Status:** Future task (separate from this migration)

### Why Smarty 4?

- PHP 8+ full support
- Better performance
- Modern features
- Active development

### Key Differences (Smarty 3 → 4)

1. **No SmartyBC class** - Must use pure Smarty 4 API
2. **Changed plugin registration** - Different method signatures
3. **New template syntax** - Enhanced features
4. **Stricter error handling** - Less forgiving

### Prerequisites for Smarty 4

- ✅ Smarty 3 migration complete (this document)
- ✅ PHP 8.0+ installed (we have 7.4 currently)
- ✅ All deprecated methods removed
- ❌ PHP upgrade to 8.0+ needed first

### Recommended Sequence

```
1. Complete Smarty 2 → 3 migration (this plan)
2. Test thoroughly on PHP 7.4
3. Upgrade PHP 7.4 → 8.0 (Phase 2 of main migration plan)
4. Test Smarty 3 on PHP 8.0
5. THEN consider Smarty 3 → 4 migration
```

**Note:** Do NOT attempt Smarty 4 migration until PHP 8 upgrade is complete.

---

## Appendix A: File Inventory

### Files Modified in This Migration

1. **index.php** (lines 42-45, 73-75)
   - Autoloader fix
   - Remove alicorn theme override

2. **sys/Interface.php** (lines 46, 53-54)
   - Use `addPluginsDir()` method
   - Use `registerPlugin()` method

3. **static/api/volumes.php** (line 410)
   - Change to `smarty3/Smarty.class.php` path

4. **interface/plugins/function.firebird_manifest.php** (line 13)
   - `get_template_vars()` → `getTemplateVars()`

5. **interface/plugins/modifier.dateadd.php** (entire file)
   - Replace `strftime()` with `DateTime::format()`

### Files/Directories Deleted

1. **interface/themes/alicorn/** (18 files)
2. **interface/compile/alicorn/** (all compiled templates)
3. **interface/compile/firebird/*.php** (temporary deletion for recompilation)

### Files Created

1. **migration_plan_2025/smarty2_smarty3Migration.md** (this document)
2. **docs/SMARTY_USAGE.md** (Smarty usage guide)
3. **smoke_test_smarty3.sh** (test script)
4. **verify_smarty3_templates.sh** (verification script)

---

## Appendix B: Smarty Version Comparison

### Key Differences: Smarty 2 vs Smarty 3

| Feature | Smarty 2 | Smarty 3 |
|---------|----------|----------|
| PHP Requirement | PHP 4.0.6+ | PHP 5.2+ |
| PHP 8 Compatible | ❌ No | ⚠️ Partial (3.1.39+) |
| Method Names | snake_case | camelCase |
| Backward Compat | N/A | SmartyBC class |
| Plugin System | Simple | Enhanced |
| Template Syntax | Basic | Extended |
| Performance | Baseline | ~20% faster |
| Active Development | ❌ No | ⚠️ Maintenance mode |

### Method Name Changes

| Smarty 2 Method | Smarty 3 Method | Notes |
|-----------------|-----------------|-------|
| `register_function()` | `registerPlugin('function', ...)` | SmartyBC supports both |
| `register_modifier()` | `registerPlugin('modifier', ...)` | SmartyBC supports both |
| `register_block()` | `registerPlugin('block', ...)` | SmartyBC supports both |
| `get_template_vars()` | `getTemplateVars()` | SmartyBC may support old |
| `clear_all_assign()` | `clearAllAssign()` | camelCase |
| `clear_assign()` | `clearAssign()` | camelCase |

### Template Syntax Compatibility

```smarty
{* These work in both Smarty 2 and 3: *}
{$variable}
{$array.key}
{$array[0]}
{if $condition}...{/if}
{foreach $array as $item}...{/foreach}
{include file="template.tpl"}
{$var|modifier:"param"}

{* Smarty 2 style (still works in 3): *}
{$smarty.foreach.loop.first}
{$smarty.foreach.loop.last}
{$smarty.foreach.loop.iteration}

{* Smarty 3 new style (preferred): *}
{foreach $array as $item}
  {if $item@first}...{/if}
  {if $item@last}...{/if}
  {$item@iteration}
{/foreach}
```

---

## Appendix C: Quick Reference Commands

### Build & Deploy
```bash
# Build container
docker compose build vufind

# Start application
docker compose up -d

# Restart application
docker compose restart vufind

# View logs
docker compose logs -f vufind

# Tail error logs only
docker compose logs vufind | grep -i "error\|fatal\|warning"
```

### Testing
```bash
# Run PHPUnit
docker compose run phpunit

# Run Playwright
docker compose run playwright

# Run smoke tests
./smoke_test_smarty3.sh

# Verify Smarty 3 compilation
./verify_smarty3_templates.sh
```

### Template Management
```bash
# Delete compiled templates
rm -f interface/compile/firebird/*.php

# Check compiled template versions
head -1 interface/compile/firebird/*.php | grep "Smarty version"

# Count compiled templates
find interface/compile/firebird/ -name "*.php" | wc -l
```

### Git Operations
```bash
# Create branch
git checkout -b smarty-3-migration

# Create tag
git tag -a pre-smarty3-migration -m "Before migration"

# View changes
git diff

# Commit
git add .
git commit -m "Message"

# View history
git log --oneline -10
```

### Debugging
```bash
# Check Smarty 3 installation
docker compose exec vufind ls -la /usr/share/php/smarty3/

# Test PHP code
docker compose exec vufind php -r "require 'sys/Interface.php'; echo 'OK';"

# Check autoloader
docker compose exec vufind php << 'EOF'
<?php
function sample_autoloader($class) {
  if (strpos($class, 'Smarty') === 0) {
    echo "Excluding Smarty class: $class\n";
    return false;
  }
  echo "Loading: $class\n";
}
sample_autoloader('Smarty_Internal_Template');
sample_autoloader('Apache_Solr_Service');
?>
EOF
```

---

## Document Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2025-11-17 | Claude | Initial migration plan created |

---

**END OF SMARTY 2 → SMARTY 3 MIGRATION PLAN**

For questions or issues, consult the troubleshooting guide or contact the development team.

Next document: `smarty3_smarty4Migration.md` (to be created after this migration is complete)