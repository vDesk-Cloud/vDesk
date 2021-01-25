<?php use vDesk\Pages\Functions; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>vDesk - Virtual Desktop</title>
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
    <header>
        <h1><a href="<?= Functions::URL("vDesk", "Index") ?>">v<span style="color: #2AB0ED">D</span>esk</a></h1>
        <nav>
            <?php foreach($Pages as $ExistingPage): ?>
                <?php if($ExistingPage->Name !== "Index"): ?>
                    <a class="<?= $ExistingPage->Name === $Current->Name ? "Current" : "" ?>"
                       href="<?= Functions::URL("vDesk", "Page", $ExistingPage->Name) ?>"><?= $ExistingPage->Description ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <a href="<?= Functions::URL("Documentation", "Index") ?>">Documentation</a>
            <a href="https://www.github.com/vDesk-Cloud">Github</a>
        </nav>
    </header>
    <?= Functions::Template("vDesk/{$Current->Name}", $Current->Values->ToArray()) ?>
    <footer>
        Copyright © 2020 Kerry Holz
        <aside>This website uses icons from <a target="_blank" href="https://www.icons8.com">icons8.com</a></aside>
    </footer>
</article>
</body>
</html>