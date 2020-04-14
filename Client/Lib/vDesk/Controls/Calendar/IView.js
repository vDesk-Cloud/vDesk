"use strict";
/**
 * Fired if further navigation would reach a date within the previous time range the IView will display.
 * @event vDesk.Controls.Calendar.IView#previous
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'previous' event.
 * @property {vDesk.Controls.Calendar.IView} detail.sender The current instance of the IView.
 * @property {Date} detail.date The previous date to display.
 */
/**
 * Fired if further navigation would reach a date within the next time range the IView will display.
 * @event vDesk.Controls.Calendar.IView#next
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'next' event.
 * @property {vDesk.Controls.Calendar.IView} detail.sender The current instance of the IView.
 * @property {Date} detail.date The next date to display.
 */
/**
 * Fired if the current date of the IView has been changed.
 * @event vDesk.Controls.Calendar.IView#datechanged
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'datechanged' event.
 * @property {vDesk.Controls.Calendar.IView} detail.sender The current instance of the IView.
 * @property {Date} detail.date The new date of the IView.
 */
/**
 * Interface for classes that represent a generic view of a timespan in a calendar.
 * @interface
 * @memberOf vDesk.Controls.Calendar
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.IView = function IView() {};

vDesk.Controls.Calendar.IView.prototype = {

    /**
     * Gets the control of the IView.
     * @abstract
     * @type {HTMLElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Gets the title of the IView.
     * @abstract
     * @type {String}
     */
    Title: Interface.FieldNotImplemented,

    /**
     * Gets the cell of the IView representing the current date.
     * @abstract
     * @type {vDesk.Controls.Calendar.Cell}
     */
    Now: Interface.FieldNotImplemented,

    /**
     * Gets the current selected cell of the IView.
     * @abstract
     * @type {vDesk.Controls.Calendar.Cell}
     */
    Selected: Interface.FieldNotImplemented,

    /**
     * Gets the cells of the IView.
     * @abstract
     * @type {Array<vDesk.Controls.Calendar.Cell>}
     */
    Cells: Interface.FieldNotImplemented,

    /**
     * Displays the previous date of the IView.
     * @abstract
     * @type {Function}
     */
    Backward: Interface.MethodNotImplemented,

    /**
     * Displays the next date of the IView.
     * @abstract
     * @type {Function}
     */
    Forward: Interface.MethodNotImplemented,

    /**
     * Displays a specified date or time(-range).
     * @abstract
     * @param {Date} Date The date or time(-range) to show.
     */
    Show: Interface.MethodNotImplemented,

    /**
     * Navigates one cell up within the IView .
     * @abstract
     * @type {Function}
     */
    Up: Interface.MethodNotImplemented,

    /**
     * Navigates one cell down within the IView .
     * @abstract
     * @type {Function}
     */
    Down: Interface.MethodNotImplemented,

    /**
     * Navigates one cell left within the IView .
     * @abstract
     * @type {Function}
     */
    Left: Interface.MethodNotImplemented,

    /**
     * Navigates one cell right within the IView .
     * @abstract
     * @type {Function}
     */
    Right: Interface.MethodNotImplemented
};