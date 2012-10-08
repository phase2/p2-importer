<?php

namespace P2Importer\Processors;

use P2Importer\DataContainer;
use P2Importer\ProcessorInterface;

class SingleProcessor implements  ProcessorInterface {
  public function process(DataContainer $result, \Pimple $registry) {
    $registry['row_processor']->process($result, $registry);

    return $this;
  }
}
