Creates a lite weight importer for Drupal.  There is no front end.

Fetcher:  Get data from a source and return an Iterator

* CSV:  A csv file
  * file:  The file location.  can be any uri where you have a compatible schema
* DB:  A database table
  * db:  The db connection from the $databases array in settings.php
  * table:  the name of the table
  * count:  the number of rows
* JSON:  A json file
  * url:  The url of the json

Importer:  Controls the import process

Field Types:  Process each field from the source to the destination

Parser:  Pares the Iterator from the Fetcher using the Field Types

Processor:  Takes the output from the Parser and Persists the information

Data Container:  The P2Importer\DataContainer


            ----------------               -------------
           |                |             |             |
           |     Fetcher    | <---------- | Data Source |
           |                |             |             |
            ----------------               -------------
                   |
                   |
                   v
             ---------------               -------------
            |               |             |             |
            |    Parser     | <---------> | Field Types |
            |               |             |             |
             ---------------               -------------
                   |
                   |
                   v
             ---------------
            |               |
            |   Processor   |
            |               |
             ---------------
                   |
                   |
                   v
             ---------------
            |               |
            |    Drupal     |
            |               |
             ---------------


## Code Registry

Pimple is used as a DI container.  See http://pimple.sensiolabs.org/

 * parser: The Parser Class
 * row-parser: The Parser for Each Row
 * processor: The Processor
 * row-processor: The Processor for Each Row
 * fetcher: The Fetch Class
 * ctype:  string value for the content type so save a node as if using the NodeRowProcessor
 * Language:  The language to use in the node
 * data_container: The DataContainer Object.  Should use or extend P2Importer\DataContainer

### Field Map


The field map is an extension of Pimple P2Importer\FieldMap()

* the addField method takes a closure that returns a FieldType object
* the addUnique method takes a closure that returns an instance of P2Importer\UniqueField
* the "as_is" setting just copies the imported value to the local and avoids any field logic

### Row Processor - This is what persists the data to drupal

* P2Importer\Processors\NodeRowProcessor: Updates or creates a node.  Uses the unique fields in the field map
* P2Importer\Processors\DirectFieldRowProcessor: Does direct table updates on fields only.
  * This required State Machine Module to determine which revisions to update.
  * Does not work with file field
  * Only works with SQL field storage.
  * Only works on existing entities and not new ones
  
## Example

```
function run_import($id) {
  require_once libraries_get_path('p2_importer') . '/lib/Pimple.php';

  $di = new Pimple();

  $di['ctype'] = 'sometype';
  $di['language'] = LANGUAGE_NONE;

  // using "share" makes it resues the same instance.  
  $di['field_map'] = $di->share(function($c) {
    return create_get_field_map();
  });

  $di['fetcher'] = function($c) use ($program_id) {
    $url = get_url($id);
    return new \P2Importer\Fetchers\Json(array('url' => $url));
  };

  $di['parser'] = function($c) {
    return new \P2Importer\Parser\SingleParser();
  };

  $di['row_parser'] = function($c) {
    return new \P2Importer\RowParser();
  };

  $di['processor'] = function($c) {
    return new \P2Importer\Processors\SingleProcessor();
  };

  $di['row_processor'] = function($c) {
    return new \P2Importer\Processors\DirectFieldRowProcessor();
  };

  $di['data_container'] = function($C) {
    return new \P2Importer\DataContainer();
  };

  $importer = new \P2Importer\Importer($di);
  $importer->process();
}
```

Get the Field Map

```
function create_get_field_map() {
  $field_map = new FieldMap();

  $map = array(
    'local_field_1' => 'source_field_1',
    'local_field_2' => 'source_field_2',
    'local_field_3' => 'source_field_3',
    'local_field_4' => 'source_field_4',
  );

  // We use property because we just want to exact map the dest to the source
  foreach ($map as $local => $source) {
    $field_map->addField(function() use ($local, $source) {
        $settings = array('as_is' => TRUE);
        return new Property(
          $local,
          $source,
          $settings
        );
      });
  }

  foreach ($tax_fields as $local => $source) {
    $field_map->addField(function() use ($local, $source) {
        return new CustomFieldType(
          $local,
          $source
        );
      });
  }

  // Add unique fields to load the entity by
  $field_map->addUniqueField(function() {
      return new \P2Importer\UniqueField(
        'local_field',
        'remote_field',
        'field',
        'value'
      );
    });

  return $field_map;
}
```