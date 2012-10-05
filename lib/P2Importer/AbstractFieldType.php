<?php

namespace P2Importer;

use P2Importer\FieldTypeInterface;

abstract class AbstractFieldType implements FieldTypeInterface {
  protected $settings = array();
  protected $field_name;
  protected $import_field_name;

  /**
   * @param $field_name string
   *  Name of the field in the destination
   * @param $import_field_name string
   *  Name of the field in the source
   * @param $field_settings array
   *  Setting for the field type
   */
  public function __construct($field_name, $import_field_name, $field_settings = array()) {
    $this->field_name = $field_name;
    $this->import_field_name = $import_field_name;
    $this->settings = $field_settings;
  }

  public function getFieldName() {
    return $this->field_name;
  }

  public function getFieldSettings() {
    return $this->settings;
  }

  public function getImportFieldName() {
    return $this->import_field_name;
  }

  public function getFieldSetting($name) {
    return $this->settings[$name];
  }

  /**
   * Get the value for this field from a row
   */
  protected function getValue(DataContainer $row) {
    return $row[$this->getImportFieldName()];
  }
}
