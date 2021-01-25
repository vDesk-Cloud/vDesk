"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.Money#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.Money} detail.sender The current instance of the IEditor.
 * @property {String} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.Money#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.Money} detail.sender The current instance of the IEditor.
 */
/**
 * Initializes a new instance of the Money class.
 * @class Represents a currency based numeric type value editor of an EditControl.
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
vDesk.Controls.EditControl.Money = function Money(Value = null, Validator = null, Required = false, Enabled = false) {
    Ensure.Parameter(Value, Type.String, "Value", true);
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
            get:        () => TextBox.value.length > 0 ? `${TextBox.value}${Validator.Currency}` : null,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Value", true);
                const Amount = Number.parseFloat(Value);
                TextBox.value = Number.isFinite(Amount) ? Amount : "";
            }
        },
        Validator: {
            enumerable: true,
            get:        () => Validator,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Validator", true);
                Validator = Value;
                TextBox.min = Value?.Min ?? 0;
                TextBox.max = Value?.Max ?? Number.MAX_VALUE;
                Currency.textContent = Value?.Currency ?? "€";
            }
        },
        Valid:     {
            enumerable: true,
            get:        () => TextBox.validity.valid,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Valid");
                Control.classList.toggle("Error", !Value);
            }
        },
        Required:  {
            enumerable: true,
            get:        () => TextBox.required,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Required");
                TextBox.required = Value;
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => !TextBox.disabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                TextBox.disabled = !Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Editor Money";

    /**
     * The amount TextBox of the IEditor.
     * @type {HTMLInputElement}
     */
    const TextBox = document.createElement("input");
    TextBox.className = "Amount TextBox";
    TextBox.type = Type.Number;

    const Amount = Number.parseFloat(Value);
    TextBox.value = Number.isFinite(Amount) ? Amount : "";

    TextBox.min = Validator?.Min ?? 0;
    TextBox.max = Validator?.Max ?? Number.MAX_VALUE;
    TextBox.step = "0.1";
    TextBox.required = Required;
    TextBox.disabled = !Enabled;
    TextBox.addEventListener(
        "change",
        Event => {
            Event.stopPropagation();
            if(TextBox.value.length > 0) {
                new vDesk.Events.BubblingEvent("update", {
                    sender: this,
                    value:  `${TextBox.value}${Validator?.Currency ?? "€"}`
                }).Dispatch(Control);
            } else {
                new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            }
        },
        false
    );
    Control.appendChild(TextBox);

    /**
     * The currency label of the IEditor.
     * @type {HTMLSpanElement}
     */
    const Currency = document.createElement("span");
    Currency.className = "Currency Font Dark";
    Currency.textContent = Validator?.Currency ?? "€";
    Control.appendChild(Currency);
};
vDesk.Controls.EditControl.Money.Implements(vDesk.Controls.EditControl.IEditor);
vDesk.Controls.EditControl.Money.Types = [Extension.Type.Money];