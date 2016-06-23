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
     *  '2016-01-03' => 3,
     *  '2016-01-04' => 7,
     *  ...
     *  '2016-01-09' => 2,
     * ]
     *
     * @param int $authorId
     * @return array
     */
    public function getStats(int $authorId): array
    {
        $select = $this->pdo->prepare("
          SELECT d.date AS date, COUNT (b.*) AS count FROM (
            SELECT to_char(date_trunc('day', (CURRENT_DATE - offs)), 'YYYY-MM-DD')
            AS date
            FROM generate_series(0, 6, 1)
            AS offs
          ) d
          LEFT OUTER JOIN bookings b 
          ON (d.date = to_char(date_trunc('day', b.created), 'YYYY-MM-DD'))
          LEFT JOIN travels t on t.id = b.travel_id AND t.author_id = :author_id
          GROUP BY d.date
          ORDER BY d.date ASC
        ");
        $select->execute([':author_id' => $authorId]);
        $stats = [];
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['date']] = $row['count'];
        }
        return $stats;
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