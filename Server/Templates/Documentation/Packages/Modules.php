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
        <pre><code><?= Code\Language::JS ?><?= Code::Class("vDesk") ?>.Events.<?= Code::Class("Stream") ?>.<?= Code::Function("addEventListener") ?>(<?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>, <?= Code::Variable("Event") ?> => <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Event") ?>.<?= Code::Field("data") ?>), <?= Code::Bool("false") ?>)<?= Code::Delimiter ?></code></pre>
        <h5>Example class of a public Event</h5>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>
        
<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Archive\Element<?= Code::Delimiter ?>


<?= Code::Use ?> vDesk\Events\<?= Code::Class("PublicEvent") ?><?= Code::Delimiter ?>

<?= Code::Use ?> vDesk\Archive\<?= Code::Class("Element") ?><?= Code::Delimiter ?>

        
<?= Code::BlockComment("/**
 * Event that occurs when an Element has been deleted from the Archive.
 */") ?>
 
<?= Code::ClassDeclaration ?> <?= Code::Class("Deleted") ?> <?= Code::Extends ?> <?= Code::Class("PublicEvent") ?> {
    
    <?= Code::BlockComment("/**
     * The name of the Event.
     */") ?>
        
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?><?= Code::Delimiter ?>
        
        
    <?= Code::BlockComment("/**
     * Initializes a new instance of the Deleted Event.
     *
     * @param \\vDesk\\Archive\\Element \$Element The deleted archive Element.
     */") ?>
        
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("_construct") ?>(<?= Code::Public ?> <?= Code::Class("Element") ?> <?= Code::Variable("\$Element") ?>) { }

    <?= Code::BlockComment("/** @inheritDoc */") ?>

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>(): <?= Code::Class("Element") ?> {
        <?= Code::Return ?> <?= Code::Variable("\$this") ?>-><?= Code::Field("Element") ?><?= Code::Delimiter ?>

    }
    
}</code></pre>
        <h5>Example class of a private Event</h5>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>
        
<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Messenger\Users\Message<?= Code::Delimiter ?>


<?= Code::Use ?> vDesk\Events\<?= Code::Class("PrivateEvent") ?><?= Code::Delimiter ?>


<?= Code::BlockComment("/**
 * Event that occurs when a User sends a Message to another User.
 */") ?>
 
<?= Code::ClassDeclaration ?> <?= Code::Class("Event") ?> <?= Code::Extends ?> <?= Code::Class("PrivateEvent") ?> {
    
    <?= Code::BlockComment("/**
     * The name of the Event.
     */") ?>
     
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"vDesk.Messenger.Users.Message.Sent\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::BlockComment("/**
     * Initializes a new instance of the Event class.
     *
     * @param \\vDesk\\Security\\User           \$Receiver The receiving User.
     * @param \\vDesk\\Messenger\\Users\\Message \$Message The sent Message.
     */") ?>
     
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?>(
        <?= Code::Public ?> \vDesk\Security\<?= Code::Class("User") ?> <?= Code::Variable("\$Receiver") ?>,
        <?= Code::Private ?> \vDesk\Messenger\Users\<?= Code::Class("Message") ?> <?= Code::Variable("\$Message") ?>
                
    ) {
        <?= Code::Parent ?>::<?= Code::Function("__construct") ?>(<?= Code::Variable("\$Receiver") ?>)<?= Code::Delimiter ?>
        
    }

    <?= Code::BlockComment("/** @inheritDoc */") ?>

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>(): <?= Code::Keyword("array") ?> {
        <?= Code::Return ?> [
            <?= Code::Variable("\$this") ?>-><?= Code::Field("Message") ?>-><?= Code::Field("ID") ?>,
            <?= Code::Variable("\$this") ?>-><?= Code::Field("Message") ?>-><?= Code::Field("Sender") ?>-><?= Code::Field("ID") ?>,
            <?= Code::Variable("\$this") ?>-><?= Code::Field("Message") ?>-><?= Code::Field("Recipient") ?>-><?= Code::Field("ID") ?>

        ]<?= Code::Delimiter ?>

    }
    
}</code></pre>
    </section>
    <section id="Server">
        <h4>Server</h4>
        <p>
            Server modules are PHP files located in the <code class="Inline">Modules</code>-namespace
            and must inherit from the <code class="Inline">\vDesk\Modules\<?= Code::Class("Module") ?></code>-class to be recognized as such.<br>
            Modules are stored in the <code class="Inline"><?= Code::Const("Modules") ?>.<?= Code::Field("Modules") ?></code>-table.
        </p>
        <h5>Example class of a public Event</h5>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> Modules<?= Code::Delimiter ?>


<?= Code::BlockComment("/**
 * Event that occurs when an Element has been deleted from the Archive.
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
        <pre><code><?= Code\Language::JS ?>
vDesk.<?= Code::Class("Modules") ?>.<?= Code::Class("Archive") ?>.<?= Code::Function("GoToID") ?>()<?= Code::Delimiter ?>
</code></pre>
        <h5>
           Server
        </h5>
        <p>
            On the server exists a similar auto-initializing singleton that uses a <code class="Inline"><?= Code::Function("__callStatic") ?>()</code>-method
            mapping method-calls to unique instances of modules.<br>
            The facade will load and initialize module classes automatically upon request and stores them in an internal cache dictionary.



        </p>
        <pre><code><?= Code\Language::PHP ?>
\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Events") ?>()::<?= Code::Function("AddEventListener") ?>(
    <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>,
    <?= Code::Keyword("fn") ?>(\vDesk\Events\<?= Code::Class("Event") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
)<?= Code::Delimiter ?>
</code></pre>
        <p>
            The server side facade will register a custom autoloader for the <code class="Inline">Modules</code>-namespace,
            enabling direct references to other modules.<br>
            The facade will be automatically loaded in the callstack of the execution of a typical command,
            however it is recommended to reference modules over the facade instead of their fully qualified classname.
        </p>
        <pre><code><?= Code\Language::PHP ?>
\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("ModuleA") ?>()<?= Code::Delimiter ?>

\Modules\<?= Code::Class("ModuleB") ?>::<?= Code::Function("SomeMethod") ?>()<?= Code::Delimiter ?>

</code></pre>
    </section>
    <section id="Commands">
        <h4>Commands</h4>
        <p>
            Commands are database mappings to methods of modules and their parameters.<br>
            They allow the system a type-safe communication and prevent useless requests by mirroring parameter validation to the client.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Variable("\$Module") ?> = \vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("CustomModule") ?>()<?= Code::Delimiter ?>

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
    </section>
    <section id="Parameters">
        <h4>Parameters</h4>
        <p>
            Commands can require parameters, these
        </p>
    </section>
</article>