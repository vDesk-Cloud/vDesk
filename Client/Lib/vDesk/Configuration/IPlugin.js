"use strict";
/** 
 * Interface for classes that represent a configuration-plugin.
 * @interface
 * @memberOf vDesk.Configuration
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Configuration.IPlugin = function () {};
/**
 * Gets the underlying DOM-Node of the IPlugin.
 * @abstract
 * @type HTMLElement
 */
vDesk.Configuration.IPlugin.prototype.Control = Interface.FieldNotImplemented;
/**
 * Gets the display-name of the IPlugin.
 * @abstract
 * @type String
 */
vDesk.Configuration.IPlugin.prototype.Title = Interface.FieldNotImplemented;