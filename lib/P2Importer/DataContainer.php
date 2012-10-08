<?php

namespace P2Importer;

class DataContainer implements \Iterator, \ArrayAccess {
  // the original values
  protected $original_values = array();
  // the stored values
  protected $values = array();
  // the originals are locked
  protected $locked = FALSE;


  function __construct(array $values = array()) {
    $this->original_values = $values;
    $this->values = $values;
  }

  public function lock() {
    $this->locked = TRUE;
  }

  public function isLocked() {
    return $this->locked;
  }

  public function isUnLocked() {
    return !$this->locked;
  }

  public function getOriginalValue($key) {
    return $this->original_values[$key];
  }

  public function getOriginals() {
    return $this->original_values;
  }

  public function setAll(array $values) {
    $this->values = $values;

    if ($this->isUnLocked()) {
      $this->original_values = $values;
    }

    return $this;
  }

  public function unsetValues() {
    $this->values = array();
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

    if ($this->isUnLocked()) {
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

    if ($this->isUnLocked()) {
      unset($this->original_values[$offset]);
    }
  }

  /**
   * Return the current element
   *
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current() {
    return current($this->values);
  }

  /**
   * Move forward to next element
   *
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next() {
    next($this->values);
  }

  /**
   * Return the key of the current element
   *
   * @link http://php.net/manual/en/iterator.key.php
   * @return mixed scalar on success, or null on failure.
   */
  public function key() {
    return key($this->values);
  }

  /**
   * Checks if current position is valid
   *
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   *       Returns true on success or false on failure.
   */
  public function valid() {
    return key($this->values) !== NULL;
  }

  /**
   * Rewind the Iterator to the first element
   *
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind() {
    reset($this->values);
  }}
