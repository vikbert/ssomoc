<?php

namespace App\Controller;

use App\Module\Siam\SiamConstant;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ClientRegistry $clientRegistry): Response
    {
        $ssoAuthzUrl = $clientRegistry
            ->getClient(SiamConstant::SSO_CLIENT_NAME)
            ->getOAuth2Provider()
            ->getAuthorizationUrl(['scope' => SiamConstant::SSO_SCOPE]);

        return $this->render(
            '@templates/home/index.html.twig',
            [
                'authzUrl' => $ssoAuthzUrl,
            ]
        );
    }
}
