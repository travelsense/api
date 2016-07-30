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
    private $travel_mapper;

    /**
     * @var CategoryMapper
     */
    private $category_mapper;

    /**
     * TravelController constructor.
     *
     * @param TravelMapper   $travel_mapper
     * @param CategoryMapper $category_mapper
     */
    public function __construct(TravelMapper $travel_mapper, CategoryMapper $category_mapper)
    {
        $this->travel_mapper = $travel_mapper;
        $this->category_mapper = $category_mapper;
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
        if ($json->has('creation_mode')) {
            $travel->setCreationMode($json->get('creation_mode'));
        }
        $this->travel_mapper->insert($travel);
        if ($json->has('category_id')) {
            $this->category_mapper->addTravelToCategory($travel->getId(), $json->get('category_id'));
        }
        if ($json->has('published')) {
            $travel->setPublished($json->get('published'));
        }

        return ['id' => $travel->getId()];
    }

    /**
     * @param int $id
     * @param User $user
     * @return array
     * @throws ApiException
     */
    public function getTravel(int $id, User $user = null): array
    {
        $travel = $this->travel_mapper->fetchById($id);
        if (!$travel) {
            throw new ApiException('Travel not found', ApiException::RESOURCE_NOT_FOUND);
        }
        return $this->buildTravelView($travel, ($user ? $user->getId() : null));
    }

    /**
     * @param User $user
     * @param int  $limit
     * @param int  $offset
     * @return array
     */
    public function getUserTravels(User $user, int $limit = 10, int $offset = 0): array
    {
        $travels = $this->travel_mapper->fetchByAuthorId($user->getId(), $limit, $offset);
        return $this->buildTravelSetView($travels);
    }

    /**
     * @param int  $id
     * @param User $user
     * @return array
     */
    public function addFavorite(int $id, User $user): array
    {
        $this->travel_mapper->addFavorite($id, $user->getId());
        return [];
    }

    /**
     * @param int  $id
     * @param User $user
     * @return array
     */
    public function removeFavorite(int $id, User $user): array
    {
        $this->travel_mapper->removeFavorite($id, $user->getId());
        return [];
    }

    /**
     * @param User $user
     * @return array
     */
    public function getFavorites(User $user): array
    {
        $travels = $this->travel_mapper->fetchFavorites($user->getId());
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
        $featured_category_names = ['Featured', 'Romantic', 'Sports'];
        $featured_categories = [];
        foreach ($featured_category_names as $name) {
            $travels = $this->travel_mapper->fetchPublishedByCategory($name, 5, 0);
            $featured_categories[] = [
                'title'   => $name,
                'travels' => $this->buildTravelSetView($travels),
            ];
        }
        $result['categories'] = $featured_categories;
        return $result;
    }

    /**
     * @param string $name
     * @param User $user
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getTravelsByCategory(string $name, User $user = null, int $limit = 10, int $offset = 0): array
    {
        $travels = $this->travel_mapper->fetchByCategory($name, $limit, $offset);
        return $this->buildTravelSetView($travels, ($user ? $user->getId() : null));
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
        if ($json->has('creation_mode')) {
            $travel->setCreationMode($json->get('creation_mode'));
        }
        if ($json->has('category_id')) {
            $this->category_mapper->addTravelToCategory($id, $json->get('category_id'));
        }
        $this->travel_mapper->update($travel);

        return [];
    }

    /**
     * @param int  $id
     * @param User $user
     * @return array
     */
    public function deleteTravel(int $id, User $user): array
    {
        $travel = $this->getOwnedTravel($id, $user);
        $this->travel_mapper->delete($travel->getId());
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
        $travel = $this->travel_mapper->fetchById($id);
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
     * @param int $user_id
     * @return array
     */
    private function buildTravelView(Travel $travel, int $user_id = null): array
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
            'creation_mode' => $travel->getCreationMode(),
        ];
        if ($user_id !== null) {
            $favorites = $this->travel_mapper->fetchFavoriteIds($user_id);
            if (in_array($travel->getId(), $favorites)) {
                $view['is_favorited'] = true;
            } else {
                $view['is_favorited'] = false;
            }
        } else {
            $view['is_favorited'] = false;
        }
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
     * @param int $user_id
     * @param Travel[] $travels
     * @return array
     */
    private function buildTravelSetView(array $travels, int $user_id = null): array
    {
        $view = [];
        foreach ($travels as $travel) {
            $view[] = $this->buildTravelView($travel, $user_id);
        }
        return $view;
    }
}
