"use strict";
/**
 * Fired if the Option has been updated.
 * @event vDesk.Contacts.Contact.Option#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Contacts.Contact.Option} detail.sender The current instance of the Option.
 */
/**
 * Fired if the Option has been deleted.
 * @event vDesk.Contacts.Contact.Option#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Contacts.Contact.Option} detail.sender The current instance of the Option.
 */
/**
 * Initializes a new instance of the Option class.
 * @class Represents a contact option like an email-address or a telephone number.
 * @param {?Number} [ID=null] Initializes the Option with the specified ID.
 * @param {Number} [Type=vDesk.Contacts.Contact.Option.Type.Telephone] Initializes the Option with the specified type.
 * @param {String} [Value=""] Initializes the Option with the specified value.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Option is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {?Number} ID Gets or sets the ID of the Option.
 * @property {Number} Type Gets or sets the type of the Option.
 * @property {String} Value Gets or sets the value of the Option.
 * @property {Boolean} Valid Gets or sets a value indicating whether the value of the Option is valid.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Option is enabled.
 * @memberOf vDesk.Contacts.Contact
 * @package vDesk\Contacts
 */
vDesk.Contacts.Contact.Option = function Option(ID = null, Type = vDesk.Contacts.Contact.Option.Type.Telephone, Value = "", Enabled = true) {
    Ensure.Parameter(ID, vDesk.Struct.Type.Number, "ID", true);
    Ensure.Parameter(Type, vDesk.Struct.Type.Number, "Type");
    Ensure.Parameter(Value, vDesk.Struct.Type.String, "Value");
    Ensure.Parameter(Enabled, vDesk.Struct.Type.Boolean, "Enabled");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        ID:      {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "ID", true);
                ID = Value;
            }
        },
        Type:    {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "Type");
                Type = Value;
                ValueTextBox.pattern = null;
                switch(Value){
                    case vDesk.Contacts.Contact.Option.Type.Telephone:
                    case vDesk.Contacts.Contact.Option.Type.Fax:
                        ValueTextBox.type = "text";
                        ValueTextBox.pattern = vDesk.Utils.Expression.Telephone.toString();
                        break;
                    case vDesk.Contacts.Contact.Option.Type.Email:
                        ValueTextBox.type = Extension.Type.Email;
                        break;
                    case vDesk.Contacts.Contact.Option.Type.Website:
                        ValueTextBox.type = Extension.Type.URL;
                        break;
                }
                ValueTextBox.classList.toggle("Error", !this.Valid);
            }
        },
        Value:   {
            enumerable: true,
            get:        () => ValueTextBox.value,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Value");
                ValueTextBox.value = Value;
                ValueTextBox.classList.toggle("Error", !this.Valid);
            }
        },
        Valid:   {
            enumerable: true,
            get:        () => ValueTextBox.value.length > 0 && ValueTextBox.validity.valid,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Valid");
                ValueTextBox.classList.toggle("Error", Value);
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Enabled");
                Enabled = Value;
                ValueTextBox.disabled = !Value;
                DeleteButton.disabled = !Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'change' event.
     * @fires vDesk.Contacts.Contact.Option#update
     */
    const OnChange = () => {
        ValueTextBox.classList.toggle("Error", !this.Valid);
        if(ID !== null){
            new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Contacts.Contact.Option#delete
     */
    const OnClick = () => {
        new vDesk.Events.BubblingEvent("delete", {sender: this}).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Option";

    /**
     * THe value textbox of the Option.
     * @type {HTMLInputElement}
     */
    const ValueTextBox = document.createElement("input");
    switch(Type){
        case vDesk.Contacts.Contact.Option.Type.Telephone:
        case vDesk.Contacts.Contact.Option.Type.Fax:
            ValueTextBox.type = "text";
            ValueTextBox.pattern = vDesk.Utils.Expression.Telephone.toString();
            break;
        case vDesk.Contacts.Contact.Option.Type.Email:
            ValueTextBox.type = Extension.Type.Email;
            break;
        case vDesk.Contacts.Contact.Option.Type.Website:
            ValueTextBox.type = Extension.Type.URL;
            break;
    }
    ValueTextBox.className = "Value TextBox";
    ValueTextBox.value = Value;
    ValueTextBox.disabled = !Enabled;
    ValueTextBox.addEventListener("change", OnChange, false);

    /**
     * The delete button of the Option.
     * @type {HTMLButtonElement}
     */
    const DeleteButton = document.createElement("button");
    DeleteButton.className = "Button Delete";
    DeleteButton.textContent = "x";
    DeleteButton.title = vDesk.Locale.Contacts.DeleteOption;
    DeleteButton.disabled = !Enabled;
    DeleteButton.addEventListener("click", OnClick, false);

    Control.appendChild(ValueTextBox);
    Control.appendChild(DeleteButton);
};

/**
 * Factory method that creates a Option from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Option.
 * @return {vDesk.Contacts.Contact.Option} A Option filled with the provided data.
 */
vDesk.Contacts.Contact.Option.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, "object", "DataView");
    return new vDesk.Contacts.Contact.Option(
        DataView?.ID ?? null,
        DataView?.Type ?? vDesk.Contacts.Contact.Option.Type.Telephone,
        DataView?.Value ?? ""
    );
};

/**
 * Enumeration of available types for Options.
 * @enum {Number}
 */
vDesk.Contacts.Contact.Option.Type = {
    Telephone: 0,
    Fax:       1,
    Email:     2,
    Website:   3
};