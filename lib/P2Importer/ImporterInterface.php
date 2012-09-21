<?php

namespace P2Importer;

interface ImporterInterface {
  /**
   * Initial with the field_type_map and field_map
   */
  public function __construct(\Pimple $registry);

  public function process();
}
