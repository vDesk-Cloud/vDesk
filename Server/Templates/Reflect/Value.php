<?php
use vDesk\Pages\Functions;
use vDesk\Struct\Type;
/** @var $Value */
?>
<?php if(\is_array($Value)): ?>
    <?php $Index = 0; $Count = \count($Value); ?>
    <span class="Value array">
        [<?php foreach($Value as $Key => $Item): ?>
        <?php $Delimiter = $Count > ++$Index ? ", " : ""; ?>
        <?php if(\is_string($Key)): ?>
            <?= Functions::Template("Reflect/Value", ["Value" => $Key]) ?> =>
        <?php endif; ?>
            <?= Functions::Template("Reflect/Value", ["Value" => $Item]) ?><?= $Delimiter ?>
    <?php endforeach; ?>]</span><?php elseif(\is_object($Value)): ?><span class="Value Class">\<?= $Value::class ?></span><?php else: ?>
    <span class="Value <?= Type::Of($Value) ?>"><?= \json_encode($Value) ?></span><?php endif; ?>