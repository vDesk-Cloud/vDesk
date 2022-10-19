"use strict";
/**
 * Initializes a new instance of the Clipboard class.
 * @class Represents a clipboard of the archive. Providing functionality for storing elements.
 * @constructor
 * @property {Operations} Operations Gets an enumeration of possible operations.
 * @property {String} LastOperation Gets or sets the last performed operation.
 * @property {Array<vDesk.Archive.Element>} Elements Gets or sets the stored elements of the Clipboard.
 * @property {Boolean} ContainsElements Gets a value that determines if the Clipboard has stored elements.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.Clipboard = function Clipboard() {

    /**
     * The elements of the Clipboard.
     * @type {Array<vDesk.Archive.Element>}
     */
    let Elements = [];

    /**
     * The last operation which has been performed. Formerly cut or copy.
     * @type {String}
     */
    let LastOperation = null;

    Object.defineProperties(this, {
        LastOperation:    {
            enumerable: true,
            get:        () => LastOperation,
            set:        Value => {
                Ensure.Property(Value, "string", "LastOperation");
                LastOperation = Value;
            }
        },
        Elements:         {
            enumerable: true,
            get:        () => Elements,
            set:        Value => {
                Ensure.Property(Value, Array, "Elements");
                //Clear array
                Elements = [];
                //Append new elements.
                Value.forEach(Element => {
                    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
                    Elements.push(Element);
                });
            }
        },
        ContainsElements: {
            enumerable: true,
            get:        () => Elements.length > 0
        }
    });

    /**
     * Adds a element to the Clipboard.
     * @param {vDesk.Archive.Element} Element The element to add.
     * @return {vDesk.Archive.Clipboard.Elements}
     */
    this.Add = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        Elements.push(Element);
    };
    /**
     * Removes an element from the Clipboard.
     * @param {vDesk.Archive.Element} Element The element to remove.
     * @return {vDesk.Archive.Clipboard.Elements}
     */
    this.Remove = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        //Get the item to remove.
        const FoundElement = Elements.find(ClipboardElement => ClipboardElement.ID === Element.ID);
        //Remove the control from the childlist and the collection.
        if(FoundElement !== undefined){
            Elements.splice(Elements.indexOf(FoundElement), 1);
        }
    };

    /**
     * Removes all stored Elements from the Clipboard.
     */
    this.Clear = function() {
        Elements = [];
    };

};
/**
 * Enumeration of possible Clipboard operations.
 * @readonly
 * @enum {String}
 * @name vDesk.Archive.Clipboard.Operations
 */
Object.defineProperty(vDesk.Archive.Clipboard, "Operations", {
    value: {
        Copy:  "Copy",
        Cut:   "Cut",
        Paste: "Paste"
    }
});