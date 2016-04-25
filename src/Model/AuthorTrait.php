<?php
namespace Api\Model;

/**
 * Entity having a User as the author
 * @package Api\Model
 */
trait AuthorTrait
{
    /**
     * @var int
     */
    private $authorId;

    /**
     * @var User
     */
    private $author;

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     * @return self
     */
    public function setAuthorId(int $authorId): self
    {
        $this->author = null;
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return self
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        $this->authorId = $author->getId();
        return $this;
    }
}
