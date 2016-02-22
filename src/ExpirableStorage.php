<?php
namespace Api;

use DateTime;
use PDO;

class ExpirableStorage
{
    const DELETE = true;
    const KEEP = false;

    const SHA1_LENGTH = 40;

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * AbstractMapper constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Store an object, get a key
     *
     * @param  mixed         $object   Object
     * @param  DateTime|null $expireOn
     * @return string
     */
    public function store($object, DateTime $expireOn = null)
    {
        $serialized = serialize($object);
        $token = sha1(mt_rand() . $serialized);
        $sql = <<<SQL
INSERT INTO expirable_storage
(serialized_object, token, expires)
VALUES (:obj, :token, :expires)
RETURNING id
SQL;
        $insert = $this->pdo->prepare($sql);
        $insert->execute(
            [
            ':obj' => $serialized,
            ':token' => $token,
            ':expires' => $expireOn
            ]
        );
        return $token . $insert->fetchColumn();
    }

    /**
     * Get the stored value by key
     *
     * @param  $key
     * @param  bool $delete Delete immediately
     * @return mixed|null
     */
    public function get($key, $delete = self::DELETE)
    {
        // Not UTF safe!
        if (strlen($key) <= self::SHA1_LENGTH) {
            return null;
        }
        list($token, $id) = str_split($key, self::SHA1_LENGTH);
        $sql = <<<SQL
SELECT serialized_object from expirable_storage
WHERE id = :id AND token = :token AND (expires >= now() OR expires IS NULL)
SQL;

        $select = $this->pdo->prepare($sql);
        $select->execute(
            [
            ':id' => $id,
            ':token' => $token,
            ]
        );

        $serialized = $select->fetchColumn();

        if ($delete) {
            $this->pdo
                ->prepare('DELETE FROM expirable_storage WHERE id = :id AND token = :token')
                ->execute(
                    [
                    ':id' => $id,
                    ':token' => $token,
                    ]
                );
        }

        return @unserialize($serialized) ?: null;
    }

    /**
     * Cleanup the database
     */
    public function cleanup()
    {
        $this->pdo
            ->prepare('DELETE FROM expirable_storage WHERE expires < now()')
            ->execute();
    }
}
