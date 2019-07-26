<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Error</title>
    <style>
        body {
            margin: 0;
            padding: 50px;
            font-family: sans-serif;
        }

        h1 {
            margin-top: 0;
        }

        blockquote {
            background-color: #ffe;
            margin: 1em 0;
            padding: 2em;
        }

        code {
            background-color: rgba(230, 240, 230, 0.5);
            padding: 2px 4px;
        }
    </style>
</head>

<body>
    <h1>HTTP/404 NOT FOUND</h1>

    <?php if (isset($error) || isset($exception)): ?>
        <details>
            <summary>The page was not found</summary>

            <p>Try feeding this to a dolphin:</p>

            <p><code><?= isset($error) ? $error : $exception->getmessage() ?></code></p>
        </details>
    <?php endif ?>
</body>
</html>
