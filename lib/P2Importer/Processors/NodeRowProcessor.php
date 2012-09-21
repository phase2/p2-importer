<?php

namespace P2Importer\Processors;

use P2Importer\AbstractDataStorage;
use P2Importer\ProcessorInterface;
use P2Importer\ParserInterface;

class NodeRowProcessor extends AbstractDataStorage implements ProcessorInterface {
  public function process(ParserInterface $row, \Pimple $registry) {
    $field_map = $registry['field_map'];

    global $user;
    $user = user_load(1); // Let's just avoid any issue that will upset me.

    if ($nid = $this->node_exists($row, $registry)) {
      $node = node_load($nid);
    }
    else {
      $node = $this->stub_node($registry);
    }

    // Add the fields to the node
    foreach ($row as $field_name => $field_value) {
      // Property
      if (is_a($field_map[$field_name], 'P2Importer\\FieldTypes\\Property')) {
        $node->{$field_name} = $field_value;
      }
      else {
        // Get the settings
        if (empty($field_map[$field_name]['field_settings']['multiple'])) {
          $node->{$field_name} = array(
            $node->language => array($field_value),
          );
        }
        else {
          if (empty($node->{$field_name})) {
            $node->{$field_name}= array(
              $node->language => array(),
            );
            $items = array();
          }
          else {
            $items = field_get_items('node', $node, $field_name);
          }

          foreach ($field_value as $value) {
            if (!empty($items)) {
              $exists = FALSE;
              foreach ($items as $item) {
                if (isset($value['nid']) && isset($item['nid'])) {
                  if ((int) $value['nid'] == (int) $item['nid']) {
                    $exists = TRUE;
                  }
                }
                else {
                  if ($value == $value) {
                    $exists = TRUE;
                  }
                }

              }
              if (!$exists) {
                $node->{$field_name}[$node->language][] = $value;
              }
            }
            else {
              $node->{$field_name}[$node->language][] = $value;
            }
          }
        }
      }
    }

    node_save($node);

    return $this;
  }

  public function preProcess(\Pimple $registry) {}

  public function postProcess(\Pimple $registry) {}

  protected function stub_node(\Pimple $registry) {
    global $user;

    $node = new \stdClass();
    $node->type = $registry['ctype'];
    $node->uid = $user->uid;
    $node->language = $registry['language'] ?: LANGUAGE_NONE;
    $node->status = 1;

    return $node;
  }

  protected function node_exists($values, \Pimple $registry) {
    $query = new \EntityFieldQuery();
    $query->entityCondition('entity_type', 'node')
      ->propertyCondition('status', 1)
      ->entityCondition('bundle', $registry['ctype']);
    $query = $this->add_unique($query, $values, $registry);

    $result = $query->execute();

    if (!empty($result['node'])) {
      $nids = array_keys($result['node']);
      return reset($nids);
    }

    return FALSE;
  }

  protected function add_unique(\EntityFieldQuery $query, $values, \Pimple $registry) {
    $field_map = $registry['field_map'];
    if (!empty($field_map['unique_fields'])) {
      foreach ($field_map['unique_fields'] as $field) {
        switch ($field['field_type']) {
          case 'field':
            $value = $values[$field['field_name']];
            $query->fieldCondition($field['field_name'], $field['table_field'],
              $value[$field['table_field']], '=');
            break;
          case 'property':
            $query->propertyCondition($field['field_name'], $values[$field['field_name']]);
            break;
        }
      }
    }

    return $query;
  }
}
