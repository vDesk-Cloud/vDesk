<?php

use vDesk\Pages\Functions;

/** @var \Pages\Documentation\Index $Page */
?>
<article class="Index">
    <h2>Welcome to the <span style="color: #2AB0ED">D</span>ocumentation Package!</h2>
    <p>
        This package provides a collection of technical whitepapers, programming guides and tutorials and will be continuously expanded in the future.
    </p>
    <p>
        If you want to contribute to this collection, the GitHub repository of the package can be found <a href="https://github.com/vDesk-Cloud/Documentation">here</a>.<br>
        If you have any good ideas or want to provide feedback, you can contact the author <a href="<?= Functions::URL("vDesk", "Page", "Contact") ?>">here</a>.<br>
        If you want to contribute to the project, consider reading the <a href="<?= Functions::URL("vDesk", "Page", "Contribute") ?>">Contribution guideline</a> first.
    </p>
    <h3>General topics</h3>
    <ul>
        <?php foreach($Page->Pages as $Page): ?>
            <?php if($Page->Name !== "Index"): ?>
                <li>
                    <a href="<?= Functions::URL("Documentation", "Topic", $Page->Name) ?>"><?= $Page->Label ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <h3>Packages</h3>
    <ul>
        <?php foreach(\vDesk\Modules::Documentation()::Packages() as $Package): ?>
            <?php if($Package->Name !== "Index"): ?>
                <li>
                    <a href="<?= Functions::URL("Documentation", "Package", $Package->Name) ?>"><?= $Package->Label ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</article>