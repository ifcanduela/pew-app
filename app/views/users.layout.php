<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?= app_title($this->title()) ?></title>
    <link rel="stylesheet" href="<?= url("assets/css/app.bundle.css") ?>">

    <?= $this->block("styles") ?>
</head>

<body class="users-layout <?= pew("controller_slug") ?>--<?= pew("action") ?>">
    <div>
        <?php foreach (flash() as $key => $value): ?>
            <p class="alert <?= $key ?>">
                <?= $value ?>
            </p>
        <?php endforeach ?>

        <?= $this->child() ?>
    </div>

    <script src="<?= url("assets/js/app.bundle.js") ?>"></script>
    <?= $this->block("scripts") ?>
</body>
</html>
