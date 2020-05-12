# Emergency Temporary Access in the catalog

## Where the code lives

The catalog deals with ETAS information in a few places:

  * `sys/HTStatus.php` where it sets emergency access status via the cookie's `x` parameter
  * `sys/Solr.php` via `#fulltext_filter_add_etas`
  * `services/RecordUtils.php` with `#is_emergency_access` which determines if  particular item is valid
    for ETAS based on the login information and the individual item information
  * `alicorn/Reccord/view.tpl` and `alicorn/Search/list-list.tpl`, which changes the displayed link based on
    `RecordUtils#is_emergency_access`

## How to back it out

If the ETAS key (`x`) in the cookie disappears or is set to `false`, all the ETAS code will be no-ops. 
