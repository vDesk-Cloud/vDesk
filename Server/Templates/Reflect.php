<?php

use vDesk\Pages\Functions;
/** @var \Pages\Reflect $Page */
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $Page->Documentation->Name ?></title>
    <link rel="icon" href="<?= Functions::Image("favicon.ico") ?>" type="image/x-icon">
    <?php foreach($Page->Stylesheets as $Stylesheet): ?>
        <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
    <?php endforeach; ?>
    <?php foreach($Page->Scripts as $Script): ?>
        <script src="<?= Functions::Script($Script) ?>"></script>
    <?php endforeach; ?>
</head>
<body>
<article class="Page">
    <header class="Index">
        <h1>Index</h1>
        <?= $Page->Index ?>
    </header>
    <?= $Page->Documentation ?>
</article>
</body>
</html>