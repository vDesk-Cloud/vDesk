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
    <?php foreach($Page->Tutorial->Stylesheets as $Stylesheet): ?>
        <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
    <?php endforeach; ?>
    <?php foreach($Page->Scripts as $Script): ?>
        <script src="<?= Functions::Script($Script) ?>"></script>
    <?php endforeach; ?>
    <?php foreach($Page->Tutorial->Scripts as $Script): ?>
        <script src="<?= Functions::Script($Script) ?>"></script>
    <?php endforeach; ?>
</head>
<body>
<main class="Tutorials">
    <header>
        <h1><a href="<?= Functions::URL("vDesk", "Index") ?>">v<span style="color: #2AB0ED">D</span>esk\<a href="<?= Functions::URL("Documentation", "Index") ?>"><span
                            style="color: #2AB0ED">D</span>ocumentation</a></a>\<a href="<?= Functions::URL("Documentation", "Page", "Tutorials") ?>"><span
                        style="color: #2AB0ED">T</span>utorials</a></a></h1>
        <button class="Toggle" onclick="this.nextElementSibling.classList.toggle('Hidden');">☰</button>
        <section class="Hidden">
            <nav class="Pages">
                <?php foreach($Page->Pages as $ExistingPage): ?>
                    <?php if($ExistingPage->Name !== "Index"): ?>
                        <a class="Page <?= $ExistingPage->Name === $Page->Name ? "Current" : "" ?>"
                           href="<?= Functions::URL("Documentation", "Page", $ExistingPage->Name) ?>"><?= $ExistingPage->Description ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a href="https://www.github.com/vDesk-Cloud">Github</a>
            </nav>
            <nav class="Tutorials">
                <?php foreach($Page->Tutorials as $Tutorial): ?>
                    <?php if($Tutorial->Name !== "Index"): ?>
                        <a class="Tutorial <?= $Tutorial->Name === $Page->Tutorial->Name ? "Current" : "" ?>"
                           href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", $Tutorial->Name) ?>"><?= $Tutorial->Description ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </section>
    </header>
    <?= $Page->Tutorial ?>
    <footer>
        Copyright © 2020 Kerry Holz
    </footer>
</main>
</body>
</html>