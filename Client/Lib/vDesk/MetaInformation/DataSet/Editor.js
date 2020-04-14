"use strict";
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for modifying or creating DataSets of metainformations.
 * @param {vDesk.Archive.Element} Element Initializes the Editor with the specified Element to tag with metadata.
 * @param {vDesk.MetaInformation.DataSet} DataSet Initializes the Editor with the specified DataSet to edit.
 * @param {Boolean} [Enabled=false]
 *
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Archive.Element} Element Gets or sets the Element of the Editor.
 * @property {?vDesk.MetaInformation.DataSet} DataSet Gets or sets the current edited DataSet of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the data of the current edited DataSet of Editor has been changed.
 * @memberOf vDesk.MetaInformation.DataSet
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.DataSet.Editor = function Editor(Element, DataSet = null, Enabled = false) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
    Ensure.Parameter(DataSet, vDesk.MetaInformation.DataSet, "DataSet", true);
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The added Rows of the Mask of the Editor.
     * @type {Array<vDesk.MetaInformation.DataSet.Row>}
     */
    let Added = [];

    /**
     * The updated Rows of the Mask of the Editor.
     * @type {Array<vDesk.MetaInformation.DataSet.Row>}
     */
    let Updated = [];

    /**
     * The deleted Rows of the Mask of the Editor.
     * @type {Array<vDesk.MetaInformation.DataSet.Row>}
     */
    let Deleted = [];

    /**
     * Flag indicating whether the DataSet of the Editor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    /**
     * A copy of the current edited DataSet to restore its values.
     * @type {null|vDesk.MetaInformation.DataSet}
     */
    let PreviousDataSet = null;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Element: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Archive.Element, "Element");
                Element = Value;
            }
        },
        DataSet: {
            enumerable: true,
            get:        () => DataSet,
            set:        Value => {
                Ensure.Property(Value, vDesk.MetaInformation.DataSet, "DataSet", true);

                if(DataSet !== null) {
                    Control.removeChild(DataSet.Control);
                }

                DataSet = Value;

                //Clear Rows.
                Added = [];
                Updated = [];
                Deleted = [];

                if(Value !== null) {
                    Control.textContent = "";
                    if(Value.ID !== null){
                        PreviousDataSet = vDesk.MetaInformation.DataSet.FromDataView(Value);
                    }
                    Control.appendChild(Value.Control);
                } else {
                    Control.textContent = vDesk.Locale["MetaInformation"]["NoDataSet"];
                }
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
                if(DataSet !== null) {
                    DataSet.Enabled = Value;
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.MetaInformation.Mask.Editor#change
     */
    const OnAdd = Event => {
        Event.stopPropagation();
        if(DataSet.ID !== null && !~Added.indexOf(Event.detail.sender)) {
            Added.push(Event.detail.sender);
        }
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.MetaInformation.DataSet.Row#event:update
     * @fires vDesk.MetaInformation.DataSet.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        if(DataSet.ID !== null && Event.detail.sender.ID !== null && !~Updated.indexOf(Event.detail.sender)) {
            Updated.push(Event.detail.sender);
            //Check if the Row has been cleared before.
            const Index = Deleted.indexOf(Event.detail.sender);
            if(~Index) {
                Deleted.splice(Index, 1);
            }
        }

        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.MetaInformation.DataSet.Row#event:delete
     * @fires vDesk.MetaInformation.DataSet.Editor#change
     * @param {CustomEvent} Event
     */
    const OnDelete = Event => {
        Event.stopPropagation();

        if(DataSet.ID !== null && Event.detail.sender.ID !== null && !~Deleted.indexOf(Event.detail.sender)) {
            Deleted.push(Event.detail.sender);
            //Check if the Row has been updated before.
            const Index = Updated.indexOf(Event.detail.sender);
            if(~Index) {
                Updated.splice(Index, 1);
            }
        }

        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Saves possible changes.
     */
    this.Save = function() {
        if(DataSet === null) {
            throw new SyntaxError("DataSet to save is null!");
        }
        //Check if the DataSet is not virtual.
        if(DataSet.ID !== null) {
            //Delete DataSet if all Rows have been cleared.
            if(DataSet.Rows.every(Row => Row.Value === null)) {
                this.Delete();
            } else {
                //Update DataSet.
                vDesk.Connection.Send(
                    new vDesk.Modules.Command(
                        {
                            Module:     "MetaInformation",
                            Command:    "UpdateDataSet",
                            Parameters: {
                                ID:     DataSet.ID,
                                Rows:    DataSet.Rows.map(Row => ({
                                    ID:    Row.ID,
                                    Value: Row.Value
                                })),
                            },
                            Ticket:     vDesk.User.Ticket
                        }
                    ),
                    Response => {
                        if(Response.Status) {
                            this.DataSet = vDesk.MetaInformation.DataSet.FromDataView(Response.Data);
                            Enabled = false;
                            new vDesk.Events.BubblingEvent("update", {
                                sender:  this,
                                dataset: DataSet
                            }).Dispatch(Control);
                        } else {
                            alert(Response.Data);
                            this.Reset();
                        }
                    }
                );
            }
        } else {
            //Check if the Mask has been changed.
            if(PreviousDataSet !== null && PreviousDataSet.Mask.ID !== DataSet.Mask.ID) {
                vDesk.Connection.Send(
                    new vDesk.Modules.Command(
                        {
                            Module:     "MetaInformation",
                            Command:    "DeleteDataSet",
                            Parameters: {ID: PreviousDataSet.ID},
                            Ticket:     vDesk.User.Ticket
                        }
                    ),
                    Response => {
                        if(!Response.Status) {
                            this.Reset();
                            alert(Response.Data);
                        }
                    }
                );
            }
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "MetaInformation",
                        Command:    "CreateDataSet",
                        Parameters: {
                            Element: Element.ID,
                            Mask:    DataSet.Mask.ID,
                            Rows:    DataSet.Rows.map(Row => ({
                                Row:   Row.Row.ID,
                                Value: Row.Value
                            }))
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    //Check if the Command has been successfully executed and populate data to a new DataSet.
                    if(Response.Status) {
                        this.DataSet = vDesk.MetaInformation.DataSet.FromDataView(Response.Data);
                        Enabled = false;
                        new vDesk.Events.BubblingEvent("create", {
                            sender:  this,
                            dataset: DataSet
                        }).Dispatch(Control);
                    } else {
                        this.Reset();
                        alert(Response.Data);
                    }
                }
            );

        }
    };

    /**
     * Deletes the current edited DataSet.
     */
    this.Delete = function() {
        if(DataSet.ID !== null) {
            //Delete DataSet.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "MetaInformation",
                        Command:    "DeleteDataSet",
                        Parameters: {ID: PreviousDataSet.ID},
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        this.DataSet = null;
                    } else {
                        this.Reset();
                        alert(Response.Data);
                    }
                }
            );
        }
    };

    /**
     * Resets possible made changes of the current edited Mask.
     */
    this.Reset = function() {
        this.DataSet = DataSet === null || DataSet.ID === null ? null : PreviousDataSet;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "DataSet Editor Font Dark";
    Control.addEventListener("add", OnAdd, false);
    Control.addEventListener("update", OnUpdate, false);
    Control.addEventListener("delete", OnDelete, false);

    if(DataSet !== null) {
        PreviousDataSet = vDesk.MetaInformation.DataSet.FromDataView(DataSet);
        DataSet.Enabled = Enabled;
        Control.appendChild(DataSet.Control);
    } else {
        Control.textContent = vDesk.Locale["MetaInformation"]["NoDataSet"];
    }
};

/**
 /**
 * Factory method that creates an Editor from a specified Element.
 * @param {vDesk.Archive.Element} Element The Element to use to create an instance of the Editor.
 * @return {vDesk.MetaInformation.DataSet.Editor} An Editor yielding any DataSet of the specified Element.
 */
vDesk.MetaInformation.DataSet.Editor.FromElement = function(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    //Check if the passed element already has a dataset.
    const Response = vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "MetaInformation",
                Command:    "GetDataSet",
                Parameters: {Element: Element.ID},
                Ticket:     vDesk.User.Ticket
            }
        )
    );

    if(!Response.Status) {
        alert(Response.Data);
    }
    return new vDesk.MetaInformation.DataSet.Editor(
        Element,
        Response.Data !== null ? vDesk.MetaInformation.DataSet.FromDataView(Response.Data) : Response.Data
    );

};
