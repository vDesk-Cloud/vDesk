<?php use vDesk\Documentation\Code;
use vDesk\Pages\Functions; ?>
<article>
    <header>
        <h2>
            Archive
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.<br>
            The functionality of the event system is provided by the <a href="<?= \vDesk\Pages\Functions::URL("vDesk", "Page", "Packages#Events") ?>">Events</a>-package.
        </p>

        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Archive">Introduction</a></li>
            <li><a href="#Navigation">Navigating</a></li>
            <li><a href="#Upload">Uploading files to the archive</a></li>
            <li>
                <a href="#View">Viewing files</a>
                <ul class="Topics">
                    <li><a href="#CustomViewer">Custom file viewers</a></li>
                </ul>
            </li>
            <li>
                <a href="#Edit">Editing files</a>
                <ul class="Topics">
                    <li><a href="#CustomEditor">Custom file editors</a></li>
                </ul>
            </li>
            <li><a href="#Download">Downloading files</a></li>
            <li><a href="#ACL">Managing access of files and folders</a></li>
            <li><a href="#Search">Search</a></li>
            <li><a href="#Events">Events dispatched by the archive</a></li>
            <li><a href="#Events">Managing access of files and folders</a></li>
        </ul>
    </header>
    <section id="Archive">
        <h3>Archive</h3>
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
    <section id="Upload">
        <h4>Uploading files to the archive</h4>
        <p>
            The execution of several commands causes certain global events to be triggered;
            for example: the deletion of an Entry from the Archive will trigger a global <code class="Inline">vDesk.Archive.Element.Deleted</code>-event.<br>
        </p>
        <p>
            Global events are being identified by inheriting from the abstract <code class="Inline">\vDesk\Events\<?= Code::Class("PublicEvent") ?></code>- or
            <code class="Inline">\vDesk\Events\<?= Code::Class("PrivateEvent") ?></code>-classes, which share the <code class="Inline">\vDesk\Events\<?= Code::Class("GlobalEvent") ?></code>-class as a parent
            that acts like a database model with reduced functionality following the "dataview" pattern defined by the <code class="Inline">\vDesk\Data\<?= Code::Class("IDataView") ?></code>-interface.
        </p>
        <p>
            When scheduled, the event system will serialize the return value of their <code class="Inline"><?= Code::Function("ToDataView") ?>()</code>-method in the database,
            which then can be received afterwards through the <code class="Inline">\Modules\<?= Code::Class("Events") ?>::<?= Code::Function("Stream") ?>()</code>-method
            that returns a generator that periodically scans the database for new events.<br>
            Public events are stored in the <code class="Inline"><?= Code::Const("Events") ?>.<?= Code::Field("Public") ?></code>-table,
            while private events are stored in the <code class="Inline"><?= Code::Const("Events") ?>.<?= Code::Field("Private") ?></code>-table.
        </p>
        <p>
            The scan interval is stored in the global  <code class="Inline"><?= Code::Class("Settings") ?>::<?= Code::Variable("\$Remote") ?>[<?= Code::String("\"Events\"") ?>][<?= Code::String("\"Interval\"") ?>]</code>-setting.<br>
            Public events will be deleted at the moment of querying minus the configured interval, while private events will be deleted after they have been sent to the receiver.
        </p>
        <p>
            Global events can be received on the client by registering an event listener on the <code class="Inline">vDesk.Events.<?= Code::Class("Stream") ?></code>-class,
            which acts as a facade to its underlying <a href="https://developer.mozilla.org/en-US/docs/Web/API/EventSource">EventSource</a>.
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
    <section id="EventListeners">
        <h3>Event listeners</h3>
        <p>
            The event system supports multiple ways of registering event listeners.
            These are directly attached listeners via passing an event name and a closure to the <code class="Inline"><?= Code::Class("Events") ?>::<?= Code::Function("AddEventListener") ?>()</code>-method
            and file-based event listeners, which will be loaded and executed upon schedule.
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
    <section id="Search">
        <h4>Search</h4>
        <p>
            The archive package registers a search filter for performing a "LIKE"-search on the "Name"-column of the <code class="Inline"><?= Code::Const("Archive") ?>.<?= Code::Field("Elements") ?></code>-table.
            File results can be directly viewed by clicking on the search result in the result list on the left side;
            double-clicking will load the archive module and open the file in a new window.<br>

        </p>
            <aside class="Image" onclick="this.classList.toggle('Fullscreen')" style="text-align: center">
                <img src="<?= Functions::Image("Documentation","Packages", "Search", "Search.png") ?>" alt="Image showing the context menu of the archive while downloading a file">
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
</article>