<?php

namespace app\controllers;

class Users extends \pew\Controller
{
    public function login()
    {
        if ($this->request->is_post()) {
            $username = $this->request->post('username');

            $user = $this->model->find_by_username($username);

            if (!$user) {
                $this->session->set_flash('ko', 'Invalid username or password');
                redirect('users/login');
            }

            if (!password_verify($this->request->post('password'), $user->password)) {
                $this->session->set_flash('ko', 'Invalid username or password');
                redirect('users/login');
            }

            $this->session->user = $username;

            if ($this->request->post('remember_me')) {
                $thirty_days = 60 * 60 * 24 * 30;
                setcookie(SESSION_KEY, $user->uid(), time() + $thirty_days, '/', null, false, true);
            }

            redirect('');
        }

        return [];
    }

    public function logout()
    {
        $this->session->delete('user');
        setcookie(SESSION_KEY, false, 1, '/', null, false, true);
        session_destroy();
        redirect('');
    }

    public function signup()
    {
        if ($this->request->is_post()) {

            $username = $this->request->post('username');

            if (!preg_match('/[A-Za-z\_][A-Za-z\_]{4,20}/', $username)) {
                $this->session->set_flash('ko', 'Please select a valid username');
                redirect('users/signup');
            }

            if ($this->request->post('password') !== $this->request->post('password_confirm')) {
                $this->session->set_flash('ko', 'The passwords must match');
                redirect('users/signup');
            }

            if (strlen($this->request->post('password')) < 6) {
                $this->session->set_flash('ko', 'Your password is too short');
                redirect('users/signup');
            }

            if ($this->model->find_by_username($username)) {
                $this->session->set_flash('ko', 'Please select a valid username');
                redirect('users/signup');
            }

            $password = password_hash($this->request->post('password'), PASSWORD_DEFAULT);

            $this->model->save([
                    'username' => $username,
                    'password' => $password,
                    'email' => $this->request->post('email'),
                    'slug' => \pew\libs\Str::slug($username),
                ]);

            $this->session->set_flash('ok', 'Account created successfully');

            redirect('users/login');
        }
    }

    public function reset_password()
    {

    }

    public function change_password()
    {

    }

    public function delete_account()
    {

    }

    public function forgot_password($token = null)
    {
        if ($token) {
            $user = $this->model->find_by_token($token);

            if ($user) {
                $this->session->user = $user->username;
                redirect('users/change-password');
            }
        }

        if ($this->request->is_post()) {
            $email = $this->request->post('email');
            $user = $this->model->find_by_email($email);

            if ($user) {
                $token = base64_encode($user->password . $user->email);
                $user->token = $token;
                $user->save();

                $message = Swift_Message::newInstance()
                    ->setSubject('Recover your Knotes password')
                    ->setFrom(array('ifcanduela@gmail.com' => 'Igor F. Canduela'))
                    ->setTo(array($user->email))
                    ->setBody("Hello, person!\n\nHere is your friendly password recovery e-mail. Please visit the following URL in your browser and reset your password.\n\n" . url('users/forgot_password/' . $token) . "\n\nIf you did *not* request a password reset, you do *not* have to follow the link.\n\nCheers!\n- Igor")
                    ->addPart('<p>Hello, person!</p><p>Here is your friendly password recovery e-mail. Please visit <a href="' . url('users/forgot_password/' . $token) . '">' . url('users/forgot_password/' . $token) . '</a> in your browser and reset your password.</p><p>If you did <strong>not</strong> request a password reset, you do not have to follow the link.</p><p>Cheers!<br>- Igor</p>', 'text/html');

                $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
                    ->setUsername(pew('gmail_username'))
                    ->setPassword(pew('gmail_password'));

                $mailer = Swift_Mailer::newInstance($transport);

                $mailer->send($message);
            }

            $this->session->set_flash('ok', 'An e-mail was sent to the address you requested. If you do not receive it, maybe the e-mail address was invalid.');
            redirect(here());
        }

        return [];
    }
}
