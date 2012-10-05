<?php

namespace P2Importer\Parser;

use P2Importer\DataContainer;
use P2Importer\ParserInterface;

class MultiRowParser implements ParserInterface {
  public function parse(DataContainer $result, \Pimple $registry) {
    $this->preProcess($result, $registry);
    foreach ($result as $key => $row) {
      $row = $result['data_container']->setAll($row)->lock();
      $registry['row_parser']->parse($row, $registry);
      $result[$key] = $row;
    }
    $this->postProcess($result, $registry);
    return $this;
  }

  public function preProcess(DataContainer $values, \Pimple $registry) {}

  public function postProcess(DataContainer $values, \Pimple $registry) {}
}
