<?php

namespace P2Importer;

class FieldMap extends \Pimple {

  public function __construct() {
    $this['field_import_map'] = new \ArrayIterator();
    $this['unique_fields'] = new \ArrayIterator();
  }

  /**
   * Set the field info
   */
  public function addField(\Closure $c) {
    $field_type = $c($this);
    $this[$field_type->getFieldName()] = $field_type;
    $this['field_import_map'][$field_type->getImportFieldName()] = $field_type->getFieldName();
  }

  /**
   * Add a unique field
   */
  public function addUniqueField(\Closure $c) {
    $unique_field = $c($this);
    $this['unique_fields'][$unique_field->field_name] = $unique_field;
  }
}
