<?php
namespace Mapper\DB;

use AbstractPDOMapper;
use Model\Travel;

class TravelMapper extends AbstractPDOMapper
{
    /**
     * @param $id
     * @return Travel|null
     */
    public function fetchById($id)
    {
        $select = $this->prepare('SELECT * FROM travels WHERE id = :id');
        $select->execute(['id' => $id]);
        return $this->fetch($select);
    }

    /**
     * Insert into DB, update id
     * @param Travel $travel
     */
    public function insert(Travel $travel)
    {
        $insert = $this->prepare('INSERT INTO travels (title, description) VALUES (:title, :description) RETURNING id');
        $insert->execute([
            ':title' => $travel->getTitle(),
            ':description' => $travel->getDescription(),
        ]);
        $id = $insert->fetchColumn();
        $travel->setId($id);
    }

    public function create(array $row)
    {
        $travel = new Travel();
        return $travel
            ->setId($row['id'])
            ->setDescription($row['description'])
            ->setTitle($row['title']);
    }
}