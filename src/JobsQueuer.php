<?php

namespace Jobs;

class JobsQueuer {

  var $job_types;

  function __construct($job_types) {
    $this->job_types = $job_types;
  }

  /**
   * Go through all possible jobs and queue up all necessary jobs.
   *
   * @throws JobException if something bad happened
   */
  function doQueue(\Db\Connection $db, \Db\Logger $logger) {
    $logger->log("Querying " . count($this->job_types) . " job types for pending jobs");

    foreach ($this->job_types as $job_type) {
      $pending = $job_type->getPending($db);

      if ($pending) {
        $logger->log("Found " . count($pending) . " pending jobs for " . $job_type->getName());
        foreach ($pending as $job) {
          $this->insertJob($db, $job);
        }
      }

      // notify the job queuer that we've successfully inserted all of these queue jobs
      $job_type->finishedQueue($db, $pending);
    }

    $logger->log("Complete");
  }

  /**
   * @throws JobException if something bad happened
   */
  function insertJob(\Db\Connection $db, $job) {
    if (!isset($job['job_type'])) {
      throw new JobException("No job_type defined in " . print_r($job, true));
    }

    $q = $db->prepare("INSERT INTO jobs SET job_type=:job_type,
        arg=:arg");
    return $q->execute(array(
      "job_type" => $job['job_type'],
      "arg" => $job['arg'],
    ));
  }

}
