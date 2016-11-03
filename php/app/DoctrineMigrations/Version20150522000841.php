<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150522000841 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE testresult (id VARCHAR(255) NOT NULL, testcase_id VARCHAR(255) NOT NULL, datetime_run DATETIME NOT NULL, exit_code INTEGER NOT NULL, output CLOB NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_testcase_id_datetime_run ON testresult (testcase_id, datetime_run)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE testresult');
    }
}
