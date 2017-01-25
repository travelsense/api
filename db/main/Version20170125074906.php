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
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $tables = [
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

        foreach ($tables as $table) {
            $uuid = HasUuidTrait::generateUuid();
            $this->addSql("ALTER TABLE $table ADD COLUMN uuid TEXT NOT NULL DEFAULT '$uuid'");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $tables = [
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

        foreach ($tables as $table) {
            $this->addSql("ALTER TABLE $table DROP COLUMN uuid");
        }
    }
}
