<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;

class Property extends AbstractFieldType {
  public function process(\Iterator $row) {
    return $row[$this->getImportFieldName()];
  }
}
