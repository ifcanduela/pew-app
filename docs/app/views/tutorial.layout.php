<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->title; ?></title>
    <link rel="stylesheet" href="<?php url('www/css/default.css'); ?>" />
    <link rel="stylesheet" href="<?php url('www/js/sh/styles/shCore.css'); ?>">
    <link rel="stylesheet" href="<?php url('www/js/sh/styles/shThemePew.css'); ?>">
    <script type="text/javascript" src="<?php url('www/js/jquery.js'); ?>"></script>
    <script type="text/javascript" src="<?php url('www/js/sh/scripts/shCore.js'); ?>"></script>
    <script type="text/javascript" src="<?php url('www/js/sh/scripts/shBrushPhp2.js'); ?>"></script>
    <script type="text/javascript" src="<?php url('www/js/sh/scripts/shBrushJscript.js'); ?>"></script>
    <script type="text/javascript" src="<?php url('www/js/sh/scripts/shBrushXml.js'); ?>"></script>
    <script type="text/javascript" src="<?php url('www/js/sh/scripts/shBrushSql.js'); ?>"></script>
</head>

<body>
    
    <div id="header">
        <h1>Pew-Pew-Pew</h1>
    </div>

    <div id="tabs">

        <ul id="nav">
            <li><a href="<?php url('tutorial/01'); ?>">Setting things app</a></li>
            <li><a href="<?php url('tutorial/02'); ?>">Tables and tables of data</a></li>
            <li><a href="<?php url('tutorial/03'); ?>">A controller</a></li>
            <li><a href="<?php url('tutorial/04'); ?>">Nice view</a></li>
            <li><a href="<?php url('tutorial/05'); ?>">Dealing with regret</a></li>
            <li><a href="<?php url('tutorial/06'); ?>">All of it</a></li>
            <li><a href="<?php url('tutorial/07'); ?>">Cheap manpower</a></li>
            <li><a href="<?php url('tutorial/08'); ?>">About you</a></li>
            <li><a href="<?php url('tutorial/09'); ?>">Modeling the app</a></li>
            <li><a href="<?php url('tutorial/10'); ?>">What do you think about [topic]?</a></li>
        </ul>
        
        <div>
            <?php echo $this->output; ?>
        </div>
        
        <span style="clear:both;"></span>
    </div>
    
    <div id="footer"><p>&copy; 2011 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    <?php $this->element('debug'); ?></div>
</body>
<script type="text/javascript">
    SyntaxHighlighter.all();
</script>
</html>
