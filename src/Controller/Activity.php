<?php
namespace Controller;

use PDO;

class Activity
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getList($limit, $offset)
    {
        $select = $this->pdo->prepare('
            SELECT
              activities.id,
              activities.action,
              users.id AS user_id,
              users.first AS user_first,
              users.last AS user_last,
              users.image AS user_image,
              users.hometown AS user_hometown,
              travels.id AS travel_id,
              travels.name AS travel_name,
              travels.image AS travel_image
            FROM
              activities
              INNER JOIN users ON activities.user = users.id
              INNER JOIN travels ON activities.travel = travels.id
            ORDER BY activities.id DESC LIMIT :limit');
        
        $select->execute([':offset' => (int) $offset, ':limit' => (int) $limit]);
        
        $result = [];

        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $result[] = [
                'id'     => $row['id'],
                'action' => $row['action'],
                'user'   => [
                    'id'       => $row['user_id'],
                    'first'    => $row['user_first'],
                    'last'     => $row['user_last'],
                    'image'    => $row['user_image'],
                    'hometown' => $row['user_hometown'],
                ],
                'travel' => [
                    'id'    => $row['travel_id'],
                    'name'  => $row['travel_name'],
                    'image' => $row['travel_image'],
                ],
            ];
        }

        return $result;
    }
}