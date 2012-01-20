<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->title; ?></title>
    <link rel="stylesheet" href="<?php www('/css/default.css'); ?>" />
    <script type="text/javascript" src="<?php www('js/jquery.js'); ?>"></script>
</head>

<body>
    
    <div id="header">
        <h1>Pew-Pew-Pew</h1>
    </div>

    <div id="main">
    
    <?php echo $this->output; ?>
    
    </div>
    
    <div id="footer"><p>&copy; 2011 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    <?php $this->element('debug'); ?></div>

</body>

</html>
