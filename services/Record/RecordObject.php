<?php
/**
  * @author Bill Dueber
  * @version 1.0
**/


/**
  * An ordered list of standard VUFind record arrays
  *
  * An object to deal with sets of records identified by an external-to-solr source (e.g., a tagging application or 
  * collection builder). Basically, just an ordered set of IDs and the records associated with them.
  *
  * Subclass this to create application-specific sets with appropriate #load and #find methods
  *
**/


class RecordObject
{
  /** 
    * @access public
    * @var string
  **/
  public $id;

  /** 
    * The array data as returned by Solr via the xsl, as used everywhere else in vufind.
    * 
    * @access private
    * @var array
  **/
  
  private $solrarray;
  
  
  
}