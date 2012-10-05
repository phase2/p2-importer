<?php

namespace P2Importer;

interface ProcessorInterface {
  /**
   * Process the data from the parser
   *
   * @param DataContainer $data
   * @param \Pimple         $registry
   *
   * @return ProcessorInterface
   */
  public function process(DataContainer $data, \Pimple $registry);
}
