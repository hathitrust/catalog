<?php
/**
  * @author Bill Dueber
  * @version 1.0
**/

require_once 'services/Search/SearchStructure.php';


/**
  * An ordered list of standard VUFind record arrays
  *
  * An object to deal with sets of records identified by an external-to-solr source (e.g., a tagging application or 
  * collection builder). Basically, just an ordered set of IDs and the records associated with them.
  *
  * Subclass this to create application-specific sets with appropriate #load and #find methods
  *
**/


class RecordList implements Iterator
{
  
/** 
  * @access private
  * @var Solr
**/
  private $db;
  
  /** 
    * Ordered list of ids of the records in the list
    * @access private
    * @var array
  **/
  private $ids = array();

  /** 
    * Array of records hashed on id
    * @access private
    * @var array
  **/
  private $records = array();

  /** 
    * @access public
    * @var string|bool
  **/
  public $sort;

  /** 
    * @access private
    * @var bool
  **/
  private $valid;


  /**
    * Set up the object, along with a solr connection
    * @global array $configArray
    * @param array|string $ids  An id (or array of ids) to add upon creation
    *
  **/
  
  function __construct($ids=array()) {
    
    global $configArray;
    
    // Setup Search Engine Connection
    $class = $configArray['Index']['engine'];
    $this->db = new $class($configArray['Index']['url']);
        
    # Fill whatever IDs we have
    $this->ids = $ids;
  }
  
  
  /**
    * Add an id or array of ids to the object
    * Will not add the same id more than once; a twice-added id retains
    * its original place in the order.
    *
    * @param array|string $ids  An id (or array of ids) to add
    * @return integer The number of ids added
  **/
  
  function add_ids($ids) {
    if (!is_array($ids)) {
      $ids = array($ids);
    }
    $newcount = 0;
    foreach ($ids as $id) {
      if (!in_array($id, $this->ids)) {
        $this->ids[] = $id;
        $newcount++;
      }
    }
    return $newcount;
  }


  /**
    * Add a record or set of records to the object
    * If the id (as $record['id']) already exists, the record will
    * retains its old place in the list (as opposed to being appended), but 
    * the record body will be replaced by the passed object.
    *
    * @param array $records A RecordObject or record-structure-array, or an array of either, as returned from, e.g, 
    *                       Solr::simpleSearch
    * @throws Exception 'Trying to add record without id set'
    * @return integer The number of new records added
  **/
  
  function add_records($records) {
    // First we need to determine if we have a single record or not and the type
    
    // If it's a single REcordObejct, make it an array. Otherwise, if $records['id']
    // is set, it's a single record-structure-array and we need to make an array
    // of *that*
    
    if (is_subclass_of($records, 'RecordObject') || 
        (is_array($records) && isset($records['id']))) {
      $records = array($records);
    }
    
    $newcount = 0;
    foreach ($records as $r) {
      // Turn it into an object
      if (is_array($r) && isset($r['id'])) {
        $r = new RecordObject($r);
      }
      $id = $r->id;
      $this->records[$id] = $r;
      if (!in_array($id, $this->ids)) {
        $this->ids[] = $id;
        $newcount++;
      }
    }
    return $newcount;
  }
  
  /**
    * Get the records associated with the ids into $this->records[id] = $record
    * @return integer The new total number of records
    *
  **/
  
  function fill() {
    $need = array();
    foreach ($this->ids as $id) {
      if (!isset($this->records[$id])) {
        $need[] = $id;
      }
    }
    
    $ss = new SearchStructure(true); // true makes it come out empty
    $ss->nolimit = true;
    if ($this->sort) {
      $ss->sort = $this->sort;
    }
    
    $ss->addFilter('id', $need);

    $newrecords = $this->db->simpleSearch($ss);

    foreach ($newrecords['record'] as $r) {
      $this->records[$r['id']] = $r;
    }
    return count($this->records);
  }
  
  /**
    * Return a record based on its unique id. Will automatically call a fill if necessary
    *
    * @param string $id  The id of the record you want
    * @return array|false Either the record desired, or the false value
  **/
  
  function byID($id) {
    if (isset($this->records[$id])) {
      return $this->records[$id];
    }
    
    // If we've got the id but need to fetch it, call fill
    
    if (in_array($id, $this->records)) {
      $this->fill();
      return $this->records[$id];
    }
    
    // Otherwise, return false
    return false;
  }
  
  /** 
    * Return a record based on its position in the id list (zero-based)
    * 
    * @param integer $index The index of the record you want
    * @return array|false Return the record on success, false if it dne
    * 
  **/
    function byIndex($index) {
      if (!isset($this->ids[$index])) {
        return false;
      } else {
        return $this->byID($this->ids[$index]);
      }
    }
    
    
  /**
    * Functions needed for the iterator interface. Just piggyback on normal array current/next/rewind
    * Mostly copied from http://www.sitepoint.com/article/php5-standard-library/
  **/
  
  function rewind() {
    $this->valid = (FALSE !== reset($this->ids)); 
  }

  function current() {
    return $this->records[current($this->ids)];
  }

  function key() {
    return current($this->ids);
  }

  function next() {
    $this->valid = (FALSE !== next($this->ids));
  }

  function valid() {
    return $this->valid;
  }

}





?>