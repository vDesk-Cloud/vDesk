<?php

use Pages\Reflect;

/** @var \Pages\Reflect\Index $Page */
?>
<nav>
    <?php if(\count($Page->Classes) > 0): ?>
        <h2>Classes</h2>
        <ul class="Classes" id="Classes">
            <?php foreach($Page->Classes as $Reflector): ?>
                <li <?= $Reflector === $Page->Current ? "Current" : "" ?>>
                    <?= Reflect::Link($Reflector) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if(\count($Page->Interfaces) > 0): ?>
        <h2>Interfaces</h2>
        <ul class="Interfaces" id="Interfaces">
            <?php foreach($Page->Interfaces as $Reflector): ?>
                <li <?= $Reflector === $Page->Current ? "Current" : "" ?>>
                    <?= Reflect::Link($Reflector) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if(\count($Page->Traits) > 0): ?>
        <h2>Traits</h2>
        <ul class="Traits" id="Traits">
            <?php foreach($Page->Traits as $Reflector): ?>
                <li <?= $Reflector === $Page->Current ? "Current" : "" ?>>
                    <?= Reflect::Link($Reflector) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</nav>
