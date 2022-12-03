<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation $Page */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $Page->Content?->Title ?? "vDesk - Documentation" ?></title>
    <link rel="icon" href="<?= Functions::Image("favicon.ico") ?>" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $Page->Content?->Description ?? "" ?>">
<?php foreach($Page->Stylesheets as $Stylesheet): ?>
    <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
<?php endforeach; ?>
<?php foreach($Page->Content->Stylesheets as $Stylesheet): ?>
    <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
<?php endforeach; ?>
<?php foreach($Page->Scripts as $Script): ?>
    <script src="<?= Functions::Script($Script) ?>"></script>
<?php endforeach; ?>
<?php foreach($Page->Content->Scripts as $Script): ?>
    <script src="<?= Functions::Script($Script) ?>"></script>
<?php endforeach; ?>
</head>
<body>
<main>
    <header>
        <h1>
            <a href="<?= Functions::URL("vDesk", "Index") ?>">v<span style="color: #2AB0ED">D</span>esk</a>\<a href="<?= Functions::URL("Documentation", "Index") ?>"><span style="color: #2AB0ED">D</span>ocumentation</a>
        </h1>
        <button class="Toggle" onclick="this.nextElementSibling.classList.toggle('Hidden');">☰</button>
        <nav class="Hidden">
<?php foreach($Page->Pages as $ExistingPage): ?>
<?php if($ExistingPage->Name !== "Index"): ?>
            <a class="<?= $ExistingPage->Name === $Page->Content->Name ? "Current" : "" ?>" href="<?= Functions::URL("Documentation", "Topic", $ExistingPage->Name) ?>"><?= $ExistingPage->Label ?></a>
<?php endif; ?>
<?php endforeach; ?>
            <a href="https://www.github.com/vDesk-Cloud">Github</a>
        </nav>
    </header>
    <?= $Page->Content ?>
    
    <footer>
        Copyright © 2022 Kerry Holz
        <aside>This website uses icons from <a target="_blank" href="https://www.icons8.com">icons8.com</a></aside>
    </footer>
</main>
</body>
</html>