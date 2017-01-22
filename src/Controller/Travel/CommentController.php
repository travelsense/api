<?php
namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\Exception\ApiException;
use Api\JSON\DataObject;
use Api\Mapper\DB\Travel\CommentMapper;
use Api\Model\Travel\Comment;
use Api\Model\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment API controller
 */
class CommentController extends ApiController
{
    /**
     * @var CommentMapper
     */
    private $comment_mapper;

    /**
     * CommentController constructor.
     *
     * @param CommentMapper $comment_mapper
     */
    public function __construct(CommentMapper $comment_mapper)
    {
        $this->comment_mapper = $comment_mapper;
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
        $this->comment_mapper->insert($comment);
        return ['id' => $comment->getId()];
    }

    /**
     * @param int  $id
     * @param User $user
     * @return array
     */
    public function deleteById(int $id, User $user): array
    {
        $comment = $this->comment_mapper->fetchById($id);
        if (empty($comment)) {
            throw new ApiException('Comment not found', ApiException::RESOURCE_NOT_FOUND);
        }
        if ($comment->getAuthorId() !== $user->getId()) {
            throw new ApiException('Access denied', ApiException::ACCESS_DENIED);
        }
        $this->comment_mapper->delete($id);
        return [];
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
        foreach ($this->comment_mapper->fetchByTravelId($id, $limit, $offset) as $comment) {
            $response[] = [
                'id' => $comment->getId(),
                'created' => $comment->getCreated()->format(self::DATETIME_FORMAT),
                'text' => $comment->getText(),
                'author' => [
                    'id'        => $comment->getAuthorId(),
                    'firstName' => $comment->getAuthorFirstName(),
                    'lastName'  => $comment->getAuthorLastName(),
                    'picture'   => $comment->getAuthorPicture(),
                ]
            ];
        }
        return $response;
    }
}
