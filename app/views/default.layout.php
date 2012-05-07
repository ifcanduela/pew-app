<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->title; ?></title>
    <link rel="stylesheet" href="<?php www('/css/default.css'); ?>" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>

<body>
    
    <div id="header">
        <h1>Pew-Pew-Pew</h1>
    </div>

    <div id="main">
    
    <?= $output; ?>
    
    </div>
    
    <div id="footer"><p>&copy; 2011 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    <?php $this->element('debug'); ?></div>

</body>

</html>
