<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
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

    public function hasNoAuthorization($request) {
        return !$request->headers->has('Authorization');
    }

    public function hasNoBasicAuth($request) {
        return (($this->params->get("app_username") !== $request->getUser()) || ($this->params->get("app_password") !== $request->getPassword()));
    }

    public function isFormDataInvalid($request): \stdClass {

        $object = new \stdClass();
        $object->element = "";
        $object->error = false;
        $object->name = $request->request->get("name");
        $object->email = $request->request->get("email");
        $object->password = $request->request->get("password");

        if (empty($object->name)) {
            $object->element = "name";
            $object->error = true;
        } else if (!filter_var($object->email, FILTER_VALIDATE_EMAIL)) {
            $object->element = "email";
            $object->error = true;
        } else if (empty($object->password)) {
            $object->element = "password";
            $object->error = true;
        }

        return $object;
    }

    public function emailHasBeenUsed($email) {
        $user = $this->em->getRepository(User::class)->findOneBy(["email" => $email]);
        if ($user)
            return true;
        return false;
    }

    public function saveUser($form) {
        $user = new User();
        $user->setName($form->name);
        $user->setEmail($form->email);
        $user->setPassword($form->password);
        $password = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setRoles(["ROLE_USER"]);
        $object = new \stdClass();
        $object->success = true;
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $ex) {
            $object->success = false;
            $this->logger->error($ex->getMessage());
        }
        return $object;
    }

    public function returnMessage($error, $code) {
        return new JsonResponse(["message" => $this->translator->trans($error)], $code);
    }

    public function returnMessageElement($error, $code, $element) {
        return new JsonResponse(["message" => $this->translator->trans($error, ["%element%" => $element])], $code);
    }

}
