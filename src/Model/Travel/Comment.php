<?php
namespace Api\Model\Travel;

use Api\Model\AuthorTrait;
use Api\Model\IdTrait;
use Api\Model\TimestampTrait;

class Comment
{
    use IdTrait;
    use TimestampTrait;
    use AuthorTrait;

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
    public function getTravelId()
    {
        return $this->travelId;
    }

    /**
     * @param int $travelId
     * @return Comment
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
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
}
