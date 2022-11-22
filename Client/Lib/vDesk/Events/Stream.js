"use strict";
/**
 * Initializes a new instance of the Stream class.
 * @class Represents a central interface for receiving global events which have been dispatched on the server.
 * @memberOf vDesk.Events
 * @author Kerry <DevelopmentHero@gmail.com>
 * @todo Drop EventDispatcher alias in Events-1.2.1
 */
vDesk.Events.Stream = vDesk.Events.EventDispatcher = (function Stream() {

    /**
     * The EventSource of the Stream.
     * @type EventSource
     */
    let Source = null;

    /**
     * Connects the Stream to the current server.
     * @name vDesk.Events.Stream#Connect
     */
    const Connect = function() {
        Source = new EventSource(`${vDesk.Connection.Address}&Module=Events&Command=Stream&Ticket=${vDesk.Security.User.Current.Ticket}`);
        Source.onerror = e => console.log(e);
    };

    /**
     * Disconnects the Stream from the current server.
     * @name vDesk.Events.Stream#Disconnect
     */
    const Disconnect = function() {
        if(Source !== null){
            Source.close();
        }
    };

    /**
     * Adds an event listener to the Stream.
     * @param {String} Type The type of event the event handler listens to.
     * @param {Function} Callback The event listener to register.
     * @param {Boolean|Object} [Options=false] Event listener options.
     * @name vDesk.Events.Stream#addEventListener
     */
    const addEventListener = function(Type, Callback, Options = false) {
        Source.addEventListener(Type, Callback, Options);
    };

    /**
     * Removes an event listener from the Stream.
     * @param {String} Type The type of event the event handler listens to.
     * @param {Function} Callback The event listener to remove.
     * @param {Boolean|Object} [Options=false] Event listener options.
     * @name vDesk.Events.Stream#removeEventListener
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

//Register startup routine.
vDesk.Load.Events = {
    Status: "Connecting to event stream",
    Load:   () => vDesk.Events.Stream.Connect()
};

//Register shutdown routine.
vDesk.Unload.Events = {
    Status: "disconnecting from event stream",
    Unload: () => vDesk.Events.Stream.Disconnect()
};