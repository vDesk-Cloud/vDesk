"use strict";
/**
 * Initializes a new instance of the Text class.
 * @class Plugin for displaying the contents of a text file
 * @param {vDesk.Archive.Element} Element The element to display the file of.
 * @property {HTMLDivElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.View
 * @package vDesk\Archive
 */
vDesk.Archive.Element.View.Text = function Text(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLPreElement}
     */
    const Control = document.createElement("pre");
    Control.className = "ElementViewer Text Font Dark";

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Archive",
                Command:    "Download",
                Parameters: {ID: Element.ID},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Buffer => {
            //Create an objecturl from the binarydata.
            const Reader = new FileReader();
            Reader.onload = () => Control.textContent = Reader.result;
            Reader.readAsText(new Blob([Buffer], {type: "text/plain"}));
        },
        true
    );
};

/**
 * The file extensions the plugin can handle.
 * @enum {String}
 */
vDesk.Archive.Element.View.Text.Extensions = [
    "txt",
    "xml",
    "html",
    "xhtml",
    "css",
    "csv",
    "yaml",
    "yml",
    "bat",
    "php",
    "js",
    "json",
    "sql"
];
