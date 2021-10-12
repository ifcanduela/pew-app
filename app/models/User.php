<?php

namespace app\models;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * @prop string $username
 * @prop string $password
 * @prop string $email
 * @prop string $login_token
 * @prop int $created
 * @prop int $updated
 */
class User extends \pew\Model
{
    public $tableName = "users";

    /**
     * Check the credentials of a user.
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword(string $password)
    {
        # provided password does not match database password
        if (!password_verify($password, $this->password)) {
            return false;
        }

        # check if the password was hashed with an old algorithm
        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->password = password_hash($password, PASSWORD_DEFAULT);
            $this->save();
        }

        return true;
    }

    /**
     * Generate a random token for the "Remember Me" functionality.
     *
     * @return string
     */
    public function generateLoginToken()
    {
        $this->login_token = bin2hex(random_bytes(16));
        $this->save();

        return $this->login_token;
    }

    /**
     * Refresh the login cookie.
     */
    public function getRememberCookie(): Cookie
    {
        $token = $this->generateLoginToken();
        $cookie = Cookie::create(COOKIE_KEY)->withValue($token)
            ->withExpires(time() + 60 * 60 * 24 * 30)
            ->withSecure(true);

        return $cookie;
    }
}
