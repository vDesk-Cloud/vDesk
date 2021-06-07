<?php /** @var \Pages\Reflect\Method\Parameter $Page */

use Pages\Reflect; ?>
<div class="Parameter">
    <?php if(!$Page->Type->Scalar): ?>
        <span class="Type"><?= Reflect::Link(new \ReflectionClass($Page->Type->Signature)) ?></span>
    <?php elseif(\count($Page->Type->UnionTypes) > 0): ?>
        <span class="Type Union">
            <?= \implode("|", \array_map(static fn($Type) => !$Type->Scalar ? Reflect::Link(new \ReflectionClass($Type->Signature)) : $Type, $Page->Type->UnionTypes)) ?>
        </span>
    <?php else: ?>
        <?= $Page->Type ?>
    <?php endif; ?>
    <span class="Name"><?= $Page->Name ?></span>
    <span class="Description"><?= $Page->Description ?></span>
</div>
