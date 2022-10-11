<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation\Tutorials $Page */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>vDesk - Virtual Desktop</title>
    <link rel="icon" href="<?= Functions::Image("favicon.ico") ?>" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php foreach($Page->Stylesheets as $Stylesheet): ?>
    <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
<?php endforeach; ?>
<?php foreach($Page->Topic->Stylesheets as $Stylesheet): ?>
    <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
<?php endforeach; ?>
<?php foreach($Page->Scripts as $Script): ?>
    <script src="<?= Functions::Script($Script) ?>"></script>
<?php endforeach; ?>
<?php foreach($Page->Topic->Scripts as $Script): ?>
    <script src="<?= Functions::Script($Script) ?>"></script>
<?php endforeach; ?>
</head>
<body>
<main class="Tutorials">
    <header>
        <h1>
            <a href="<?= Functions::URL("vDesk", "Index") ?>">v<span style="color: #2AB0ED">D</span>esk</a>\<a href="<?= Functions::URL("Documentation", "Index") ?>"><span style="color: #2AB0ED">D</span>ocumentation</a>\<a href="<?= Functions::URL("Documentation", "Category", "Client", "Topic", "Index") ?>"><span style="color: #2AB0ED">C</span>lient</a>
        </h1>
        <button class="Toggle" onclick="this.nextElementSibling.classList.toggle('Hidden');">☰</button>
        <section class="Hidden">
            <nav class="Pages">
<?php foreach($Page->Pages as $ExistingPage): ?>
<?php if($ExistingPage->Name !== "Index"): ?>
                <a class="Page <?= $ExistingPage->Name === $Page->Name ? "Current" : "" ?>" href="<?= Functions::URL("Documentation", "Topic", $ExistingPage->Name) ?>"><?= $ExistingPage->Description ?></a>
<?php endif; ?>
<?php endforeach; ?>
                <a href="https://www.github.com/vDesk-Cloud">Github</a>
            </nav>
            <nav class="Tutorials">
<?php foreach($Page->Topics as $Topic): ?>
<?php if($Topic->Name !== "Index" && $Topic->Name !== "Client"): ?>
                <a class="Tutorial <?= $Topic->Name === $Page->Topic->Name ? "Current" : "" ?>" href="<?= Functions::URL("Documentation", "Category", "Client", "Topic", $Topic->Name) ?>"><?= $Topic->Description ?></a>
<?php endif; ?>
<?php endforeach; ?>
            </nav>
        </section>
    </header>
    <?= $Page->Topic ?>
    <footer>
        Copyright © 2021 Kerry Holz
        <aside>This website uses icons from <a target="_blank" href="https://www.icons8.com">icons8.com</a></aside>
    </footer>
</main>
</body>
</html>