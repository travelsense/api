<?php

namespace Api\Controller\Travel;

use Api\Controller\ApiController;
use Api\Exception\ApiException;
use Api\JSON\DataObject;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\ActionMapper;
use Api\Model\Travel\Travel;
use Api\Model\Travel\Action;
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
     * @var ActionMapper
     */
    private $action_mapper;

    /**
     * TravelController constructor.
     *
     * @param TravelMapper   $travel_mapper
     * @param CategoryMapper $category_mapper
     */
    public function __construct(TravelMapper $travel_mapper, CategoryMapper $category_mapper, ActionMapper $action_mapper)
    {
        $this->travel_mapper = $travel_mapper;
        $this->category_mapper = $category_mapper;
        $this->action_mapper = $action_mapper;
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
        if ($json->has('image')) {
            $travel->setImage($json->get('image'));
        }
        if ($json->has('creation_mode')) {
            $travel->setCreationMode($json->get('creation_mode'));
        }
        $this->travel_mapper->insert($travel);
        
        $actions = $this->createActions((array) $json->get('content'), $travel->getId());
        $travel->setActions($actions)
        $this->action_mapper->insertActions($travel->getActions());

        if ($json->has('category_id')) { //TODO: remove in version 2.0 #126
            $ids = (array) $json->get('category_id');
            $this->category_mapper->setTravelCategories($travel->getId(), $ids);
        }
        if ($json->has('category_ids')) {
            $this->category_mapper->setTravelCategories($travel->getId(), $json->getArrayOf('integer', 'category_ids'));
        }
        if ($json->has('published')) {
            $travel->setPublished($json->get('published'));
        }

        return ['id' => $travel->getId()];
    }

    public function createActions(array $actionsJSON, int $travel_id): array
    {
        $actions = array();
        foreach ($actionsJSON as $action) {
            $actions[] = $this->createAction($action, $travel_id);
        }
        return $actions;
    }

    public function createAction($json, int $travel_id): Action
    {
        // print_r($json);
        $action = new Action();
        $action->setTravelId($travel_id);
        $action->setOffsetStart($json->offsetStart);
        $action->setOffsetEnd($json->offsetEnd);
        if (property_exists($json, 'car')) {
            $action->setCar($json->car);
        } else {
            $action->setCar(false);
        }
        $action->setAirports($json->airports);
        $action->setHotels($json->hotels);
        $action->setSightseeings($json->sightseeings);
        $action->setType($json->type);
        return $action;
    }

    /**
     * @param $id
     * @return array
     * @throws ApiException
     */
    public function getTravel(int $id): array
    {
        $travel = $this->travel_mapper->fetchById($id);
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
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getTravelsByCategory(string $name, int $limit = 10, int $offset = 0): array
    {
        $travels = $this->travel_mapper->fetchByCategory($name, $limit, $offset);
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
            $actions = $this->createActions((array)$json->get('content'), $id);
            $travel->setActions($actions);
            $this->action_mapper->deleteTravelActions($id);
            $this->action_mapper->insertActions($travel->getActions());
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
        if ($json->has('category_id')) { //TODO: remove in version 2.0 #126
            $ids = (array) $json->get('category_id');
            $this->category_mapper->setTravelCategories($travel->getId(), $ids);
        }
        if ($json->has('category_ids')) {
            $this->category_mapper->setTravelCategories($travel->getId(), $json->getArrayOf('integer', 'category_ids'));
        }
        $this->travel_mapper->update($travel);

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
     * @return array
     */
    private function buildTravelView(Travel $travel): array
    {
        $author = $travel->getAuthor();
        $view = [
            'id'          => $travel->getId(),
            'title'       => $travel->getTitle(),
            'description' => $travel->getDescription(),
            'content'     => count($travel->getActions())?$this->buildActionsView($travel->getActions()):$travel->getContent(),
            'image'       => $travel->getImage(),
            'created'     => $travel->getCreated()->format(self::DATETIME_FORMAT),
            'category'    => $travel->getCategoryIds() ? $travel->getCategoryIds()[0] : null,
            'category_ids'    => $travel->getCategoryIds(),
            'published'   => $travel->isPublished(),
            'creation_mode' => $travel->getCreationMode(),
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
     * @param Action[] $actions
     * @return array
     */
    private function buildActionsView(array $actions): array
    {
        $actionsJSON = array();
        foreach ($actions as $action) {
            $actionsJSON[] = $this->buildActionView($action);
        }
        return $actionsJSON;
    }

    /**
     * @param Action $action
     * @return array
     */
    private function buildActionView(Action $action): array
    {
        $view = [
            'id'            => $action->getId(),
            'offsetStart'   => $action->getOffsetStart(),
            'offsetEnd'     => $action->getOffsetEnd(),
            'car'           => $action->getCar(),
            'airports'      => $action->getAirports(),
            'hotels'        => $action->getHotels(),
            'sightseeings'  => $action->getSightseeings(),
            'type'          => $action->getType()
        ];
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
