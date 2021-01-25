"use strict";
/**
 * Interface for classes that represent a searchresult.
 * @interface
 * @memberOf vDesk.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Search.IResult = function() {
};
vDesk.Search.IResult.prototype = {

    /**
     * Gets the control of the associated Viewer of the IResult.
     * @abstract
     * @type {HTMLElement}
     */
    Viewer: function() {
        const Element = document.createElement("div");
        /* @todo Move in separate default SearchResult. */
        Element.textContent = "";//`${vDesk.Locale.Search.MissingViewerPlugin}!`;
        return Element;
    }(),

    /**
     * Gets the associated icon of the IResult.
     * @abstract
     * @type {String}
     */
    Icon: "Default",

    /**
     * Gets the name of the IResult.
     * @abstract
     * @type {String}
     */
    Name: "",

    /**
     * Gets the type of the IResult.
     * @abstract
     * @type {String}
     */
    Type: "",

    /**
     * Gets the 'Open'-handler of the IResult.
     * @abstract
     * @type {Function}
     */
    Open: function() {
        /* @todo Move in separate default SearchResult. */
        alert(`${vDesk.Locale.Search.MissingAction}!`);
    }
};