<?php

namespace Jobs;

/**
 * Represents something that can generate job instances
 * and instantiate instances of them from parameters.
 */
abstract class JobType {

  /**
   * Get a list of all job instances that should be run soon.
   * @return a list of job parameters
   */
  function getPending(\Db\Connection $db) {
    return array();
  }

  /**
   * Prepare a {@link JobInstance} that can be executed from
   * the given parameters.
   */
  abstract function createInstance($params);

  /**
   * Do any post-job-queue behaviour e.g. marking the job queue
   * as checked.
   */
  abstract function finishedQueue(\Db\Connection $db, $jobs);

  function getName() {
    return get_class($this);
  }

}
