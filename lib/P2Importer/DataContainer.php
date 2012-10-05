<?php

namespace P2Importer;

class DataContainer implements \IteratorAggregate, \ArrayAccess {
  // the original values
  protected $original_values;
  // the stored values
  protected $values;
  // do not allow originals to be changed
  protected $lock_originals = FALSE;


  function __construct(array $values = array()) {
    $this->original_values = new \RecursiveArrayIterator($values);
    $this->values = new \RecursiveArrayIterator($values);
  }

  public function lock() {
    $this->lock_originals = TRUE;
    return $this;
  }

  public function getOriginalValue($key) {
    return $this->original_values[$key];
  }

  public function getOriginals() {
    return $this->original_values;
  }

  public function setAll($values) {
    $this->values = $values;

    if (!$this->lock_originals) {
      $this->original_values = $values;
    }
    return $this;
  }

  public function unsetValues() {
    $this->values = new \RecursiveArrayIterator();
    return $this;
  }

  /**
   * Whether a offset exists
   *
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset
   *                      An offset to check for.
   * @return boolean true on success or false on failure.
   *       The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset) {
    return isset($this->values[$offset]);
  }

  /**
   * Offset to retrieve
   *
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset
   *                      The offset to retrieve.
   *
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset) {
    return $this->values[$offset];
  }

  /**
   * Offset to set
   *
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset
   *                      The offset to assign the value to.
   * @param mixed $value
   *                      The value to set.
   *
   * @return void
   */
  public function offsetSet($offset, $value) {
    $this->values[$offset] = $value;

    if (!$this->lock_originals) {
      $this->original_values[$offset] = $value;
    }
  }

  /**
   * Offset to unset
   *
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset
   *                      The offset to unset.
   *
   * @return void
   */
  public function offsetUnset($offset) {
    unset($this->values[$offset]);
  }

  /**
   * Retrieve an external iterator
   *
   * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
   * @return \Traversable An instance of an object implementing Iterator or Traversable
   */
  public function getIterator() {
    return $this->values;
  }

}
