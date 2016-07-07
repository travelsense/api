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

    /**
     * @param int $author_id
     * @return self
     */
    public function setAuthorId(int $author_id): self
    {
        $this->author_id = $author_id;
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
        $this->author_id = $author->getId();
        return $this;
    }
}
