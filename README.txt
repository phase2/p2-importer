Creates a lite weight importer for Drupal.  There is no front end.

Fetcher:  Get data from a source and return an Iterator
- CSV:  A csv file
  - file:  The file location.  can be any uri where you have a compatible schema
- DB:  A database table
  - db:  The db connection from the $databases array in settings.php
  - table:  the name of the table
  - count:  the number of rows
- JSON:  A json file
  - url:  The url of the json

Importer:  Controls the import process

Field Types:  Process each field from the source to the destination

Parser:  Pares the Iterator from the Fetcher using the Field Types

Processor:  Takes the output from the Parser and Persists the information


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


Code Registry
--------------

Pimple is used as a DI container.  See http://pimple.sensiolabs.org/

 - parser: The Parser Class
 - row-parser: The Parser for Each Row
 - processor: The Processor
 - row-processor: The Processor for Each Row
 - fetcher: The Fetch Class
 - ctype:  string value for the content type so save a node as if using the NodeRowProcessor
 - Language:  The language to use in the node
