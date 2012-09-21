<?php

namespace P2Importer;

interface ParserInterface {
  public function parse(\Iterator $result, \Pimple $registry);

  public function preProcess(\Pimple $registry);

  public function postProcess(\Pimple $registry);
}
