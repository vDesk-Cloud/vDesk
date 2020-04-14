"use strict";
/**
 * Initializes a new instance of the VideoPlayer class.
 * @class Plugin for playing video files.
 * @param {vDesk.Archive.Element} Element Initializes the VideoPlayer with the Element to display.
 * @property {HTMLVideoElement} Control Gets the underlying DOM-Node.
 * @memberOf vDesk.Archive.Element.View
 */
vDesk.Archive.Element.View.VideoPlayer = function VideoPlayer(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    /**
     * Thy underlying DOM-Node.
     * @type {HTMLVideoElement}
     */
    const Control = document.createElement("video");
    Control.className = "ElementViewer VideoPlayer";
    Control.loop = true;
    Control.controls = true;
    Control.autoplay = true;

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Archive",
                Command:    "Download",
                Parameters: {ID: Element.ID},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Buffer => {
            //Create an ObjectURL from the binary data.
            Control.src = URL.createObjectURL(new Blob([Buffer], {type: "video/" + Element.Extension}));
            Control.onload = () => URL.revokeObjectURL(Control.src);
        },
        true
    );

};
/**
 * The file extensions the plugin can handle
 * @type {Array}
 * @enum {String}
 */
vDesk.Archive.Element.View.VideoPlayer.Extensions = ["mp4", "webm", "avi", "mpg"];
