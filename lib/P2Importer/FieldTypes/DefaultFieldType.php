<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;

class DefaultFeildType extends AbstractFieldType {
  public function process(\Iterator $row) {
    return array('value' => $row[$this->getImportFieldName()]);
  }
}
