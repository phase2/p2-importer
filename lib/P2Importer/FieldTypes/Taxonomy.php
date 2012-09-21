<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;

class Taxonomy extends AbstractFieldType {
  public function process(\Iterator $row) {
    $value = $row[$this->getImportFieldName()];
    // Get the vocab
    if (empty($this->settings['vocab'])) {
      return array();
    }

    // Load the vocab
    if (!$vocab = taxonomy_vocabulary_machine_name_load($this->getFieldSetting('vocab'))) {
      return array();
    }

    $terms = taxonomy_term_load_multiple(array(), array('name' => trim($value), 'vid' => $vocab->vid));
    if (empty($terms)) {
      // Let's create a new term
      $term = new \StdClass();
      $term->vid = $vocab->vid;
      $term->name = $value;
      taxonomy_term_save($term);
    }
    else {
      $term = reset($terms);
    }

    if (isset($term->tid)) {
      return array('tid' => $term->tid);
    }
    else {
      return array();
    }
  }
}
