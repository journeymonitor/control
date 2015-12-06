<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151206212051 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE statistic (testresult_id VARCHAR(255) NOT NULL, runtimeMilliseconds INTEGER NOT NULL, numberOf200 INTEGER NOT NULL, numberOf400 INTEGER NOT NULL, numberOf500 INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(testresult_id))');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE statistic');
    }
}
