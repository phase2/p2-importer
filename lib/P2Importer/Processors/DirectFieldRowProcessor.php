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
        ->fields('nra', array('vid'))
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

            // First update the revisions
            db_update($revision_table)
              ->condition('nid', $nid)
              ->condition('vid', $vid)
              ->fields($condition_fields)
              ->execute();

            // Update the main table
            db_update($table)
              ->condition('nid', $nid)
              ->condition('vid', $vid)
              ->fields($condition_fields)
              ->execute();

            db_ignore_slave();
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
}
