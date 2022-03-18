"use strict";
/**
 * Initializes a new instance of the Editor class.
 * @class Respresents a content presenter for displaying the file of an element.
 * Loads registered plugin to display the file.
 * @param {vDesk.Archive.Element} Element The element to display the file of.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @memberOf vDesk.Archive.Element
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.Element.Editor = function Editor(Element) {
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
    for(const Plugin of Object.values(vDesk.Archive.Element.Edit)) {
        //Check if the plugin can handle the extension.
        if(~Plugin.Extensions.indexOf(Element.Extension)) {
            Control = new Plugin(Element).Control;
            return;
        }
    }
};