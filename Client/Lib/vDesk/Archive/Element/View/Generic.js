"use strict";
/**
 * Initializes a new instance of the Generic class.
 * @class Plugin for displaying a file with the browser default mimehandler.
 * @param {vDesk.Archive.Element} Element The Element to display the file of.
 * @property {HTMLDivElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.View
 * @package vDesk\Archive
 */
vDesk.Archive.Element.View.Generic = function Generic(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Viewer";

    /**
     * The media control containing the data.
     * @type HTMLObjectElement
     */
    const MediaObject = document.createElement("object");
    MediaObject.style.cssText = "width: 100%; height: 100%;";

    Control.appendChild(MediaObject);

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
            //Create an ObjectURL from the binarydata.
            MediaObject.type = "application/" + Element.Extension;
            MediaObject.data = URL.createObjectURL(new Blob([Buffer], {type: "application/" + Element.Extension}));
            MediaObject.onload = () => URL.revokeObjectURL(MediaObject.data);
        },
        true
    );
};

/**
 * The file extensions the plugin can handle.
 * @enum {String}
 */
vDesk.Archive.Element.View.Generic.Extensions = [];
