"use strict";
/**
 * Initializes a new instance of the EventDispatcher class.
 * @class Represents a central interface for receiving global events which have been dispatched on the server.
 * @hideconstructor
 * @tutorial events
 * @memberOf vDesk.Events
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Events.EventDispatcher = (function EventDispatcher() {

    /**
     * The EventSource of the EventDispatcher.
     * @type EventSource
     */
    let Source = null;

    /**
     * Eventhandler that listens on the 'beforeunload' event.
     * Closes the eventsource if the window is being unloaded.
     */
    const OnBeforeUnload = () => {
        if(Source !== null) {
            Source.close();
        }
    };

    /**
     * Connects the EventDispatcher to the current connected server.
     * @name vDesk.Events.EventDispatcher#Connect
     */
    const Connect = function() {
        //Close previous connection.
        if(Source !== null) {
            Source.close();
        }
        Source = new EventSource(
            `${vDesk.Connection.Address}&Module=EventDispatcher&Command=GetEvents&Ticket=${vDesk.User.Ticket}`);
        window.addEventListener("beforeunload", OnBeforeUnload, true);
        Source.onerror = e => console.log(e);
    };

    /**
     * Adds an eventlistener to the EventDispatcher.
     * @param {String} Type The type of event the eventhandler listens to.
     * @param {Function} Callback The callback to execute.
     * @param {Boolean|Object} [Options=false] Eventlistener options.
     * @name vDesk.Events.EventDispatcher#addEventListener
     */
    const addEventListener = function(Type, Callback, Options = false) {
        Source.addEventListener(Type, Callback, Options);
    };

    /**
     * Removes an eventlistener from the EventDispatcher.
     * @param {String} Type The type of event the eventhandler listens to.
     * @param {Function} Callback The callback to remove.
     * @param {Boolean|Object} [Options=false] Eventlistener options.
     * @name vDesk.Events.EventDispatcher#removeEventListener
     */
    const removeEventListener = function(Type, Callback, Options = false) {
        Source.removeEventListener(Type, Callback, Options);
    };

    return {
        Connect:             Connect,
        addEventListener:    addEventListener,
        removeEventListener: removeEventListener
    };
})();
//Register as startup-routine.
vDesk.Load.EventDispatcher = {
    Status: "Loading eventdispatcher",
    Load:   () => vDesk.Events.EventDispatcher.Connect()
};