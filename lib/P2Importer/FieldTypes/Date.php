<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;

class Date extends AbstractFieldType {
  public function process(\Iterator $row) {
    return array(
      'value' => strtotime($row[$this->import_field_name]),
      'timezone' => $this->settings['timezone'] ?: 'America/New_York',
      'timezone_db' => $this->settings['timezone_db'] ?: 'America/New_York',
      'date_type' => 'datestamp',
    );
  }
}
