<?php

namespace P2Importer;

interface FieldTypeInterface extends ProcessorInterface {
  public function __construct($field_name, $import_field_name, $field_settings = array());
  public function getFieldName();
  public function getFieldSettings();
  public function getFieldSetting($name);
  public function getImportFieldName();
}
