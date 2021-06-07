<?php

use Pages\Reflect;

/** @var \Pages\Reflect\ClassPage $Page */

$Authors = \count($Page->Authors);
$Index   = 0;
?>
<article class="Class">
    <header>
        <h2>Class <?= $Page->Name ?></h2>
        <p class="Description">
            <?= $Page->Description ?>
        </p>
        <?php if($Page->Reflector->getParentClass() !== false): ?>
            <div class="Parent">
                <b>Extends:</b> <?= $Page->Reflector->getParentClass()->name ?></div>
        <?php endif; ?>
        <?php if(\count($Page->Reflector->getInterfaces()) > 0): ?>
            <div class="Interfaces">
                <b>Implements:</b> <?= \implode(", ", \array_map(static fn($Interface) => Reflect::Link($Interface), $Page->Reflector->getInterfaces())) ?></div>
        <?php endif; ?>
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
            <span class="Header">Syntax</span>
            <span class="Modifiers">
                    <?php if($Page->Final) : ?>
                        <span class="final">final</span>
                    <?php elseif($Page->Abstract) : ?>
                        <span class="abstract">abstract</span>
                    <?php endif; ?>
                </span>
            <span class="Keyword">class</span>
            <span class="Type Class"><?= $Page->Reflector->getShortName() ?></span>
            <?php if($Page->Reflector->getParentClass() !== false) : ?>
                <span class="Keyword">extends</span> <span class="Parent"><?= new Reflect\Type(Signature: $Page->Reflector->getParentClass()->name) ?></span>
            <?php endif; ?>
            <?php if(\count($Page->Reflector->getInterfaceNames()) > 0) : ?>
                <span class="Keyword">implements</span> <span class="Interfaces"><?= \implode(", ", \array_map(static fn($Interface) => new Reflect\Type(Signature: $Interface), $Page->Reflector->getInterfaceNames())) ?></span>
            <?php endif; ?>
            <?php if(\count($Page->Reflector->getTraitNames()) > 0) : ?>
                {<br>
                <?php foreach($Page->Reflector->getTraitNames() as $Trait): ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="Keyword">use</span> <?= new Reflect\Type(Signature: $Trait) ?><span class="Keyword">;</span>
                <?php endforeach; ?><br>
                }
            <?php endif; ?>
        </code>
    </header>
    <section class="Summary">
        <h3>Summary</h3>
        <?php if(\count($Page->Constants) > 0): ?>
            <nav class="Constants">
                <h4>Constants</h4>
                <ul>
                    <?php foreach($Page->Constants as $Constant): ?>
                        <li><?= Reflect::Link($Constant->Reflector) ?></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
        <?php if(\count($Page->Properties) + \count($Page->VirtualProperties) > 0): ?>
            <nav class="Properties">
                <h4>Properties</h4>
                <ul>
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
            </nav>
        <?php endif; ?>
        <?php if(\count($Page->Methods) > 0): ?>
            <nav class="Methods">
                <h4>Methods</h4>
                <ul>
                    <?php foreach($Page->Methods as $Method): ?>
                        <li><?= Reflect::Link($Method->Reflector) ?></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </section>
    <!-- Constants -->
    <?php if(\count($Page->Constants) > 0): ?>
        <section class="Constants">
            <h3>Constants</h3>
            <?php foreach($Page->Constants as $Constant): ?>
                <?= $Constant ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <!-- Properties -->
    <?php if(\count($Page->Properties) + \count($Page->VirtualProperties) > 0): ?>
        <section class="Properties">
            <h3>Properties</h3>
            <?php foreach($Page->Properties as $Property): ?>
                <?= $Property ?>
            <?php endforeach; ?>
            <?php foreach($Page->VirtualProperties as $Property): ?>
                <?= $Property ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
    <!-- Methods -->
    <?php if(\count($Page->Methods) + \count($Page->VirtualMethods) > 0): ?>
        <section class="Methods">
            <h3>Methods</h3>
            <?php foreach($Page->Methods as $Method): ?>
                <?= $Method ?>
            <?php endforeach; ?>
            <?php foreach($Page->VirtualMethods as $Method): ?>
                <?= $Method ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</article>