<?php

return [
    
    'app_title' => 'Pew-Pew-Pew',

    'env' => 'dev',

    # closures will receive the injection container as argument
    'currentUser' => function ($c) {
        if (isset($c['session']['user'])) {
            return User::findOneById($c['session']['user.id']);
        }

        return false;
    },
];
