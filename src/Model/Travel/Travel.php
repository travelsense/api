<?php
namespace Api\Model\Travel;

use Api\Model\AuthorTrait;
use Api\Model\IdTrait;
use Api\Model\TimestampTrait;
use Api\Security\Access\SubjectInterface;

class Travel implements SubjectInterface
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
     * @var array
     */
    private $category_ids = [];

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $geotags;

    /**
     * @var bool
     */
    private $published = false;

    /**
     * @var string
     */
    private $creation_mode;

    /**
     * @var Action[]
     */
    private $actions = [];

    /**
     * @var int
     */
    private $estimated_price;

    /**
     * @var int
     */
    private $transportation;

    /**
     * @var string
     */
    private $app_version;

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
     * @return array
     */
    public function getCategoryIds() : array
    {
        return $this->category_ids;
    }

    /**
     * @param array $category_ids
     * @return Travel
     */
    public function setCategoryIds(array $category_ids)
    {
        $this->category_ids = $category_ids;
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
     * @return string
     */
    public function getGeotags()
    {
        return $this->geotags;
    }

    /**
     * @param string $geotags
     * @return Travel
     */
    public function setGeotags($geotags)
    {
        $this->geotags = $geotags;
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

    /**
     * @return string
     */
    public function getCreationMode()
    {
        return $this->creation_mode;
    }

    /**
     * @param string $creation_mode
     * @return Travel
     */
    public function setCreationMode($creation_mode)
    {
        $this->creation_mode = $creation_mode;
        return $this;
    }

    /**
     * @return int
     */
    public function getEstimatedPrice()
    {
        return $this->estimated_price;
    }

    /**
     * @param int $estimated_price
     * @return Travel
     */
    public function setEstimatedPrice(int $estimated_price = null)
    {
        $this->estimated_price = $estimated_price;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransportation()
    {
        return $this->transportation;
    }

    /**
     * @param int $transportation
     * @return Travel
     */
    public function setTransportation($transportation)
    {
        $this->transportation = $transportation;
        return $this;
    }

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action[] $actions
     * @return Travel
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAppVersion()
    {
        return $this->app_version;
    }

    /**
     * @param string $app_version
     */
    public function setAppVersion(string $app_version = null)
    {
        $this->app_version = $app_version;
    }

    /**
     * @return int
     */
    public function getDaysCount(): int
    {
        $offsetStart = PHP_INT_MAX;
        $offsetEnd = 0;

        foreach ($this->getActions() as $action) {
            $offsetStart = min($action->getOffsetStart(), $offsetStart);
            $offsetEnd = max($action->getOffsetEnd(), $offsetEnd);
        }
        return max($offsetEnd - $offsetStart, 0);
    }
}
