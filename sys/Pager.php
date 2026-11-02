<?php

class Pager {
  function __construct(
    public string $base_url,
    public int $total_records,
    public int $records_per_page = 100,
    public int $current_page = 1
  ) {}

  function last_page() {
    $page_count = $this->total_records / ($this->records_per_page ?: 100);
    $last_page = 1;
    if ($page_count == intval($page_count)) {
      $last_page = $page_count;
    } else {
      $last_page = 1 + intval($page_count);
    }
    if ($last_page < 1) {
      $last_page = 1;
    }
    return $last_page;
  }

  function next_url() {
    if ($this->current_page == $this->last_page()) {
      return '';
    }
    $updatedParams = array_merge($_GET, ['page' => intval($this->current_page) + 1]);
    $queryString = http_build_query($updatedParams);
    return htmlspecialchars($this->base_url . $queryString, ENT_QUOTES, 'UTF-8');
  }

  function previous_url() {
    if ($this->current_page == 1) {
      return '';
    }
    return $this->base_url . "&page=" . intval($this->current_page - 1); 
  }
}

?>
