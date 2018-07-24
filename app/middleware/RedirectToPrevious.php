<?php

namespace app\middleware;

use pew\libs\Session;
use pew\request\Middleware;

class RedirectToPrevious extends Middleware
{
    public function before(Session $session)
    {
        if ($session->has("return_to")) {
            $returnTo = $session->get("return_to");
            $session->remove("return_to");

            return $this->redirect($returnTo);
        }
    }
}
