<?php
/** @var \Pages\Reflect\Index $Page */
?>
<nav class="Index" id="Index">
	<?php foreach ($Page->Reflectors as $Reflector): ?>
		<?= \Pages\Reflect::Link($Reflector) ?>
	<?php endforeach; ?>
</nav>