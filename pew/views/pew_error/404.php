<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title>404 @ <?php echo APPLICATION_TITLE; ?></title>
    <style type="text/css">
        body {
            background: #b7d478 url(<?php url('www/img/pew-bg-cut.png') ?>) no-repeat bottom left;
            background-attachment: fixed;
            border-top: 25px solid #333;
            color: #343;
            margin: 0;
            padding: 0;
            font: normal 16px/165% 'trebuchet ms', helvetica, sans-serif;
        }
        div {
            margin-left: 50%;
            opacity: .9;
        }
        img {
            border-radius: 2px;
            border: 10px solid white;
            box-shadow: rgba(0, 0, 0, 0.5) 1px 1px 5px;
        }
        a {
            color: #28c;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div>
        <h1>The Error Four Oh Four</h1>
        
        <p>Sorry, the page you were looking for does not exist. Try one of the following:</p>
        
        <ul>
            <li><a href="<?php url(Pew::Get('App')->url); ?>">Try again</a>: If you think it might help.</li>
            <li><a href="<?php url(); ?>">Go home</a>: And start from the beginning.</li>
        </ul>
        
        <p>In compensation, a picture of a cat:</p>
        
        <?php $cats = array('cat', 'cats', 'kitten', 'kittens', 'kitteh');
              $cat = $cats[array_rand($cats)]; ?>
        
        <img src="http://<?= $cat ?>.jpg.to" alt="This cat">
    </div>
</body>
</html>
