<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation\Client $Page */
?>
<article>
    <header>
        <h2>Client</h2>
        <p>
            The client category contains documents about JavaScript and client APIs as well as previews of controls.
        </p>
    </header>
    <section>
        <h3>Topics</h3>
        <ul>
            <?php foreach(\vDesk\Modules::Documentation()::ClientPages() as $Topic): ?>
                <?php if($Topic->Name !== "Index" && $Topic->Name !== "Client"): ?>
                    <li>
                        <a href="<?= Functions::URL("Documentation", "Category", "Client", "Topic", $Topic->Name) ?>"><?= $Topic->Description ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
</article>