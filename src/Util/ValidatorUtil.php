<?php

namespace App\Util;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Description of RegisterValidator
 *
 * @author Gaspar Teixeira <gaspar.teixeira@gmail.com>
 */
class ValidatorUtil {

    private $logger;
    private $params;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $params) {
        $this->logger = $logger;
        $this->params = $params;
    }

    public function hasAuthorization(Request $request): bool {
        return $request->headers->has("Authorization");
    }

    public function isAuthenticated(Request $request): bool {
        return ($this->params->get("app_username") === $request->getUser()) && ($this->params->get("app_password") === $request->getPassword());
    }

    public function isFormDataValid(Request $request): \stdClass {
        $object = new \stdClass();
        $object->isValid = true;
        $object->element = "";

        $name = $request->request->get("name");
        $email = $request->request->get("email");
        $password = $request->request->get("password");

        if (empty($name)) {
            $object->isValid = false;
            $object->element = "name";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $object->isValid = false;
            $object->element = "email";
        } else if (empty($password)) {
            $object->isValid = false;
            $object->element = "password";
        }
        return $object;
    }

}
