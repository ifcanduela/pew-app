<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <base href="<?= APP_SERVER_URL ?>">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="www/css/default.css">
</head>

<body>
    
    <div id="header">
        <h1>Pew-Pew-Pew</h1>
    </div>

    <div id="main">
    
    <?= $output; ?>
    
    </div>
    
    <div id="footer"><p>&copy; 2012 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    <?php $this->element('debug'); ?></div>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="www/js/app.js"></script>
</body>

</html>
