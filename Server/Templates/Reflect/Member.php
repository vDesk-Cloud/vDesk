<?php /** @var \Pages\Reflect\Member $Page */ ?>
<div class="member" id="Members.<?= $Page->Name ?>">
	<h4>
        <span class=\"modifiers\"><span class=\"<?= $Page->Modifier ?>\"><?= $Page->Modifier ?></span></span><?= $Page->Name ?> : <?= $Page->Type ?>
    </h4>
	<div class="description">
	<?= $Page->Description ?>
	</div>
	<div class="signature">
		<div class="name">
			<?= $Page->Name ?>
		</div>
		<div class="type">
			<?= $Page->Type ?>
		</div>
        <div class="value">
            <?php if(\is_array($Page->Value)): ?>
                <?= \json_encode($Page->Value, JSON_PRETTY_PRINT) ?>
            <?php else: ?>
                <?= $Page->Value ?>
            <?php endif; ?>
        </div>
	</div>
</div>