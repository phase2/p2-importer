<?php

namespace P2Importer;

use P2Importer\FetcherInterface;

abstract class AbstractFetcher implements FetcherInterface {
  protected $settings = array();

  public function __construct(array $settings) {
    $this->settings = $settings;
  }
}
