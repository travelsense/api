<?php
namespace Controller;

use Exception\ApiException;
use Mapper\DB\TravelCommentMapper as DBCommentMapper;
use Mapper\JSON\TravelCommentMapper as JSONCommentMapper;

class TravelCommentController
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

    /**
     * @param int $id Travel id
     * @param int $page
     */
    public function getByTravel($id, $page)
    {
        $limit = 10;
        $comments = $this->dbCommentMapper->fetchByTravelId($id, $limit, $page * $limit);
    }
}