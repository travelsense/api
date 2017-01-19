<?php
namespace Api\Model\Travel;

use Api\Model\HasAuthorTrait;
use Api\Model\HasIdTrait;
use Api\Model\HasTimestampTrait;

class Comment
{
    use HasIdTrait;
    use HasTimestampTrait;
    use HasAuthorTrait;

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
