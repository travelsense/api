<?php
namespace Api\Model\Travel;

use Api\Model\IdTrait;

class Action
{
    use IdTrait;

    /**
     * @var int
     */
    private $travel_id;
 
    /**
     * @var int
     */
    private $offset_start;
 
    /**
     * @var int
     */
    private $offset_end;
 
    /**
     * @var bool
     */
    private $car;
 
    /**
     * @var string
     */
    private $airports;
 
    /**
     * @var string
     */
    private $hotels;
 
    /**
     * @var string
     */
    private $sightseeings;
 
    /**
     * @var string
     */
    private $type;
    
    /**
     * @return int
     */
    public function getTravelId()
    {
        return $this->travel_id;
    }

    /**
     * @param int $travel_id
     * @return Action
     */

    public function setTravelId(int $travel_id)
    {
        $this->travel_id = $travel_id;
        return $this;
    }
 
    /**
     * @return int
     */
    public function getOffsetStart()
    {
        return $this->offset_start;
    }

    /**
     * @param int $offset_start
     * @return Action
     */
    public function setOffsetStart($offset_start)
    {
        $this->offset_start = $offset_start;
        return $this;
    }
 
    /**
     * @return int
     */
    public function getOffsetEnd()
    {
        return $this->offset_end;
    }

    /**
     * @param int $offset_end
     * @return Action
     */
    public function setOffsetEnd($offset_end)
    {
        $this->offset_end = $offset_end;
        return $this;
    }
 
    /**
     * @return bool
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @param bool $car
     * @return Action
     */
    public function setCar($car)
    {
        $this->car = $car;
        return $this;
    }
 
    /**
     * @return string
     */
    public function getAirports()
    {
        return $this->airports;
    }

    /**
     * @param string $airports
     * @return Action
     */
    public function setAirports($airports)
    {
        $this->airports = $airports;
        return $this;
    }

    /**
     * @return string
     */
    public function getHotels()
    {
        return $this->hotels;
    }

    /**
     * @param string $hotels
     * @return Action
     */
    public function setHotels($hotels)
    {
        $this->hotels = $hotels;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSightseeings()
    {
        return $this->sightseeings;
    }

    /**
     * @param string $sightseeings
     * @return Action
     */
    public function setSightseeings($sightseeings)
    {
        $this->sightseeings = $sightseeings;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Action
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
