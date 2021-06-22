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

    public function login(Session $session, bool $rememberMe = false)
    {
        $this->refreshSession();

        if ($rememberMe) {
            $this->refreshCookie();
        }

        return;
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
     * Refresh the session data.
     */
    public function refreshSession()
    {
        pew("session")->set("user_id", $this->id);
    }

    /**
     * Refresh the login cookie.
     */
    public function refreshCookie()
    {
        setcookie(SESSION_KEY, $this->generateLoginToken(), time() + 60 * 60 * 24 * 30, "/", null, false, true);
    }

    /**
     * Login a user using a cookie token.
     */
    public static function loginWithToken(string $token)
    {
        /** @var User $user */
        $user = static::find()->where(["token" => $token])->one();

        if (!$user) {
            return null;
        }

        $user->refreshSession();

        return $user;
    }
}
