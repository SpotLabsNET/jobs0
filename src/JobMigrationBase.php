<?php

namespace Jobs;

class JobMigrationBase extends \Db\Migration {

  /**
   * Apply only the current migration.
   * @return true on success or false on failure
   */
  function apply(\Db\Connection $db) {
    $q = $db->prepare("CREATE TABLE jobs (
      id int not null auto_increment primary key,
      created_at timestamp not null default current_timestamp,
      job_type varchar(32) not null,
      arg int null,

      is_executed tinyint not null default 0,
      is_error tinyint not null default 0,
      is_executing tinyint not null default 0,

      execution_count tinyint not null default 0,
      executed_at timestamp null,

      INDEX(job_type),
      INDEX(is_executed),
      INDEX(is_error),
      INDEX(is_executing),
      INDEX(execution_count)
    );");
    return $q->execute();
  }

}
