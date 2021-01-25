"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Enum#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Enum} detail.sender The current instance of the IEditor.
 * @property {String} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Enum#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Enum} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Enum class.
 * @class Represents a enum type value editor of an EditControl.
 * @param {String} [Value=null] Initializes the IEditor with the specified value.
 * @param {Array|Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @property {HTMLInputElement} Control Gets the underlying DOM-Node.
 * @property {?String} Value Gets or sets the value of the IEditor.
 * @property {?Array|Object} Validator Gets or sets the validator of the IEditor.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the IEditor is valid.
 * @property {Boolean} Required Gets or sets a value indicating whether the IEditor requires a value.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the IEditor is enabled.
 * @memberOf vDesk.Controls.EditControl
 * @implements vDesk.Controls.EditControl.IEditor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.Enum = function Enum(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, Type.String, "Value", true);
    Ensure.Parameter(Validator, [Array, Type.Object], "Validator", true);
    Ensure.Parameter(Required, Type.Boolean, "Required");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => Control
        },
        Value:     {
            enumerable: true,
            get:        () => Control.options[Control.selectedIndex] === EmptyOption ? null : Control.options[Control.selectedIndex].value,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Value", true);
                let Index = 0;
                for(const Option of Control.options) {
                    if(Option.value === Value ?? "") {
                        Control.selectedIndex = Index;
                        break;
                    }
                    Index++;
                }
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => {
                Ensure.Property(Value, [Array, Type.Object], "Validator", true);
                Validator = Value;
                //Clear options.
                while(Control.hasChildNodes()) {
                    Control.removeChild(Control.lastChild);
                }

                Control.appendChild(EmptyOption);

                if(Value instanceof Array) {
                    //Append new options.
                    Value.forEach((Value, Index) => {
                        const Option = document.createElement("option");
                        Option.value = Value;
                        Option.textContent = Value;
                        Control.appendChild(Option);
                        if(Value === this.Value ?? "") {
                            Control.selectedIndex = Index;
                        }
                    });
                } else {
                    //Append new options.
                    Value?.forEach((Value, Key) => {
                        const Option = document.createElement("option");
                        Option.value = Value;
                        Option.textContent = Key;
                        if(Value === this.Value ?? "") {
                            Option.selected = true;
                        }
                        Control.appendChild(Option);
                    });
                }
            }
        },
        Valid:     {
            enumerable: true,
            get:        () => {
                if(!Required && Control.selectedIndex === 0) {
                    return true;
                }
                return Validator instanceof Array
                       ? ~Validator.indexOf(Control.options[Control.selectedIndex].value)
                       : ~Object.values(Validator).indexOf(Control.options[Control.selectedIndex].value)
            },
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                Control.classList.toggle("Error", !Value);
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
            get:        () => !Control.disabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Control.disabled = !Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLSelectElement}
     */
    const Control = document.createElement("select");
    Control.className = "Editor Enum TextBox";
    Control.disabled = !Enabled;

    /**
     * The empty option of the IEditor.
     * @type {HTMLOptionElement}
     */
    const EmptyOption = document.createElement("option");
    EmptyOption.value = "";

    Control.appendChild(EmptyOption);

    //Append specified options.
    if(Validator instanceof Array) {
        //Append new options.
        Validator.forEach((AvailableValue, Index) => {
            const Option = document.createElement("option");
            Option.value = AvailableValue;
            Option.textContent = AvailableValue;
            if(AvailableValue === Value ?? "") {
                Option.selected = true;
            }
            Control.appendChild(Option);
        });
    } else {
        //Append new options.
        Validator?.forEach((AvailableValue, Key) => {
            const Option = document.createElement("option");
            Option.value = AvailableValue;
            Option.textContent = Key;
            if(AvailableValue === Value ?? "") {
                Option.selected = true;
            }
            Control.appendChild(Option);
        });
    }

    Control.addEventListener(
        "change",
        Event => {
            Event.stopPropagation();
            if(Control.options[Control.selectedIndex] === EmptyOption) {
                new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            } else {
                new vDesk.Events.BubblingEvent("update", {
                    sender: this,
                    value:  Control.options[Control.selectedIndex].value
                }).Dispatch(Control);
            }
        },
        false
    );
};
vDesk.Controls.EditControl.Enum.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Enum.Types = [Extension.Type.Enum];