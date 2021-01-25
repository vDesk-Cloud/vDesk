"use strict";
/**
 * Initializes a new instance of the Table class.
 * @class Represents a generic table, capable of sorting, filtering and normalizing data.
 * @param {Array<vDesk.Controls.Table.Column>|Array<Column>} [Columns=[]] Initializes the table with a given set of columns.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Controls.Table.ColumnCollection} Columns Gets or sets the columns of the table.
 * @property {vDesk.Controls.Table.RowCollection} Rows Gets or sets the rows of the table.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the table is enabled.
 * @property {Boolean} Sortable Gets or sets a value indicating whether the  rows of table are sortable.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table = function Table(Columns = []) {
    Ensure.Parameter(Columns, Array, "Columns");

    /**
     * Flag indicating whether the Table is enabled.
     * @type {Boolean}
     */
    let Enabled = true;

    /**
     * Flag indicating whether the Table is sortable.
     * @type {Boolean}
     */
    let Sortable = true;

    /**
     * Flag indicating whether the current sorted Columns order is ascending.
     * @type {Boolean}
     */
    let Ascending = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Columns: {
            enumerable: true,
            get:        () => ColumnCollection,
            set:        Value => {
                Ensure.Property(Value, Array, "Columns");

                //Remove Columns.
                ColumnCollection.forEach(Column => HeaderRow.removeChild(Column.Control));

                //Clear ColumnCollection.
                ColumnCollection.Clear();

                //Append new columns.
                Value.forEach(Column => {
                    if(!(Column instanceof vDesk.Controls.Table.Column)) {
                        Column = vDesk.Controls.Table.Column.FromColumnDescriptor(Column);
                    }
                    ColumnCollection.push(Column);
                    HeaderRow.appendChild(Column.Control);
                });

                //Remove Rows.
                Rows.forEach(Row => Control.removeChild(Row.Control));
                //Clear RowCollection.
                Rows.Clear();
            }
        },
        Rows:    {
            enumerable: true,
            get:        () => Rows,
            set:        Value => {
                Ensure.Property(Value, [Array, vDesk.Controls.Table.RowCollection], "Rows");

                //Remove rows.
                Rows.Clear();

                //Append new rows.
                Value.forEach(Row => Rows.Add(Row));
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Rows.forEach(Row => Row.Enabled = Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * Sorts the Rows of the Table according the selected Column.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Table.Column#event:select
     */
    const OnSelect = Event => {

        Event.stopPropagation();

        if(ColumnCollection.Selected !== Event.detail.sender) {
            ColumnCollection.Selected = Event.detail.sender;
            ColumnCollection.Selected.Ascending = true;
        } else {
            //Flip sort order.
            ColumnCollection.Selected.Ascending = !ColumnCollection.Selected.Ascending;
        }

        //Check if the Column is sortable.
        if(ColumnCollection.Selected.Comparator !== null) {

            //Clear Table.
            Rows.forEach(Row => Control.removeChild(Row.Control));

            //Sort and reappend Rows.
            const Fragment = document.createDocumentFragment();
            Rows.slice()
                .sort((FirstRow, SecondRow) => ColumnCollection.Selected.Compare(FirstRow, SecondRow))
                .forEach(Row => Fragment.appendChild(Row.Control));
            Control.appendChild(Fragment);

            window.addEventListener("click", OnClick, false);
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * Resets the 'sorting'-focus of a current sorted column's header.
     */
    const OnClick = () => {
        ColumnCollection.Selected = null;
        window.removeEventListener("click", OnClick, false);
    };

    /**
     * Eventhandler that listens on the 'add' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Table.ColumnCollection#event:add
     */
    const OnColumnAdd = Event => {
        if(Rows.length > 0) {
            throw new SyntaxError("Can't modify table-schema while the table contains rows.");
        }

        HeaderRow.appendChild(Event.detail.value.Control);
    };

    /**
     * Eventhandler that listens on the 'add' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Table.RowCollection#event:add
     */
    const OnRowAdd = Event => {

        if(ColumnCollection.length === 0) {
            throw new SyntaxError("Can't add row while the table-schema of the table is undefined.");
        }

        Event.detail.value.Index = Rows.length;
        Control.appendChild(Event.detail.value.Control);

    };

    /**
     * Eventhandler that listens on the remove event.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Table.RowCollection#event:remove
     */
    const OnRowRemove = Event => {
        Control.removeChild(Event.detail.value.Control);
        //Reorder indices.
        Rows.forEach((Row, Index) => Row.Index = Index);
    };

    this.Query = function(Query) {
        Query = "SELECT * WHERE Derp = 'hui'";
    };

    /**
     * Resets the the rows of the table to their initial order.
     */
    this.Reset = function() {
        //Clear Table.
        Rows.forEach(Row => Control.removeChild(Row.Control));

        //Reappend Rows.
        const Fragment = document.createDocumentFragment();
        Rows.forEach(Row => Fragment.appendChild(Row.Control));
        Control.appendChild(Fragment);
    };

    /**
     * Creates a new row with the schema-definition of the table.
     * @return {vDesk.Controls.Table.Row} The created row.
     */
    this.CreateRow = function(Values = null) {
        Ensure.Parameter(Values, Type.Object, "Values", true);
        if(ColumnCollection.length === 0) {
            throw new SyntaxError("Can't create empty row while the table-schema of the table is undefined.");
        }
        if(Values !== null){
            const Row = new vDesk.Controls.Table.Row(ColumnCollection);
            ColumnCollection.forEach(Column => Row[Column.Name] = Values?.[Column.Name] ?? Row[Column.Name]);
            return Row;
        }
        return new vDesk.Controls.Table.Row(ColumnCollection);
    };

    /**
     * Sorts the Rows of the Table according a specified predicate.
     * @param {Function} [Predicate=(First, Second) => First.Index - Second.Index] The function to use as a sort comparator.
     */
    this.Sort = function(Predicate = (First, Second) => First.Index - Second.Index) {
        Ensure.Parameter(Predicate, Type.Function, "Predicate");

        //Clear Table.
        // Rows.filter(Row => Row.Control.parentNode === Control).forEach(Row => Control.removeChild(Row.Control));
        Rows.sort(Predicate);

        //Reappend Rows.
        const Fragment = document.createDocumentFragment();
        Rows.forEach(Row => Fragment.appendChild(Row.Control));
        Control.appendChild(Fragment);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableElement}
     */
    const Control = document.createElement("table");
    Control.className = "Table BorderLight";

    /**
     * The header row of the Table.
     * @type {HTMLTableRowElement}
     */
    const HeaderRow = document.createElement("tr");
    HeaderRow.className = "Header BorderLight Foreground Font Light";
    HeaderRow.addEventListener("select", OnSelect, false);

    /**
     * The RowCollection containing the Rows of the Table.
     * @type {vDesk.Controls.Table.RowCollection}
     */
    const Rows = new vDesk.Controls.Table.RowCollection();
    Rows.addEventListener("add", OnRowAdd);
    Rows.addEventListener("remove", OnRowRemove);

    /**
     * The ColumnCollection containing the Columns of the Table.
     * @type {vDesk.Controls.Table.ColumnCollection}
     */
    const ColumnCollection = new vDesk.Controls.Table.ColumnCollection();

    //Loop through passed columns and add them to the table.
    Columns.forEach(Column => {
        if(!(Column instanceof vDesk.Controls.Table.Column)) {
            Column = vDesk.Controls.Table.Column.FromColumnDescriptor(Column);
        }
        ColumnCollection.push(Column);
        HeaderRow.appendChild(Column.Control);
    });

    ColumnCollection.addEventListener("add", OnColumnAdd);

    Control.appendChild(HeaderRow);
};

/**
 * Filters the rows of the table based on a predicate.
 * @param {Function} Predicate A function to test each row for a condition.
 * @return {Array<vDesk.Table.Row>} The rows that match the condition.
 * @example
 * //Example of filtering the table
 * let Table = new vDesk.Controls.Table([
 *     {Name: "ID", Type: "int", AutoIncrement: true, Label: "ID"},
 *     {Name: "Code", Type: "string", Label: "Countrycode", Unique: true},
 *     {Name: "Name", Type: "string", Label: "Country"}
 * ]);
 *
 * let First = Table.Rows.Create();
 * First.Code = "DE";
 * First.Name = "Deutschland";
 *
 * let Second = Table.Rows.Create();
 * Second.Code = "AU";
 * Second.Name = "Österreich";
 *
 * let Third = Table.Rows.Create();
 * Third.Code = "CH";
 * Third.Name = "Schweiz";
 *
 * Table.Rows.Add(First);
 * Table.Rows.Add(Second);
 * Table.Rows.Add(Third);
 *
 * //Filter Table
 * let Countries = Table.Where(Row => Row.ID > 1);
 *
 * //[2, "CH", "Schweiz"], [3, "AU", "Österreich"]..
 */
vDesk.Controls.Table.prototype.Where = function(Predicate) {
    return this.Rows.filter(Predicate);
};