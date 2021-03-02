<?php
/** @var \Pages\Reflect\Constant $Page */

use vDesk\Pages\Functions;

?>
<div class="Constant" id="Constant.<?= $Page->Name ?>">
    <h4>
        <span class="Modifiers"><span class=\"<?= $Page->Modifier ?>\"><?= $Page->Modifier ?></span></span> <?= $Page->Name ?>: <?= $Page->Type ?>
    </h4>
    <p class="Description">
        <?= $Page->Description ?>
    </p>
    <code class="Syntax">
        <h4>Syntax</h4>
        <span class="Modifiers">
            <span class="<?= $Page->Modifier ?>"><?= $Page->Modifier ?> const</span>
        </span>
        <span class="Name">
            <?= $Page->Name ?>
        </span>
        = <?= Functions::Template("Reflect/Value", ["Value" => $Page->Value]) ?><span class="Keyword">;</span>
    </code>
</div>