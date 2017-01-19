<?php
namespace Api\Model;

/**
 * Entity having a User as the author
 * @package Api\Model
 */
trait HasAuthorTrait
{
    /**
     * @var int
     */
    private $author_id;

    /**
     * @var User
     */
    private $author;

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->author_id;
    }

    public function getAuthorFirstName(): string
    {
        return $this->author->getFirstName();
    }

    public function getAuthorLastName(): string
    {
        return $this->author->getLastName();
    }

    public function getAuthorPicture(): string
    {
        return $this->author->getPicture();
    }

    /**
     * @param int $author_id
     * @return HasAuthorTrait
     */
    public function setAuthorId(int $author_id): self
    {
        $this->author_id = $author_id;
        return $this;
    }

    /**
     * @param User $author
     * @return HasAuthorTrait
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        $this->setAuthorId($author->getId());
        return $this;
    }
}
