<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\Struct\Properties;

/**
 * Represents an eventlistener that gets invokes if a specified {@link \vDesk\Events\IEvent} Event has occurred.
 *
 * @property-read string|null Name Gets the name of the Event the EventListener is listening on.
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class EventListener {
    
    use Properties;
    
    /**
     * The callback of the EventListener.
     *
     * @var callable|null
     */
    private $Callback;
    
    /**
     * Creates a new instance of the EventListener class.
     *
     * @param string   $Name     Sets the name of the event the EventListener will hook on.
     * @param callable $Callback The callback to execute when an Event with the specified name has been occurred.
     */
    public function __construct(private string $Name, callable $Callback) {
        $this->Callback = $Callback;
        $this->AddProperty("Name", [\Get => fn(): string => $this->Name]);
    }
    
    /**
     * Executes a registered EventListener callback.
     *
     * @param \vDesk\Events\Event $Event The Dispatched Event.
     */
    public function Handle(Event $Event): void {
        $Callback = $this->Callback;
        $Callback($Event->Arguments);
    }
    
}
