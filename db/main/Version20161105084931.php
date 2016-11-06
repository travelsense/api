<?php

namespace Api\DB\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add index, end_index to actions
 */
class Version20161105084931 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE actions ADD COLUMN index INTEGER DEFAULT -1');
        $this->addSql('ALTER TABLE actions ADD COLUMN end_index INTEGER DEFAULT -1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE actions DROP COLUMN index');
        $this->addSql('ALTER TABLE actions DROP COLUMN end_index');
    }
}
