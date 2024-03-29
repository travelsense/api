<?php
namespace Api;

use DateTime;
use Doctrine\DBAL\Connection;

class ExpirableStorage
{
    const DELETE = true;
    const KEEP = false;

    const SHA1_LENGTH = 40;

    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Store an object, get a key
     *
     * @param  mixed         $object Object
     * @param  DateTime|null $expires
     * @return string
     */
    public function store($object, DateTime $expires = null): string
    {
        $serialized = serialize($object);
        $token = sha1(mt_rand() . $serialized);
        $insert = $this->connection
            ->prepare("
              INSERT INTO expirable_storage
                (serialized_object, token, expires)
              VALUES (:obj, :token, :expires)
              RETURNING id
           ");
        $insert->execute(
            [
                ':obj'     => $serialized,
                ':token'   => $token,
                ':expires' => $expires,
            ]
        );
        return $token . $insert->fetchColumn();
    }

    /**
     * Get the stored value by key
     *
     * @param       $key
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

        $select = $this->connection->prepare(
            'SELECT serialized_object FROM expirable_storage 
            WHERE 
              id = :id 
              AND token = :token 
              AND (expires >= now() OR expires IS NULL)'
        );
        $select->execute(
            [
                ':id'    => $id,
                ':token' => $token,
            ]
        );

        $serialized = $select->fetchColumn();

        if ($delete) {
            $this->connection
                ->prepare('DELETE FROM expirable_storage WHERE id = :id AND token = :token')
                ->execute(
                    [
                        ':id'    => $id,
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
        $this->connection
            ->prepare('DELETE FROM expirable_storage WHERE expires < now()')
            ->execute();
    }
}
