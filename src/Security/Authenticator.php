<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;

/**
 * Description of Authenticator check out the page below
 * https://symfony.com/doc/current/security/guard_authentication.html
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class Authenticator extends AbstractGuardAuthenticator {

    private $_em;
    private $_jwtProvider;
    private $_translator;

    public function __construct(EntityManagerInterface $em, JWTEncoderInterface $jwtEncoder, TranslatorInterface $translator) {
        $this->_em = $em;
        $this->_jwtProvider = $jwtEncoder;
        $this->_translator = $translator;
    }

    public function checkCredentials($credentials, UserInterface $user): bool {
        return true;
    }

    public function getCredentials(Request $request) {
        if (!$request->headers->has('Authorization')) {
            return;
        }
        $extractor = new AuthorizationHeaderTokenExtractor(
                'Bearer', 'Authorization'
        );
        $token = $extractor->extract($request);
        if (!$token) {
            return;
        }
        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $data = $this->_jwtProvider->decode($credentials);

        if (!$data)
            throw new CustomUserMessageAuthenticationException($this->_translator->trans("auth.token.invalid"));

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user)
            throw new AuthenticationCredentialsNotFoundException();


        if ($user->getHash() != $data['hash'])
            throw new CustomUserMessageAuthenticationException($this->_translator->trans("auth.token.expired"));

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $data = ["message" => $this->_translator->trans("auth.failure")];
        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse {

        $data = ["message" => $this->_translator->trans("auth.required")];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request): bool {
        return $request->headers->has('Authorization');
    }

    public function supportsRememberMe(): bool {
        return false;
    }

}
