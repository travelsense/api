<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use BadMethodCallException;
use PDO;

class BookingMapper extends AbstractPDOMapper
{
    /**
     * @param int $userId
     * @param int $travelId
     */
    public function registerBooking(int $userId, int $travelId)
    {
        $insert = $this->pdo->prepare("INSERT INTO bookings (user_id, travel_id) VALUES (:user_id, :travel_id) ON CONFLICT DO NOTHING");
        $insert->execute([
            ':user_id' => $userId,
            ':travel_id' => $travelId,
        ]);
    }

    /**
     * @param int $authorId
     * @return int
     */
    public function getBooksTotal(int $authorId): int
    {
        $select = $this->pdo->prepare("SELECT COUNT(*) FROM bookings b LEFT JOIN travels t on t.id = b.travel_id WHERE t.author_id = :author_id");
        $select->execute([':author_id' => $authorId]);
        return $select->fetchColumn();
    }

    /**
     * Get booking stats for the last 7 days as array:
     * [
     *  ['date' => '2016-01-03', 'count'=> 3],
     *  ...
     *  ['date' => '2016-01-09' => 'count' => 2],
     * ]
     *
     * @param int $authorId
     * @return array
     */
    public function getStats(int $authorId): array
    {
        $select = $this->pdo->prepare("
          SELECT 
            d.date AS date, 
            COUNT (b.*) AS count 
          FROM (
            SELECT CURRENT_DATE - offs AS date FROM generate_series(0, 6) AS offs
          ) d
          LEFT JOIN bookings b 
            ON (b.created >= d.date) AND (b.created < d.date + 1)
          LEFT JOIN travels t 
            ON t.id = b.travel_id AND t.author_id = :author_id
          GROUP BY d.date
          ORDER BY d.date ASC
        ");
        $select->execute([':author_id' => $authorId]);
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create object by a DB row
     *
     * @param  array $row
     * @return mixed
     */
    protected function create(array $row)
    {
        throw new BadMethodCallException();
    }
}