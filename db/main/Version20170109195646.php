<?php

namespace Api\DB\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170109195646 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'CREATE TABLE stats
            (
              id SERIAL NOT NULL PRIMARY KEY ,
              date TIMESTAMP DEFAULT now(),
              name TEXT,
              value INTEGER);'
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE stats;');
    }
}
