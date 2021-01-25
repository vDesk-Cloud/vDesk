"use strict";
/**
 * Initializes a new instance of the BubblingEvent class.
 * @class Represents a bubbling event that bubbles up the domtree instead of traversing down.
 * @param {String} Type Sets the identifying type of the BubblingEvent.
 * @param {Object} [Arguments = Object] Set the event arguments of the BubblingEvent.
 * @param {Boolean} [Cancelable = true] Flag indicating whether the BubblingEvent can be canceled.
 * @param {Boolean} [Bubbling = true] Flag indicating whether the BubblingEvent bubbles after it has been dispatched to its eventtarget.
 * @memberOf vDesk.Events
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Events.BubblingEvent = function BubblingEvent(Type, Arguments = {}, Cancelable = true, Bubbling = true) {

    /**
     * The dom-event of the bubblingevent.
     * @type CustomEvent
     * @ignore
     */
    const Event = vDesk.Events.Create(Type, Arguments, Cancelable, Bubbling);

    /**
     * Dispatches the event to the specified EventTarget.
     * @param {EventTarget} Target The Target to dispatch the event.
     */
    this.Dispatch = function(Target) {
        Target.dispatchEvent(Event);
    };
};