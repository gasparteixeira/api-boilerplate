<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

/**
 * Description of RegisterService
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class RegisterService {

    private $em;
    private $logger;
    private $params;
    private $translator;
    private $encoder;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, TranslatorInterface $translator, ParameterBagInterface $params, UserPasswordEncoderInterface $encoder) {
        $this->em = $em;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->params = $params;
        $this->encoder = $encoder;
    }

    public function isEmailUnique($request): bool {
        $user = $this->em->getRepository(User::class)->findOneBy(["email" => $request->request->get("email")]);
        return NULL === $user;
    }

    public function isUserSaved($request): bool {
        $user = new User();
        $user->setName($request->request->get("name"));
        $user->setEmail($request->request->get("email"));
        $user->setPassword($request->request->get("password"));
        $password = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setRoles(["ROLE_USER"]);
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            return false;
        }
        return true;
    }

    public function returnMessage($error, $code): JsonResponse {
        return new JsonResponse(["message" => $this->translator->trans($error)], $code);
    }

    public function returnMessageElement($error, $code, $element): JsonResponse {
        return new JsonResponse(["message" => $this->translator->trans($error, ["%element%" => $element])], $code);
    }

}
