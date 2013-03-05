<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <base href="<?= APP_URL ?>">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="www/css/default.css">
</head>

<body>
    
    <div id="header">
        <h1><?= $title ?></h1>
    </div>

    <div id="main">
    
    <?= $output; ?>
    
    </div>
    
    <div id="footer">
    	<p>&copy; 2012 ifcanduela | 
    		Powered by <a href="https://github.com/ifcanduela/Pew-Pew-Pew">Pew-Pew-Pew</a></p>
	</div>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="www/js/app.js"></script>
</body>

</html>
