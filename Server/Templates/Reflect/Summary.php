<?php
use Pages\Reflect;
/** @var \Pages\Reflect\Summary $Page */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Summary</title>
    <link rel="stylesheet" href="Stylesheet.css">
    <script src="Search.js"></script>
</head>
<body>
<main>
    <header>
        <h1><a href="index.html">Index</a></h1>
        <label for="Search">Search:</label>
        <input id="Search" type="text" oninput="Search(this.value)">
        <?= $Page->Index ?>
    </header>
    <article class="Summary">
        <h2>Summary</h2>
        <?php if(\count($Page->Index->Classes) > 0): ?>
            <section class="Classes">
                <h3>Classes</h3>
                <ul>
                    <?php foreach($Page->Index->Classes as $Class): ?>
                        <li><?= Reflect::Link($Class) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if(\count($Page->Index->Interfaces) > 0): ?>
            <section class="Interfaces">
                <h3>Interfaces</h3>
                <ul>
                    <?php foreach($Page->Index->Interfaces as $Interface): ?>
                        <li><?= Reflect::Link($Interface) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if(\count($Page->Index->Traits) > 0): ?>
            <section class="Traits">
                <h3>Traits</h3>
                <ul>
                    <?php foreach($Page->Index->Traits as $Trait): ?>
                        <li><?= Reflect::Link($Trait) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if(\count($Page->Errors) > 0): ?>
            <section class="Errors">
                <h3>Errors</h3>
                <ul>
                    <?php foreach($Page->Errors as $Error): ?>
                        <li><?= $Error ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <?php if(\count($Page->Exceptions) > 0): ?>
        <section class="Exceptions">
            <h3>Exceptions</h3>
            <ul>
                <?php foreach($Page->Exceptions as $Exception): ?>
                    <li><?= $Exception->getMessage() . $Exception->getTraceAsString() ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </section>
    </article>
</main>
</body>
</html>