"use strict";
/**
 * Fired if the value of the IEditor has been updated.
 * @event vDesk.Controls.EditControl.IEditor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.EditControl.IEditor} detail.sender The current instance of the IEditor.
 * @property {*} detail.value The updated value of the IEditor.
 */
/**
 * Fired if the value of the IEditor has been cleared.
 * @event vDesk.Controls.EditControl.IEditor#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.EditControl.IEditor} detail.sender The current instance of the IEditor.
 */
/**
 * Interface for value editor controls of EditControls.
 * @param {*} [Value=null] Initializes the IEditor with the specified value.
 * @param {Object} [Validator=null] Initializes the IEditor with the specified validator.
 * @param {Boolean} [Required=false] Flag indicating whether the IEditor requires a value.
 * @param {Boolean} [Enabled=false] Flag indicating whether the IEditor is enabled.
 * @interface
 * @memberOf vDesk.Controls.EditControl
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.EditControl.IEditor = function IEditor(Value = null, Validator = null, Required = false, Enabled = false) {};

vDesk.Controls.EditControl.IEditor.prototype = {

    /**
     * Gets the underlying DOM-Node.
     * @abstract
     * @type {HTMLElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Gets or sets the value of the IEditor.
     * @abstract
     * @type {*}
     */
    Value: Interface.FieldNotImplemented,

    /**
     * Gets or sets the validator of the IEditor.
     * @abstract
     * @type {Object|null}
     */
    Validator: Interface.FieldNotImplemented,

    /**
     * Gets or sets a value indicating whether the value of the IEditor is valid.
     * @abstract
     * @type {Boolean}
     */
    Valid: Interface.FieldNotImplemented,

    /**
     * Gets or sets a value indicating whether the IEditor requires a value.
     * @abstract
     * @type {Boolean}
     */
    Required: Interface.FieldNotImplemented,

    /**
     * Gets or sets a value indicating whether the IEditor is enabled.
     * @abstract
     * @type {Boolean}
     */
    Enabled: Interface.FieldNotImplemented
};