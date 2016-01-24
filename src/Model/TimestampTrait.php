<?php
namespace Model;

use DateTime;

trait TimestampTrait
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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     * @return TimestampTrait
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     * @return TimestampTrait
     */
    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
        return $this;
    }

}