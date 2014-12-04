<?php

namespace Jobs;

class JobsRunner {

  function __construct(JobTypeMapper $mapper) {
    $this->mapper = $mapper;
  }

  /**
   * Try run one job.
   * TODO timeout logic
   * TODO failed job logic
   */
  function runOne(\Db\Connection $db, \Db\Logger $logger) {
    $job = $this->findJob($db);
    if ($job) {
      $job_type = $this->mapper->findJobType($job['job_type']);
      if (!$job_type) {
        throw new JobException("Could not find job type mapping for " . $job['job_type']);
      }

      $instance = $job_type->createInstance($job);

      // mark it as executing
      $q = $db->prepare("UPDATE jobs SET is_executing=1, execution_count=execution_count + 1 WHERE id=? LIMIT 1");
      $q->execute(array($job['id']));

      try {
        $instance->execute($db, $logger);

        // it passed
        $q = $db->prepare("UPDATE jobs SET is_executing=0, is_executed=1, executed_at=NOW() WHERE id=? LIMIT 1");
        $q->execute(array($job['id']));

        $logger->log("Complete");
      } catch (Exception $e) {
        $logger->error($e->getMessage());

        $q = $db->prepare("INSERT INTO job_exceptions SET job_id=:job_id,
          class_name=:class_name,
          message=:message,
          filename=:filename,
          line_number=:line_number");
        $q->execute(array(
          "job_id" => $job['id'],
          "class_name" => get_class($e),
          "message" => $e->getMessage(),
          "filename" => $e->getFile(),
          "line_number" => $e->getLine(),
        ));
        $this->insertException($db, $job, $e);

        // it failed
        $q = $db->prepare("UPDATE jobs SET is_executing=0, is_error=1, executed_at=NOW() WHERE id=? LIMIT 1");
        $q->execute(array($job['id']));
      }
    } else {
      $logger->log("No jobs found");
    }
  }

  function findJob(\Db\Connection $db) {
    $q = $db->prepare("SELECT * FROM jobs WHERE is_executed=0 LIMIT 1");
    $q->execute();
    return $q->fetch();
  }

}
