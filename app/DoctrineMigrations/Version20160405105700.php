<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160405105700 extends AbstractMigration
{
    // With transaction, we would get "General error: 1 cannot change into wal mode from within a transaction"
    public function isTransactional()
    {
        return false;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('PRAGMA journal_mode=WAL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('PRAGMA journal_mode=DELETE');
    }
}
