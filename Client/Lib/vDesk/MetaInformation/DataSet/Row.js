"use strict";
/**
 * Fired if the content of the Row has been updated.
 * @event vDesk.MetaInformation.DataSet.Row#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Row} detail.sender The current instance of the Row.
 */
/**
 * Fired if the content of a virtual Row has been updated.
 * @event vDesk.MetaInformation.DataSet.Row#add
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'add' event.
 * @property {vDesk.MetaInformation.Mask.Row} detail.sender The current instance of the Row.
 */
/**
 * Fired if the content of the Row has been changed.
 * @event vDesk.MetaInformation.DataSet.Row#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.MetaInformation.Mask.Row} detail.sender The current instance of the Row.
 */
/**
 * Initializes a new instance of the Row class.
 * @class Represents a row of data of an dataset.
 * @param {Number} [ID=null] Initializes the Row with the specified ID.
 * @param {vDesk.MetaInformation.Mask.Row} [Row=null] The associated Mask.Row of the DataSet.Row.
 * @param {*} [Value=null] Initializes the Row with the specified Value.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Row is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {Number} ID Gets or sets the ID of the Row.
 * @property {vDesk.MetaInformation.Mask.Row} Row Gets or sets the associated Mask.Row of the DataSet.Row.
 * @property {?*} Value Gets or sets the content of the Row.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Row is enabled.
 * @memberOf vDesk.MetaInformation.DataSet
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\MetaInformation
 */
vDesk.MetaInformation.DataSet.Row = function Row(Row, ID = null, Value = null, Enabled = true) {
    Ensure.Parameter(Row, vDesk.MetaInformation.Mask.Row, "Row");
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

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
                Row = Value;
                EditControl.Label = `${Value.Name}${Value.Required ? "*" : ""}`;
                EditControl.Type = Value.Type;
                EditControl.Validator = Value.Validator;
            }
        },
        ID:      {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Value:   {
            enumerable: true,
            get:        () => EditControl.Value,
            set:        ValueToSet => {
                Value = ValueToSet;
                if(ValueToSet !== null){
                    EditControl.Value = ValueToSet;
                }
            }
        },
        Valid:   {
            get: () => Row.Required ? Value !== null && EditControl.Valid : EditControl.Valid,
            set: Value => EditControl.Valid = Value
        },
        Enabled: {
            enumerable: true,
            get:        () => EditControl.Enabled,
            set:        Value => EditControl.Enabled = Value
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @fires vDesk.MetaInformation.DataSet.Row#update
     * @fires vDesk.MetaInformation.DataSet.Row#add
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Value = EditControl.Value;
        //Check if the Row is virtual.
        if(ID === null){
            new vDesk.Events.BubblingEvent("add", {sender: this}).Dispatch(Control);
        }else{
            new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);
        }
        Control.addEventListener("update", OnUpdate, {once: true});
    };

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.EditControl#event:clear
     * @fires vDesk.MetaInformation.DataSet.Row#clear
     * @param {CustomEvent} Event
     */
    const OnClear = Event => {
        Event.stopPropagation();
        Value = null;
        new vDesk.Events.BubblingEvent("delete", {sender: this}).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Row";
    Control.addEventListener("update", OnUpdate, {once: true});
    Control.addEventListener("clear", OnClear, false);

    /**
     * The EditControl of the DataSet.Row.
     * @type {vDesk.Controls.EditControl}
     */
    const EditControl = new vDesk.Controls.EditControl(
        `${Row?.Name ?? ""}${Row?.Required ?? false ? "*" : ""}`,
        null,
        Row?.Type ?? Type.String,
        Value,
        Row?.Validator ?? null,
        Row?.Required ?? false,
        Enabled
    );
    Control.appendChild(EditControl.Control);

};

/**
 * Factory method that creates a DataSet.Row from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Row.
 * @return {vDesk.MetaInformation.DataSet.Row} A Row filled with the provided data.
 */
vDesk.MetaInformation.DataSet.Row.FromDataView = function(DataView) {
    return new vDesk.MetaInformation.DataSet.Row(
        DataView?.ID ?? null,
        DataView?.Row ?? null,
        DataView?.Value ?? null
    );
};