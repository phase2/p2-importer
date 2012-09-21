<?php

namespace P2Importer;

interface FieldTypeInterface {
  public function __construct($field_name, $import_field_name, $field_settings);
  public function process(\Iterator $row);
  public function getFieldName();
  public function getFieldSettings();
  public function getFieldSetting($name);
  public function getImportFieldName();
}
