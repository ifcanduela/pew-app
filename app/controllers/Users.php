<?php

namespace app\controllers;

use app\models\User;

class Users extends \pew\Controller
{
    public function login($session)
    {
        if ($this->request->isPost()) {
            $username = $this->request->post('username');

            $user = User::findOneByUsername($username);

            if (!$user) {
                $session->addFlash('ko', 'Invalid username or password');
                return $this->redirect('login');
            }

            if (!password_verify($this->request->post('password'), $user->password)) {
                $session->addFlash('ko', 'Invalid username or password');
                return $this->redirect('login');
            }

            $session['user'] = $user->attributes();

            if ($this->request->post('remember_me')) {
                $thirty_days = 60 * 60 * 24 * 30;
                setcookie(SESSION_KEY, $user->id, time() + $thirty_days, '/', null, false, true);
            }

            return $this->redirect('/');
        }

        return [];
    }

    public function logout($session)
    {
        unset($session['user']);
        setcookie(SESSION_KEY, false, 1, '/', null, false, true);
        session_destroy();
        
        return $this->redirect('/');
    }

    public function signup($session)
    {
        if ($this->request->isPost()) {
            $username = $this->request->post('username');

            if (!preg_match('/[A-Za-z\_][A-Za-z\_]{4,20}/', $username)) {
                $session->addFlash('ko', 'Please select a valid username');
                return $this->redirect('signup');
            }

            if ($this->request->post('password') !== $this->request->post('password_confirm')) {
                $session->addFlash('ko', 'The passwords must match');
                return $this->redirect('signup');
            }

            if (strlen($this->request->post('password')) < 6) {
                $session->addFlash('ko', 'Your password is too short');
                return $this->redirect('signup');
            }

            if ($usernameExists = User::findOneByUsername($username)) {
                $session->addFlash('ko', 'Please select a valid username');
                return $this->redirect('signup');
            }

            $password = password_hash($this->request->post('password'), PASSWORD_DEFAULT);

            $user = User::fromArray([
                    'username' => $username,
                    'password' => $password,
                    'email' => $this->request->post('email'),
                ])->save();

            $session->addFlash('ok', 'Account created successfully');

            return $this->redirect('login');
        }
    }
}
