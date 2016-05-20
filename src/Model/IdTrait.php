<?php
namespace Api\Model;

/**
 * Entity with id
 * @package Api\Model
 */
trait IdTrait
{
    /**
     * @var int
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
