<?php

namespace Api\Controller;

use Api\Mapper\DB\CommentMapper;
use Api\JSON\DataObject;
use Api\Model\User;
use Api\Model\Travel\Comment;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment API controller
 */
class CommentController extends ApiController
{
    use LoggerAwareTrait;

    /**
     * @var CommentMapper
     */
    private $commentMapper;

    /**
     * CommentController constructor.
     *
     * @param CommentMapper $commentMapper
     */
    public function __construct(CommentMapper $commentMapper)
    {
        $this->commentMapper = $commentMapper;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function createTravelComment(Request $request, User $user)
    {
        $json = DataObject::createFromString($request->getContent());

        $comment = new Comment();
        $comment->setAuthorId($user->getId());
        $comment->setTravelId($json->get('travel_id'));
        $comment->setText($json->getString('text'));
        $this->commentMapper->insert($comment);
        return ['id' => $comment->getId()];
    }
}
    