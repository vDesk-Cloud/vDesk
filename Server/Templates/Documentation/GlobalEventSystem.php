<?php use vDesk\Documentation\Code; ?>
<article>
    <header>
        <h2>
            Global event system.
        </h2>
        <p>
            The execution of several commands will cause the emmitting of certain serverside events.<br>
            Serverside events that implement the 'IGlobalEvent'-interface will occur within the serverside event-stream, which can be received through the clientside
            EventDispatcher.<br> To listen on a certain event, an eventhandler must get attached on the EventDispatcher, listening on the
            appropriate type of the event.
        </p>
    </header>
    <section>
        <h3>
            Client
        </h3>
        <p>The deletion of an Entry from the Archive for example, will trigger a global<code class="Inline">vDesk.Archive.Element.<?= Code::Class("Deleted") ?></code>-event.<br>
            To receive those events, we can attach an eventlistener on the EventDispatcher in the following way:</p>
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

        
<?= Code::BlockComment("/**
 * Represents an Event that occurs when a User send a Message to another User.
 *
 * @package vDesk\Messenger
 * @author  Kerry &lt;DevelopmentHero@gmail.com&gt;.
 */") ?>
 
<?= Code::ClassDeclaration ?> <?= Code::Class("Deleted") ?> <?= Code::Extends ?> <?= Code::Class("PublicEvent") ?> {
    
    <?= Code::BlockComment("/**
     * The name of the Event.
     */") ?>
        
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?><?= Code::Delimiter ?>
        
        
    <?= Code::BlockComment("/**
     * @inheritdoc
     */") ?>
        
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>() {
        <?= Code::Return ?> (<?= Code::Keyword("string") ?>)<?= Code::Variable("\$this") ?>-><?= Code::Field("Arguments") ?>-><?= Code::Field("ID") ?><?= Code::Delimiter ?>
        
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