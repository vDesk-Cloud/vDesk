<?php

use vDesk\Configuration\Settings;
use vDesk\IO\Path;

?>
<!DOCTYPE html>
<html>
<head>
    <title>vDesk\<?= $Title ?> MVC framework</title>
    <link rel="icon" href="http://localhost/vDesk-dev/Server/favicon.ico" type="image/x-icon">
    <style>
        article {
            font-family: Arial, serif;
            color: black;
        }

        pre {
            font-family: Courier New, monospace;
        }
    </style>
</head>
<body>
<article style="text-align: center;">
    <header>
        <h1><?= $Message ?>!</h1>
        <h3>Welcome to v<span style="color: #2AB0ED">D</span>esk\<?= $Title ?>!</h3>
    </header>
    <section>
        <p>This is just a default view shipped with the Package.
            <br>The Module serving this Page is located at:</p>
        <pre><?= \Server . Path::Separator . "Modules" . Path::Separator . "Pages.php" ?></pre>
        <p>The Page file is located at:</p>
        <pre><?= Settings::$Local["Pages"]["Pages"] . Path::Separator ?>Pages.php</pre>
        <p>The template file is located at:</p>
        <pre><?= Settings::$Local["Pages"]["Templates"] . Path::Separator ?>Pages.php</pre>
    </section>
    <footer>
        For help contact the author <a href="mailto:<?= $Author ?>"><?= $Author ?></a>
    </footer>
</article>
</body>
</html>