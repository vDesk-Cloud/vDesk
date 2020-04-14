"use strict";
/**
 * Initializes a new instance of the Uploader class.
 * @class Provides functionality for uploading files to the server.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.Uploader = function Uploader() {

    /**
     * Uploads a file to the server and displays the progress on the associated Element and Item.
     * @param {File} File The file to upload.
     * @param {vDesk.Archive.Element} Element The Element the file belongs to.
     * @param {vDesk.Archive.TreeView.Item} Item The Item the file belongs to.
     */
    this.Upload = function(File, Element, Item) {
        Ensure.Parameter(File, Blob, "File");
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        Ensure.Parameter(Item, vDesk.Archive.TreeView.Item, "Item");

        //Setup progressbar.
        const ElementProgressBar = document.createElement("progress");
        ElementProgressBar.value = 0;
        ElementProgressBar.max = 100;
        ElementProgressBar.className = "Upload Pending";

        const ItemProgressBar = document.createElement("progress");
        ItemProgressBar.value = 0;
        ItemProgressBar.max = 100;
        ItemProgressBar.className = "Upload Pending";

        //Populate rudimentary data.
        Element.Name = File.name.substr(0, File.name.lastIndexOf("."));
        Element.Extension = File.name.substr(File.name.lastIndexOf(".") + 1).toLowerCase();
        Element.Icon = Element.Extension;
        Element.ShowThumbnail = false;
        Element.Control.appendChild(ElementProgressBar);

        Item.Name = Element.Name;
        Item.Type = vDesk.Archive.Element.File;
        Item.Icon = Element.Icon;
        Item.ShowThumbnail = false;
        Item.Control.appendChild(ItemProgressBar);

        //Upload file.
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "Upload",
                    Parameters: {
                        Parent: Element.Parent.ID,
                        Name:   File.name,
                        File:   File
                    },
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    ElementProgressBar.classList.replace("Pending", "Finished");
                    ItemProgressBar.classList.replace("Pending", "Finished");

                    //Populate data from the server into the newly created element.
                    Element.ID = Response.Data.ID;
                    Element.Name = Response.Data.Name;
                    Element.Type = Response.Data.Type;
                    Element.CreationTime = Response.Data.CreationTime;
                    Element.Guid = Response.Data.Guid;
                    Element.Extension = Response.Data.Extension;
                    Element.File = Response.Data.File;
                    Element.Size = Response.Data.Size;
                    Element.Thumbnail = Response.Data.Thumbnail;
                    Element.AccessControlList = vDesk.Security.AccessControlList.FromDataView(Response.Data.AccessControlList);

                    //Populate data from the server into the belonging Item if any.
                    Item.ID = Element.ID;
                    Item.Name = Element.Name;
                    Item.Type = Element.Type;
                    Item.Parent = Element.Parent;
                    Item.Thumbnail = Element.Thumbnail;
                    Item.ShowThumbnail = true;

                    window.setTimeout(() => {
                        vDesk.Visual.Animation.FadeOut(ElementProgressBar, 500, () => Element.Control.removeChild(ElementProgressBar));
                        vDesk.Visual.Animation.FadeOut(ItemProgressBar, 500, () => Item.Control.removeChild(ItemProgressBar));
                    }, 2000);
                } else {
                    ElementProgressBar.classList.replace("Pending", "Error");
                    ItemProgressBar.classList.replace("Pending", "Error");
                    window.setTimeout(() => new vDesk.Events.BubblingEvent("uploadfailed", {
                        element: Element,
                        item:    Item
                    }).Dispatch(window), 2000);
                }
            },
            false,
            Progress => {
                if(Progress.upload.lengthComputable) {
                    ElementProgressBar.value = ItemProgressBar.value = (Progress.upload.loaded / Progress.upload.total) * 100;
                }
            }
        );
    };
};