<?php

namespace app\models;

use pew\lib\Session;

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

    protected function login(Session $session, bool $rememberMe = false): string
    {
        $session->set(USER_KEY, $user->id);
        $login_token = bin2hex(random_bytes(16));

        if ($rememberMe) {
            $this->login_token = $login_token;
            $this->save();
        }

        return $login_token;
    }

    /**
     * Generate a random token for the "Remember Me" functionbality.
     *
     * @return string
     */
    public function generateLoginToken()
    {
        $this->login_token = bin2hex(random_bytes(16));
        $this->save();

        return $this->login_token;
    }

    public static function loginWithToken(Session $session)
    {
        $request = pew("request");
        $loginToken = $request->cookies->get(SESSION_KEY);

        $user = static::findOneByLoginToken($loginToken);

        if ($user) {
            $user->login($session, false);
        }
    }
}
