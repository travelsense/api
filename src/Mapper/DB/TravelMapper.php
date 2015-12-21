<?php

namespace Mapper\DB;

use Model\Travel;
use PDO;
use PDOStatement;

class TravelMapper extends AbstractMapper
{
    /**
     * @param $id
     * @return Travel|null
     */
    public function fetchById($id)
    {
        $select = $this->pdo->prepare('SELECT * FROM travels WHERE id = :id');
        $select->execute(['id' => $id]);
        return $this->createTravel($select);
    }

    /**
     * Insert into DB, update id
     * @param Travel $travel
     */
    public function insert(Travel $travel)
    {
        $insert = $this->pdo
            ->prepare('INSERT INTO travels (title, description) VALUES (:title, :description) RETURNING id');
        $insert->execute([
            ':title' => $travel->getTitle(),
            ':description' => $travel->getDescription(),
        ]);
        $id = $insert->fetchColumn();
        $travel->setId($id);
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