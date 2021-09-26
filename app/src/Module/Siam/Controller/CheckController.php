<?php

declare(strict_types = 1);

namespace App\Module\Siam\Controller;

use App\Module\Siam\SiamConstant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CheckController extends AbstractController
{
    #[Route(path: '/api/siam/check', name: SiamConstant::ROUTE_CHECK)]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse($this->generateUrl('app_profile'), 302);
    }
}
