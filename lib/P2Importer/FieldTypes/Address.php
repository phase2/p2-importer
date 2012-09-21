<?php

namespace P2Importer\FieldTypes;

use P2Importer\FieldTypes\DefaultFeildType;

class Address extends DefaultFeildType {
  public function process(\Iterator $row) {
    $fields = array();
    if (is_array($this->settings['fields'])) {
      foreach ($this->settings['fields'] as $field) {
        if (isset($row[$field])) {
          $fields[] = $row[$field];
        }
      }
    }

    $address = implode(' ', $fields);

    return parent::process($address);
  }
}

