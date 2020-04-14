"use strict";
/**
 * Fired if the current edited Mask of the Editor has been changed.
 * @event vDesk.MetaInformation.Mask.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.MetaInformation.Mask.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a new Mask has been created.
 * @event vDesk.MetaInformation.Mask.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.MetaInformation.Mask.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.MetaInformation.Mask} detail.mask The newly created Mask.
 */
/**
 * Fired if the current edited Mask of the Editor has been updated.
 * @event vDesk.MetaInformation.Mask.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.MetaInformation.Mask} detail.mask The updated Mask.
 */
/**
 * Fired if the current edited Mask of the Editor has been deleted.
 * @event vDesk.MetaInformation.Mask.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.MetaInformation.Mask.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.MetaInformation.Mask} detail.mask The deleted Mask.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for creating new, modifying or deleting existing {@link vDesk.MetaInformation.Mask}s.
 * @param {vDesk.MetaInformation.Mask} Mask Initializes the Editor with the specified Mask.
 * @param {Boolean} [Enabled=false] Flag indicating whether the Editor is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.MetaInformation.Mask} Mask Gets or sets the current edited Mask of the Editor.
 * @property {Boolean} Changed Gets a value indicating whether the Mask of the Editor has been changed.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @memberOf vDesk.MetaInformation.Mask
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Mask.Editor = function Editor(Mask, Enabled = true) {
    Ensure.Parameter(Mask, vDesk.MetaInformation.Mask, "Mask");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The added Rows of the Mask of the Editor.
     * @type {Array<vDesk.MetaInformation.Mask.Row>}
     */
    let Added = [];

    /**
     * The updated Rows of the Mask of the Editor.
     * @type {Array<vDesk.MetaInformation.Mask.Row>}
     */
    let Updated = [];

    /**
     * The deleted Rows of the Mask of the Editor.
     * @type {Array<vDesk.MetaInformation.Mask.Row>}
     */
    let Deleted = [];

    /**
     * Flag indicating whether the Mask of the Editor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    /**
     * A copy of the current edited Mask to restore its values.
     * @type {vDesk.MetaInformation.Mask}
     */
    let PreviousMask = vDesk.MetaInformation.Mask.FromDataView(Mask);

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Mask:    {
            enumerable: true,
            get:        () => Mask,
            set:        Value => {
                Ensure.Property(Value, vDesk.MetaInformation.Mask, "Mask");
                Mask = Value;
                NameTextBox.value = Value.Name;
                Added = [];
                Updated = [];
                Deleted = [];

                PreviousMask = vDesk.MetaInformation.Mask.FromDataView(Value);

                //Clear Rows list.
                Table.Rows.Clear();
                document.createDocumentFragment();
                Value.Rows
                    .sort((First, Second) => First.Index - Second.Index)
                     .forEach(Row => Table.Rows.Add(new vDesk.MetaInformation.Mask.Row.Editor(Row, Enabled)));
                Changed = false;
            }
        },
        Changed: {
            enumerable: true,
            get:        () => Changed
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                NameTextBox.disabled = !Value;
                Table.Rows.forEach(Editor => Editor.Enabled = Value);
                AddRowButton.disabled = !Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'drop' event.
     * @listens vDesk.MetaInformation.Mask.Row.Editor#event:drop
     * @fires vDesk.MetaInformation.Mask.Editor#change
     * @param {CustomEvent} Event
     */
    const OnDrop = Event => {

        if(Event.detail.editor.Row.Index < Event.detail.sender.Row.Index) {

            //Swap index of the dropped Row with index of the target Row.
            Event.detail.editor.Row.Index = Event.detail.sender.Row.Index;
            //Increment index property of each Row beginning at the drop target.
            for(let Index = Table.Rows.indexOf(Event.detail.editor) + 1; Index < Table.Rows.length; Index++) {
                Table.Rows[Index].Row.Index--;
                //Check if the Row is not virtual and add it to the collection of updated Rows.
                if(Table.Rows[Index].Row.ID !== null && !~Updated.indexOf(Table.Rows[Index].Row)) {
                    Updated.push(Table.Rows[Index].Row);
                }
            }
        } else {

            //Swap index of the dropped Row with index of the target Row.
            Event.detail.editor.Row.Index = Event.detail.sender.Row.Index;

            //Increment index property of each Row beginning at the drop target.
            for(let Index = Table.Rows.indexOf(Event.detail.sender); Index < Table.Rows.length; Index++) {
                if(Table.Rows[Index] !== Event.detail.editor) {
                    Table.Rows[Index].Row.Index++;
                    //Check if the Row is not virtual and add it to the collection of updated Rows.
                    if(Table.Rows[Index].Row.ID !== null && !~Updated.indexOf(Table.Rows[Index].Row)) {
                        Updated.push(Table.Rows[Index].Row);
                    }
                }
            }
        }

        //Check if the Row of the dropped Editor is not virtual and add it to the collection of updated Rows.
        if(Event.detail.editor.Row.ID !== null && !~Updated.indexOf(Event.detail.editor.Row)) {
            Updated.push(Event.detail.editor.Row);
        }

        //Sort Rows ascending by index.
        Table.Sort((First, Second) => First.Row.Index - Second.Row.Index);

        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.MetaInformation.Mask.Row.Editor#event:update
     * @fires vDesk.MetaInformation.Mask.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        if(Mask.ID !== null && Event.detail.sender.Row.ID !== null && !~Updated.indexOf(Event.detail.sender.Row)) {
            Updated.push(Event.detail.sender.Row)
        }
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.MetaInformation.Mask.Row.Editor#event:delete
     * @fires vDesk.MetaInformation.Mask.Editor#change
     * @param {CustomEvent} Event
     */
    const OnDelete = Event => {
        Event.stopPropagation();
        Table.Rows.Remove(Event.detail.sender);
        if(Mask.ID !== null && Event.detail.sender.Row.ID !== null) {
            Deleted.push(Event.detail.sender.Row);
        }
        Mask.Rows.splice(Mask.Rows.indexOf(Event.detail.sender.Row), 1);
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.MetaInformation.Mask.Editor#change
     */
    const OnClickAddRowButton = () => {
        const Row = new vDesk.MetaInformation.Mask.Row(null, Mask.Rows.length);
        Added.push(Row);
        Table.Rows.Add(new vDesk.MetaInformation.Mask.Row.Editor(Row, Enabled));
        Mask.Rows.push(Row);
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'input' event.
     * @fires vDesk.MetaInformation.Mask.Editor#change
     */
    const OnInput = () => {
        NameTextBox.classList.toggle(
            "Error",
            NameTextBox.value.length === 0
            || vDesk.MetaInformation.Masks.some(Mask => Mask.Name === NameTextBox.value)
        );
        Mask.Name = NameTextBox.value;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Saves possible made changes of the current edited Mask.
     */
    this.Save = function() {
        if(Mask.ID !== null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "MetaInformation",
                        Command:    "UpdateMask",
                        Parameters: {
                            ID:     Mask.ID,
                            Name:   NameTextBox.value,
                            Add:    Added.map(Row => ({
                                Index:     Row.Index,
                                Name:      Row.Name,
                                Type:      Row.Type,
                                Required:  Row.Required,
                                Validator: Row.Validator
                            })),
                            Update: Updated.map(Row => ({
                                ID:        Row.ID,
                                Index:     Row.Index,
                                Name:      Row.Name,
                                Type:      Row.Type,
                                Required:  Row.Required,
                                Validator: Row.Validator
                            })),
                            Delete: Deleted.map(Row => Row.ID)
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        this.Mask = vDesk.MetaInformation.Mask.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("update", {
                            sender: this,
                            mask:   Mask
                        }).Dispatch(Control);
                    }
                }
            );
        } else {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "MetaInformation",
                        Command:    "CreateMask",
                        Parameters: {
                            Name: NameTextBox.value,
                            Rows: Mask.Rows.map(Row => ({
                                Index:     Row.Index,
                                Name:      Row.Name,
                                Type:      Row.Type,
                                Required:  Row.Required,
                                Validator: Row.Validator
                            }))
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        this.Mask = vDesk.MetaInformation.Mask.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("create", {
                            sender: this,
                            mask:   Mask
                        }).Dispatch(Control);
                    }
                }
            );
        }
    };

    /**
     * Deletes the current edited Mask.
     */
    this.Delete = function() {
        if(Mask.ID !== null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "MetaInformation",
                        Command:    "DeleteMask",
                        Parameters: {ID: Mask.ID},
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        new vDesk.Events.BubblingEvent("delete", {
                            sender: this,
                            mask:   Mask
                        }).Dispatch(Control);
                    }
                }
            );
        }
    };

    /**
     * Resets possible made changes of the current edited Mask.
     */
    this.Reset = function() {
        this.Mask = PreviousMask;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Mask Editor";
    Control.addEventListener("drop", OnDrop, false);

    /**
     * The row containing the name TextBox of the Mask.
     * @type {HTMLDivElement}
     */
    const ControlsRow = document.createElement("div");
    ControlsRow.className = "Controls";

    /**
     * The name TextBox of the Editor.
     * @type {HTMLInputElement}
     */
    const NameTextBox = document.createElement("input");
    NameTextBox.className = "TextBox Name";
    NameTextBox.type = "text";
    NameTextBox.value = Mask.Name;
    NameTextBox.disabled = !Enabled;
    NameTextBox.addEventListener("input", OnInput, false);

    ControlsRow.appendChild(NameTextBox);

    /**
     * The rows Table of the Editor
     * @type {vDesk.Controls.Table}
     */
    const Table = new vDesk.Controls.Table(
        [
            {
                Name:  "Name",
                Label: vDesk.Locale["vDesk"]["Name"],
                Type:  HTMLInputElement
            },
            {
                Name:  "Type",
                Label: vDesk.Locale["vDesk"]["Type"],
                Type:  HTMLSelectElement
            },
            {
                Name:  "Required",
                Label: vDesk.Locale["MetaInformation"]["Required"],
                Type:  HTMLInputElement
            },
            {
                Name:  "Validator",
                Label: "Validator",
                Type:  HTMLInputElement
            },
            {
                Name:  "Delete",
                Label: " ",
                Type:  HTMLButtonElement
            }
        ]
    );
    Table.Control.classList.add("Rows");
    Table.Control.classList.add("BorderLight");
    Table.Control.addEventListener("update", OnUpdate, false);
    Table.Control.addEventListener("delete", OnDelete, false);

    /**
     * The header row of the Editor.
     * @type {HTMLTableHeaderCellElement}
     */
    const Header = document.createElement("th");
    Header.className = "Header";

    const NameCell = document.createElement("td");
    NameCell.textContent = vDesk.Locale["vDesk"]["Name"];

    Mask.Rows
        .sort((First, Second) => First.Index - Second.Index)
        .forEach(Row => Table.Rows.Add(new vDesk.MetaInformation.Mask.Row.Editor(Row, Enabled)));

    /**
     * The addrow button of the Mask.
     * @type {HTMLButtonElement}
     */
    const AddRowButton = document.createElement("button");
    AddRowButton.className = "Button Add";
    AddRowButton.textContent = "+";
    AddRowButton.title = vDesk.Locale["MetaInformation"]["AddRow"];
    AddRowButton.disabled = !Enabled;
    AddRowButton.addEventListener("click", OnClickAddRowButton, false);

    Control.appendChild(ControlsRow);
    Control.appendChild(Table.Control);
    Control.appendChild(AddRowButton);

};
