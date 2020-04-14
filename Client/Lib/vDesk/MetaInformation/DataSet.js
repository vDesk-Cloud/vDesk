/**
 * @typedef {Object} DataSet Represents the data of a dataset.
 * @property {Number} ID Gets the ID of the dataset.
 * @property {Number} MaskID Gets the ID of the mask the dataset has been tagged under.
 * @property {Number} Rows Gets the DataRows of the dataset.
 */

/**
 * Initializes a new instance of the DataSet class.
 * @class Represents a set of metadata of an @link vDesk.Archive.Element
 * @param {vDesk.MetaInformation.Mask} Mask Initializes the DataSet with the specified Mask.
 * @param {Number} [ID=null] Initializes the DataSet with the specified ID.
 * @param {Array<vDesk.MetaInformation.DataSet.Row>} [Rows=[]] Initializes the DataSet with the specified set of Rows.
 * @param {Boolean} [Enabled=true] Flag indicating whether the DataSet is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Number} ID Gets or sets the ID of the DataSet.
 * @property {Mask} Mask Gets or sets mask of the DataSet that defines its structure.
 * @property {Array<vDesk.MetaInformation.DataSet.Row>} Rows Gets or sets the DataRows of the dataset.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the DataSet is enabled.
 * @memberOf vDesk.MetaInformation
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.DataSet = function DataSet(Mask, ID = null, Rows = [], Enabled = true) {
    Ensure.Parameter(Mask, vDesk.MetaInformation.Mask, "Mask");
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Rows, Array, "Rows");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

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

                //Remove DataRows.
                Rows.forEach(DataRow => Control.removeChild(DataRow.Control));

                //Clear array
                Rows = [];

                const Fragment = document.createDocumentFragment();
                Mask.Rows.forEach(MaskRow => {
                    const DataRow = new vDesk.MetaInformation.DataSet.Row(null, MaskRow);
                    Rows.push(DataRow);
                    Fragment.appendChild(DataRow.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        ID:      {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID");
                ID = Value;
            }
        },
        Rows:    {
            enumerable: true,
            get:        () => Rows,
            set:        Value => {
                Ensure.Property(Value, Array, "Rows");

                //Remove elements.
                Rows.forEach(DataRow => Control.removeChild(DataRow.Control));

                //Clear array
                Rows = [];

                //Append new entries.
                const Fragment = document.createDocumentFragment();
                Value.forEach(DataRow => {
                    Ensure.Parameter(DataRow, vDesk.MetaInformation.DataSet.Row, "DataRow");
                    if(Mask.Rows.find(MaskRow => MaskRow.ID === DataRow.Row.ID) === undefined) {
                        throw new ArgumentError(`MaskRow with ID: [${DataRow.Row.ID}] of Mask: [${Mask.Name}] doesn't exist!`);
                    }
                    Rows.push(DataRow);
                    Fragment.appendChild(DataRow.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Valid:   {
            get: () => Rows.every(Row => Row.Valid)
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Rows.forEach(DataRow => DataRow.Enabled = Value);
            }
        }
    });

    /**
     * Adds a Row to the DataSet.
     * @param {vDesk.MetaInformation.DataSet.Row} Row The Row to add.
     */
    this.Add = function(Row) {
        Ensure.Parameter(Row, vDesk.MetaInformation.DataSet.Row, "Row");

        //Check if the associated MaskRow exists.
        if(Rows.find(DataRow => DataRow.Row.ID === Row.Row.ID) !== undefined) {
            throw new ArgumentError("Data for MaskRow with ID: [" + Row.Row.ID + "] has been already set!");
        }

        Rows.push(Row);
        Control.appendChild(Row.Control);

    };

    /**
     * Removes all DataRows of the dataset.
     */
    this.Clear = function() {
        //Remove elements.
        Rows.forEach(DataRow => Control.removeChild(DataRow.Control));

        //Clear array
        Rows = [];
    };

    /**
     * Returns the Row of the DataSet which matches the given MaskRowID.
     * @param {Number} MaskRowID The ID of the MaskRow the Row to search belongs to.
     * @return {vDesk.MetaInformation.DataSet.Row|null} The Row whose MaskRowID equals the search value, else null.
     */
    this.Find = function(MaskRowID) {
        return Rows.find(DataRow => DataRow.Row.ID === MaskRowID) || null;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "DataSet";

    //Fill DataSet with empty DataSet.Rows.
    Mask.Rows.forEach(MaskRow => {
        let DataRow = Rows.find(DataRow => DataRow.Row.ID === MaskRow.ID);
        if(DataRow === undefined) {
            DataRow = new vDesk.MetaInformation.DataSet.Row(MaskRow);
            Rows.push(DataRow);
        }
        Control.appendChild(DataRow.Control);
    });

    Rows.sort((First, Second) => First.Row.Index - Second.Row.Index);
};

/**
 * Factory method that creates a DataSet from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the DataSet.
 * @return {vDesk.MetaInformation.DataSet} A DataSet filled with the provided data.
 * @todo Evaluate returning dependent models (in the entire system) as {Mask: {ID: 12}} instead of {Mask: 12}.
 */
vDesk.MetaInformation.DataSet.FromDataView = function(DataView) {
    const Mask = vDesk.MetaInformation.Masks.find(Mask => Mask.ID === (DataView.Mask.ID || DataView.Mask));
    return new vDesk.MetaInformation.DataSet(
        Mask,
        DataView.ID || null,
        (DataView.Rows || []).map(Row => new vDesk.MetaInformation.DataSet.Row(
            Mask.Rows.find(MaskRow => MaskRow.ID === (Row.Row.ID || Row.Row)),
            Row.ID || null,
            Row.Value || null
        ))
    );
};