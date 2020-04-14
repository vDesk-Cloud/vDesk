"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Boolean#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Boolean} detail.sender The current instance of the IEditor.
 * @property {Boolean} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Boolean#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Boolean} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Boolean class.
 * @class
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */

/**
 * Initializes a new instance of the Boolean class.
 * @class Represents a boolean type value editor of an EditControl.
 * @param {Boolean} [Value=null] Initializes the IEditor with the specified value.
 * @param {Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?Boolean} Value Gets or sets the value of the IEditor.
 * @property {?Object} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.Boolean = function Boolean(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, Type.Boolean, "Value", true);
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => Control
        },
        Value:     {
            enumerable: true,
            get:        () => Control.checked,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Value", true);
                Control.checked = Value || false;
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => Validator = Value
        },
        Valid:     {
            enumerable: true,
            get:        () => true,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                Control.classList.toggle("Error", !Value);
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
    Control.className = "Editor Boolean";
    Control.type = "checkbox";
    Control.checked = Value || false;
    Control.disabled = !Enabled;
    Control.addEventListener(
        "change",
        Event => {
            Event.stopPropagation();
            if(Control.value.length > 0) {
                new vDesk.Events.BubblingEvent("update", {
                    sender: this,
                    value:  Control.value
                }).Dispatch(Control);
            } else {
                new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            }
        },
        false
    );

};
vDesk.Controls.EditControl.Boolean.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Boolean.Types = [Type.Boolean, Type.Bool];