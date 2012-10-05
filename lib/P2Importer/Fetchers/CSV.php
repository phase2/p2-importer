<?php

namespace P2Importer\Fetchers;

use P2Importer\AbstractFetcher;

class CSV extends AbstractFetcher {
  public function load() {
    if (empty($this->settings['file'])) {
      throw new \Exception("Missing Required Settings: file");
    }

    $file = new \SplFileObject(drupal_realpath($this->settings['file']));
    // Set the csv flags
    $file->setFlags(\SplFileObject::READ_CSV);

    if (!empty($this->settings['csv_control'])) {
      $file->setCsvControl($this->settings['csv_control']);
    }

    return $file;
  }
}
