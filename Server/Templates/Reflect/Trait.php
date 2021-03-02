<?php

/**
 * @var \Pages\Reflect\TraitPage $Page
 */

use Pages\Reflect;
use vDesk\Pages\Functions;

$Authors = \count($Page->Authors);
$Index   = 0;
?>
<section class="Documentation Trait">
    <h2>Trait <?= $Page->Name ?></h2>
    <div class="Definition">
        <h3>Description</h3>
        <p class="Description">
            <?= $Page->Description ?>
        </p>
        <?php if(\count($Page->Reflector->getTraits()) > 0): ?>
            <div class="Traits">
                <b>Uses:</b> <?= \implode(", ", \array_map(static fn($Trait) => Reflect::Link($Trait), $Page->Reflector->getTraits())) ?></div>
        <?php endif; ?>
        <?php if($Page->Version !== null): ?>
            <div class="Version">
                <b>Version:</b> <?= $Page->Version ?></div>
        <?php endif; ?>
        <?php if($Authors > 0): ?>
            <p class="Authors">
                <b>Authors:</b>
                <?php foreach($Page->Authors as $Name => $Mail): ?>
                    <a href="mailto:<?= $Mail ?>"><?= $Name ?></a><?= ++$Index < $Authors ? "," : "" ?>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>
        <code class="Syntax">
            <h4>Syntax</h4>
            <span class="Keyword">trait</span> <span class="Type Class"><?= $Page->Reflector->getShortName() ?></span>
            <?php if($Page->Reflector->getParentClass() !== false) : ?>
                <span class="Keyword">extends</span> <span class="Parent"><?= $Page->Reflector->getParentClass()->name ?></span>
            <?php endif; ?>
            <?php if(\count($Page->Reflector->getInterfaceNames()) > 0) : ?>
                <span class="Keyword">implements</span> <span class="Interfaces"><?= \implode(", ", $Page->Reflector->getInterfaceNames()) ?></span>
            <?php endif; ?>
        </code>
    </div>
    <div class="Summary">
        <h3>Summary</h3>
        <ul class="Constants">
            <li><h4>Constants</h4></li>
            <?php foreach($Page->Constants as $Constant): ?>
                <li><?= Reflect::Link($Constant->Reflector) ?></li>
            <?php endforeach; ?>
        </ul>
        <ul class="Properties">
            <li><h4>Properties</h4></li>
            <?php foreach($Page->Properties as $Property): ?>
                <li><?= Reflect::Link($Property->Reflector) ?></li>
            <?php endforeach; ?>
            <?php if(\count($Page->VirtualProperties) > 0): ?>
                <li><h5>Virtual</h5></li>
                <?php foreach($Page->VirtualProperties as $Property): ?>
                    <li><a href="#VirtualProperty.<?= $Property->Name ?>"><?= $Property->Name ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <ul class="Methods">
            <li><h4>Methods</h4></li>
            <?php foreach($Page->Methods as $Method): ?>
                <li><?= Reflect::Link($Method->Reflector) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- Constants -->
    <?php if(\count($Page->Constants) > 0): ?>
        <hr>
        <div class="Constants">
            <h3>Constants</h3>
            <?php foreach($Page->Constants as $Constant): ?>
                <?= $Constant ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-- Properties -->
    <?php if(\count($Page->Properties) + \count($Page->VirtualProperties) > 0): ?>
        <hr>
        <div class="Properties">
            <h3>Properties</h3>
            <?php foreach($Page->Properties as $Property): ?>
                <?= $Property ?>
            <?php endforeach; ?>
            <?php foreach($Page->VirtualProperties as $Property): ?>
                <?= $Property ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-- Methods -->
    <?php if(\count($Page->Methods) + \count($Page->VirtualMethods) > 0): ?>
        <hr>
        <div class="Methods">
            <h3>Methods</h3>
            <?php foreach($Page->Methods as $Method): ?>
                <?= $Method ?>
            <?php endforeach; ?>
            <?php foreach($Page->VirtualMethods as $Method): ?>
                <?= $Method ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>