<?php use vDesk\Pages\Functions; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>vDesk</title>
    <link rel="icon" href="<?= Functions::Image("favicon.ico") ?>" type="image/x-icon">
    <?php foreach($Current->Stylesheets as $Stylesheet): ?>
        <link rel="stylesheet" href="<?= Functions::Stylesheet($Stylesheet) ?>">
    <?php endforeach; ?>
    <?php foreach($Page->Scripts as $Script): ?>
        <script src="<?= Functions::Script($Script) ?>"></script>
    <?php endforeach; ?>
</head>
<body>
<article class="Page">
    <header>
        <h1><a href="<?= Functions::URL("vDesk", "Index") ?>">v<span style="color: #2AB0ED">D</span>esk\<a href="<?= Functions::URL("Documentation", "Index") ?>"><span
                            style="color: #2AB0ED">D</span>ocumentation</a></a></h1>
        <nav>
            <?php foreach($Pages as $Page): ?>
                <?php if($Page->Name !== "Index"): ?>
                    <a class="<?= $Page->Name === $Current->Name ? "Current" : "" ?>"
                       href="<?= Functions::URL("Documentation", "Page", $Page->Name) ?>"><?= $Page->Description ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <a href="https://www.github.com/vDesk-Cloud">Github</a>
        </nav>
    </header>
    <section>
        <?= Functions::Template("Documentation/{$Current->Name}", ["Current" => $Current, "Pages" => $Pages, "Tutorials" => $Tutorials ?? []]) ?>
    </section>
    <footer>
        Copyright © 2020 Kerry Holz
    </footer>
</article>
</body>
</html>