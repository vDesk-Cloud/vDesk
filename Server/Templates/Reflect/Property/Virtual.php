<?php /** @var \Pages\Reflect\Property\Virtual $Page */

use Pages\Reflect; ?>
<div class="Property Virtual" id="VirtualProperty.<?= $Page->Name ?>">
    <h4>
        <?php if($Page->Readonly): ?>
            get
        <?php elseif ($Page->Writeonly): ?>
            set
        <?php else: ?>
            get set
        <?php endif; ?>
        <?= $Page->Name ?>:
        <?php if(!$Page->Type->Scalar): ?>
            <?= Reflect::Link(new \ReflectionClass($Page->Type->Signature)) ?>
        <?php else: ?>
            <?= $Page->Type ?>
        <?php endif; ?>
    </h4>
    <p class="Description">
        <?= $Page->Description ?>
    </p>
</div>