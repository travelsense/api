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
    private $travel_id;

    /**
     * @var string
     */
    private $text;

    /**
     * @return int
     */
    public function getTravelId(): int
    {
        return $this->travel_id;
    }

    /**
     * @param int $travel_id
     * @return Comment
     */
    public function setTravelId(int $travel_id): self
    {
        $this->travel_id = $travel_id;
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
     * @return Comment
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
}
