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
            <li><a href="<?php url('manual/01_obligatory,_albeit_brief,_introduction'); ?>">01 Obligatory, albeit brief, introduction</a></li>
            <li><a href="<?php url('manual/02_definitions_and_conventions'); ?>">02 Definitions and conventions</a></li>
            <li><a href="<?php url('manual/03_configure_a_site'); ?>">03 Configure a site</a></li>
            <li><a href="<?php url('manual/04_a_simple_example'); ?>">04 A simple example</a></li>
            <li><a href="<?php url('manual/05_the_url_parameters'); ?>">05 The URL parameters</a></li>
            <li><a href="<?php url('manual/06_controllers_and_actions'); ?>">08 Controllers and actions</a></li>
            <li><a href="<?php url('manual/07_views,_elements_and_layouts'); ?>">07 Views, elements and layouts</a></li>
            <li><a href="<?php url('manual/08_models_and_database_access'); ?>">08 Models and database access</a></li>
            <li><a href="<?php url('manual/09_sessions_and_authentication'); ?>">09 Sessions and authentication</a></li>
            <li><a href="<?php url('manual/10_some_introspection'); ?>">10 Some introspection</a></li>
            <li><a href="<?php url('manual/11_about_pew_pew_pew'); ?>">11 About Pew-Pew-Pew</a></li>
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
