"use strict";
/**
 * Initializes a new instance of the Downloader class.
 * @class Provides functionality for downloading files from the server.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Make singleton.
 */
vDesk.Archive.Downloader = function() {

    /**
     * Downloads a file to the client.
     * @param {vDesk.Archive.Element} Element The element to download the file of.
     */
    this.Download = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

        //Setup progressbar
        const ProgressBar = document.createElement("progress");
        ProgressBar.value = 0;
        ProgressBar.max = 100;
        ProgressBar.style.cssText = "width: 80px; height: 80px; z-index: 150;";
        Element.Control.appendChild(ProgressBar);

        //Fetch file.
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
                //Create an objecturl from the binarydata.
                Link.download = Element.Name + "." + Element.Extension;
                Link.href = URL.createObjectURL(new Blob([Buffer], {type: "application/octet-stream"}));
                Link.click();
                Element.Control.removeChild(ProgressBar);
                setTimeout(() => URL.revokeObjectURL(Link.href), 60000);
            },
            true,
            Progress => {
                if(Progress.download.lengthComputable) {
                    ProgressBar.value = (Progress.download.loaded / Progress.download.total) * 100;
                }
            }
        );
    };

    /**
     * Unloads the DownloadHelper.
     * @function
     */
    this.Remove = function() {
        document.body.removeChild(Link);
    };

    /**
     * The link the file refers to.
     * @type HTMLAnchorElement
     */
    const Link = document.createElement("a");
    Link.style.cssText = "display: none;";
    document.body.appendChild(Link);
};