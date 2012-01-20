<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->title; ?></title>
    <link rel="stylesheet" href="<?php url('www/css/simple-reset.css'); ?>" />
    <link rel="stylesheet" href="<?php url('www/css/default.css'); ?>" />
    <script type="text/javascript" src="<?php url('www/js/jquery.js'); ?>"></script>
</head>

<body>
    
    <div id="header">
        <h1>Pew-Pew-Pew</h1>
    </div>

    <div id="tabs">
        <ul id="nav">
            <?php
            $pages = glob(dirname(__FILE__) . DS . 'pages' . DS . '*.php');
            
            foreach ($pages as $page):
                $page = str_replace('.php', '', basename($page));
                if ($page == 'index')
                    continue;
                $title = ucwords(str_replace('_', ' ', $page));
            ?>
            <li><a href="<?php url("pages/$page"); ?>"><?php echo $title; ?></a></li>
            <?php
            endforeach;
            ?>
        </ul>

        <div>
            <?php echo $this->output; ?>
        </div>

        <span style="clear:both;"></span>
    </div>
    
    <div id="footer"><p>&copy; 2011 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    <?php $this->element('debug'); ?></div>
</body>
</html>
