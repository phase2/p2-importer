<?php

namespace P2Importer;

require_once "../Pimple.php";

class FieldMap extends \Pimple {

  public function __construct() {
    $this['field_import_map'] = array();
    $this['unique_fields'] = array();
    $this['field_settings'] = array();
  }

  /**
   * Set the field info
   */
  public function addField(\Closure $c) {
    $field_type = $c();
    $this[$field_type->getFieldName()] = $field_type;
    $this['field_settings'] = $field_type->getFieldSettings();
    $this['field_import_map'][$field_type->getImportFieldName()] = $field_type->getFieldName();
  }
}
