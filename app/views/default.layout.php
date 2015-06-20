<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <base href="<?= url() ?>">
    <title><?= $title = $this->title() ?> | <?= pew('app_title') ?></title>

    <link rel="stylesheet" href="<?= www('css/default.css') ?>">

    <!-- <script src="http://code.jquery.com/jquery.min.js"></script> -->

    <!--[if lt IE 9]>
        <script src="//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="push">
        <header>
            <div class="menu">
                <a href="<?= url() ?>">Home</a>
                | <a href="<?= url('welcome') ?>">Welcome</a>
            </div>

            <h1><a href="<?= url() ?>"><?= pew('app_title') ?></a></h1>
        </header>

        <div id="main">
            <?php foreach (flash() as $key => $value): ?>
                <p class="alert <?= $key ?>">
                    <?= $value ?>
                </p>
            <?php endforeach ?>

            <?= $this->child() ?>
        </div>
    </div>

    <footer class="sticky">
        <p>Powered by <a href="https://github.com/ifcanduela/pew">Pew-Pew-Pew</a></p>
    </footer>

    <script src="<?= www('js/app.js') ?>"></script>

    <?= $this->block('scripts') ?>
</body>
</html>
