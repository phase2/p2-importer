<?php

namespace P2Importer;

interface ParserInterface {
  public function parse(\Iterator $result, \Pimple $registry);

  public function preProcess(\Iterator $row, \Pimple $registry);

  public function postProcess(\Iterator $row, \Pimple $registry);
}
