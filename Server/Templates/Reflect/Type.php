<?php
/**
 * @var \Pages\Reflect\Type $Page
 */
?><?php if(\count($Page->UnionTypes) > 0): ?>
<span class="Type Union"><?= \implode("|", $Page->UnionTypes) ?></span>
<?php elseif($Page->Scalar): ?>
<span class="Type <?= $Page->Name ?>"><?= $Page->Name ?></span><?php else: ?>
<span class="Type Class">\<?= \ltrim($Page->Name, "\\") ?></span><?php endif; ?>
