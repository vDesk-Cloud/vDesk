"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Float#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Float} detail.sender The current instance of the IEditor.
 * @property {Number} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Float#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Float} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Float class.
 * @class Represents a float type value editor of an EditControl.
 * @param {Number} [Value=null] Initializes the IEditor with the specified value.
 * @param {Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?Number} Value Gets or sets the value of the IEditor.
 * @property {?Object} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.Float = function Float(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, Type.Number, "Value", true);
    Ensure.Parameter(Validator, Type.Object, "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => Control
        },
        Value:     {
            enumerable: true,
            get:        () => Control.value.length > 0 ? Number.parseFloat(Control.value) : null,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Value", true);
                Control.value = Value || "";
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                Validator = Value;
                Control.min = (Value || {}).Min || 0;
                Control.max = (Value || {}).Max || Number.MAX_VALUE;
                Control.step = (Value || {}).Steps || 0.1;
            }
        },
        Valid:     {
            enumerable: true,
            get:        () => Control.validity.valid,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                Control.classList.toggle("Error", !Value);
            }
        },
        Required:  {
            enumerable: true,
            get:        () => Control.required,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Required");
                Control.required = Value;
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => !Control.disabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Control.disabled = !Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLInputElement}
     */
    const Control = document.createElement("input");
    Control.className = "Editor Float TextBox";
    Control.type = Type.Number;
    Control.value = Value || "";
    Control.min = Validator.Min || 0;
    Control.max = Validator.Max || Number.MAX_VALUE;
    Control.step = Validator.Steps || 0.1;
    Control.required = Required;
    Control.disabled = !Enabled;
    Control.addEventListener(
        "change",
        Event => {
            Event.stopPropagation();
            if(Control.value.length > 0) {
                new vDesk.Events.BubblingEvent("update", {
                    sender: this,
                    value:  Number.parseFloat(Control.value)
                }).Dispatch(Control);
            } else {
                new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            }
        },
        false
    );
};
vDesk.Controls.EditControl.Float.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Float.Types = [Type.Float];

