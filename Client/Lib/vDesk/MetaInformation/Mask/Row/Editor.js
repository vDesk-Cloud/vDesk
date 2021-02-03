"use strict";
/**
 * Fired if the Row has been updated.
 * @event vDesk.MetaInformation.Mask.Row.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Row.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if the Row has been deleted.
 * @event vDesk.MetaInformation.Mask.Row.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.MetaInformation.Mask.Row.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a different Editor has been dropped on the Editor.
 * @event vDesk.MetaInformation.Mask.Row.Editor#drop
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'drop' event.
 * @property {vDesk.MetaInformation.Mask.Row.Editor} detail.sender The current instance of the Editor.
 * @property {Number} detail.index The index of the dropped Editor.
 * @property {vDesk.MetaInformation.Mask.Row.Editor} detail.editor The dropped Editor.
 */
/**
 * Class MaskEditor represents... blah.
 * @class
 * @param {vDesk.MetaInformation.Mask.Row} Row Initializes the Editor with the specified Row.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Editor is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.MetaInformation.Mask.Row} Row Gets or sets the Row of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @memberOf vDesk.MetaInformation.Mask.Row
 * @implements vDesk.Controls.Table.IRow
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Mask.Row.Editor = function Editor(Row, Enabled = true) {
    Ensure.Parameter(Row, vDesk.MetaInformation.Mask.Row, "Row");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The amount of drag operations captured on the Editor.
     * @type {Number}
     */
    let DragCount = 0;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Row:     {
            enumerable: true,
            get:        () => Row,
            set:        Value => {
                Ensure.Property(Value, vDesk.MetaInformation.Mask.Row, "Row");
                NameTextBox.value = Row.Name;
                TypeSelect.selectedIndex = TypeSelect.namedItem(Row.Type)?.index ?? 0;
                RequiredCheckbox.checked = Row.Required;

                if(Validator !== null) {
                    ValidatorCell.removeChild(Validator.Control);
                    Validator = null;
                }
                //Loop through registered IValidators and load found.
                for(const IValidator of Object.values(vDesk.MetaInformation.Mask.Row.Validator)) {
                    //Check if the IValidator can handle the type.
                    if(~IValidator.Types.indexOf(Row.Type)) {
                        Validator = new IValidator(Row.Validator, Enabled);
                        ValidatorCell.appendChild(Validator.Control);
                        break;
                    }
                }
            }
        },
        Index:   {
            get: () => Row.Index,
            set: Value => {
                Ensure.Property(Value, Type.Number, "Index");
                Row.Index = Value;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Enabled");
                Enabled = Value;
                Control.draggable = Value;
                NameTextBox.disabled = !Value;
                TypeSelect.disabled = !Value;
                RequiredCheckbox.disabled = !Value;
                DeleteButton.disabled = !Value;
                if(Validator !== null) {
                    Validator.Enabled = Value;
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'input' event on the textbox.
     * @fires vDesk.MetaInformation.Mask.Row.Editor#update
     */
    const OnInput = () => {
        Row.Name = NameTextBox.value;
        new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'change' event.
     * @param {Event} Event
     * @fires vDesk.MetaInformation.Mask.Row.Editor#update
     */
    const OnChange = Event => {
        Event.stopPropagation();
        Row.Type = TypeSelect.options[TypeSelect.selectedIndex].id;
        if(Validator !== null) {
            ValidatorCell.removeChild(Validator.Control);
            Validator = null;
        }
        //Loop through registered IValidators and load matching Validator.
        for(const IValidator of Object.values(vDesk.MetaInformation.Mask.Row.Validator)) {
            //Check if the IValidator can handle the type.
            if(~IValidator.Types.indexOf(Row.Type)) {
                Validator = new IValidator(Row.Validator, Enabled);
                ValidatorCell.appendChild(Validator.Control);
                break;
            }
        }
        new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.MetaInformation.Mask.Row.Editor#update
     */
    const OnClickCheckbox = () => {
        Row.Required = RequiredCheckbox.checked;
        new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @fires vDesk.MetaInformation.Mask.Row.Editor#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Row.Validator = Event.detail.sender.Validator;
        new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.MetaInformation.Mask.Row.Editor#delete
     */
    const OnClickDeleteButton = () => new vDesk.Events.BubblingEvent("delete", {sender: this}).Dispatch(Control);

    /**
     * Eventhandler that listens on the 'dragstart' event.
     * @param {DragEvent} Event
     */
    const OnDragStart = Event => {
        if(
            Event.target === Control
        ) {
            Event.dataTransfer.effectAllowed = "move";
            Event.dataTransfer.setReference(this);
            Control.style.cssText = "cursor: grabbing;";
            Control.removeEventListener("drop", OnDrop, false);
            Control.removeEventListener("dragenter", OnDragEnter, false);
            Control.removeEventListener("dragleave", OnDragLeave, false);
            Control.removeEventListener("dragover", OnDragOver, false);
        }
    };

    /**
     * Eventhandler that listens on the 'dragend' event.
     */
    const OnDragEnd = () => {
        Control.style.cssText = "cursor: grab;";
        Control.addEventListener("drop", OnDrop, false);
        Control.addEventListener("dragenter", OnDragEnter, false);
        Control.addEventListener("dragleave", OnDragLeave, false);
        Control.addEventListener("dragover", OnDragOver, false);
    };

    /**
     * Eventhandler that listens on the 'drop' event.
     * @param {DragEvent} Event
     * @fires vDesk.MetaInformation.Mask.Row.Editor#drop
     */
    const OnDrop = Event => {
        Event.preventDefault();
        Event.stopPropagation();
        const Editor = Event.dataTransfer.getReference();
        if(Editor !== this) {
            Control.removeEventListener("drop", OnDrop, false);
            new vDesk.Events.BubblingEvent("drop", {
                sender: this,
                editor: Editor
            }).Dispatch(Control);
            Control.addEventListener("drop", OnDrop, false);
        }
        DragCount = 0;
        Control.classList.remove("Hover");
    };

    /**
     * Eventhandler that listens on the 'dragenter' event and provides a hover effect.
     * @param {DragEvent} Event
     */
    const OnDragEnter = Event => {
        Event.preventDefault();
        Control.classList.add("Hover");
        DragCount++;
    };

    /**
     * Eventhandler that listens on the 'dragleave' event and removes the hover effect.
     * @param {DragEvent} Event
     */
    const OnDragLeave = Event => {
        Event.preventDefault();
        DragCount--;
        if(DragCount === 0) {
            Control.classList.remove("Hover");
        }
    };

    /**
     * Eventhandler that listens on the 'dragover' event and enables drop.
     * @param {DragEvent} Event
     */
    const OnDragOver = Event => Event.preventDefault();

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableRowElement}
     */
    const Control = document.createElement("tr");
    Control.className = "Row Editor BorderLight";
    Control.addEventListener("dragstart", OnDragStart, false);
    Control.addEventListener("dragend", OnDragEnd, false);
    Control.addEventListener("drop", OnDrop, false);
    Control.addEventListener("dragenter", OnDragEnter, false);
    Control.addEventListener("dragleave", OnDragLeave, false);
    Control.addEventListener("dragover", OnDragOver, false);
    Control.draggable = Enabled;

    /**
     * The name label of the Editor.
     * @type {HTMLTableCellElement}
     */
    const NameCell = document.createElement("td");
    NameCell.className = "Name Cell BorderLight";

    /**
     * The name TextBox of the Editor.
     * @type {HTMLInputElement}
     */
    const NameTextBox = document.createElement("input");
    NameTextBox.className = "Name TextBox BorderDark Background";
    NameTextBox.type = "text";
    NameTextBox.value = Row.Name;
    NameTextBox.disabled = !Enabled;
    NameTextBox.addEventListener("input", OnInput, false);

    NameCell.appendChild(NameTextBox);
    Control.appendChild(NameCell);

    /**
     * The type label of the Editor.
     * @type {HTMLTableCellElement}
     */
    const TypeCell = document.createElement("td");
    TypeCell.className = "Type Cell BorderLight";

    /**
     * The type select of the Editor.
     * @type {HTMLSelectElement}
     */
    const TypeSelect = document.createElement("select");
    TypeSelect.className = "Select Type";
    TypeSelect.disabled = !Enabled;
    TypeSelect.addEventListener("change", OnChange, false);

    /**
     * The integer option of the Editor.
     * @type {HTMLOptionElement}
     */
    const NumericOption = document.createElement("option");
    NumericOption.text = vDesk.Locale.MetaInformation.Numeric;
    NumericOption.id = vDesk.Struct.Type.Int;

    /**
     * The decimal option of the Editor.
     * @type {HTMLOptionElement}
     */
    const NumericDecimalOption = document.createElement("option");
    NumericDecimalOption.text = `${vDesk.Locale.MetaInformation.Numeric}(${vDesk.Locale.MetaInformation.Decimal})`;
    NumericDecimalOption.id = vDesk.Struct.Type.Float;

    /**
     * The text option of the Editor.
     * @type {HTMLOptionElement}
     */
    const TextOption = document.createElement("option");
    TextOption.text = vDesk.Locale.MetaInformation.Text;
    TextOption.id = vDesk.Struct.Type.String;

    /**
     * The bool option of the Editor.
     * @type {HTMLOptionElement}
     */
    const BoolOption = document.createElement("option");
    BoolOption.text = vDesk.Locale.MetaInformation.Boolean;
    BoolOption.id = vDesk.Struct.Type.Bool;

    /**
     * The money option of the Editor.
     * @type {HTMLOptionElement}
     */
    const MoneyOption = document.createElement("option");
    MoneyOption.text = vDesk.Locale.MetaInformation.Money;
    MoneyOption.id = Extension.Type.Money;

    /**
     * The email option of the Editor.
     * @type {HTMLOptionElement}
     */
    const EmailOption = document.createElement("option");
    EmailOption.text = vDesk.Locale.Security.Email;
    EmailOption.id = Extension.Type.Email;

    /**
     * The URL option of the Editor.
     * @type {HTMLOptionElement}
     */
    const URLOption = document.createElement("option");
    URLOption.text = "URL";
    URLOption.id = Extension.Type.URL;

    /**
     * The date option of the Editor.
     * @type {HTMLOptionElement}
     */
    const DateOption = document.createElement("option");
    DateOption.text = vDesk.Locale.MetaInformation.Date;
    DateOption.id = Extension.Type.Date;

    /**
     * The time option of the Editor.
     * @type {HTMLOptionElement}
     */
    const TimeOption = document.createElement("option");
    TimeOption.text = vDesk.Locale.MetaInformation.Time;
    TimeOption.id = Extension.Type.Time;

    /**
     * The datetime option of the Editor.
     * @type {HTMLOptionElement}
     */
    const DateTimeOption = document.createElement("option");
    DateTimeOption.text = vDesk.Locale.MetaInformation.DateTime;
    DateTimeOption.id = Extension.Type.DateTime;

    TypeSelect.add(NumericOption);
    TypeSelect.add(NumericDecimalOption);
    TypeSelect.add(TextOption);
    TypeSelect.add(BoolOption);
    TypeSelect.add(MoneyOption);
    TypeSelect.add(EmailOption);
    TypeSelect.add(URLOption);
    TypeSelect.add(DateOption);
    TypeSelect.add(TimeOption);
    TypeSelect.add(DateTimeOption);
    TypeSelect.selectedIndex = TypeSelect.namedItem(Row.Type)?.index ?? 0;

    TypeCell.appendChild(TypeSelect);
    Control.appendChild(TypeCell);

    /**
     * The name cell of the Editor.
     * @type {HTMLTableCellElement}
     */
    const RequiredCell = document.createElement("td");
    RequiredCell.className = "Required Cell BorderLight";

    /**
     * The required checkbox of the Editor.
     * @type {HTMLInputElement}
     */
    const RequiredCheckbox = document.createElement("input");
    RequiredCheckbox.className = "CheckBox Required";
    RequiredCheckbox.type = "checkbox";
    RequiredCheckbox.checked = Row.Required;
    RequiredCheckbox.disabled = !Enabled;
    RequiredCheckbox.addEventListener("click", OnClickCheckbox, false);

    RequiredCell.appendChild(RequiredCheckbox);
    Control.appendChild(RequiredCell);

    /**
     * The Validator cell of the Editor.
     * @type {HTMLTableCellElement}
     */
    const ValidatorCell = document.createElement("td");
    ValidatorCell.className = "Cell BorderLight";
    ValidatorCell.addEventListener("update", OnUpdate, false);

    /**
     * The Validator of the Editor.
     * @type {null|vDesk.MetaInformation.Mask.Row.IValidator}
     */
    let Validator = null;
    //Loop through registered IValidators and load found.
    for(const IValidator of Object.values(vDesk.MetaInformation.Mask.Row.Validator)) {
        //Check if the IValidator can handle the type.
        if(~IValidator.Types.indexOf(Row.Type)) {
            Validator = new IValidator(Row.Validator, Enabled);
            ValidatorCell.appendChild(Validator.Control);
            break;
        }
    }

    Control.appendChild(ValidatorCell);

    /**
     * The delete button cell of the Editor.
     * @type {HTMLTableCellElement}
     */
    const DeleteCell = document.createElement("td");
    DeleteCell.className = "Cell Delete BorderLight";

    /**
     * The delete button of the Editor.
     * @type {HTMLButtonElement}
     */
    const DeleteButton = document.createElement("button");
    DeleteButton.className = "Button Delete";
    DeleteButton.textContent = "×";
    DeleteButton.title = vDesk.Locale.MetaInformation.DeleteRow;
    DeleteButton.disabled = !Enabled;
    DeleteButton.addEventListener("click", OnClickDeleteButton, false);

    DeleteCell.appendChild(DeleteButton);
    Control.appendChild(DeleteCell);

};

vDesk.MetaInformation.Mask.Row.Editor.Implements(vDesk.Controls.Table.IRow);