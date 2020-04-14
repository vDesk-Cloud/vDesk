"use strict";
/**
 * Fired if a value has been added to the ObservableArray.
 * @event ObservableArray#add
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'add' event.
 * @property {ObservableArray} detail.sender The current instance of the ObservableArray.
 * @property {*} detail.value The added value.
 */
/**
 * Fired if a value has been removed from the ObservableArray.
 * @event ObservableArray#remove
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'remove' event.
 * @property {ObservableArray} detail.sender The current instance of the ObservableArray.
 * @property {*} detail.value The removed value.
 */
/**
 * Represents an observable array of values.
 * @augments Array
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class ObservableArray extends Array {

    /**
     * Initializes a new instance of the ObservableArray class.
     * @constructor
     * @param {type} Values Passed through to parent's constructor.
     */
    constructor(...Values) {
        super(...Values);
        this.EventListeners = {};
    }

    /**
     * Adds an eventlistener to the ObservableArray.
     * @param {String} Type The type of event the eventhandler listens to.
     * @param {Function} Callback The callback to execute.
     * @param {Boolean} [UseCapture=false] Triggers the listener on the capture phase 
     * (This parameter is unused and only for consitency purposes with the ususal 'addEventListener'-pattern).
     */
    addEventListener(Type, Callback, UseCapture = false) {
        if(!(Type in this.EventListeners)) {
            this.EventListeners[Type] = [];
        }
        this.EventListeners[Type].push(Callback);
    }

    /**
     * Removes an eventlistener from the ObservableArray.
     * @param {String} Type The type of event the eventhandler listens to.
     * @param {Function} Callback The callback to remove.
     * @param {Boolean} [UseCapture=false] (This parameter is unused and only for consitency purposes with the ususal 'addEventListener'-pattern).
     */
    removeEventListener(Type, Callback, UseCapture = false) {
        if(!(Type in this.EventListeners)) {
            return;
        }
        this.EventListeners[Type].forEach((Listener, Index, Listeners) => {
            if(Listener === Callback) {
                Listeners.splice(Index, 1);
                return;
            }
        });
    }

    /**
     * Dispatches an event to the ObservableArray.
     * @param {Event} Event The Event to dispatch.
     * @return {Boolean} True if the event has not been cancelled; otherwise, false.
     */
    dispatchEvent(Event) {
        if(!(Event.type in this.EventListeners)) {
            return true;
        }
        this.EventListeners[Event.type].forEach(Listener => Listener.call(this, Event));
        return !Event.defaultPrevented;
    }

    /**
     * Adds one or more values to the ObservableArray
     * @param {*} Values The values to add.
     * @fires ObservableArray#add
     */
    push(...Values) {
        Values.forEach(Value => new vDesk.Events.BubblingEvent("add", {sender: this, value: Value}).Dispatch(this));
        return super.push(...Values);
    }

    /**
     * Removes a specified amount of values from the ObservableArray starting at a specified index.
     * @param {Number} Index The index from which the values will be removed.
     * @param {Number} Amount The amount of values to remove.
     * @param {*} Values Inserts one or more values to the ObservableArray at the specified staring index.
     * @return {Array<*>} The removed values.
     * @fires ObservableArray#remove
     */
    splice(Index, Amount, ...Values) {
        Values.forEach(Value => new vDesk.Events.BubblingEvent("add", {sender: this, value: Value}).Dispatch(this));
        let RemovedValues = super.splice(Index, Amount, ...Values);
        RemovedValues.forEach(Value => new vDesk.Events.BubblingEvent("remove", {sender: this, value: Value}).Dispatch(this));
        return RemovedValues;
    }
}