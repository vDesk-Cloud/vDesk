"use strict";
/**
 * Initializes a new instance of the InlineFrame class.
 * @class Plugin for displaying a file with the browser default mimehandler.
 * @param {vDesk.Archive.Element} Element The Element to display the file of.
 * @property {HTMLDivElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.View
 * @package vDesk\Archive
 */
vDesk.Archive.Element.View.InlineFrame = function InlineFrame(Element) {
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
     * The image control containing the data.
     * @type HTMLIFrameElement
     */
    const Frame = document.createElement("iframe");
    Frame.style.cssText = "width: 100%; height: 100%;";
    Control.appendChild(Frame);

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
            //Create an ObjectURL from the binary data.
            Frame.name = Element.Name;
            Frame.src = URL.createObjectURL(new Blob([Buffer], {type: "text/" + Element.Extension}));
            Frame.onload = () => URL.revokeObjectURL(Frame.src);
        },
        true
    );
};

/**
 * The file extensions the plugin can handle.
 * @enum {String}
 */
vDesk.Archive.Element.View.InlineFrame.Extensions = ["html", "xhtml", "htm"];
