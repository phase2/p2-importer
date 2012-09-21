<?php

namespace P2Importer;

class RowParser extends AbstractDataStorage implements ParserInterface {
  public function parse(\Iterator $row, \Pimple $registry) {
    $field_map = $registry['field_map'];
    foreach ($row as $field_name => $field_value) {
      // Does the field name exist in the map
      if (!empty($field_map[$field_name])) {
        $value = $field_map[$field_name]->process($row);

        if (empty($field_map[$field_name]['field_settings']['multiple'])) {
          $this->values[$field_name] = $value;
        }
        else {
          if (empty($this->values[$field_name])) {
            $this->values[$field_name] = array();
          }

          $this->values[$field_name][] = $value;
        }
      }
    }
    return $this;
  }

  public function preProcess(\Pimple $registry) {}

  public function postProcess(\Pimple $registry) {
    $field_map = $registry['field_map'];
    $row = clone $this->values;
    unset($this->values);
    foreach ($field_map['field_import_map'] as $import_field_name => $field_name) {
      $value = $row->$import_field_name;
      if (!empty($value)) {
        $this->values[$field_name] = $value;
      }
    }
  }
}
