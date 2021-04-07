<?php use vDesk\Pages\Functions; ?>
<article>
    <h2>Tutorials</h2>
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
</article>

