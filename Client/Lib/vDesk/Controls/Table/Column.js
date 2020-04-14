"use strict";
/**
 * Fired if the Column has been selected.
 * @event vDesk.Controls.Table.Column#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Controls.Table.Column} detail.sender The current instance of the Column.
 */
/**
 * @typedef {Object} Column Represents the schema of a Column in a table.
 * @property {!String} Name Gets or sets the name of the Column.
 * @property {!String} Type Gets or sets the type of the Columns values.
 * @property {!String} Label Gets or sets the label of the Column.
 * @property {?undefined} DefaultValue Gets or sets the defaultvalue of the Column.
 * @property {!Number} Width Gets or sets the initial width of the Column.
 * @property {!Boolean} Enabled Gets or sets a value indicating whether the Column is enabled.
 * @property {!Boolean} Unique Gets or sets a value indicating whether values of this Column must be unique.
 * @property {!Boolean} AutoIncrement Gets or sets a value indicating whether numeric values will be incremented automatically.
 * @property {!Boolean} Null Gets or sets a value indicating whether the values of the Column are nullable.
 */
/**
 * Initializes a new instance of the Column class.
 * @class Represents the schema of a Column in a table.
 * @param {String} [Name=""] Initializes the Column with the specified name.
 * @param {String} [Type=vDesk.Struct.Type.String] Initializes the Column with the specified type of the Columns values.
 * @param {String} [Label=""] Initializes the Column with the specified label.
 * @param {Function} [Comparator=null] Initializes the Column with the specified sort comparator.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Column is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Name Gets or sets the name of the Column.
 * @property {String} Type Gets or sets the type of the Columns values.
 * @property {String} Label Gets or sets the label of the Column.
 * @property {?Function} Comparator Gets or sets the sort comparator of the Column.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Column is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Column is selected.
 * @memberOf vDesk.Controls.Table
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table.Column = function Column(
    Name       = "",
    Type       = vDesk.Struct.Type.String,
    Label      = "",
    Comparator = null,
    Enabled    = true
) {
    Ensure.Parameter(Name, vDesk.Struct.Type.String, "Name");
    Ensure.Parameter(Type, [vDesk.Struct.Type.String, vDesk.Struct.Type.Function], "Type");
    Ensure.Parameter(Label, vDesk.Struct.Type.String, "Label");
    Ensure.Parameter(Comparator, vDesk.Struct.Type.Function, "Comparator", true);
    Ensure.Parameter(Enabled, vDesk.Struct.Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Column is selected.
     * @type {Boolean}
     */
    let Selected = false;

    /**
     * Flag indicating whether the current sort order of the Column is ascending.
     * @type {Boolean}
     */
    let Ascending = true;

    Object.defineProperties(this, {
        Control:    {
            enumerable: true,
            get:        () => Control
        },
        Name:       {
            enumerable: true,
            get:        () => Name,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Name");
                Name = Value;
                if(Label === null) {
                    Control.textContent = Value;
                }
            }
        },
        Type:       {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, [vDesk.Struct.Type.String, vDesk.Struct.Type.Function], "Type");
                Type = Value;
            }
        },
        Label:      {
            enumerable: true,
            get:        () => Label,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Label", true);
                Label = Value;
                Control.textContent = Value || Name;
            }
        },
        Comparator: {
            enumerable: true,
            get:        () => Comparator,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Function, "Comparator", true);
                Comparator = Value;
            }
        },
        Ascending:  {
            enumerable: true,
            get:        () => Ascending,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Ascending");
                Ascending = Value;
                if(Comparator !== null) {
                    Control.textContent = `${Selected ? Value ? "▼" : "▲" : ""}${Label || Name}`;
                }
            }
        },
        Enabled:    {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Enabled");
                Enabled = Value;
            }
        },
        Selected:   {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
                if(Comparator !== null) {
                    Control.textContent = `${Value ? Ascending ? "▼" : "▲" : ""}${Label || Name}`;
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.Table.Column#select
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * Compares 2 Rows for equality.
     *
     * @param {vDesk.Controls.Table.Row} First The first Row to compare.
     * @param {vDesk.Controls.Table.Row} Second The second Row to compare.
     * @return {Number} 1 if the first specified Row's value is higher than the second specified Row's value,
     * 0 if the values of both specified Rows are equal,
     * otherwise; -1 if the first specified Row's value is lower than the second specified Row's value.
     */
    this.Compare = function(First, Second) {
        Ensure.Parameter(First, vDesk.Controls.Table.Row, "FirstRow");
        Ensure.Parameter(Second, vDesk.Controls.Table.Row, "SecondRow");
        if(Comparator !== null) {
            return Comparator(First[Name], Second[Name], Ascending);
        }
        return 0;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableHeaderCellElement}
     */
    const Control = document.createElement("th");
    Control.className = "Cell BorderLight Font Light";
    Control.addEventListener("click", OnClick, false);

    Control.textContent = Label || Name;

};

/**
 * Factory method that creates a Column from a column descriptor.
 * @param {Object} ColumnDescriptor The column descriptor to use to create an instance of the Column.
 * @return {vDesk.Controls.Table.Column} An Column according the specified column descriptor.
 */
vDesk.Controls.Table.Column.FromColumnDescriptor = function(ColumnDescriptor) {
    Ensure.Parameter(ColumnDescriptor, Type.Object, "ColumnDescriptor");
    return new vDesk.Controls.Table.Column(
        ColumnDescriptor.Name || "",
        ColumnDescriptor.Type || Type.String,
        ColumnDescriptor.Label || ColumnDescriptor.Name || "",
        ColumnDescriptor.Comparator || null,
        ColumnDescriptor.Enabled || true
    );
};

/**
 * Enumeration of predefined predicates for sorting in an ascending order.
 * @readonly
 * @enum {Function}
 */
vDesk.Controls.Table.Column.Sort = {

    /**
     * Sorts string-values in an ascending order.
     * @name vDesk.Controls.Table.Sort.Ascending.String
     * @type {Function}
     */
    String: (Collator => (A, B, Ascending = true) => Ascending ? Collator.compare(A, B) : Collator.compare(B, A))
            (new Intl.Collator(
                vDesk.User.Locale.toLowerCase(),
                {
                    sensitivity: "base",
                    numeric:     true
                }
            )),

    /**
     * Sorts numeric-values in an ascending order.
     * @name vDesk.Controls.Table.Sort.Ascending.Int
     * @type {Function}
     */
    Int: (A, B, Ascending = true) => Ascending ? A - B : B - A,

    /**
     * Sorts numeric-values in an ascending order.
     * @name vDesk.Controls.Table.Sort.Ascending.Float
     * @type {Function}
     */
    Float: this.Int,

    /**
     * Sorts date-values in an ascending order.
     * @name vDesk.Controls.Table.Sort.Ascending.Date
     * @type {Function}
     */
    Date: (A, B, Ascending = true) => Ascending ? A.valueOf() - B.valueOf() : B.valueOf() - A.valueOf(),

    /**
     * Sorts boolean-values in an ascending order.
     * @name vDesk.Controls.Table.Sort.Ascending.Boolean
     * @type {Function}
     */
    Boolean: (A, B, Ascending = true) => {
        if(Ascending) {
            if(A === false && B === true) {
                return -1;
            }
            if(A === true && B === false) {
                return 1;
            }
            return 0;
        } else {
            if(A === true && B === false) {
                return -1;
            }
            if(A === false && B === true) {
                return 1;
            }
            return 0;
        }
    },

    /**
     * Sorts boolean-values in an ascending order.
     * @name vDesk.Controls.Table.Sort.Ascending.Bool
     * @type {Function}
     */
    Bool: this.Boolean

};