<?php
use vDesk\Pages\Functions;
/** @var \Pages\Documentation\Tutorials\Index $Page */
?>
<article>
    <header>
        <h2>Tutorials</h2>
        <p>
            This is the tutorials section of the Documentation Package containing a collection of best practices
        </p>
    </header>
    <section>
        <h3>Overview</h3>
        <ul>
            <?php foreach($Page->Tutorials as $Tutorial): ?>
                <?php if($Tutorial->Name !== "Index"): ?>
                    <li>
                        <a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", $Tutorial->Name) ?>"><?= $Tutorial->Description ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>
</article>

