"use strict";
/**
 * Initializes a new instance of the UpdateHost class.
 * @class Class that represents a [...] for [...]. | Class providing functionality for [...].
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.UpdateHost = function UpdateHost() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.UpdateHost.Hosted
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Updates.UpdateList#event:select
     */
    const OnSelect = Event => {
        if(Container.hasChildNodes()){
            Container.replaceChild(Event.detail.update.Control, Container.lastChild);
        }else{
            Container.appendChild(Event.detail.update.Control);
        }
        Delete.disabled = !vDesk.User.Permissions.InstallPackage;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickDelete = () => {
        const Item = Updates.Selected;
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "UpdateHost",
                    Command:    "Remove",
                    Parameters: {Hash: Item.Update.Hash},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(!Response.Status) {
                    alert(Response.Data);
                    return;
                }
                Container.removeChild(Item.Update.Control);
                Updates.Remove(Item);
                Updates.Selected = null;
                Delete.disabled = true;
            }
        );
    };

    /**
     * Hosts an Update on the server.
     *
     * @param {File} Update The PHAR archive of the Update to hosts.
     */
    this.Host = function(Update) {
        Ensure.Parameter(File, Update, "Update");

        const Item = new vDesk.Updates.UpdateList.Item(new vDesk.Updates.Update("Uploading"));
        const ProgressBar = document.createElement("progress");
        ProgressBar.value = 0;
        ProgressBar.max = 100;
        ProgressBar.className = "Pending";
        Item.Control.appendChild(ProgressBar);

        Updates.Add(Item);

        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "UpdateHost",
                    Command:    "Host",
                    Parameters: {Update: Update},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    ProgressBar.className = "Finished";
                    Item.Update = vDesk.Updates.Update.FromDataView(Response.Data);
                }else{
                    ProgressBar.className = "Error";
                    alert(Response.Data);
                    Updates.Remove(Item);
                }
                window.setTimeout(() => vDesk.Visual.Animation.FadeOut(ProgressBar, 500, () => Item.Control.removeChild(ProgressBar)), 2000);
            },
            false,
            Progress => {
                if(Progress.upload.lengthComputable) {
                    ProgressBar.value = (Progress.upload.loaded / Progress.upload.total) * 100;
                }
            }
        );
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "UpdateAdministration";
    Control.addEventListener("select", OnSelect, false);

    /**
     * The Packages of the Update Administration.
     * @type {vDesk.Updates.UpdateList}
     */
    const Updates = new vDesk.Updates.UpdateList();
    Control.appendChild(Updates.Control);

    /**
     * The Update container of the Update Administration.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container";
    Control.appendChild(Container);

    /**
     * The open file dialog of the Update Administration.
     * @type {HTMLInputElement}
     */
    const OpenFileDialog = document.createElement("input");
    OpenFileDialog.type = "file";
    OpenFileDialog.style.cssText = "display: none;";
    OpenFileDialog.multiple = true;
    OpenFileDialog.accept = ".phar,.phar.gz";
    OpenFileDialog.addEventListener("change", () => Array.from(OpenFileDialog.files).forEach(this.Host));
    Control.appendChild(OpenFileDialog);

    /**
     * The download link of the Administration.
     * @type {HTMLAnchorElement}
     */
    const Link = document.createElement("a");
    Link.style.display = "none";
    Control.appendChild(Link);

    /**
     * The controls of the Update Administration.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Control.appendChild(Controls);

    /**
     * The upload button of the Update Administration.
     * @type {HTMLButtonElement}
     */
    const Upload = document.createElement("button");
    Upload.className = "Button Icon UploadUpdate";
    Upload.style.backgroundImage = `url("${vDesk.Visual.Icons.Updates.Upload}")`;
    Upload.disabled = !vDesk.User.Permissions.InstallUpdate;
    Upload.textContent = vDesk.Locale.Updates.Upload;
    Upload.addEventListener("click", () => OpenFileDialog.click(), false);
    Controls.appendChild(Upload);

    /**
     * The delete button of the Update Administration.
     * @type {HTMLButtonElement}
     */
    const Delete = document.createElement("button");
    Delete.className = "Button Icon SearchUpdates";
    Delete.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete}")`;
    Delete.disabled = true;
    Delete.textContent = vDesk.Locale.vDesk.Delete;
    Delete.addEventListener("click", OnClickDelete, false);
    Controls.appendChild(Delete);

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "UpdateHost",
                Command:    "Hosted",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            if(Response.Status) {
                Updates.Items = Response.Data.map(Update => new vDesk.Updates.UpdateList.Item(vDesk.Updates.Update.FromDataView(Update)));
                Updates.Selected = Updates.Items?.[0] ?? Updates.Selected;
                Container.appendChild(Updates.Selected?.Update?.Control ?? document.createTextNode("No updates hosted"));
                Delete.disabled = Updates.Selected === null || !vDesk.User.Permissions.InstallPackage;
            } else {
                alert(Response.Data);
            }
        }
    );

};

vDesk.Configuration.Remote.Plugins.UpdateHost = vDesk.UpdateHost;