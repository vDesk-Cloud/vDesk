"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Time#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Time} detail.sender The current instance of the IEditor.
 * @property {Date} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Time#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Time} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Time class.
 * @class Represents a Date type value editor of an EditControl.
 * @param {Date} [Value=null] Initializes the IEditor with the specified value.
 * @param {Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?Date} Value Gets or sets the value of the IEditor.
 * @property {?Object} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.Time = function Time(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, window.Date, "Value", true);
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => TimePicker.Control
        },
        Value:     {
            enumerable: true,
            get:        () => Value,
            set:        ValueToSet => {
                Ensure.Property(ValueToSet, window.Date, "Value", true);
                Value = ValueToSet;
                TimePicker.Value = ValueToSet ?? new window.Date()
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                Validator = Value;
            }
        },
        Valid:     {
            enumerable: true,
            get:        () => {
                if(!Required && Value === null) {
                    return true;
                }
                return Value !== null
                       && Value >= (Validator?.Min ?? Value)
                       && Value <= (Validator?.Max ?? Value);
            },
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                TimePicker.Control.classList.toggle("Error", !Value);
            }
        },
        Required:  {
            enumerable: true,
            get:        () => Required,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Required");
                Required = Value;
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => TimePicker.Enabled,
            set:        Value => TimePicker.Enabled = Value
        }
    });

    /**
     * The TimePicker of the IEditor.
     * @type {vDesk.Controls.TimePicker}
     */
    const TimePicker = new vDesk.Controls.TimePicker(Value ?? new window.Date(), Enabled);
    TimePicker.Control.classList.add("Editor", "Time");

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.TimePicker#event:update
     * @fires vDesk.Controls.EditControl.Time#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        TimePicker.Control.removeEventListener("update", OnUpdate, false);
        Value = Event.detail.time.current;
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            value:  Event.detail.time.current
        }).Dispatch(TimePicker.Control);
        TimePicker.Control.addEventListener("update", OnUpdate, false);
    };
    TimePicker.Control.addEventListener("update", OnUpdate, false);

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.TimePicker#event:clear
     * @fires vDesk.Controls.EditControl.Time#clear
     * @param {CustomEvent} Event
     */
    const OnClear = Event => {
        Event.stopPropagation();
        TimePicker.Control.removeEventListener("clear", OnClear, false);
        Value = null;
        new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(TimePicker.Control);
        TimePicker.Control.addEventListener("clear", OnClear, false);
    };
    TimePicker.Control.addEventListener("clear", OnClear, false);

};
vDesk.Controls.EditControl.Time.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Time.Types = [Extension.Type.Time];