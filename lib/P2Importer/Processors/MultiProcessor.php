<?php

namespace P2Importer\Processors;

use P2Importer\ProcessorInterface;
use P2Importer\DataContainer;

class MultiProcessor implements  ProcessorInterface {
  public function process(DataContainer $result, \Pimple $registry) {
    foreach ($result as $row) {
      $row = $registry['data_container']->setAll($row)->lock();
      $registry['row-processor']->process($row, $registry);
    }

    return $this;
  }
}
