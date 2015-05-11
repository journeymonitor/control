<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150511215312 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL PRIMARY KEY, email VARCHAR(128) NOT NULL UNIQUE, password VARCHAR(40));');
        $this->addSql('CREATE TABLE testcases (id VARCHAR(36) NOT NULL PRIMARY KEY, userId VARCHAR(36) NOT NULL, title VARCHAR(128) NOT NULL, notifyEmail VARCHAR(128) NOT NULL UNIQUE, cadence VARCHAR(3));');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE testcases');
        $this->addSql('DROP TABLE users');
    }
}
