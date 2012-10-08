<?php

namespace P2Importer\Processors;

use P2Importer\ProcessorInterface;
use P2Importer\DataContainer;
use P2Importer\Processors\NodeRowProcessor;

class DirectFieldRowProcessor extends NodeRowProcessor {

  /**
   * Process the data from the parser
   *
   * @param DataContainer $data
   * @param \Pimple         $registry
   *
   * @return ProcessorInterface
   */
  public function process(DataContainer $data, \Pimple $registry) {
    // Get the nid of the node
    if ($nid = $this->node_exists($data, $registry)) {
      // Get the vids that need to be updated
      $vids = db_select('node_revision_states', 'nrs')
        ->fields('nrs', array('vid'))
        ->condition('nid', $nid)
        ->condition('status', 1)
        ->execute()
        ->fetchCol();

      $vids ?: array();

      // Update fields
      foreach($vids as $vid) {
        // because we are only updating taxonomy and text field
        // This will only work with a DB backed field storage
        // This would not work with field field
        foreach ($data as $field_name => $field_value) {
          $field_info = field_info_field($field_name);
          $value = $field_value[LANGUAGE_NONE][0];

          $transaction = db_transaction();

          try {
            $revision_table = _field_sql_storage_revision_tablename($field_info);
            $table = _field_sql_storage_tablename($field_info);
            $condition_fields = array();
            foreach($value as $key => $real_value) {
              $condition_fields[_field_sql_storage_columnname($field_name, $key)] =
                $real_value;
            }

            $condition_fields['revision_id'] = $vid;
            drupal_write_record($revision_table, $condition_fields, array('revision_id'));
            drupal_write_record($table, $condition_fields, array('revision_id'));
          }
          catch (\Exception $e) {
            $transaction->rollback();
            watchdog_exception('import', $e);
            throw $e;
          }
        }
      }
    }
  }

  protected function add_unique(\EntityFieldQuery $query, $values, \Pimple $registry) {
    $field_map = $registry['field_map'];
    if (!empty($field_map['unique_fields'])) {
      foreach ($field_map['unique_fields'] as $unique_field) {
        switch ($unique_field->field_type) {
          case 'field':
            $value = $values->getOriginalValue($unique_field->import_field_name);
            $value = $value[LANGUAGE_NONE][0];
            $query->fieldCondition($unique_field->field_name, $unique_field->table_field,
              $value[$unique_field->table_field], '=');
            break;
          case 'property':
            $query->propertyCondition($unique_field->field_name, $values[$unique_field->field_name]);
            break;
        }
      }
    }

    return $query;
  }
}
