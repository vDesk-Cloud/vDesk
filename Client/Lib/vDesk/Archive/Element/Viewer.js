"use strict";
/**
 * Initializes a new instance of the Viewer class.
 * @class Respresents a content presenter for displaying the file of an element.
 * Loads registered plugin to display the file.
 * @see {@link vDesk.Archive.Element.View}
 * @param {vDesk.Archive.Element} Element The element to display the file of.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @memberOf vDesk.Archive.Element
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.Element.Viewer = function Viewer(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    /**
     * The underlying DOM-Node of the current loaded plugin.
     * @type {HTMLElement}
     */
    let Control = null;

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    //Loop through registered plugins and load found.
    for(const Plugin of Object.values(vDesk.Archive.Element.View)) {
        //Check if the plugin can handle the extension.
        if(~Plugin.Extensions.indexOf(Element.Extension)) {
            Control = new Plugin(Element).Control;
            return;
        }
    }
    //If no plugin has been found, try to display it with the browser mimehandler.
    Control = new vDesk.Archive.Element.View.Generic(Element).Control;
};

vDesk.Archive.Element.Viewer.CanShow = function(Element){

};