<?php

namespace app\controllers;

use pew\lib\Session;
use pew\response\RedirectResponse;
use app\models\User;

class UsersController extends \pew\Controller
{
    /**
     * @param Session $session
     * @return array|RedirectResponse
     */
    public function login(Session $session)
    {
        if ($this->request->isPost()) {
            # try logging user in via form
            $username = $this->request->post("username");
            $password = $this->request->post("password");

            $user = User::findOneByUsername($username);

            # user does not exist
            if (!$user || !$user->login($password)) {
                $session->addFlash("ko", "Invalid username or password");
                return $this->redirect("/login");
            }

            $session->set(USER_KEY, $user->id);

            # send a cookie for long-term state
            if ($this->request->post("remember_me")) {
                $thirty_days = 60 * 60 * 24 * 30;
                setcookie(SESSION_KEY, $user->id, time() + $thirty_days, "/", null, false, true);
            }

            return $this->redirect("/");
        }

        return [];
    }

    /**
     * @param Session $session
     * @return RedirectResponse
     */
    public function logout(Session $session)
    {
        # clear the logged-in status
        $session->remove(USER_KEY);
        # clear any long-term cookies
        setcookie(SESSION_KEY, false, 1, "/", null, false, true);
        session_destroy();

        return $this->redirect("/");
    }

    /**
     * @param Session $session
     * @return array|RedirectResponse
     */
    public function signup(Session $session)
    {
        if ($this->request->isPost()) {
            $username = $this->request->post("username");

            # check for a valid username
            if (!preg_match("/[A-Za-z\_][A-Za-z\_]{4,20}/", $username)) {
                $session->addFlash("ko", "Please select a valid username");
                return $this->redirect("/signup");
            }

            # check both passwords match
            if ($this->request->post("password") !== $this->request->post("password_confirm")) {
                $session->addFlash("ko", "The passwords must match");
                return $this->redirect("/signup");
            }

            # ensure the password has a minumum length
            if (strlen($this->request->post("password")) < 6) {
                $session->addFlash("ko", "Your password is too short");
                return $this->redirect("/signup");
            }

            # check the username is not taken
            if ($usernameExists = User::findOneByUsername($username)) {
                $session->addFlash("ko", "Please select a valid username");

                return $this->redirect("/signup");
            }

            # has the password
            $password = password_hash($this->request->post("password"), PASSWORD_DEFAULT);

            # create the user
            $user = User::fromArray([
                    "username" => $username,
                    "password" => $password,
                    "email" => $this->request->post("email"),
                ]);
            $user->save();

            $session->addFlash("ok", "Account created successfully");

            return $this->redirect("/login");
        }

        return [];
    }
}
