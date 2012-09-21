<?php

namespace P2Importer\FieldTypes;

use P2Importer\AbstractFieldType;

class NodeReference extends AbstractFieldType {
  public function process(\Iterator $row) {
    global $user;

    // Get the cypte
    if (empty($this->settings['ctype']) || empty($this->settings['unique_fields'])) {
      return array();
    }

    if ($nid = $this->node_exists($row)) {
      return array('nid' => $nid);
    }
    else {
      return array();
    }
  }

  protected function add_unique(\EntityFieldQuery $query, $values) {
    if (!empty($this->settings['unique_fields'])) {
      foreach ($this->settings['unique_fields'] as $field) {
        switch ($field['field_type']) {
          case 'field':
            $query->fieldCondition($field['field_name'], $field['table_field'],
              $values[$field['import_name']], '=');
            break;
          case 'property':
            $query->propertyCondition($field['field_name'], $values[$field['import_name']]);
            break;
        }
      }
    }

    return $query;
  }

  protected function node_exists($values) {
    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
      ->propertyCondition('status', 1)
      ->entityCondition('bundle', $this->settings['ctype']);
    $query = $this->add_unique($query, $values);

    $result = $query->execute();

    if (!empty($result['node'])) {
      $nids = array_keys($result['node']);
      return reset($nids);
    }

    return FALSE;
  }

}
