<?php
use JAG\Link;

/**
 * @var InterfaceDocumentation $Interface
 * @var ReflectionClass $ReflectionClass
 * @var string $Index
 */
?>
<!DOCTYPE html>
<html>
<head>
<title>Namespace <?= $Namespace["Fullname"] ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style.css">
</head>
<body>
	<article class="page">
		<?= $Index ?>
		<section class="overview">
			<h2>Namespace <?= $Namespace["Fullname"] ?></h2>
			<ul>
				<li><h3>Classes</h3></li>
				<?php foreach ($Namespace["Classes"] as $Class): ?>
					<li><?= Link::ClassTag($Class, "", true) ?></li>
				<?php endforeach; ?>
			</ul>
			<ul>
				<li><h3>Interfaces</h3></li>
				<?php foreach ($Namespace["Interfaces"] as $Interface): ?>
					<li><?= Link::InterfaceTag($Interface, "", true) ?></li>
				<?php endforeach; ?>
			</ul>
			<ul>
				<li><h3>Traits</h3></li>
				<?php foreach ($Namespace["Traits"] as $Trait): ?>
					<li><?= Link::TraitTag($Trait, "", true) ?></li>
				<?php endforeach; ?>
			</ul>
			<ul>
				<li><h3>Namespaces</h3></li>
				<?php foreach ($Namespace["Namespaces"] as $Namespace): ?>
					<li><?= Link::NamespaceTag($Namespace["Fullname"]) ?></li>
				<?php endforeach; ?>
			</ul>

		</section>
	</article>
</body>