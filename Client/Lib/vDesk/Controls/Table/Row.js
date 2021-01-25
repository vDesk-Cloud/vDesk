"use strict";
/**
 * Fired if the Row has been selected.
 * @event vDesk.Controls.Table.Row#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Controls.Table.Row} detail.sender The current instance of the Row.
 */
/**
 * Fired if the value of a Rows Cell has been updated.
 * @event vDesk.Controls.Table.Row#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.Table.Row} detail.sender The current instance of the Row.
 * @property {vDesk.Controls.Table.Cell} detail.cell The current instance of the updated Cell.
 * @property {*} detail.value The value of the updated Cell.
 */
/**
 * Initializes a new instance of the Row class.
 * @class Represents a row of data within a table.
 * @param {Array<vDesk.Controls.Table.Column>} Columns Initializes the row with a given set of columns.
 * @property {HTMLTableRowElement} Control Gets the underlying DOM-Node.
 * @property {Number} Index Gets or sets the numerical index of the Row.
 * @property {Array<vDesk.Controls.Table.Cell>} Cells Gets the Cells of the Row.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Row is selected.
 * @memberOf vDesk.Controls.Table
 * @implements vDesk.Controls.Table.IRow
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table.Row = function Row(Columns = []) {
    Ensure.Parameter(Columns, Array, "Columns");

    /**
     * The index of the Row.
     * @type {Number}
     */
    let Index = 0;

    /**
     * The Cells of the Row.
     * @type {Array<vDesk.Controls.Table.Cell>}
     */
    const Cells = [];

    /**
     * Flag indicating whether the Row is selected.
     * @type {Boolean}
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Index:    {
            enumerable: true,
            get:        () => Index,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Index");
                Index = Value;
            }
        },
        Cells:    {
            enumerable: true,
            get:        () => Cells
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Controls.Table.Row#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.Table.Cell#event:update
     * @fires vDesk.Controls.Table.Row#update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Control.removeEventListener("update", OnUpdate, false);
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            cell:   Event.detail.sender,
            value:  Event.detail.value
        }).Dispatch(Control);
        Control.addEventListener("update", OnUpdate, false);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableRowElement}
     */
    const Control = document.createElement("tr");
    Control.className = "Row";
    Control.addEventListener("click", OnClick, false);
    Control.addEventListener("update", OnUpdate, false);

    //Loop through columns and create a new cell for each one.
    Columns.forEach(Column => {
        const Cell = new vDesk.Controls.Table.Cell(Column);

        //Link the column name to the value of the cell to the row.
        const PropertyDescriptor = Object.getOwnPropertyDescriptor(Cell, "Value");
        Object.defineProperty(this, Column.Name, PropertyDescriptor);

        Cells.push(Cell);
        Control.appendChild(Cell.Control);
    });
};

vDesk.Controls.Table.Row.Implements(vDesk.Controls.Table.IRow);