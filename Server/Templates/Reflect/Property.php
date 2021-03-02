<?php /** @var Pages\Reflect\Property $Page */

use Pages\Reflect;
use vDesk\Pages\Functions; ?>
<div class="Property" id="Property.<?= $Page->Name ?>">
    <h4><span class="<?= $Page->Modifier ?>"><?= $Page->Modifier ?></span> <?= $Page->Name ?>: <?= $Page->Type ?></h4>
    <p class="Description">
        <?= $Page->Description ?>
    </p>
    <code class="Syntax">
        <h4>Syntax</h4>
        <span class="Modifiers">
            <span class="<?= $Page->Modifier ?>"><?= $Page->Modifier ?></span>
        </span>
        <span class="Type">
            <?= $Page->Type ?>
        </span>
        <span class="Name Variable">
            $<?= $Page->Name ?>
        </span>
        = <?= Functions::Template("Reflect/Value", ["Value" => $Page->Value]) ?><span class="Keyword">;</span>
    </code>
</div>