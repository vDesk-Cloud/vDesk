<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation\Server\Index $Page */
?>
<article>
    <header>
        <h2>Server</h2>
        <p>
            The server category contains documents about server-side development, database related articles
        </p>
    </header>
    <section>
        <h3>Topics</h3>
        <ul>
            <?php foreach(\vDesk\Modules::Documentation()::ServerPages() as $Topic): ?>
                <?php if($Topic->Name !== "Index" && $Topic->Name !== "Server"): ?>
                    <li>
                        <a href="<?= Functions::URL("Documentation", "Category", "Server", "Topic", $Topic->Name) ?>"><?= $Topic->Description ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
</article>