<?php

namespace App\Model;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\SmartObject;

/**
 * Users management.
 */
class UserManager implements IAuthenticator
{

    use SmartObject;

    /**
     * @var
     */
    private $userName;
    /**
     * @var
     */
    private $userPassword;
    /**
     * @var Passwords
     */
    private $passwords;

    public function __construct(Passwords $passwords)
    {
        $this->passwords = $passwords;
    }

    public function init($userName, $password)
    {
        $this->userName = $userName;
        $this->userPassword = $password;
    }

    function authenticate(array $credentials): IIdentity
    {
        list($username, $password) = $credentials;
        if ($username != $this->userName) {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!$this->passwords->verify($password, $this->passwords->hash($this->userPassword))) {
            throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        }
        return new Identity($this->userName, "admin");
    }
}
