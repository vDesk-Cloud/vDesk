<?php use vDesk\Documentation\Code; ?>
<h2>
    Global event system.
</h2>
<p>The execution of several commands will cause the emmitting of certain serverside events.<br> Serverside events that implement the
    'IGlobalEvent'-interface will occur within the serverside event-stream, which can be received through the clientside
    EventDispatcher.<br> To listen on a certain event, an eventhandler must get attached on the EventDispatcher, listening on the
    appropriate type of the event.</p>
<h3>
    Client
</h3>
<p>The deletion of an Entry from the Archive for example, will trigger a global 'vDesk.Archive.Element.Deleted'-event.<br>
    To receive those events, we can attach an eventlistener on the EventDispatcher in the following way:</p>
<pre><code><?= Code::Class("vDesk") ?>.Events.<span class="Class">EventDispatcher</span>.<span class="Function">addEventListener</span>(<span class="Argument String">"vDesk.Archive.Element.Deleted"</span>, <span
                class="Argument Object">Event</span> => <span class="Class">console</span>.<span class="Function">log</span>(<span
                class="Argument Object">Event.data</span>)[, <span class="Argument Bool">false</span>]);</code></pre>
<p>The EventDispatcher will be available through the vDesk.Events.Dispatcher-property after the client has been started and successfully
    connected to a server.</p>
<h3>
    Server
</h3>
<p>
    There are two types of global events.

</p>
<pre><code><?= Code\Language::PHP ?>
(<?= Code::New ?> \vDesk\Archive\Element\<span class="Class">Deleted</span>(<span class="Variable">$Arguments</span>))-><span class="Function">Dispatch</span>();
</code></pre>

<h4>Example class of global Event</h4>
<pre><code><?= Code::PHP ?>
        
<?= Code::Declare ?> (strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Archive\Element<?= Code::Delimiter ?>


<?= Code::Use ?> vDesk\Events\<span class="Class">PublicEvent</span><?= Code::Delimiter ?>

        
<?= Code::BlockComment("/**
 * Represents an Event that occurs when an {@link \vDesk\Archive\Element} has been deleted from the Archive.
 *
 * @package vDesk\Archive
 * @author  Kerry &lt;DevelopmentHero@gmail.com&gt;.
 */") ?>
 
<span class="Keyword">class</span> <span class="Class">Deleted</span> <span class="Keyword">extends</span> <span class="Class">PublicEvent</span> {
    
    <?= Code::BlockComment("/**
     * The name of the Event.
     */") ?>
        
    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Name") ?> = <?= Code::String("\"vDesk.Archive.Element.Deleted\"") ?><?= Code::Delimiter ?>
        
        
    <?= Code::BlockComment("/**
     * @inheritdoc
     */") ?>
        
    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>() {
        <?= Code::Return ?> (string)<?= Code::Variable("\$this") ?>-><?= Code::Field("Arguments") ?>-><?= Code::Field("ID") ?><?= Code::Delimiter ?>
        
    }
    
}</code></pre>