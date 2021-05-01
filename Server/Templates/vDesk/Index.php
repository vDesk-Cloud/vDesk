<?php
use vDesk\Pages\Functions;
/** @var \Pages\vDesk\Index $Page */
?>
<article class="Index">
    <?= Functions::Template("vDesk/Index/Description", ["Page" => $Page]) ?>
    <?= Functions::Template("vDesk/Index/Features", ["Page" => $Page]) ?>
    <?= Functions::Template("vDesk/Index/Technology", ["Page" => $Page]) ?>
    <?= Functions::Template("vDesk/Index/Customization", ["Page" => $Page]) ?>
    <?= Functions::Template("vDesk/Index/Development", ["Page" => $Page]) ?>
</article>
