<?php

namespace P2Importer;

use P2Importer\ParserInterface;

class Processor implements  ProcessorInterface {
  public function process(DataContainer $result, \Pimple $registry) {
    foreach ($result as $row) {
      $row = $result['data_container']->setAll($row)->lock();
      $registry['row-processor']->process($row, $registry);
    }

    return $this;
  }
}
