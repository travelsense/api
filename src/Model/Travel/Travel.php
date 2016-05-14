<?php
namespace Api\Model\Travel;

use Api\Model\AuthorTrait;
use Api\Model\IdTrait;
use Api\Model\TimestampTrait;

class Travel
{
    use IdTrait;
    use TimestampTrait;
    use AuthorTrait;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $title;

    /**
     * @var object|array
     */
    private $content;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var string
     */
    private $image;

    /**
     * @var bool
     */
    private $published = false;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Travel
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Travel
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array|object
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array|object $content
     * @return Travel
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param $categoryId
     * @return Travel
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Travel
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isPublished(): bool 
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     * @return Travel
     */
    public function setPublished(bool $published)
    {
        $this->published = $published;
        return $this;
    }
}
