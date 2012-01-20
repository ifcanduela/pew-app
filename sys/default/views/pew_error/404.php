<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <title>Pew Error :: <?php echo $error_title; ?></title>
    <style type="text/css">
        html {
            background-color:white;
            margin:1em;
            border:1px dashed #999;
        }
        body {
            background-color:#f6f6f6;
            margin:0;
            padding:0;
            font:normal 16px/165% 'trebuchet ms', helvetica, sans-serif;
        }
        h1 {
            background-color:#fff;
            border-bottom:1px solid #999;
            margin:0;
            padding:0.4em;
            color:#643;
        }
        p {
            margin:0;
            padding:2em;
        }
    </style>
</head>

<body>

    <h1>The Error Four Oh Four</h1>
    
    <p>Sorry, the page you were looking for does not exist. Try one of the following:</p>
    
    <ul>
        <li><a href="<?php url(Pew::Get('App')->url); ?>">Try again</a>: If you think it might help.</li>
        <li><a href="<?php url(); ?>">Go home</a>: And start from the beginning.</li>
    </ul>
    
    <p>In compensation, a picture of a cat.</p>
    
    <img src="http://cats.jpg.to" alt="This cat">

</body>
</html>
