<?php

namespace P2Importer;

use P2Importer\ParserInterface;

interface ProcessorInterface {
  /**
   * Process the data from the parser
   *
   * @param ParserInterface $data
   * @param \Pimple         $registry
   *
   * @return ProcessorInterface
   */
  public function process(ParserInterface $data, \Pimple $registry);

  public function preProcess(\Pimple $registry);

  public function postProcess(\Pimple $registry);
}
