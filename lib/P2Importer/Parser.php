<?php

namespace P2Importer;

class Parser extends AbstractDataStorage implements ParserInterface {
  public function parse(\Iterator $result, \Pimple $registry) {
    $this->preProcess($result, $registry);
    foreach ($result as $key => $row) {
      $this->values[$key] = $registry['row_parser']->parse($row, $registry);
    }
    $this->postProcess($result, $registry);
    return $this;
  }

  public function preProcess(\Iterator $result, \Pimple $registry) {}

  public function postProcess(\Iterator $result, \Pimple $registry) {}
}
