<?php
namespace Api\Model;

/**
 * Entity with id
 * @package Api\Model
 */
trait HasIdTrait
{
    /**
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
}
