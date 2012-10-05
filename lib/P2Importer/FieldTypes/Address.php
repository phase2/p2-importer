<?php

namespace P2Importer\FieldTypes;

use P2Importer\DataContainer;
use P2Importer\FieldTypes\DefaultFieldType;

class Address extends DefaultFieldType {
  public function process(DataContainer $row, \Pimple $registry) {
    $fields = array();
    if (is_array($this->settings['fields'])) {
      foreach ($this->settings['fields'] as $field) {
        if (isset($row[$field])) {
          $fields[] = $row[$field];
        }
      }
    }

    $address = implode(' ', $fields);

    return array(
      'value' => $address,
    );
  }
}

