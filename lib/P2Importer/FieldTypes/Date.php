<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;
use P2Importer\DataContainer;

class Date extends AbstractFieldType {
  public function process(DataContainer $row, \Pimple $registry) {
    return array(
      'value' => strtotime($this->getValue($row)),
      'timezone' => $this->settings['timezone'] ?: 'America/New_York',
      'timezone_db' => $this->settings['timezone_db'] ?: 'America/New_York',
      'date_type' => 'datestamp',
    );
  }
}
