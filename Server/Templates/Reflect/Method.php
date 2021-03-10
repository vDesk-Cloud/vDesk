<?php

use Pages\Reflect;
use vDesk\Pages\Functions;

/** @var \Pages\Reflect\Method $Page */

$Count = \count($Page->Parameters) - 1;
if($Page->Reflector->isConstructor()) {
    $Page->ReturnType = new Reflect\Type(Signature: $Page->Reflector->getDeclaringClass()->name);
}
if($Count > 2) {
    $Break = "<br>";
    $Space = "&nbsp;&nbsp;&nbsp;&nbsp;";
} else {
    $Break = "";
    $Space = "";
}
?>
<div class="Method" id="Method.<?= $Page->Name ?>">
    <h4>
        <span class="<?= $Page->Modifier ?>"><?= $Page->Modifier ?></span>
        <?= $Page->Name ?>: <?= $Page->ReturnType ?>
    </h4>
    <?php if($Page->Inherited): ?>
        <div>
            Inherited from: <?= Reflect::Link($Page->Reflector->getDeclaringClass()->getParentClass()) ?>
        </div>
    <?php endif; ?>
    <p class="Description">
        <?= $Page->Description ?>
    </p>
    <!-- Parameters -->
    <?php if(\count($Page->Parameters) > 0): ?>
        <h5>Parameters</h5>
        <div class="Parameters">
            <?php foreach($Page->Parameters as $Parameter): ?>
                <?= $Parameter ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <!-- Exceptions -->
    <?php if(\count($Page->Exceptions) > 0): ?>
        <h5>Exceptions</h5>
        <div class="Exceptions">
            <?php foreach($Page->Exceptions as $Exception): ?>
                <?= $Exception ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if($Page->ReturnType?->Name !== null): ?>
        <h5>Return value</h5>
        <div class="ReturnValue">
            <?php if(!$Page->ReturnType->Scalar): ?>
                <span class="Type"><?= Reflect::Link(new \ReflectionClass($Page->ReturnType->Signature)) ?></span>
            <?php else: ?>
                <?= $Page->ReturnType ?>
            <?php endif; ?>
            <span class="Description"><?= $Page->ReturnDescription ?></span>
        </div>
    <?php endif; ?>
    <code class="Syntax">
        <span class="Header">Syntax</span>
        <span class="Modifiers">
            <span class="<?= $Page->Modifier ?>"><?= $Page->Modifier ?></span>
        </span>
        <span class="Keyword">function</span>
        <span class="Name"><?= $Page->Name ?></span>(<?= $Break ?>
        <span class="Parameters">
            <?php foreach($Page->Parameters as $Index => $Parameter): ?>
                <?= $Space ?><span class="Parameter">
                <?= $Parameter->Nullable ? "?" : "" ?><?= new Reflect\Type(Signature: (string)$Parameter->Reflector->getType()) ?>
                <?= $Parameter->Reference ? "&" : "" ?>
                <?= $Parameter->Variadic ? "..." : "" ?>
                <span class="Name">$<?= $Parameter->Name ?></span><?php if($Parameter->DefaultValue): ?> = <?= Functions::Template("Reflect/Value", ["Value" => $Parameter->Value]) ?><?php endif; ?><?= $Index < $Count ? "," : "" ?>
                </span><?= $Break ?>
            <?php endforeach; ?>
        </span>): <?= $Page->ReturnType ?>
    </code>
</div>
