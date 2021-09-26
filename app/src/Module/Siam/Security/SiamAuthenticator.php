<?php

declare(strict_types = 1);

namespace App\Module\Siam\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Module\Siam\SiamConstant;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SiamAuthenticator extends SocialAuthenticator
{
    private ClientRegistry $clientRegistry;
    private UserRepository $userRepository;
    private EventDispatcherInterface $dispatcher;
    private UrlGeneratorInterface $generator;

    public function __construct(
        ClientRegistry $clientRegistry,
        UserRepository $userRepository,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $generator
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
        $this->generator = $generator;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => 'Authentication Required',
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === SiamConstant::ROUTE_CHECK;
    }

    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getSiamClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        /** @var SiamResourceOwner $resourceOwner */
        $resourceOwner = $this->getSiamClient()->getOAuth2Provider()->getResourceOwner($credentials);
        $ownerData = $resourceOwner->toArray();

        $userIdentifier = $ownerData['mail'] ?? $ownerData['login'];

        $user = new User();
        $user->setUsername($userIdentifier);

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $providerKey
    ): RedirectResponse {
        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new AuthenticationException('User not found by token');
        }

        $user->login();
        $this->userRepository->save($user);

        $response = new RedirectResponse($this->generator->generate('app_profile'));
        $response->headers->setCookie(new Cookie('authToken', $user->getAuthToken()));

        return $response;
    }

    private function getSiamClient(): OAuth2ClientInterface
    {
        $ssoClient = $this->clientRegistry->getClient(SiamConstant::SSO_CLIENT_NAME);
        $ssoClient->setAsStateless();

        return $ssoClient;
    }
}
