<?php

use vDesk\Pages\Functions;

?>
<!DOCTYPE html>
<html>
<head>
    <title>404 - Not found</title>
    <style>
        html {
            font-family: Arial;
        }
    </style>
</head>
<body>
<article class="error"
         style="width: 60%; margin-left: 20%; text-align: center;">
    <header style="text-align: center;">
        <h1>404 - Not found</h1>
    </header>
    <section class="content" style="text-align: center;">
        <h3>Looks like we've taken a wrong turn.</h3>
        <a href="<?= Functions::URL() ?>">Get back?</a>
    </section>
</article>
</body>
</html>