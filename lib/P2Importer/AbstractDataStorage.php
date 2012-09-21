<?php

namespace P2Importer;

abstract class AbstractDataStorage {
  protected $values = array();

  public function __get($name) {
    return $this->values[$name];
  }
}
