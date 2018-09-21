<?php

namespace app\models;

class User extends \pew\Model
{
    public $tableName = "users";

    /**
     * Check the credentials of a user.
     *
     * @param  string $password
     * @return bool
     */
    public function login(string $password)
    {
        # provided password does not match database password
        if (!password_verify($password, $this->password)) {
            return false;
        }

        # password was hashed with an old algorithm
        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->password = password_hash($this->request->post("password"));
            $this->save();
        }

        return true;
    }
}
