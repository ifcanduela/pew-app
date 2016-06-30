<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Error</title>
</head>

<body>
    <h1>HTTP/404 NOT FOUND</h1>

    <p>The page was not found</p>

    <?php if (isset($error) || isset($exception)): ?>
        <blockquote cite="http://">
            <p>Try feeding this to a dolphin:</p>

            <pre>
                <?php if (isset($error)): ?>
                    <?= $error ?>
                <?php else: ?>
                    <?= $exception->getMessage() ?>
                <?php endif ?>
            </pre>
        </blockquote>
    <?php endif ?>
</body>
</html>
