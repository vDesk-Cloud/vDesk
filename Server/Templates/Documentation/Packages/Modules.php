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
                    <li><a href="#Server">Server</a></li>
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
            Events are small PHP classes consisting at least of a public "Name"-constant and a protected "TimeStamp"-property and have to inherit from the abstract
            <code class="Inline">\vDesk\Events\<?= Code::Class("Event") ?></code>-class to be recognized as such.<br>
            Built in events usually use their namespace separated by dots as an identifier to ensure globally uniqueness.<br>
            The timestamp will be set when dispatching the event via calling its final <code class="Inline"><?= Code::Function("Dispatch") ?>()</code>-method;
            this will pass its instance to the <code class="Inline">\Modules\<?= Code::Class("Events") ?>::<?= Code::Function("Dispatch") ?>()</code>-method,
            adding it to the Events module's event queue for schedule.<br>
            The module registers its <code class="Inline"><?= Code::Function("Schedule") ?>()</code>-method as a shutdown function,
            removing the need of manually scheduling the dispatched events to the registered listeners.
        </p>
        <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
            <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Archive.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
        </aside>
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
            and must inherit from the <code class="Inline">\vDesk\Modules\<?= Code::Class("Module") ?></code>-class
        </p>
        <h5>
            Example of registering an event listener
        </h5>
        <pre><code><?= Code\Language::PHP ?>
\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Events") ?>()::<?= Code::Function("AddEventListener") ?>(
    <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>,
    <?= Code::Keyword("fn") ?>(\vDesk\Events\<?= Code::Class("Event") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
)<?= Code::Delimiter ?>
</code></pre>
        <p>
            File-based event listeners are simple PHP files which must return a tuple array of the event's name to listen on and a callback closure
            which are stored in an "Events"-folder that is by default located in the "Server"-directory or optionally in the Archive package's "System"-directory.<br>
            The Events package will scan on installation if the setup is bundled with the Archive package and thus asks if the event listener files shall be stored and searched on the filesystem, in the archive or both.<br>
            The storage mode is stored in the local  <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Local") ?>[<?= Code::String("\"Events\"") ?>][<?= Code::String("\"Mode\"") ?>]</code>-setting.
        </p>
        <p>
            To match an event, the listener file must contain the name of the desired event in its file name on the beginning.
            For example: if an event is named "Security.User.Created", the filename of the event listener may be called "Security.User.Created.GreetNewUser.php".
        </p>
        <h5>
            Example of an event listener file
        </h5>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>


<?= Code::Use ?> \vDesk\Archive\Element\<?= Code::Class("Deleted") ?><?= Code::Delimiter ?>


<?= Code::Return ?> [
    <?= Code::Class("Deleted") ?>::<?= Code::Const("Name") ?>,
    <?= Code::Keyword("fn") ?>(<?= Code::Class("Deleted") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
]<?= Code::Delimiter ?>
</code></pre>
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
    <section id="Download">
        <h4>Downloading files</h4>
        <p>
            Files can be downloaded via right-clicking them and choosing the "Save" option in the context menu.
        </p>
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
                <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Save.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
            </aside>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                Due to technical reasons, the file is first being downloaded into the allocated RAM of the browser and then served over a dialog for finally saving it.<br>
                This may limit the maximum size of downloads to the browser's settings or available RAM on systems without swap files for example.
            </p>
        </aside>
        <p>

        </p>
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


        <pre><code><?= Code\Language::PHP ?>
\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Events") ?>()::<?= Code::Function("AddEventListener") ?>(
    <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>,
    <?= Code::Keyword("fn") ?>(\vDesk\Events\<?= Code::Class("Event") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Info") ?>(<?= Code::String("\"Element '") ?>{<?= Code::Variable("\$Event") ?>-><?= Code::Field("Element") ?>-><?= Code::Field("Name") ?>}<?= Code::String("' has been deleted\"") ?>)
)<?= Code::Delimiter ?>
</code></pre>
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
                <img src="<?= Functions::Image("Documentation","Packages", "Archive", "Save.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
            </aside>
        <p>

        </p>
    </section>
</article>