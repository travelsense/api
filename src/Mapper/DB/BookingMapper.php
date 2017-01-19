<?php
namespace Api\Mapper\DB;

use Doctrine\DBAL\Driver\Connection;

class BookingMapper
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $user_id
     * @param int $travel_id
     * @param float $reward
     */
    public function registerBooking(int $user_id, int $travel_id, float $reward = 0)
    {
        $insert = $this->connection->prepare(
            'INSERT INTO bookings (user_id, travel_id, reward)
            VALUES (:user_id, :travel_id, :reward) ON CONFLICT DO NOTHING'
        );
        $insert->execute([
            ':user_id' => $user_id,
            ':travel_id' => $travel_id,
            ':reward' => $reward,
        ]);
    }

    /**
     * @param int $author_id
     * @return array
     */
    public function getBookingsTotal(int $author_id): array
    {
        $select = $this->connection->prepare(
            'SELECT
            COUNT(*) AS bookings_total,
            SUM(reward) AS reward_total
            FROM bookings b
            JOIN travels t on t.id = b.travel_id
            WHERE t.author_id = :author_id'
        );
        $select->execute([':author_id' => $author_id]);
        return $select->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get booking stats for the last 7 days as array:
     * [
     *  ['date' => '2016-01-03', 'count'=> 3],
     *  ...
     *  ['date' => '2016-01-09' => 'count' => 2],
     * ]
     *
     * @param int $author_id
     * @return array
     */
    public function getStats(int $author_id): array
    {
        $select = $this->connection->prepare(
            'SELECT 
              d.date AS date, 
              COUNT (t.*) AS count 
            FROM (
              SELECT CURRENT_DATE - offs AS date FROM generate_series(0, 6) AS offs
            ) d
            LEFT JOIN bookings b 
              ON (b.created >= d.date) AND (b.created < d.date + 1)
            LEFT JOIN travels t 
              ON t.id = b.travel_id AND t.author_id = :author_id
            GROUP BY d.date
            ORDER BY d.date ASC'
        );
        $select->execute([':author_id' => $author_id]);
        return $select->fetchAll(\PDO::FETCH_ASSOC);
    }
}
