<?php

namespace P2Importer;

/**
 * The fetcher for the import script
 *
 * In charge of access the data, row by row
 */
interface FetcherInterface {
  /**
   * Set settings
   *
   * @param $settings array
   */
  public function __construct(array $settings);

  /**
   * Load stuff from a source
   *
   * @return \Iterator
   */
  public function load();
}
