<?php

namespace App\Model;

use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

/**
 * Users management.
 */
class UserManager extends Object implements IAuthenticator {

    /**
     * @var
     */
    private $userName;
    /**
     * @var
     */
    private $userPassword;

    public function __construct($userName, $userPassword) {

        $this->userName = $userName;
        $this->userPassword = $userPassword;
    }

    /**
     * Performs an authentication.
     * @param array $credentials
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials;

        if ($username!= $this->userName) {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, Passwords::hash($this->userPassword))) {
            throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        }
        return new Identity($this->userName, "admin");
    }

}

class DuplicateNameException extends \Exception {

}
