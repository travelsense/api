<?php

namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\Exception\ApiException;
use Api\JSON\DataObject;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Model\Travel\Travel;
use Api\Model\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Travel API controller
 */
class TravelController extends ApiController
{
    /**
     * @var TravelMapper
     */
    private $travelMapper;

    /**
     * @var CategoryMapper
     */
    private $categoryMapper;

    /**
     * TravelController constructor.
     *
     * @param TravelMapper   $travelMapper
     * @param CategoryMapper $categoryMapper
     */
    public function __construct(TravelMapper $travelMapper, CategoryMapper $categoryMapper)
    {
        $this->travelMapper = $travelMapper;
        $this->categoryMapper = $categoryMapper;
    }

    /**
     * @param Request $request
     * @param User    $user
     * @return array
     */
    public function createTravel(Request $request, User $user): array
    {
        $json = DataObject::createFromString($request->getContent());

        $travel = new Travel();
        $travel->setAuthor($user);
        $travel->setTitle($json->getString('title'));
        $travel->setDescription($json->getString('description'));
        $travel->setContent($json->get('content'));
        if ($json->has('image')) {
            $travel->setImage($json->get('image'));
        }
        $this->travelMapper->insert($travel);
        if ($json->has('category_id')) {
            $this->categoryMapper->addTravelToCategory($travel->getId(), $json->get('category_id'));
        }
        if ($json->has('published')) {
            $travel->setPublished($json->get('published'));
        }

        return ['id' => $travel->getId()];
    }

    /**
     * @param $id
     * @return array
     * @throws ApiException
     */
    public function getTravel(int $id): array
    {
        $travel = $this->travelMapper->fetchById($id);
        if (!$travel) {
            throw new ApiException('Travel not found', ApiException::RESOURCE_NOT_FOUND);
        }
        return $this->buildTravelView($travel);
    }

    /**
     * @param User $user
     * @param int  $limit
     * @param int  $offset
     * @return array
     */
    public function getUserTravels(User $user, int $limit = 10, int $offset = 0): array
    {
        $travels = $this->travelMapper->fetchByAuthorId($user->getId(), $limit, $offset);
        return $this->buildTravelSetView($travels);
    }

    /**
     * @param int  $id
     * @param User $user
     * @return array
     */
    public function addFavorite(int $id, User $user): array
    {
        $this->travelMapper->addFavorite($id, $user->getId());
        return [];
    }

    /**
     * @param int  $id
     * @param User $user
     * @return array
     */
    public function removeFavorite(int $id, User $user): array
    {
        $this->travelMapper->removeFavorite($id, $user->getId());
        return [];
    }

    /**
     * @param User $user
     * @return array
     */
    public function getFavorites(User $user): array
    {
        $travels = $this->travelMapper->fetchFavorites($user->getId());
        return $this->buildTravelSetView($travels);
    }

    /**
     * @return array
     */
    public function getFeatured(): array
    {
        $result = [
            'banners' => [
                [
                    'title'    => 'Hawaii',
                    'subtitle' => 'Popular Destinations',
                    'image'    => 'http://www.astonhotels.com/assets/slides/690x380-Hawaii-Sunset.jpg',
                    'category' => 'Hawaii',
                ],
                [
                    'title'    => 'Mexico',
                    'subtitle' => 'Authentic experience',
                    'image'    => 'http://image1.masterfile.com/em_w/02/93/35/625-02933564em.jpg',
                    'category' => 'Mexico',
                ],
                [
                    'title'    => 'California',
                    'subtitle' => 'Explore local experiences',
                    'image'    => 'http://cdn.sheknows.com/articles/2012/02/southern-california-beach-horiz.jpg',
                    'category' => 'California',
                ],
            ],
        ];
        $featuredCategoryNames = ['Featured', 'Romantic', 'Sports'];
        $featuredCategories = [];
        foreach ($featuredCategoryNames as $name) {
            $travels = $this->travelMapper->fetchPublishedByCategory($name, 5, 0);
            $featuredCategories[] = [
                'title'   => $name,
                'travels' => $this->buildTravelSetView($travels),
            ];
        }
        $result['categories'] = $featuredCategories;
        return $result;
    }

    /**
     * @param string $name
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getTravelsByCategory(string $name, int $limit = 10, int $offset = 0): array
    {
        $travels = $this->travelMapper->fetchByCategory($name, $limit, $offset);
        return $this->buildTravelSetView($travels);
    }

    /**
     * @param int     $id
     * @param Request $request
     * @param User    $user
     * @return array
     */
    public function updateTravel(int $id, Request $request, User $user): array
    {
        $travel = $this->getOwnedTravel($id, $user);
        $json = DataObject::createFromString($request->getContent());
        if ($json->has('title')) {
            $travel->setTitle($json->getString('title'));
        }
        if ($json->has('description')) {
            $travel->setDescription($json->getString('description'));
        }
        if ($json->has('content')) {
            $travel->setContent($json->get('content'));
        }
        if ($json->has('image')) {
            $travel->setImage($json->get('image'));
        }
        if ($json->has('published')) {
            $travel->setPublished($json->get('published'));
        }
        if ($json->has('category_id')) {
            $this->categoryMapper->addTravelToCategory($id, $json->get('category_id'));
        }
        $this->travelMapper->update($travel);

        return [];
    }

    /**
     * @param      $id
     * @param User $user
     * @return array
     */
    public function deleteTravel(int $id, User $user): array
    {
        $travel = $this->getOwnedTravel($id, $user);
        $this->travelMapper->delete($travel->getId());
        return [];
    }

    /**
     * @param int  $id
     * @param User $user
     * @return Travel
     * @throws ApiException
     */
    private function getOwnedTravel(int $id, User $user): Travel
    {
        $travel = $this->travelMapper->fetchById($id);
        if (!$travel) {
            throw new ApiException('Travel not found', ApiException::RESOURCE_NOT_FOUND);
        }
        if ($travel->getAuthorId() !== $user->getId()) {
            throw new ApiException('Access denied', ApiException::ACCESS_DENIED);
        }
        return $travel;
    }

    /**
     * @param Travel $travel
     * @return array
     */
    private function buildTravelView(Travel $travel): array
    {
        $author = $travel->getAuthor();
        $view = [
            'id'          => $travel->getId(),
            'title'       => $travel->getTitle(),
            'description' => $travel->getDescription(),
            'content'     => $travel->getContent(),
            'image'       => $travel->getImage(),
            'created'     => $travel->getCreated()->format(self::DATETIME_FORMAT),
            'category'    => $travel->getCategoryId(),
            'published'   => $travel->isPublished(),
        ];

        if ($author) {
            $view['author'] = [
                'id'        => $author->getId(),
                'firstName' => $author->getFirstName(),
                'lastName'  => $author->getLastName(),
                'picture'   => $author->getPicture(),
            ];
        }
        return $view;
    }

    /**
     * @param Travel[] $travels
     * @return array
     */
    private function buildTravelSetView(array $travels): array
    {
        $view = [];
        foreach ($travels as $travel) {
            $view[] = $this->buildTravelView($travel);
        }
        return $view;
    }
}
