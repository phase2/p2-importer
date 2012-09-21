<?php

namespace P2Importer;

use P2Importer\ParserInterface;

class Processor extends AbstractDataStorage implements  ProcessorInterface {
  public function process(ParserInterface $result, \Pimple $registry) {
    foreach ($result as $row) {
      $value[] = $registry['row-processor']->process($row, $registry);
    }

    return $this;
  }

  public function preProcess(\Pimple $registry) {}

  public function postProcess(\Pimple $registry) {}
}
