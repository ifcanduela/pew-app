<?php

/**
 * @var \pew\View $this
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="<?= url() ?>">

    <title><?= app_title($this->title()) ?></title>
    <link rel="stylesheet" href="<?= url("assets/css/app.css") ?>">
    <?= $this->block("styles") ?>
</head>

<body class="<?= pew("controller_slug") ?>--<?= pew("action_slug") ?>">
    <header class="main-menu">
        <div>
            <a class="app-title" href="<?= url() ?>"><?= pew("app_title") ?></a>
            <a href="<?= url() ?>">Home</a>
        </div>

        <div>
            <?php if (pew("user")): ?>
                <a href="<?= url("logout") ?>">Log out</a>
            <?php else: ?>
                <a href="<?= url("login") ?>">Log in</a>
                <a href="<?= url("signup") ?>">Sign up</a>
            <?php endif ?>
        </div>
    </header>

    <div id="main">
        <?php foreach (flash() as $key => $value): ?>
            <p class="alert <?= $key ?>">
                <?= $value ?>
            </p>
        <?php endforeach ?>

        <?= $this->content() ?>
    </div>

    <footer>
        <p>Powered by <a href="https://github.com/ifcanduela/pew">Pew-Pew-Pew</a></p>
    </footer>

    <script src="<?= url("assets/js/app.js") ?>"></script>
    <?= $this->block("scripts") ?>
</body>
</html>
