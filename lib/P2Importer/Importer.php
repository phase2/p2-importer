<?php

namespace P2Importer;

use P2Importer\ImporterInterface;

class Importer implements ImporterInterface {
  protected $registry;

  public function __construct(\Pimple $registry) {
    $this->registry = $registry;
  }

  public function process() {
    try {
      $result = $this->registry['fetcher']->load();
    } catch (\Exception $e) {
      watchdog_exception('importer', $e);
      return FALSE;
    }

    $data_container = $this->registry['data_container']->setAll($result)->lock();

    if (!empty($result)) {
      $this->registry['parser']->parse($data_container, $this->registry);
      $this->registry['processor']->process($data_container, $this->registry);
    }
  }
}
