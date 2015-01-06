<?php

namespace Jobs;

use \Monolog\Logger;

/**
 * Represents an instance of a job that can be executed
 * (the actual job logic is wrapped in {@link #run()}).
 */
abstract class JobInstance {

  var $params;

  function __construct($params) {
    $this->params = $params;
  }

  function execute(\Db\Connection $db, Logger $logger) {
    $this->run($db, $logger);
  }

  /**
   * Actual job logic.
   */
  abstract function run(\Db\Connection $db, Logger $logger);

  function getArgument() {
    return $this->params['arg'];
  }

  function getParameters() {
    return $this->params;
  }

}
