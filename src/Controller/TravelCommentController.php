<?php
namespace Controller;

use Exception\ApiException;
use Mapper\DB\TravelCommentMapper as DBCommentMapper;
use Mapper\JSON\TravelCommentMapper as JSONCommentMapper;

class CommentController
{
    /**
     * @var DBCommentMapper
     */
    private $dbCommentMapper;

    /**
     * @var JSONCommentMapper
     */
    private $jsonCommentMapper;

    /**
     * CommentController constructor.
     * @param DBCommentMapper $dbCommentMapper
     * @param JSONCommentMapper $jsonCommentMapper
     */
    public function __construct(DBCommentMapper $dbCommentMapper, JSONCommentMapper $jsonCommentMapper)
    {
        $this->dbCommentMapper = $dbCommentMapper;
        $this->jsonCommentMapper = $jsonCommentMapper;
    }

    public function getComment($id)
    {
        $Comment = $this->dbCommentMapper->fetchById($id);
        if (null === $Comment) {
            throw ApiException::create(ApiException::RESOURCE_NOT_FOUND);
        }
        return $this->jsonCommentMapper->toArray($Comment);
    }
}