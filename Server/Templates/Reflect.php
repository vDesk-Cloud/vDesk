<?php
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
<main>
    <header class="Index">
        <h1><a href="index.html">Index</a></h1>
        <label for="Search">Search:</label>
        <input id="Search" type="text" oninput="Search(this.value)">
        <?= $Page->Index ?>
    </header>
    <?= $Page->Documentation ?>
</main>
</body>
</html>