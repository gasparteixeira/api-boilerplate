<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
 * Description of AuthController
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class AuthController extends AbstractController {

    /**
     * @Route("/api/token", name="auth_token", methods={"POST"})
     * @SWG\Post(
     *     path="/api/token",
     *     summary="Request a new token",
     *     description="Login with username and password as basic atuh to get the token",
     *     operationId="postTokenAction",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="Basic",
     *         required=true,
     *         type="string"
     *      ),
     *     @SWG\Response(
     *         response=Response::HTTP_OK,
     *         description="{token: ...}",
     *        @Model(type=JsonResponse::class)
     *     )
     * )
     *
     */
    public function postTokenAction(Request $request, TranslatorInterface $translator): JsonResponse {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $request->getUser()]);
        if (!$user) {
            $data = ["message" => $translator->trans("auth.user.notfound")];
            return new JsonResponse($data, Response::HTTP_FORBIDDEN);
        }


        $isValid = $this->get('security.password_encoder')->isPasswordValid($user, $request->getPassword());

        if (!$isValid)
            throw new BadCredentialsException();

        $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600 // 1 hour expiration
        ]);
        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/api/retoken", name="auth_renew_token", methods={"POST"})
     * @Method("POST")
     * @SWG\Post(
     *     path="/api/retoken",
     *     summary="Request to renew the token",
     *     description="Renew a valid token and non expired token from user",
     *     operationId="postRenewTokenAction",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="Bearer",
     *         required=true,
     *         type="string"
     *      ),
     *     @SWG\Response(
     *         response=Response::HTTP_OK,
     *         description="{token: ...}",
     *        @Model(type=JsonResponse::class)
     *     )
     * )
     *
     */
    public function postRenewTokenAction(Request $request, TranslatorInterface $translator): JsonResponse {

        return new JsonResponse(['token' => ""]);
    }

}
