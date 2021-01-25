"use strict";
/**
 * Contains event related classes.
 * @namespace Events
 * @memberOf vDesk
 */
vDesk.Events = {
    /**
     * Factorymethod for creating customevents.
     * @param {String} Type The type of the event.
     * @param {Object} [Arguments = {}] The arguments of the event.
     * @param {Boolean} [Cancelable = true] Indicates whether the event can be canceled.
     * @param {Boolean} [Bubbling = true] Indicates whether the event bubbles.
     * @return {CustomEvent|Event} The created event.
     */
    Create: function(Type, Arguments = {}, Cancelable = true, Bubbling = true) {
        return new CustomEvent(
            Type,
            {
                detail:     Arguments,
                bubbles:    Bubbling,
                cancelable: Cancelable
            }
        );
    }
};

/**
 * Gets or sets a value indicating whether the event has been canceled.
 * @type {Boolean}
 */
CustomEvent.prototype.Canceled = false;

/**
 * Cancels the propagation of the event.
 */
CustomEvent.prototype.Cancel = function() {
    if(this.cancelable) {
        this.Canceled = true;
        this.stopPropagation();
    }
};