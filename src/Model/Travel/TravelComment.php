<?php
namespace Api\Model\TravelComment;

use Api\Model\TimestampTrait;
use Api\Model\User;
use Api\Model\Travel;

class TravelComment
{
    use TimestampTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int 
     */
    private $authorId;

    /**
     * @var int 
     */
    private $travelId;

    /**
     * @var string 
     */
    private $text;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return TravelComment
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     * @return User
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTravelId()
    {
        return $this->travelId;
    }

    /**
     * @param int $travelId
     * @return Travel
     */
    public function setTravelId($travelId)
    {
        $this->travelId = $travelId;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return TravelComment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
}
