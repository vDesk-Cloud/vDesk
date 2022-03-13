"use strict";
/**
 * Initializes a new instance of the ImageViewer class.
 * @class Plugin for displaying imagedata.
 * @param {vDesk.Archive.Element} Element The element to display the image of.
 * @property {HTMLDivElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.View
 * @package vDesk\Archive
 */
vDesk.Archive.Element.View.ImageViewer = function ImageViewer(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "ElementViewer Image";

    /**
     * The image control containing the data.
     * @type {HTMLImageElement}
     */
    const Image = document.createElement("img");
    Control.appendChild(Image);

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
            Image.src = URL.createObjectURL(new Blob([Buffer], {type: "image/" + Element.Extension}));
            Image.onload = () => URL.revokeObjectURL(Image.src);
        },
        true
    );
};

/**
 * The file extensions the plugin can handle.
 * @enum {String}
 */
vDesk.Archive.Element.View.ImageViewer.Extensions = ["jpg", "jpeg", "png", "gif", "bmp", "tiff"];
