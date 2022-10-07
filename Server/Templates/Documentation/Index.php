<?php

use vDesk\Pages\Functions;

/** @var \Pages\Documentation\Index $Page */
?>
<article class="Index">
    <h2>Welcome to the <span style="color: #2AB0ED">D</span>ocumentation Package!</h2>
    <p>
        This package provides a collection of technical whitepapers and tutorials and will be continuously expanded in the future.
    </p>
    <p>
        If you want to contribute to this collection, the GitHub repository of the package can be found <a href="https://github.com/vDesk-Cloud/Documentation">here</a>.<br>
        If you have any good ideas or want to provide feedback, you can contact the author <a href="<?= Functions::URL("vDesk", "Page", "Contact") ?>">here</a>.<br>
        If you want to contribute to the project, consider reading the <a href="<?= Functions::URL("vDesk", "Page", "Contribute") ?>">Contribution guideline</a> first.
    </p>
    <h3>Overview</h3>
    <h4>General topcis</h4>
    <ul>
        <?php foreach($Page->Pages as $Page): ?>
            <?php if($Page->Name !== "Index"): ?>
                <li>
                    <a href="<?= Functions::URL("Documentation", "Topic", $Page->Name) ?>"><?= $Page->Description ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <h4>Client topics</h4>
    <ul>
        <?php foreach(\vDesk\Modules::Documentation()::ClientPages() as $Topic): ?>
            <?php if($Topic->Name !== "Index"): ?>
                <li>
                    <a href="<?= Functions::URL("Documentation", "Category", "Client", "Topic", $Topic->Name) ?>"><?= $Topic->Description ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <h4>Server topics</h4>
    <ul>
        <?php foreach(\vDesk\Modules::Documentation()::ServerPages() as $Topic): ?>
            <?php if($Topic->Name !== "Index"): ?>
                <li>
                    <a href="<?= Functions::URL("Documentation", "Category", "Server", "Topic", $Topic->Name) ?>"><?= $Topic->Description ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</article>