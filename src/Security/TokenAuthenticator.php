<?php
namespace Security;

use Api\Security\SessionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class TokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var SessionManager
     */
    private $sessionManager;

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (! $userProvider instanceof TokenUserProvider) {
            throw new \InvalidArgumentException;
        }
        $userId = $this->sessionManager->getUserId($token->getCredentials());
        if (empty($userId)) {
            throw new CustomUserMessageAuthenticationException('Invalid or expired token');
        }
        $user = $userProvider->loadUserByUsername($userId);
        return new PreAuthenticatedToken(
            $user,
            $token->getCredentials(),
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * @param TokenInterface $token
     * @param                $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param         $providerKey
     * @return PreAuthenticatedToken|void
     */
    public function createToken(Request $request, $providerKey)
    {
        $authHeader = $request->headers->get('Authorization');
        if (!preg_match('/^Token (.+)/i', $authHeader, $matches)) {
            throw new BadCredentialsException('No token provided');
        }
        return new PreAuthenticatedToken(null, $matches[1], $providerKey);
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response(
            $exception->getMessage(),
            Response::HTTP_UNAUTHORIZED,
            ['WWW-Authenticate' => 'Token']
        );
    }
}
