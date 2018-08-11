<?php

namespace app\models;

class User extends \pew\Model
{
    public $tableName = "users";

    public function login($password)
    {
        # provided password does not match database password
        if (!password_verify($this->request->post("password"), $this->password)) {
            $session->addFlash("ko", "Invalid username or password");
            return $this->redirect("login");
        }

        # password was hashed with an old algorithm
        if (password_needs_rehash($this->password, PASSWORD_DEFAULT)) {
            $this->password = password_hash($this->request->post("password"));
            $this->save();
        }

    }
}
