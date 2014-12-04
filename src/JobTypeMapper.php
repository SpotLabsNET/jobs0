<?php

namespace Jobs;

interface JobTypeMapper {

  /**
   * Get the {@link JobType} for the given {@code $job_type} type string.
   */
  function findJobType($job_type);

}
