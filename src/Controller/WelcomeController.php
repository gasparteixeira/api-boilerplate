<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of WelcomeController
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class WelcomeController extends AbstractFOSRestController {

    /**
     * @Route("/", name="welcome")
     */
    public function indexAction(): Response {
        return new JsonResponse([
            'welcome' => 'Your API is working!'
        ]);
    }

}
