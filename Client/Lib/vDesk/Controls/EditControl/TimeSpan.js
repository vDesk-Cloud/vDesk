"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.TimeSpan#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.TimeSpan} detail.sender The current instance of the IEditor.
 * @property {String} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.TimeSpan#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.TimeSpan} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Time class.
 * @class Represents a Date type value editor of an EditControl.
 * @param {String} [Value=null] Initializes the IEditor with the specified value.
 * @param {Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?String} Value Gets or sets the value of the IEditor.
 * @property {?Object} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.TimeSpan = function TimeSpan(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, Type.String, "Value", true);
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => TimeSpanPicker.Control
        },
        Value:     {
            enumerable: true,
            get:        () => Value,
            set:        ValueToSet => {
                Ensure.Property(ValueToSet, Type.String, "Value", true);
                Value = ValueToSet;
                TimeSpanPicker.Value = ValueToSet || "00:00:00";
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
            get:        () => Required ? Value !== null : true,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                TimeSpanPicker.Control.classList.toggle("Error", !Value);
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
            get:        () => TimeSpanPicker.Enabled,
            set:        Value => TimeSpanPicker.Enabled = Value
        }
    });

    /**
     * The TimePicker of the IEditor.
     * @type {vDesk.Controls.TimeSpanPicker}
     */
    const TimeSpanPicker = new vDesk.Controls.TimeSpanPicker(Value || "00:00:00", Enabled);
    TimeSpanPicker.Control.classList.add("Editor", "TimeSpan");

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.TimeSpanPicker#event:update
     * @fires vDesk.Controls.EditControl.TimeSpan#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        TimeSpanPicker.Control.removeEventListener("update", OnUpdate, false);
        Value = Event.detail.timespan.current;
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            value:  Event.detail.timespan.current
        }).Dispatch(TimeSpanPicker.Control);
        TimeSpanPicker.Control.addEventListener("update", OnUpdate, false);
    };
    TimeSpanPicker.Control.addEventListener("update", OnUpdate, false);

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.TimeSpanPicker#event:clear
     * @fires vDesk.Controls.EditControl.TimeSpan#clear
     * @param {CustomEvent} Event
     */
    const OnClear = Event => {
        Event.stopPropagation();
        TimeSpanPicker.Control.removeEventListener("clear", OnClear, false);
        Value = null;
        new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(TimeSpanPicker.Control);
        TimeSpanPicker.Control.addEventListener("clear", OnClear, false);
    };
    TimeSpanPicker.Control.addEventListener("clear", OnClear, false);

};
vDesk.Controls.EditControl.TimeSpan.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.TimeSpan.Types = [Extension.Type.TimeSpan];