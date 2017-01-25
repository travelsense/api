<?php

namespace Api\DB\Migration;

use Api\Model\HasUuidTrait;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add column uuid
 */
class Version20170125074906 extends AbstractMigration
{
    private $tables = [
        'actions',
        'banners',
        'categories',
        'expirable_storage',
        'hotels',
        'sessions',
        'stats',
        'travel_comments',
        'travels',
        'users'
    ];

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        foreach ($this->tables as $table) {
            $this->addSql("ALTER TABLE $table ADD COLUMN uuid UUID NOT NULL DEFAULT uuid_generate_v4()");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        foreach ($this->tables as $table) {
            $this->addSql("ALTER TABLE $table DROP COLUMN uuid");
        }
    }
}
