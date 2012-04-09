<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     dateadd
 * Purpose:  given a date as YYYYMMDD and a number of days to add, 
 *           return a string of the form YYYY-mm-dd for the computed 
 *           date
 * -------------------------------------------------------------
 */


function smarty_modifier_dateadd($dt, $days, $format='%Y-%m-%d') {
  return strftime($format, strtotime("$dt + $days days"));
}