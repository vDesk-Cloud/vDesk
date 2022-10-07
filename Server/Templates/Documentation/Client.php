<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation\Client\Index $Page */
?>
<article>
    <header>
        <h2>Client</h2>
        <p>
            The client category contains JavaScript-tutorials and controls previews.
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