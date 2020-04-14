"use strict";
/**
 * Fired if the values of the IValidator has been updated.
 * @event vDesk.MetaInformation.Mask.Row.IValidator#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.MetaInformation.Mask.Row.IValidator} detail.sender The current instance of the IValidator.
 */
/**
 * Interface IValidator represents... blah.
 * @interface
 * @memberOf vDesk.MetaInformation.Mask.Row
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.MetaInformation.Mask.Row.IValidator = function IValidator() {};

vDesk.MetaInformation.Mask.Row.IValidator.prototype = {

    /**
     * Gets underlying DOM-Node of the IValidator.
     * @abstract
     * @type {HTMLElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Gets or sets the validator of the IValidator.
     * @abstract
     * @type {Object|null}
     */
    Validator: Interface.FieldNotImplemented,

    /**
     * Gets or sets a value indicating whether the IValidator is enabled.
     * @abstract
     * @type {Boolean}
     */
    Enabled: Interface.FieldNotImplemented

};

/**
 * Gets the supported types of the IValidator.
 * @abstract
 * @type {Array<String>}
 */
vDesk.MetaInformation.Mask.Row.IValidator.Types = Interface.FieldNotImplemented;