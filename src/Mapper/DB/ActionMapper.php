<?php
namespace Api\Mapper\DB;

use Api\AbstractPDOMapper;
use Api\Model\Travel\Action;
use DateTime;
use PDO;
use PDOStatement;

/**
 * Class ActionMapper
 * @package Api\Mapper\DB
 */
class ActionMapper extends AbstractPDOMapper
{
    /**
     * Insert into DB, update id
     *
     * @param Action $action
     */
    public function insert(Action $action)
    {
        $insert = $this->pdo->prepare('
            INSERT INTO actions (travel_id, offset_start, offset_end, car,  airports, hotels, sightseeings, type)
            VALUES (:travel_id, :offset_start, :offset_end, :car, :airports::JSON, :hotels::JSON, :sightseeings::JSON, :type) RETURNING id
        ');
        $this->bindCommonValues($insert, $action);
        $insert->execute();
        $row = $insert->fetch(PDO::FETCH_ASSOC);
        $action->setId($row['id']);
    }

    /**
     * @param int $id
     */
    public function delete(int $id)
    {
        $this->pdo
            ->prepare("DELETE FROM actions WHERE id = :id")
            ->execute([':id' => $id]);
    }

    /**
     * Update title and description in DB
     *
     * @param Action $action
     */
    public function update(Action $action)
    {
        $update = $this->pdo->prepare('
            UPDATE actions SET
            travel_id = :travel_id, 
            offset_start = :offset_start, 
            offset_end = :offset_end, 
            car = :car,
            airports = :airports, 
            hotels = :hotels,
            sightseeings = :sightseeings,
            type = :type
            WHERE id = :id
        ');
        $this->bindCommonValues($update, $action);
        $update->bindValue('id', $action->getId(), PDO::PARAM_INT);
        $update->execute();
    }

    /**
     * @param array $row
     * @return Action
     */
    protected function create(array $row)
    {
        $action = new Action();
        $action
            ->setId($row['id'])
            ->setTravelId($row['travel_id'])
            ->setOffsetStart($row['offset_start'])
            ->setOffsetEnd($row['offset_end'])
            ->setCar($row['car'])
            ->setAirports(json_decode($row['airports']))
            ->setHotels(json_decode($row['hotels']))
            ->setSightseeings(json_decode($row['sightseeings']))
            ->setType($row['type']);
        return $action;
    }

    /**
     * @param PDOStatement $statement
     * @param Action $action
     */
    private function bindCommonValues(PDOStatement $statement, Action $action)
    {
        $values = [
            'travel_id' => $action->getTravelId(),
            'offset_start' => $action->getOffsetStart(),
            'offset_end' => $action->getOffsetEnd(),
            'car' => $action->getCar(),
            'airports' => json_encode($action->getAirports()),
            'hotels' => json_encode($action->getHotels()),
            'sightseeings' => json_encode($action->getSightseeings()),
            'type' => $action->getType()
        ];
        $this->bindValues($statement, $values);
    }

    /**
     * @param int $travel_id
     * @return Action[]
     */
    public function fetchActionsForTravel(int $travel_id): array
    {
        $select = $this->pdo->prepare('
            SELECT * FROM actions
            WHERE travel_id = :travel_id
            ');
        $select->execute(['travel_id' => $travel_id]);
        return $this->buildAll($select);
    }
    
    /**
     * @param int $travel_id
     */
    public function deleteTravelActions(int $travel_id)
    {
        $this->pdo
            ->prepare("DELETE FROM actions WHERE travel_id = :travel_id")
            ->execute([':travel_id' => $travel_id]);
    }
    
    /**
     * Insert into DB, update id
     *
     * @param Action[] $actions
     */
    public function insertActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->insert($action);
        }
    }
}
