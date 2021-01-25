<?php use vDesk\Pages\Functions; ?>
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
<h4>Pages</h4>
<ul>
    <?php foreach($Pages as $Page): ?>
        <?php if($Page->Name !== "Index"): ?>
            <li>
                <a href="<?= Functions::URL("Documentation", "Page", $Page->Name) ?>"><?= $Page->Description ?></a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<h4>Tutorials</h4>
<ul>
    <?php foreach($Tutorials as $Tutorial): ?>
        <?php if($Tutorial->Name !== "Index"): ?>
            <li>
                <a href="<?= Functions::URL("Documentation", "Page", "Tutorials", "Tutorial", $Tutorial->Name) ?>"><?= $Tutorial->Description ?></a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
