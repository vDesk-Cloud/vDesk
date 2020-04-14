/**
 * Interface for classes that represent a Row of a Table.
 * @interface
 * @memberOf vDesk.Controls.Table
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Table.IRow = function IRow() {};

vDesk.Controls.Table.IRow.prototype = {

    /**
     * Gets the underlying DOM-Node.
     * @abstract
     * @type {HTMLTableRowElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Gets or sets the Index of the IRow.
     * @abstract
     * @type {Number}
     */
    Index: Interface.FieldNotImplemented,

    /**
     * Gets or sets a value indicating whether the IRow is selected.
     * @abstract
     * @type {Boolean}
     */
    Selected: Interface.FieldNotImplemented

};