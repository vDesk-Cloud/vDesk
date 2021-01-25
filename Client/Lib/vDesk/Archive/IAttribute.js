/**
 * Interface for plugins for displaying and modifying custom attributes of {@link vDesk.Archive.Element}s
 * @param {vDesk.Archive.Element} Element Initializes the IAttribute with the specified Element.
 * @interface
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.IAttribute = function(Element) {
};
vDesk.Archive.IAttribute.prototype = {

    /**
     * Gets the underlying DOM-Node.
     * @abstract
     * @type {HTMLElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Gets the title of the IAttribute.
     * @abstract
     * @type {String}
     */
    Title: Interface.FieldNotImplemented

};

/**
 * Gets a callback function that determines whether the current User has the permission to see the IAttribute.
 * @abstract
 * @static
 * @type {Function}
 */
vDesk.Archive.IAttribute.Permission = Interface.FieldNotImplemented;