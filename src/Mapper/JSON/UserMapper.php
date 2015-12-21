<?php
namespace Mapper\JSON;
use Symfony\Component\HttpFoundation\Request;
use Model\User;

class UserMapper
{
    public function toArray(User $user)
    {
        return [
            'email' => $user->getEmail(),
            'picture' => $user->getPicture(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];
    }

    /**
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {
        $json = new DataObject($request->getContent());

        $user = new User();
        $user
            ->setEmail($json->get('email', 'string'))
            ->setPassword($json->get('password', 'string'))
            ->setFirstName($json->get('firstName', 'string'))
            ->setLastName($json->get('lastName', 'string'))
            ->setPicture($json->get('picture', 'string', null, ''));

        return $user;
    }
}