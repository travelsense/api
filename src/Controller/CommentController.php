<?php

namespace Api\Controller;

use Api\JSON\DataObject;
use Api\Mapper\DB\CommentMapper;
use Api\Model\Travel\Comment;
use Api\Model\User;
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
     * @param int     $id Travel id
     * @param Request $request
     * @param User    $user
     * @return array
     */
    public function createTravelComment(int $id, Request $request, User $user): array
    {
        $json = DataObject::createFromString($request->getContent());

        $comment = new Comment();
        $comment->setAuthorId($user->getId());
        $comment->setTravelId($id);
        $comment->setText($json->getString('text'));
        $this->commentMapper->insert($comment);
        return ['id' => $comment->getId()];
    }
    
    public function delete(int $id)
    {
        //$comment = $this->commentMapper->
    }

    /**
     * @param int $id Travel ID
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllByTravelId(int $id, int $limit = 10, int $offset = 0): array
    {
        $response = [];
        foreach ($this->commentMapper->fetchByTravelId($id, $limit, $offset) as $comment) {
            $author = $comment->getAuthor();
            $response[] = [
                'id' => $comment->getId(),
                'created' => $comment->getCreated()->format(self::DATETIME_FORMAT),
                'text' => $comment->getText(),
                'author' => [
                    'id'        => $author->getId(),
                    'firstName' => $author->getFirstName(),
                    'lastName'  => $author->getLastName(),
                    'picture'   => $author->getPicture(),
                ]
            ];
        }
        return $response;
    }
}
