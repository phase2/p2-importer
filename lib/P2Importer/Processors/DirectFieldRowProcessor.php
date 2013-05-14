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

      // Get the current revision id for the node.
      $current_vid = db_select('node', 'n')
        ->fields('n', array('vid'))
        ->condition('nid', $nid)
        ->execute()
        ->fetchField();

      // Manually update fields across revisions.
      foreach($vids as $vid) {

        // Log message to terminal.
        if (function_exists('drush_log')) {
          drush_log(dt('Syncing fields on node !nid, revision !vid (Current revision is !currentvid)',
            array('!nid' => $nid, '!vid' => $vid, '!currentvid' => $current_vid)), 'ok');
        }

        // Load specific node revision to check existence of field values.
        // This may not be necessary anymore since we are now using db_merge,
        // but there may be additional future benefits, so leaving in place.
        $node = node_load($nid, $vid);

        // Because we are only updating taxonomy and text field
        // This will only work with a DB backed field storage
        // This would not work with field field
        foreach ($data as $field_name => $field_value) {
          $field_info = field_info_field($field_name);
          // @TODO: This is only affecting the first item.  Need to update this
          //   to work with fields with multiple values.
          $value = $field_value[LANGUAGE_NONE][0];
          if (!$value) {
            error_log($field_name);
            watchdog('psu_ps_client', "fieldname is @name", array('@name' => $field_name));
          }

          try {
            $revision_table = _field_sql_storage_revision_tablename($field_info);
            $table = _field_sql_storage_tablename($field_info);
            $fields = array();
            foreach($value as $key => $real_value) {
              if (!in_array($key, array('safe_value', 'safe_summary'))) {
                $fields[_field_sql_storage_columnname($field_name, $key)] =
                  $real_value;
              }
            }

            // Set the field values.
            $fields['entity_type'] = 'node';
            $fields['bundle'] = $node->type;
            $fields['language'] = $node->language;
            $fields['deleted'] = 0;
            $fields['delta'] = 0;
            $fields['entity_id'] = $nid;

            $fields['revision_id'] = $vid;

            // Write the revisions table for all items.
            db_merge($revision_table)
              ->key(array(
                  'entity_type' => $fields['entity_type'],
                  'entity_id' => $fields['entity_id'],
                  'revision_id' => $fields['revision_id'],
                  'deleted' => $fields['deleted'],
                  'delta' => $fields['delta'],
                  'language' => $fields['language'],
              ))
              ->fields($fields)
              ->execute();


            // Write to the data table only if this is for the current version.
            if ($vid == $current_vid) {
              db_merge($table)
                ->key(array(
                    'entity_type' => $fields['entity_type'],
                    'entity_id' => $fields['entity_id'],
                    'deleted' => $fields['deleted'],
                    'delta' => $fields['delta'],
                    'language' => $fields['language'],
                ))
                ->fields($fields)
                ->execute();
            }
          }
          catch (\Exception $e) {
            watchdog_exception('import', $e);
            throw $e;
          }
        }
      }
    }
    else {
      if (function_exists('drush_log')) {
        drush_log(dt('P2-Importer: node_exists() did not return an nid. Reference nid is !data.',
          array('!data' => $data->getOriginalValue('nid'))), 'warning');
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
