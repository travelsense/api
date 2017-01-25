<?php
namespace Api\Model\Travel;

use Api\Persistence\Storable;
use Api\Persistence\Storage;

class Category implements Storable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromSaved(array $dto): self
    {
        $cat = new self($dto['name']);
        $cat->id = $dto['id'];
        return $cat;
    }

    public function saveTo(Storage $storage)
    {
        $this->id = $storage->insert(['name'=> $this->name]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
