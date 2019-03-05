<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Service\RegisterService;
use App\Util\ValidatorUtil;

/**
 * Description of RegisterController
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class RegisterController extends AbstractFOSRestController {

    private $service;
    private $validator;

    public function __construct(RegisterService $service, ValidatorUtil $validator) {
        $this->service = $service;
        $this->validator = $validator;
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
    public function postRegisterAction(Request $request): JsonResponse {

        // validation
        if (!$this->validator->hasAuthorization($request)) {
            return $this->validator->returnMessage("auth.required", Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->validator->isAuthenticated($request)) {
            return $this->validator->returnMessage("auth.failure", Response::HTTP_UNAUTHORIZED);
        }

        $formData = $this->validator->isFormDataValid($request);
        if (!$formData->isValid) {
            return $this->validator->returnMessageElement("register.invalid", Response::HTTP_BAD_REQUEST, $formData->element);
        }

        if (!$this->service->isEmailUnique($request)) {
            return $this->validator->returnMessageElement("register.email.used", Response::HTTP_BAD_REQUEST, $request->request->get("email"));
        }

        // persist user
        if ($this->service->isUserSaved($request)) {
            return $this->validator->returnMessageElement("register.success", Response::HTTP_OK, $request->request->get("name"));
        } else {
            return $this->validator->returnMessage("register.failure", Response::HTTP_BAD_REQUEST);
        }
    }

}
