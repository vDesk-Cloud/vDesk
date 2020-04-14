"use strict";
/**
 * Interface for classes that represent a custom view for the search-module.
 * @interface
 * @memberOf vDesk.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Search.ICustomSearch = function() {};
vDesk.Search.ICustomSearch.prototype = {
    /**
     * Gets the control of the ICustomSearch.
     * @abstract
     * @type {HTMLElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Gets the title of the ICustomSearch.
     * @abstract
     * @type {String}
     */
    Title: Interface.FieldNotImplemented,

    /**
     * Gets the icon of the ICustomSearch.
     * @abstract
     * @type {String}
     */
    Icon: Interface.FieldNotImplemented,

    /**
     * Gets the toolbargroups of the ICustomSearch.
     * @abstract
     * @type {Array<vDesk.Controls.ToolBar.Group>}
     */
    ToolBarGroups: Interface.FieldNotImplemented
};