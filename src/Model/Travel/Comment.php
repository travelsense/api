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
    public function getTravelId(): int
    {
        return $this->travelId;
    }

    /**
     * @param int $travelId
     * @return self
     */
    public function setTravelId(int $travelId): self
    {
        $this->travelId = $travelId;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
}
