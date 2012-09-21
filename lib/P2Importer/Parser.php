<?php

namespace P2Importer;

class Parser extends AbstractDataStorage implements ParserInterface {
  public function parse(\Iterator $result, \Pimple $registry) {
    $this->preProcess($registry);
    foreach ($result as $key => $row) {
      $this->values[$key] = $registry['row_parser']->parse($row, $registry);
    }
    $this->postProcess($registry);
    return $this;
  }

  public function preProcess(\Pimple $registry) {}

  public function postProcess(\Pimple $registry) {}
}
