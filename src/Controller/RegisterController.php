<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\User;

/**
 * Description of RegisterController
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class RegisterController extends AbstractFOSRestController {

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

        if (!$request->headers->has('Authorization'))
            return new JsonResponse(["message" => $translator->trans("auth.required")], Response::HTTP_UNAUTHORIZED);


        if (($this->getParameter("app_username") !== $request->getUser()) || ($this->getParameter("app_password") !== $request->getPassword()))
            return new JsonResponse(["message" => $translator->trans("auth.failure")], Response::HTTP_UNAUTHORIZED);


        return new JsonResponse(['message' => $translator->trans("register.success", ["%name%" => $request->getUser()])]);
    }

}
