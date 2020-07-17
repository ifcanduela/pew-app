<?php

namespace app\middleware;

use pew\request\Middleware;
use pew\request\Request;

class ReturnJson extends Middleware
{
    public function before(Request $r)
    {
        $r->forceJsonResponse();
    }
}
