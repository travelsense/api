<?php

namespace Mapper\DB;

use Model\Travel;
use PDOStatement;

class TravelCommentMapper extends AbstractMapper
{
    /**
     * @param $id
     * @return Travel|null
     */
    public function fetchById($id)
    {
        $select = $this->pdo->prepare('SELECT * FROM travel WHERE id = :id');
        $select->execute(['id' => $id]);
        return $this->createTravel($select);
    }

    /**
     * @param PDOStatement $stmt
     * @return Travel|null
     */
    private function createTravel(PDOStatement $stmt)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (false === $row) {
            return null;
        }
        $travel = new Travel();
        return $travel
            ->setId($row['id'])
            ->setDescription($row['description'])
            ->setTitle($row['title']);
    }
}