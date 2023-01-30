<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            Modules
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= \vDesk\Pages\Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>

        <h3>Overview</h3>
        <ul class="Topics">
            <li>
                <a href="#Modules">Modules</a>
                <ul class="Topics">
                    <li><a href="#Client">Client</a></li>
                    <li>
                        <a href="#Server">Server</a>
                        <ul class="Topics">
                            <li><a href="#Register">Registering Modules</a></li>
                        </ul>
                    </li>
                    <li><a href="#Invocation">Invoking modules</a></li>
                </ul>
            </li>
            <li><a href="#Commands">Commands</a></li>
            <li><a href="#Parameters">Commands</a></li>
        </ul>
    </header>
    <section id="Modules">
        <h3>Modules</h3>
        <p>
            The Modules package builds the foundation of vDesk's business logic.<br>
            It implements interfaces for client-/server-modules and provides a powerful type-safe parameter API.<br>
            Modules are what's called "Controllers" in MVC-frameworks.
        </p>
        <p>
            Modules are what's called "Controllers" in a typical MVC-framework, except that they are actual "Models" themself in vDesk
            - this enables request-validation before executing a Command ("Action"); even preventing the submission of malicious requests
            by mirroring the commands and their parameters to the client for pre-validation.
        </p>
    </section>
    <section id="Client">
        <h4>Client</h4>
        <p>
            Client Modules are javascript files located in <code class="Inline">Modules</code>-namespace
            and must implement either the <code class="Inline">vDesk.Modules.<?= Code::Class("IModule") ?></code>-interface
        </p>
        <aside class="Code">
            <?= Code\Language::JS ?>
            <h5>//vDesk/Client/Modules/CustomModule.js</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(25) ?>
        <pre><code><?= Code::String("\"use strict\"") ?><?= Code::Delimiter ?>

<?= Code::Variable("Modules") ?>.<?= Code::Class("CustomModule") ?> = <?= Code::Function ?> <?= Code::Function("CustomModule") ?>() {

    <?= Code::Variable("Object") ?>.<?= Code::Function("defineProperties") ?>(<?= Code::This ?>, {
        <?= Code::Field("Control") ?>: {<?= Code::Field("value") ?>: <?= Code::Const("Control") ?>},
        <?= Code::Field("Name") ?>: {<?= Code::Field("value") ?>: <?= Code::String("\"CustomModule\"") ?>},
        <?= Code::Field("Title") ?>: {<?= Code::Field("value") ?>: <?= Code::String("\"Display Name\"") ?>},
        <?= Code::Field("Icon") ?>: {<?= Code::Field("value") ?>: <?= Code::Variable("vDesk") ?>.<?= Code::Field("Visual") ?>.<?= Code::Class("Icons") ?>.<?= Code::Const("CustomModule") ?>},
    })<?= Code::Delimiter ?>


    <?= Code::BlockComment("/**
     * Called when the Module is being loaded (click on the module list on the left side)
     */") ?>

    <?= Code::This ?>.<?= Code::Function("Load") ?> = <?= Code::Function ?>() {

    }<?= Code::Delimiter ?>


    <?= Code::BlockComment("/**
     * Called before another Module is loaded.
     */") ?>

    <?= Code::This ?>.<?= Code::Function("Unload") ?> = <?= Code::Function ?>() {

    }<?= Code::Delimiter ?>


}<?= Code::Delimiter ?>
</code></pre>
    </section>
    <section id="Server">
        <h4>Server</h4>
        <p>
            Server modules are PHP files located in the <code class="Inline">Modules</code>-namespace
            and must inherit from the <code class="Inline">\vDesk\Modules\<?= Code::Class("Module") ?></code>-class to be recognized as such.<br>
            Modules are stored in the <code class="Inline"><?= Code::Const("Modules") ?>.<?= Code::Field("Modules") ?></code>-table.
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>//vDesk/Server/Modules/CustomModule.php</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(18) ?>
        <pre><code><?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> Modules<?= Code::Delimiter ?>


<?= Code::BlockComment("/**
 * Custom Module implementation.
 */") ?>

<?= Code::ClassDeclaration ?> <?= Code::Class("CustomModule") ?> <?= Code::Extends ?> \vDesk\Modules\<?= Code::Class("Module") ?> {

    <?= Code::BlockComment("/**
     * Initializes a new instance of the Deleted Event.
     *
     * @param \\vDesk\\Archive\\Element \$Element The deleted archive Element.
     */") ?>

    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("Command") ?>(<?= Code::Public ?> <?= Code::Class("Element") ?> <?= Code::Variable("\$Element") ?>) { }

}</code></pre>
    </section>
    <section id="Invocation">
        <h4>Invocation</h4>
        <p>
            The modules package provides several facades for running modules
        </p>
        <p>
            Invoking a module on the client
            On the client is a proxy available that holds unique instances of client modules located in
        </p>
        <aside class="Code">
            <?= Code\Language::JS ?>
            <h5>Calling Modules on the client</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(1) ?>
        <pre><code><?= Code::Class("Modules") ?>.<?= Code::Class("Archive") ?>.<?= Code::Function("GoToID") ?>()<?= Code::Delimiter ?>
</code></pre>
        </aside>
        <h5>
           Server
        </h5>
        <p>
            On the server exists a similar auto-initializing singleton that uses a <code class="Inline"><?= Code::Function("__callStatic") ?>()</code>-method
            mapping method-calls to unique instances of modules.<br>
            The facade will load and initialize module classes automatically upon request and stores them in an internal cache dictionary.



        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Calling Modules on the server</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(4) ?>
        <pre><code>\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Events") ?>()::<?= Code::Function("AddEventListener") ?>(
    <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>,
    <?= Code::Keyword("fn") ?>(\vDesk\Events\<?= Code::Class("Event") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
)<?= Code::Delimiter ?>
</code></pre>
        </aside>
        <p>
            The server side facade will register a custom autoloader for the <code class="Inline">Modules</code>-namespace,
            enabling direct references to other modules.<br>
            The facade will be automatically loaded in the callstack of the execution of a typical command,
            however it is recommended to reference modules over the facade instead of their fully qualified classname.
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Invocation shorthand</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(2) ?>
            <pre><code>\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("ModuleA") ?>()<?= Code::Delimiter ?>

\Modules\<?= Code::Class("ModuleB") ?>::<?= Code::Function("SomeMethod") ?>()<?= Code::Delimiter ?>
</code></pre>
        </aside>
    </section>
    <section id="Commands">
        <h4>Commands</h4>
        <p>
            Commands are database mappings to methods of modules and their parameters.<br>
            They allow the system a type-safe communication and prevent useless requests by mirroring parameter validation to the client.
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Example of adding a Command with Parameters to a custom Module</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(17) ?>
        <pre><code><?= Code::Variable("\$Module") ?> = \vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("CustomModule") ?>()<?= Code::Delimiter ?>

<?= Code::Variable("\$Module") ?>-><?= Code::Field("Commands") ?>-><?= Code::Function("Add") ?>(
    <?= Code::New ?> \vDesk\Modules\Module\<?= Code::Class("Command") ?>(
        <?= Code::Int("Name") ?>: <?= Code::String("\"CustomCommand\"") ?>,
        <?= Code::Int("Parameters") ?>: <?= Code::New ?> \vDesk\Struct\Collections\Observable\<?= Code::Class("Collection") ?>([
            <?= Code::New ?> \vDesk\Modules\Module\Command\<?= Code::Class("Parameter") ?>(
                <?= Code::Int("Name") ?>: <?= Code::String("\"CustomParameter\"") ?>,
                <?= Code::Int("Type") ?>: \vDesk\Struct\<?= Code::Class("Type") ?>::<?= Code::Const("String") ?>,
                <?= Code::Int("Optional") ?>: <?= Code::False ?>,
                <?= Code::Int("Nullable") ?>: <?= Code::True ?>,
            )
        ]),
        <?= Code::Int("RequireTicket") ?>: <?= Code::True ?>,
        <?= Code::Int("Binary") ?>: <?= Code::False ?>

    )
)<?= Code::Delimiter ?>

<?= Code::Variable("\$Module") ?>-><?= Code::Function("Save") ?>()<?= Code::Delimiter ?>
</code></pre>
        </aside>
    </section>
    <section id="Parameters">
        <h4>Parameters</h4>
        <p>
            Commands can require parameters, these
        </p>
    </section>
</article>