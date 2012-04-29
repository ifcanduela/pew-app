<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->title; ?></title>
    <link rel="shortcut icon" href="<?php url('favicon.png'); ?>">
    <link rel="stylesheet" href="<?php www('css/reset.css'); ?>">
    <link rel="stylesheet" href="<?php www('css/styles.css'); ?>">
    <script type="text/javascript" src="<?php www('js/jquery.js'); ?>"></script>
</head>

<body id="<?php echo $this->request->controller; ?>" class="<?php echo $this->request->action; ?>">
    
    <header>
        <h1>Pew-Pew-Pew</h1>
    </header>

    <div id="main">
        <?php echo $this->output; ?>
    </div>

    <footer>
        <p>&copy; 2011 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    </footer>
    
    <?php $this->element('debug'); ?>
</body>
</html>
