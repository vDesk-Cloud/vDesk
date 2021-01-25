"use strict";
/**
 * Initializes a new instance of the Editor class.
 * @class Class that represents a [...] for [...]. | Class providing functionality for [...].
 * @param {vDesk.Calendar.Event} Event Initializes the Editor with the specified Event.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Boolean} Changed Gets a value indicating whether the Participants of the current edited Event of the Editor have been changed.
 * @memberOf vDesk.Calendar.Event.Participant
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Calendar.Event.Participant.Editor = function Editor(Event) {
    Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");

    Object.defineProperties(this, {
        Control: {
            enumerable: false,
            get:        () => Control
        },
        Changed: {
            enumerable: true,
            get:        () => false
        }
    });

    const Control = document.createElement("div");

    this.Save = function() {
        //Add or remove participants of the Event. Only the desired User can update his own status (in the Viewer).
    };

    this.Delete = function() {
        //Remove all participants from the Event.
    };

    this.Reset = function() {
        //Reset changes.
    };
};
