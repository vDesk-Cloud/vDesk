"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.DateTime#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.DateTime} detail.sender The current instance of the IEditor.
 * @property {Date} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.DateTime#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.DateTime} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the DateTime class.
 * @class Represents a DateTime type value editor of an EditControl.
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
vDesk.Controls.EditControl.DateTime = function DateTime(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, window.Date, "Value", true);
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => DateTimePicker.Control
        },
        Value:     {
            enumerable: true,
            get:        () => Value,
            set:        ValueToSet => {
                Ensure.Property(ValueToSet, window.Date, "Value", true);
                Value = ValueToSet;
                DateTimePicker.Value = ValueToSet ?? new window.Date()
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
                DateTimePicker.Control.classList.toggle("Error", !Value);
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
            get:        () => DateTimePicker.Enabled,
            set:        Value => DateTimePicker.Enabled = Value
        }
    });

    /**
     * The DateTimePicker of the IEditor.
     * @type {vDesk.Controls.DateTimePicker}
     */
    const DateTimePicker = new vDesk.Controls.DateTimePicker(Value ?? new window.Date(), Enabled);
    DateTimePicker.Control.classList.add("Editor", "DateTime");

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.DateTimePicker#event:update
     * @fires vDesk.Controls.EditControl.DateTime#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        DateTimePicker.Control.removeEventListener("update", OnUpdate, false);
        Value = Event.detail.datetime.current;
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            value:  Event.detail.datetime.current
        }).Dispatch(DateTimePicker.Control);
        DateTimePicker.Control.addEventListener("update", OnUpdate, false);
    };
    DateTimePicker.Control.addEventListener("update", OnUpdate, false);

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.DateTimePicker#event:clear
     * @fires vDesk.Controls.EditControl.DateTime#clear
     * @param {CustomEvent} Event
     */
    const OnClear = Event => {
        Event.stopPropagation();
        DateTimePicker.Control.removeEventListener("clear", OnClear, false);
        Value = null;
        new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(DateTimePicker.Control);
        DateTimePicker.Control.addEventListener("clear", OnClear, false);
    };
    DateTimePicker.Control.addEventListener("clear", OnClear, false);

};
vDesk.Controls.EditControl.DateTime.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.DateTime.Types = [Extension.Type.DateTime];