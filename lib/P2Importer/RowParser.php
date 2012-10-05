<?php

namespace P2Importer;

class RowParser implements ParserInterface {
  public function parse(DataContainer $row, \Pimple $registry) {
    $this->preProcess($row, $registry);
    $field_map = $registry['field_map'];
    foreach ($row as $remote_field_name => $field_value) {
      // separate this out because unset resets the pointer
      $registry['field_to_remove'] = new \ArrayIterator();
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
          $registry['field_to_remove'][] = $remote_field_name;
        }
      }
      else {
        $registry['field_to_remove'][] = $remote_field_name;
      }
    }

    $this->postProcess($row, $registry);
    return $this;
  }

  public function postProcess(DataContainer $values, \Pimple $registry) {
    foreach ($registry['field_to_remove'] as $field_name) {
      unset($values[$field_name]);
    }
    $this->transform($values, $registry);
  }

  /**
   * Change the values to key by the local field name and not the remote
   *
   * @param DataContainer $values
   * @param \Pimple       $registry
   *
   * @return DataContainer
   */
  protected function transform(DataContainer $values, \Pimple $registry) {
    $field_map = $registry['field_map'];
    $return = new DataContainer();
    $return->setAll($values->getOriginals())->lock()->unsetValues();

    foreach($values as $import_field_name => $value) {
      $field_name = $field_map['field_import_map'][$import_field_name];
      $return[$field_name] = $value;
    }
    $values = $return;

    return $this;
  }


  public function preProcess(DataContainer $values, \Pimple $registry) {}

}
