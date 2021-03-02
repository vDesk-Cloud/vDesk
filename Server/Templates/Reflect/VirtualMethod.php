<?php
use JAG\Link;
use JAG\VirtualMethod;

/**
 * @var VirtualMethod $Method
 */
?>
<div class="method virtual"
	id="<?= $Method->Name . ($Method->Static ? "_": "") ?>">
	<h4>
		<span class="type scalar">virtual <?= $Method->Modifier ?></span> <?= $Method->Name ?> : <span
			class="type <?= $Method->ReturnType->Internal ? "scalar" : "object" ?>"><?= $Method->ReturnType->Name ?></span>
	</h4>
	<div class="description">
		<?= $Method->Description . PHP_EOL ?>
	</div>
	<?php if(count($Method->Parameters > 0)): ?>
		<br>
	<h5>Parameters</h5>
	<?php foreach ($Method->Parameters as $Parameter): ?>
	<div class="parameter">
			<?php if ($Parameter->Type->Internal): ?>
			<div class="type scalar">
				<?= $Parameter->Type->Name ?>
			</div>
			<?php else: ?>
			<div class="type object">
			<a href="<?= Link::Class($Parameter->Type->Name) ?>"><?= $Parameter->Type->Name ?></a>
		</div>
			<?php endif; ?>
		<div class="name">
			<?= $Parameter->Name . PHP_EOL ?>
		</div>
		<div class="description">-</div>
	</div>
	<?php endforeach; ?>
	<?php endif; ?>
	<br>
	<?php if ($Method->ReturnType->Name !== "void"): ?>
	<h5>Return Value</h5>
	<div class="returnvalue">
		<div
			class="type <?= $Method->ReturnType->Internal ? "scalar" : "object" ?>">
			<?php if (!$Method->ReturnType->Internal): ?>
				<a href="<?= Link::Class($Method->ReturnType->Name) ?>"><?= $Method->ReturnType->Name ?></a>
			<?php else: ?>
				<?= $Method->ReturnType->Name . PHP_EOL ?>
			<?php endif; ?>
		</div>
		<div class="description">-</div>
	</div>
	<?php endif; ?>
</div>
