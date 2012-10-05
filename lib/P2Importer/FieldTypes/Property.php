<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;
use P2Importer\DataContainer;

class Property extends AbstractFieldType {
  public function process(DataContainer $row, \Pimple $registry) {
    return $this->getValue($row);
  }
}
