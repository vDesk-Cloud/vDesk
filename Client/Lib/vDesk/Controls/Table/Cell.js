"use strict";
/**
 * Fired if the Cells value has been updated.
 * @event vDesk.Controls.Table.Cell#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.Table.Cell} detail.sender The current instance of the Cell.
 * @property {*} detail.value The value of the Cell.
 */
/**
 * Initializes a new instance of the Cell class.
 * @class Represents a Cell of a {@link vDesk.Controls.Table|Table}{@link vDesk.Controls.Table.Row|Row}.
 * @param {vDesk.Controls.Table.Column} Column Initializes the Cell with the specified Column.
 * @param {String|Boolean|Date|Number|Null} [Value=""] Initializes the Cell with the specified value.
 * @property {HTMLTableCellElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Controls.Table.Column} Column Gets or sets the Column of the cell.
 * @property {String} Type Gets or sets the type of the cell.
 * @property {?Number|Null} Width Gets or sets the width of the cell.
 * @property {String|Boolean|Date|Number|HTMLElement} Value Gets or sets the value of the cell.
 * @memberOf vDesk.Controls.Table
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table.Cell = function Cell(Column, Value = null) {
    Ensure.Parameter(Column, vDesk.Controls.Table.Column, "Column");
    Ensure.Parameter(Value, Column.Type, "Value", true);

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Column:  {
            enumerable: true,
            get:        () => Column,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Table.Column, "Column");
                Column = Value;
            }
        },
        Type:    {
            enumerable: true,
            get:        () => Column.Type
        },
        Value:   {
            enumerable: true,
            get:        () => Value,
            set:        ValueToSet => {
                Ensure.Property(ValueToSet, Column.Type, "Value", true);
                if(Value instanceof HTMLElement) {
                    Control.removeChild(Value);
                }
                Value = ValueToSet;
                if(Value instanceof HTMLElement) {
                    Control.appendChild(Value);
                } else if(Value instanceof Date) {
                    Control.textContent = Value.toLocaleDateString(vDesk.User.Locale.toLowerCase());
                } else {
                    Control.textContent = (Value || "").toString();
                }
            }
        }
    });


    /**
     * The underlying DOM-Node.
     * @type {HTMLTableCellElement}
     */
    const Control = document.createElement("td");
    Control.className = `Cell TextBox BorderLight Font Dark ${Column.Name}`;
    if(Value instanceof HTMLElement) {
        Control.appendChild(Value);
    } else {
        Control.textContent = (Value || "").toString();
    }
};

//Sumpfkrug 24,89‬€
//Kasten 31,80€
//Hahn 4,68€
//Klemmen 9,48‬€