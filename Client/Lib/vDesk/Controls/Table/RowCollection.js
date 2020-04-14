"use strict";
/**
 * Represents an observable collection of {@link vDesk.Controls.Table.IRow}s.
 * @memberOf vDesk.Controls.Table
 * @augments ObservableArray
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table.RowCollection = class RowCollection extends ObservableArray {

    constructor(..._) {
        super(..._);
        this._Selected = null;
    }

    get Selected() {
        return this._Selected;
    }

    set Selected(Value) {
        Ensure.Property(Value, vDesk.Controls.Table.IRow, "Selected", true);
        if(this._Selected !== null) {
            this._Selected.Selected = false;
        }
        this._Selected = Value;
        this._Selected = true;
    }

    /**
     * Adds an IRow to the RowCollection.
     * @param {vDesk.Controls.Table.IRow} Row The IRow to add.
     * @fires vDesk.Controls.Table.RowCollection#add
     */
    Add(Row) {
        Ensure.Parameter(Row, vDesk.Controls.Table.IRow, "Row");
        super.push(Row);
    }

    /**
     * Removes an IRow from the RowCollection.
     * @param {vDesk.Controls.Table.IRow} Row The IRow to remove.
     * @return {vDesk.Controls.Table.IRow|null} The removed IRow if it was a member of the RowCollection; otherwise, null;
     * @fires vDesk.Controls.Table.RowCollection#remove
     */
    Remove(Row) {
        Ensure.Parameter(Row, vDesk.Controls.Table.IRow, "Row");
        return super.splice(super.indexOf(Row), 1)[0] || null;
    }

    /**
     * Searches the RowCollection for any IRows that match a specified predicate and removes them from the RowCollection.
     * @param {Function} Predicate A function to test each IRow for a condition.
     * @return {Array<vDesk.Controls.Table.IRow>} An array containing the removed IRows that matched the specified condition.
     */
    RemoveWhere(Predicate) {
        Ensure.Parameter(Predicate, Type.Function, "Predicate");
        const Rows = super.filter(Predicate);
        Rows.forEach(Row => this.Remove(Row));
        return Rows;
    }

    /**
     * Clears the RowCollection.
     */
    Clear() {
        super.splice(0, this.length);
    }
};
