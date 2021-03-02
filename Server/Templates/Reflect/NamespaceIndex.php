<?php
use JAG\Index;
use JAG\Link;
$IsCurrent = (strpos(substr($Current, 0, strrpos($Current, "\\")), $Namespace["Fullname"]) !== false);

?>
<div class="namespaceindex">
	<div class="header">
		<span class="toggler"
			onclick="this.parentNode.parentNode.children[1].classList.toggle('visible'); this.textContent = (this.textContent == '▼' ? '▲' : '▼')"><?= $IsCurrent ? "▲" : "▼" ?></span>

		<a href="<?= Link::Namespace($Namespace["Fullname"]) ?>"
			class="namespacelink <?= ($Current === $Namespace["Fullname"]) ? "current" : "" ?>"><?= Index::Name($Namespace["Fullname"]) ?></a>
	</div>

	<div class="content <?= $IsCurrent ? "visible" : "" ?>">
		<ul class="classes">
	<?php foreach ($Namespace["Classes"] as $Class): ?>
	<li><a href="<?= Link::Class($Class) ?>"
				class="classlink <?= ($Current === $Class) ? "current" : "" ?>"><?= Index::Name($Class) ?></a></li>
	<?php endforeach; ?>
	<?php foreach ($Namespace["Traits"] as $Trait): ?>
	<li><a href="<?= Link::Trait($Trait) ?>"
				class="classlink <?= ($Current === $Trait) ? "current" : "" ?>"><?= Index::Name($Trait) ?></a></li>
	<?php endforeach; ?>
	<?php foreach ($Namespace["Interfaces"] as $Interface): ?>
	<li><a href="<?= Link::Interface($Interface) ?>"
				class="classlink <?= ($Current === $Interface) ? "current" : "" ?>"><?= Index::Name($Interface) ?></a></li>
	<?php endforeach; ?>
	</ul>
		<div class="subnamespaces">
	<?php foreach ($Namespace["Namespaces"] as $Namespace): ?>
		<?= Template("NamespaceIndex", ["Namespace" => $Namespace, "Current" => $Current]) ?>
	<?php endforeach; ?>
	</div>
	</div>
</div>
