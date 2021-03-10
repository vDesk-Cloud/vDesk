<?php

use Pages\Reflect;

/** @var \Pages\Reflect\InterfacePage $Page */

$Authors = \count($Page->Authors);
$Index   = 0;
?>
<section class="Documentation Interface">
    <h2>Interface <?= $Page->Name ?></h2>
    <div class="Definition">
        <h3>Description</h3>
        <p class="Description">
            <?= $Page->Description ?>
        </p>
        <?php if(\count($Page->Reflector->getInterfaces()) > 0): ?>
            <p class="Interfaces">
                <b>Extends:</b> <?= \implode(", ", \array_map(static fn($Interface) => Reflect::Link($Interface), $Page->Reflector->getInterfaces())) ?>
            </p>
        <?php endif; ?>
        <?php if($Page->Version !== null): ?>
            <p class="Version">
                <b>Version:</b> <?= $Page->Version ?></p>
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
            <span class="Header">Syntax</span>
            <span class="Keyword">interface</span> <span class="Type Class"><?= $Page->Reflector->getShortName() ?></span>
            <?php if(\count($Page->Reflector->getInterfaceNames()) > 0) : ?>
                <span class="Keyword">extends</span> <span class="Interfaces"><?= \implode(", ", \array_map(static fn($Interface) => new Reflect\Type(Signature: $Interface), $Page->Reflector->getInterfaceNames())) ?></span>
            <?php endif; ?>
        </code>
    </div>
    <div class="Summary">
        <h3>Summary</h3>
        <?php if(\count($Page->Constants) > 0): ?>
            <div class="Constants">
                <h4>Constants</h4>
                <ul>
                    <?php foreach($Page->Constants as $Constant): ?>
                        <li><?= Reflect::Link($Constant->Reflector) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if(\count($Page->Methods) > 0): ?>
            <div class="Methods">
                <h4>Methods</h4>
                <ul>
                    <?php foreach($Page->Methods as $Method): ?>
                        <li><?= Reflect::Link($Method->Reflector) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <?php if(\count($Page->Constants) > 0): ?>
        <!-- Constants -->
        <div class="Constants">
            <h3>Constants</h3>
            <?php foreach($Page->Constants as $Constant): ?>
                <?= $Constant ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if(\count($Page->Methods) + \count($Page->VirtualMethods) > 0): ?>
        <!-- Methods -->
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