<?php

namespace P2Importer;

interface ParserInterface {
  public function parse(DataContainer $result, \Pimple $registry);

  public function preProcess(DataContainer $result, \Pimple $registry);

  public function postProcess(DataContainer $result, \Pimple $registry);
}
