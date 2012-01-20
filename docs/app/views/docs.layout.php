<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
	<meta name="description" content="Pew-Pew-Pew PHP framework documentation">
	<meta name="author" content="ifcanduela">
	<title><?php echo $this->title; ?></title>
    <link rel="shortcut icon" href="<?php url('favicon.png'); ?>">
        <link rel="stylesheet" href="<?php www('sh/styles/shCore.css'); ?>" media="screen">
        <link rel="stylesheet" href="<?php www('sh/styles/shThemeDefault.css'); ?>" media="screen">
    <link rel="stylesheet" href="<?php www('css/styles.css'); ?>" media="screen">
    <link rel="stylesheet" href="<?php www('css/print.css'); ?>" media="print">
    <script src="<?php www('js/jquery.js'); ?>"></script>
        <script src="<?php www('sh/scripts/shCore.js'); ?>"></script>
        <script src="<?php www('sh/scripts/shBrushCpp.js'); ?>"></script>
        <script src="<?php www('sh/scripts/shBrushSql.js'); ?>"></script>
        <script src="<?php www('sh/scripts/shBrushPhp2.js'); ?>"></script>
        <script src="<?php www('sh/scripts/shBrushXml.js'); ?>"></script>
</head>

<body>
    
    <header>
        Pew-Pew-Pew
    </header>
	
    <nav id="nav">
        <a class="book 
            <?php if ($this->parameters['controller'] === 'book'): ?>
                active
            <?php endif; ?>"
            href="<?php url('book'); ?>">The Book of Pew&trade;</a>
        <a class="tutorial
            <?php if ($this->parameters['controller'] === 'tutorial'): ?>
                active
            <?php endif; ?>"
            href="<?php url('tutorial'); ?>">Standard Blog Tutorial</a>
        <a class="reference
            <?php if ($this->parameters['controller'] === 'reference'): ?>
                active
            <?php endif; ?>"
            href="<?php url('reference'); ?>">Reference and Fundamentals</a>
    </nav>
	
	<?php echo $this->element('sidebar'); ?>
	
	<div id="main">
		<?php echo $this->output; ?>
	
		<?php $this->element('nav'); ?>	
	</div>
    
    <footer>
        <p><!-- &copy; -->2011 ifcanduela | Powered by Pew-Pew-Pew <?php echo VERSION; ?></p>
    </footer>
    
    <?php if (DEBUG): ?>
    <div id="debug-popup">
        <?php $this->element('debug'); ?>
    </div>
    <?php endif; ?>

	<script type="text/javascript">
		SyntaxHighlighter.all();
		
		$('h2, h3').append('<span class="heading-block"></span>')
	</script>
</html>
