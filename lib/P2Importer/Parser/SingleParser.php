<?php

namespace P2Importer\Parser;

use P2Importer\DataContainer;
use P2Importer\ParserInterface;

class SingleParser implements ParserInterface {
  public function parse(DataContainer $result, \Pimple $registry) {
    $this->preProcess($result, $registry);
    $registry['row_parser']->parse($result, $registry);
    $this->postProcess($result, $registry);
    return $this;
  }

  public function preProcess(DataContainer $values, \Pimple $registry) {}

  public function postProcess(DataContainer $values, \Pimple $registry) {}
}
