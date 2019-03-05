<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

/**
 * Description of AuthController
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class AuthController extends AbstractController {

    private $passwordEncoder;
    private $jwtEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder) {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

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
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $request->getUser()]);
        if (!$user) {
            return new JsonResponse(["message" => $translator->trans("auth.user.not_found")], Response::HTTP_FORBIDDEN);
        }

        $isValid = $this->passwordEncoder->isPasswordValid($user, $request->getPassword());
        if (!$isValid)
            return new JsonResponse(["message" => $translator->trans("auth.user.bad_credentials")], Response::HTTP_FORBIDDEN);

        $em = $this->getDoctrine()->getManager();
        $hash = substr(sha1(rand()), 0, 16);
        $user->setHash($hash);
        $user->setLastLoginAt(new \DateTime("now"));
        $em->flush();

        $token = $this->jwtEncoder->encode([
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'hash' => $user->getHash(),
            'exp' => time() + $this->getParameter("jwt_token_ttl") // 1 hour expiration
        ]);
        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }

    /**
     * @Route("/api/retoken", name="auth_renew_token", methods={"POST"})
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
