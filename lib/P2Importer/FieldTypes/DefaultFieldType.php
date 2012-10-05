<?php

namespace P2Importer\FieldTypes;

use P2Importer\DataContainer;
use P2Importer\AbstractFieldType;

class DefaultFieldType extends AbstractFieldType {
  public function process(DataContainer $row, \Pimple $registry) {
    return array('value' => $this->getValue($row));
  }
}
