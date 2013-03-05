<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <base href="<?php url() ?>">
    <title><?= $title; ?></title>
    <link rel="shortcut icon" href="<?php url('favicon.png'); ?>">
    <link rel="stylesheet" href="<?php www('css/reset.css'); ?>">
    <link rel="stylesheet" href="<?php www('css/styles.css'); ?>">
    <script type="text/javascript" src="<?php www('js/jquery.js'); ?>"></script>
</head>

<body id="<?= $this->request->controller; ?>" class="<?= $this->request->action; ?>">
    
    <header>
        <h1>Pew-Pew-Pew</h1>
    </header>

    <div id="main">
        <?= $output; ?>
    </div>

    <footer>
        <p>&copy; 2011 ifcanduela | Powered by Pew-Pew-Pew <?= VERSION; ?></p>
    </footer>
    
    <?php $this->element('debug'); ?>
</body>
</html>
