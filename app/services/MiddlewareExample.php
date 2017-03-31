<?php

namespace app\services;

class MiddlewareExample extends \pew\request\Middleware
{
    /**
     * Action to be performed before the request handler is invoked.
     *
     * Parameters will be injected as requested.
     *
     * Returning a Symfony Response will short-circuit the request handler and
     * the 'after' middleware.
     *
     * @return Symfony\Component\HttpFoundation\Response|null
     */
    public function before($currentUser)
    {
        return new \Symfony\Component\HttpFoundation\RedirectResponse('/login');
    }

    /**
     * Action to be performed after the request handler is invoked.
     *
     * Parameters will be injected as requested.
     *
     * Returning a Symfony Response will short-circuit the pending 'after'
     * middleware.
     *
     * @return Symfony\Component\HttpFoundation\Response|null
     */
    public function after()
    {

    }
}
