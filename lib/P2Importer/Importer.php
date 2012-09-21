<?php

namespace P2Importer;

use P2Importer\ImporterInterface;
use P2Importer\P2Exception;

class Importer implements ImporterInterface {
  protected $registry;

  public function __construct(\Pimple $registry) {
    $this->registry = $registry;
  }

  public function process() {
    $fetcher = $this->registry['fetcher'];
    $result = $fetcher->load();

    if (!empty($result)) {
      $result = $this->registry['parser']->parse($result);
      $this->registry['processor']->process($result);
    }
  }
}
