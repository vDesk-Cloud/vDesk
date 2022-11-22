<?php use vDesk\Documentation\Code; ?>
<article>
    <header>
        <h2>
            Events
        </h2>
        <p>
            This document describes the logic behind the global event system and how to dispatch and listen on events.
        </p>
        <p>
            The execution of several commands will cause to emit certain serverside events.<br>
            For example: the deletion of an Entry from the Archive will trigger a global <code class="Inline">vDesk.Archive.Element.Deleted</code>-event.<br>


        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li>
                <a href="#Events">Events</a>
                <ul class="Topics">
                    <li><a href="#GlobalEvents">Global events</a></li>
                </ul>
            </li>
            <li>
                <a href="#Setups">Event listeners</a>
                <ul class="Topics">
                    <li><a href="#SetupFormat">Format</a></li>
                    <li><a href="#SetupCreation">Creating setups</a></li>
                </ul>
            </li>
        </ul>
    </header>

    <section>
        <h3>
            Events
        </h3>
        <p>
            Events are small PHP classes of any structure which inherit from the <code class="Inline">vDesk\Events\<?= Code::Class("Event") ?></code>-class
            and must implement a public "Name"-constant.
        </p>
        <p>
            Global events can be received on the client by registering an event listener on the <code class="Inline">vDesk.Events.<?= Code::Class("Stream") ?></code>-facade,
            which acts as.<br>
            To receive those events, we can attach an eventlistener on the event stream in the following way:</p>
        <pre><code><?= Code\Language::JS ?>
<?= Code::Class("vDesk") ?>.Events.<?= Code::Class("Stream") ?>.<?= Code::Function("addEventListener") ?>(<?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>, <?= Code::Variable("Event") ?> => <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Event") ?>.<?= Code::Field("data") ?>), <?= Code::Bool("false") ?>)<?= Code::Delimiter ?></code></pre>
        <p>
            The EventDispatcher will be available through the <code class="Inline">vDesk.Events.<?= Code::Class("EventDisptacher") ?></code>-property after the client has been started and successfully
            connected to a server.
        </p>

        Serverside events which inherit from the <code class="Inline">vDesk\Events\<?= Code::Class("GlobalEvent") ?></code>-class will occur within the global event-stream, which can be received through the clientside
        EventDispatcher.<br> To listen on a certain event, an eventhandler must get attached on the EventDispatcher, listening on the
        appropriate type of the event.

    </section>

    <section>
        <h3>
            Client
        </h3>
        <p>
            Global events can be received on the client by registering an event listener on the <code class="Inline">vDesk.Events.<?= Code::Class("Stream") ?></code>-facade,
            which acts as.<br>
            To receive those events, we can attach an eventlistener on the event stream in the following way:</p>
        <pre><code><?= Code\Language::JS ?>
<?= Code::Class("vDesk") ?>.Events.<?= Code::Class("EventDispatcher") ?>.<?= Code::Function("addEventListener") ?>(<?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>, <?= Code::Variable("Event") ?> => <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Event") ?>.<?= Code::Field("data") ?>), <?= Code::Bool("false") ?>)<?= Code::Delimiter ?></code></pre>
        <p>
            The EventDispatcher will be available through the <code class="Inline">vDesk.Events.<?= Code::Class("EventDisptacher") ?></code>-property after the client has been started and successfully
            connected to a server.
        </p>
    </section>
    <section>
        <h3>
            Server
        </h3>
        <p>
            Serverside Events are scheduled over the <code class="Inline">Modules\<?= Code::Class("EventDisptacher") ?>::<?= Code::Function("Schedule") ?>()</code>-method,
            which is embedded into the finally part of vDesk's main try/catch-statement as a soft dependency.
        </p>
        <h4>
            Example of registering a serverside EventListener.
        </h4>
        <pre><code><?= Code\Language::PHP ?>
\vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("EventDispatcher") ?>()::<?= Code::Function("AddEventListener") ?>(
    <?= Code::New ?> \vDesk\Events\<?= Code::Class("EventListener") ?>(
        <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?>,
        <?= Code::Keyword("fn") ?>(\vDesk\Events\<?= Code::Class("Event") ?> <?= Code::Variable("\$Event") ?>) => <?= Code::Class("Log") ?>::<?= Code::Function("Debug") ?>((<?= Code::Keyword("string") ?>)<?= Code::Variable("\$Event") ?>-><?= Code::Field("Arguments") ?>
        
    )
)<?= Code::Delimiter ?>
</code></pre>
        <p>
            Describe how archive listeners are registered<br>
            Describe how archive listeners are registered<br>
        </p>
        <pre><code><?= Code\Language::PHP ?>
(<?= Code::New ?> \vDesk\Archive\Element\<?= Code::Class("Deleted") ?>(<?= Code::Variable("\$Arguments") ?>))-><?= Code::Function("Dispatch") ?>()<?= Code::Delimiter ?>
</code></pre>
    </section>
    <section>
        <p>
            Events are separated in private Events which get only dispatched to a certain User or public Events, which will be received from every current logged in User.
        </p>
        <h4>Example class of a public Event</h4>
        <pre><code><?= Code::PHP ?>
        
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
        <h4>Example class of a private Event</h4>
        <pre><code><?= Code::PHP ?>
        
<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Messenger\Users\Message<?= Code::Delimiter ?>


<?= Code::Use ?> vDesk\Events\<?= Code::Class("PrivateEvent") ?><?= Code::Delimiter ?>


<?= Code::BlockComment("/**
 * Represents an Event that occurs when a User sends a Message to another User.
 *
 * @package vDesk\Messenger
 * @author  Kerry &lt;DevelopmentHero@gmail.com&gt;.
 */") ?>
 
<?= Code::ClassDeclaration ?> <?= Code::Class("Event") ?> <?= Code::Extends ?> <?= Code::Class("PrivateEvent") ?> {
    
    <?= Code::BlockComment("/**
     * The name of the Event.
     */") ?>
     
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"vDesk.Messenger.Users.Message.Sent\"") ?><?= Code::Delimiter ?>
    
    
    <?= Code::BlockComment("/**
     * Initializes a new instance of the Event class.
     *
     * @param \\vDesk\\Security\\User           \$Receiver  Initializes the Event with the specified receiver.
     * @param \\vDesk\\Messenger\\Users\\Message \$Arguments Initializes the Event with the specified Message.
     */") ?>
     
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?>(
        <?= Code::Public ?> \vDesk\Security\<?= Code::Class("User") ?> <?= Code::Variable("\$Receiver") ?>,
        <?= Code::Private ?> \vDesk\Messenger\Users\<?= Code::Class("Message") ?> <?= Code::Variable("\$Arguments") ?>
                
    ) {
        <?= Code::Parent ?>::<?= Code::Function("__construct") ?>(<?= Code::Variable("\$Arguments") ?>)<?= Code::Delimiter ?>
        
    }
    
}</code></pre>
    </section>
</article>