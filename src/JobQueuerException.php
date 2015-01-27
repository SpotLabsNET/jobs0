<?php

namespace Jobs;

/**
 * Represents an exception that occured while trying to queue jobs.
 */
class JobQueuerException extends \Exception {

  public function __construct($message, \Exception $previous = null) {
    parent::__construct($message, 0, $previous);
  }

}
