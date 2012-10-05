<?php

namespace P2Importer\Fetchers;

use P2Importer\AbstractFetcher;

class DB extends AbstractFetcher {
  public function load() {
    $table = $this->settings['table'] ?: NULL;
    $db = $this->settings['db'] ?: NULL;
    $count = empty($this->settings['row_count']) ? NULL : $this->settings['row_count'];

    db_set_active($db);
    $query = db_select($table)->fields($table);
    if ($count) {
      $query->range(0, $count);
    }
    $result = $query->execute();
    db_set_active();

    return $result;
  }
}


