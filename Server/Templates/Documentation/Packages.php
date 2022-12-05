<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation\Packages $Page */
?>
<article>
    <header>
        <h2>Packages</h2>
        <p>
            This is a collection of documentation and tutorials of vDesk's packages.<br>
            For a complete list of available packages, visit the  <a href="<?= Functions::URL("vDesk", "Page", "Packages") ?>">Packages</a>-section of the website.
        </p>
    </header>
    <section>
        <ul>
            <?php foreach(\Modules\Documentation::Packages() as $Package): ?>
                <?php if($Package->Name !== "PackagesDocumentation"): ?>
                    <li>
                        <a href="<?= Functions::URL("Documentation", "Category", "Packages", "Package", $Package->Name) ?>"><?= $Package->Label ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
</article>