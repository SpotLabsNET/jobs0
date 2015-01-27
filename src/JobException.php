<?php

namespace Jobs;

/**
 * Represents an exception that occured while executing a {@link JobInstance}.
 * This can be wrapping a child exception.
 */
class JobException extends \Exception {

  public function __construct($message, \Exception $previous = null) {
    parent::__construct($message, 0, $previous);
  }

}
