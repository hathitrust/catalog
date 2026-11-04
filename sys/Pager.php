<?php

class Pager {
  function __construct(
    public string $base_url,
    public int $total_records,
    public int $records_per_page = 100,
    public int $current_page = 1
  ) {
    // Search/Home.php should already have filtered out bogus records_per_page and current_page values,
    // this is just insurance.
    if ($this->records_per_page < 1) {
      $this->records_per_page = 100;
    }
    if ($this->current_page < 1) {
      $this->current_page = 1;
    }
  }

  function last_page(): int {
    $page_count = $this->total_records / $this->records_per_page;
    $last_page = 1;
    if ($page_count == intval($page_count)) {
      $last_page = intval($page_count);
    } else {
      $last_page = 1 + intval($page_count);
    }
    if ($last_page < 1) {
      $last_page = 1;
    }
    return $last_page;
  }

  function next_url(): string {
    if ($this->current_page == $this->last_page()) {
      return '';
    }
    $updatedParams = array_merge($_GET, ['page' => intval($this->current_page) + 1]);
    # Get rid of module and action used in routing
    unset($updatedParams['module']);
    unset($updatedParams['action']);
    $query = http_build_query($updatedParams);
    # Replace [x] on array values with [] for the advanced search component
    $query = preg_replace('/%5B[0-9]+%5D=/', '%5B%5D=', $query);
    return htmlspecialchars($this->base_url . $query, ENT_QUOTES, 'UTF-8');
  }

  function previous_url(): string {
    if ($this->current_page == 1) {
      return '';
    }
    $updatedParams = array_merge($_GET, ['page' => intval($this->current_page) - 1]);
    # Get rid of module and action used in routing
    unset($updatedParams['module']);
    unset($updatedParams['action']);
    $query = http_build_query($updatedParams);
    # Replace [x] on array values with [] for the advanced search component
    $query = preg_replace('/%5B[0-9]+%5D=/', '%5B%5D=', $query);
    return htmlspecialchars($this->base_url . $query, ENT_QUOTES, 'UTF-8');
  }
}

?>
