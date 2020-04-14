"use strict";
/**
 * Represents an observable collection of {@link vDesk.Controls.Table.Column|Columns}.
 * @property {?vDesk.Controls.Table.Column|null} Selected Gets or sets the current selected Column of the ColumnCollection.
 * @memberOf vDesk.Controls.Table
 * @augments ObservableArray
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table.ColumnCollection = class ColumnCollection extends ObservableArray {

    constructor(..._) {
        super(..._);
        this._Selected = null;
    }

    get Selected() {
        return this._Selected;
    }

    set Selected(Value) {
        Ensure.Property(Value, vDesk.Controls.Table.Column, "Selected", true);
        if(this._Selected !== null) {
            this._Selected.Selected = false;
        }
        this._Selected = Value;
        if(this._Selected !== null) {
            this._Selected.Selected = true;
        }

    }

    /**
     * Adds a Column to the ColumnCollection.
     * @param {vDesk.Controls.Table.Column} Column The Column to add.
     * @fires vDesk.Controls.Table.ColumnCollection#add
     */
    Add(Column) {
        Ensure.Parameter(Column, vDesk.Controls.Table.Column, "Column");
        this.push(Column);
    }

    /**
     * Removes a Column from the ColumnCollection.
     * @param {vDesk.Controls.Table.Column} Column The Column to remove.
     * @return {vDesk.Controls.Table.Column|null} The removed Column if it was a member of the ColumnCollection; otherwise, null;
     * @fires vDesk.Controls.Table.ColumnCollection#remove
     */
    Remove(Column) {
        Ensure.Parameter(Column, vDesk.Controls.Table.Column, "Column");
        return super.splice(super.indexOf(Column), 1)[0] || null;
    }

    /**
     * Clears the ColumnCollection.
     * @return {ColumnCollection} The current instance for further chaining.
     */
    Clear() {
        super.splice(0, this.length);
    }
};
