"use strict";
/**
 * Interface for modules which provide an user interface.
 * @interface
 * @memberOf vDesk.Modules
 * @augments vDesk.Modules.IModule
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Modules
 */
vDesk.Modules.IVisualModule = function() {

};
/**
 * Gets the control of the IVisualModule.
 * @abstract
 * @type HTMLElement
 */
vDesk.Modules.IVisualModule.prototype.Control = Interface.FieldNotImplemented;

/**
 * Gets the title of the IVisualModule.
 * @abstract
 * @type String
 */
vDesk.Modules.IVisualModule.prototype.Title = Interface.FieldNotImplemented;

/**
 * Gets the Icon of the IVisualModule.
 * @abstract
 * @type String
 */
vDesk.Modules.IVisualModule.prototype.Icon = Interface.FieldNotImplemented;

vDesk.Modules.IVisualModule.Implements(vDesk.Modules.IModule);