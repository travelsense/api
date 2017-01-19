<?php
namespace Api\Model;

use DateTime;

trait HasTimestampTrait
{
    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var DateTime
     */
    private $updated;

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     * @return HasTimestampTrait
     */
    public function setCreated(DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }
}
