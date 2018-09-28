<?php

namespace app\models;

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
    public function login(string $password)
    {
        # provided password does not match database password
        if (!password_verify($password, $this->password)) {
            return false;
        }

        # check if password was hashed with an old algorithm
        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->password = password_hash($password);
            $this->save();
        }

        return true;
    }
}
