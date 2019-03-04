<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Service\RegisterService;

/**
 * Description of RegisterController
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class RegisterController extends AbstractFOSRestController {

    private $service;

    public function __construct(RegisterService $service) {
        $this->service = $service;
    }

    /**
     * @Route("/api/register", name="post_register", methods={"POST"})
     * @SWG\Post(
     *     path="/api/register",
     *     summary="Post to register an user",
     *     description="Register a new user",
     *     operationId="postRegisterAction",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="Basic",
     *         required=true,
     *         type="string"
     *      ),
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="Name of User",
     *         required=true,
     *         type="string"
     *      ),
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="Email of user",
     *         required=true,
     *         type="string"
     *      ),
     *     @SWG\Parameter(
     *         name="password",
     *         in="formData",
     *         description="Password of user",
     *         required=true,
     *         type="string"
     *      ),
     *     @SWG\Response(
     *         response=Response::HTTP_OK,
     *         description="{message: 'user created'}",
     *        @Model(type=JsonResponse::class)
     *     )
     * )
     *
     */
    public function postRegisterAction(Request $request, TranslatorInterface $translator): JsonResponse {

        if ($this->service->hasNoAuthorization($request)) {
            return $this->service->returnMessage("auth.required", Response::HTTP_UNAUTHORIZED);
        }

        if ($this->service->hasNoBasicAuth($request)) {
            return $this->service->returnMessage("auth.failure", Response::HTTP_UNAUTHORIZED);
        }

        $form = $this->service->isFormDataInvalid($request);

        if ($form->error) {
            return $this->service->returnMessageElement("register.invalid", Response::HTTP_BAD_REQUEST, $form->element);
        }

        if ($this->service->emailHasBeenUsed($form->email)) {
            return $this->service->returnMessageElement("register.email.used", Response::HTTP_BAD_REQUEST, $form->element);
        }

        $save = $this->service->saveUser($form);
        if ($save->success) {
            return $this->service->returnMessageElement("register.success", Response::HTTP_OK, $form->name);
        } else {
            return $this->service->returnMessage("register.failure", Response::HTTP_BAD_REQUEST);
        }
    }

}
