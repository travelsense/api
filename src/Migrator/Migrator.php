<?php
namespace Api\Migrator;

use Exception;
use PDO;

class Migrator
{
    const DIR_UP = 'up';
    const DIR_DN = 'dn';

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * DB name
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $dir;

    private $upgrades = [];
    private $downgrades = [];

    private $table = '__history';

    /**
     * Migration constructor.
     * @param PDO    $pdo
     * @param string $name
     * @param        $dir
     */
    public function __construct(PDO $pdo, $name, $dir)
    {
        $this->pdo = $pdo;
        $this->name = $name;
        $this->dir = $dir;
    }

    /**
     * Initialize
     */
    public function init()
    {
        $this->createHistoryTable();
        $this->loadMigrations();
    }


    /**
     * Get current version. Version 0 means the db is not yet initialized
     * @return int
     */
    public function getVersion()
    {
        $select = $this->pdo->prepare("SELECT version FROM {$this->table} ORDER BY  id DESC LIMIT 1");
        $select->execute();
        return (int)$select->fetchColumn();
    }


    /**
     * Upgrade up to version
     * @param int $target Version number or maximum possible
     * @throws Exception
     */
    public function upgrade($target = null)
    {
        $this->pdo->beginTransaction();
        $current = $this->getVersion();
        try {
            foreach ($this->upgrades as $version => $file) {
                if ($current >= $version) {
                    continue;
                }
                $this->pdo->exec($this->getContents($file));
                $this->setVersion($version);
                if ($target !== null && $version >= $target) {
                    break;
                }
            }
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function getAvailableUpgrades()
    {
        $current = $this->getVersion();
        $upgrades = [];
        foreach ($this->upgrades as $version => $file) {
            if ($current >= $version) {
                continue;
            }
            $upgrades[$version] = $file;
        }
        return $upgrades;
    }


    /**
     * @param $version
     */
    private function setVersion($version)
    {
        $insert = $this->pdo->prepare("INSERT INTO {$this->table} (version) VALUES (:ver)");
        $insert->execute([
            ':ver' => $version,
        ]);
    }

    /**
     * @param string $file
     * @return string
     */
    private function getContents($file)
    {
        return file_get_contents(sprintf("%s/%s", $this->dir, $file));
    }

    private function loadMigrations()
    {
        $this->upgrades = [];
        $this->downgrades = [];
        $pattern = "/^{$this->name}\\.(\\d+)\\.(up|dn)\\..*\$/";
        foreach (new \DirectoryIterator($this->dir) as $file) {
            if (preg_match($pattern, $file->getFilename(), $matches)) {
                $ver = (int)$matches[1];
                $direction = $matches[2];
                if ($direction === self::DIR_UP) {
                    $this->upgrades[$ver] = $matches[0];
                } else {
                    $this->downgrades[$ver] = $matches[0];
                }
            }
        }
        ksort($this->upgrades);
        krsort($this->downgrades);
    }

    /**
     * Initialize
     */
    private function createHistoryTable()
    {
        $create =
            "CREATE TABLE IF NOT EXISTS {$this->table} ("
            . "id SERIAL NOT NULL PRIMARY KEY, "
            . "version INTEGER NOT NULL, "
            . "ts TIMESTAMP NOT NULL DEFAULT now()"
            . ")";

        $this->pdo->exec($create);
    }
}
