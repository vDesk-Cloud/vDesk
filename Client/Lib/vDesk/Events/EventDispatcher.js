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
     * Connects the EventDispatcher to the current connected server.
     * @name vDesk.Events.EventDispatcher#Connect
     */
    const Connect = function() {
        Source = new EventSource(
            `${vDesk.Connection.Address}&Module=Events&Command=Stream&Ticket=${vDesk.Security.User.Current.Ticket}`);
        Source.onerror = e => console.log(e);
    };

    /**
     * Closes the underlying EventSource of the EventDispatcher.
     */
    const Disconnect = function() {
        if(Source !== null){
            Source.close();
        }
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
        Disconnect:          Disconnect,
        addEventListener:    addEventListener,
        removeEventListener: removeEventListener
    };
})();
//Register as startup-routine.
vDesk.Load.Events = {
    Status: "Loading eventdispatcher",
    Load:   () => vDesk.Events.EventDispatcher.Connect()
};
//Register as startup-routine.
vDesk.Unload.Events = {
    Status: "Closing eventdispatcher",
    Load:   () => vDesk.Events.EventDispatcher.Disconnect()
};