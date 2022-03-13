"use strict";
/**
 * Interface for modules.
 * @interface
 * @memberOf vDesk.Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Modules
 */
vDesk.Modules.IModule = function() {
};
/**
 * Gets the name of the IModule.
 * @abstract
 * @type String
 */
vDesk.Modules.IModule.prototype.Name = Interface.FieldNotImplemented;

/**
 * Loads the module.
 * @abstract
 * @type {Function}
 */
vDesk.Modules.IModule.prototype.Load = Interface.MethodNotImplemented;

/**
 * Unloads the module.
 * @abstract
 * @type {Function}
 */
vDesk.Modules.IModule.prototype.Unload = Interface.MethodNotImplemented;