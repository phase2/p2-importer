<?php

namespace P2Importer;

class RowParser implements ParserInterface {
  public function parse(DataContainer $row, \Pimple $registry) {
    $this->preProcess($row, $registry);
    $field_map = $registry['field_map'];
    $fields_to_remove = array();
    foreach ($row as $remote_field_name => $field_value) {
      // separate this out because unset resets the pointer
      // Does the field name exist in the map
      if (!empty($field_map['field_import_map'][$remote_field_name])) {
        $field_name = $field_map['field_import_map'][$remote_field_name];
        $field_type = $field_map[$field_name];
        $field_settings = $field_type->getFieldSettings();

        if ($value = $field_type->process($row, $registry)) {
          if (!empty($field_settings['as_is'])) {
            $row[$remote_field_name] = $value;
          }
          else {
            if (empty($field_settings['multiple'])) {
              $row[$remote_field_name] = $value;
            }
            else {
              if (empty($row[$field_name])) {
                $row[$remote_field_name] = array();
              }
              $row[$remote_field_name][] = $value;
            }
          }
        }
        else {
          $fields_to_remove[] = $remote_field_name;
        }
      }
      else {
        $fields_to_remove[] = $remote_field_name;
      }
    }

    $registry['fields_to_remove'] = $fields_to_remove;

    $this->postProcess($row, $registry);
    return $this;
  }

  public function postProcess(DataContainer $values, \Pimple $registry) {
    $fields_to_remove = $registry['fields_to_remove'];

    foreach ($fields_to_remove as $field_name) {
      unset($values[$field_name]);
    }
    $return = array();

    $field_map = $registry['field_map'];
    foreach($values as $import_field_name => $value) {
      $field_name = $field_map['field_import_map'][$import_field_name];
      $return[$field_name] = $value;
    }

    $values->setAll($return);
    return $this;
  }

  public function preProcess(DataContainer $values, \Pimple $registry) {}

}
