<?php

use vDesk\Pages\Functions;

/** @var \Pages\Reflect $Page */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?= $Page->Documentation->Name ?></title>
    <link rel="stylesheet" href="Stylesheet.css">
    <script src="Search.js"></script>
</head>
<body>
<article class="Page">
    <header class="Index">
        <h1><a href="index.html">Index</a></h1>
        <label>
            Search:
            <input type="text" oninput="Search(this.value)">
        </label>
        <?= $Page->Index ?>
    </header>
    <?= $Page->Documentation ?>
</article>
</body>
</html>