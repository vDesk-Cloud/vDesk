"use strict";
/**
 * Initializes a new instance of the Administration class.
 * @class Class that represents a plugin for installing Updates.
 * @property {HTMLDivElement} Control gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the Administration plugin.
 * @memberOf vDesk.Updates
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Updates
 */
vDesk.Updates.Administration = function Administration() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Updates.Updates
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Updates.UpdateList#event:select
     */
    const OnSelect = Event => {
        while(Container.hasChildNodes()){
            Container.removeChild(Container.lastChild);
        }
        Container.appendChild(Event.detail.update.Control);
        Install.disabled = !vDesk.Security.User.Current.Permissions.InstallUpdate;
        Download.disabled = false;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickSearch = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:  "Updates",
                    Command: "Search",
                    Ticket:  vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(!Response.Status){
                    alert(Response.Data);
                    return;
                }
                Updates.Clear();
                while(Container.hasChildNodes()){
                    Container.removeChild(Container.lastChild);
                }
                Response.Data.forEach(Update => Updates.Add(new vDesk.Updates.UpdateList.Item(vDesk.Updates.Update.FromDataView(Update))));
                Install.disabled = true;
                Download.disabled = true;
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickDownload = () => {
        const Item = Updates.Selected;
        const ProgressBar = document.createElement("progress");
        ProgressBar.value = 0;
        ProgressBar.max = 100;
        ProgressBar.className = "Pending";
        Item.Control.appendChild(ProgressBar);

        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Updates",
                    Command:    "Download",
                    Parameters: {
                        Source: Item.Update.Source,
                        Hash:   Item.Update.Hash
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Buffer => {
                ProgressBar.className = "Finished";
                //Create an objecturl from the binarydata.
                Link.download = `${Item.Update.Package}[${Item.Update.Version}].phar`;
                Link.href = URL.createObjectURL(new Blob([Buffer], {type: "application/octet-stream"}));
                Link.click();
                window.setTimeout(() => URL.revokeObjectURL(Link.href), 60000);
                window.setTimeout(() => vDesk.Visual.Animation.FadeOut(ProgressBar, 500, () => Item.Control.removeChild(ProgressBar)), 2000);
            },
            true,
            Progress => {
                if(Progress.download.lengthComputable){
                    ProgressBar.value = (Progress.download.loaded / Progress.download.total) * 100;
                }
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickInstall = () => {
        let Item = Updates.Selected;

        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Updates",
                    Command:    "Install",
                    Parameters: {
                        Source: Item.Update.Source,
                        Hash:   Item.Update.Hash
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(!Response.Status){
                    alert(Response.Data);
                    return;
                }

                alert(`Update ${Item.Update.Package} [${Item.Update.RequiredVersion}] has been successfully installed!`);
                Updates.Remove(Item);

                //Remove similar Updates.
                Updates.Items.filter(AvailableItem => AvailableItem.Update.Hash === Item.Update.Hash).forEach(Updates.Remove);

                if(Updates.Items.length === 0){
                    Install.disabled = true;
                    Download.disabled = true;
                }
                while(Container.hasChildNodes()){
                    Container.removeChild(Container.lastChild);
                }
            }
        );
    };

    /**
     * Deploys an Update on the server.
     *
     * @param {File} Update The PHAR archive of the Update to deploy.
     */
    this.Deploy = function(Update) {
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
                    Module:     "Updates",
                    Command:    "Deploy",
                    Parameters: {Update: Update},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(Response.Status){
                    ProgressBar.className = "Finished";
                    Item.Update = vDesk.Updates.Update.FromDataView(Response.Data);
                }else{
                    ProgressBar.className = "Error";
                    alert(Response.Data);

                    //Dont keep anyway
                    Updates.Remove(Item);
                }
                window.setTimeout(() => vDesk.Visual.Animation.FadeOut(ProgressBar, 500, () => Item.Control.removeChild(ProgressBar)), 2000);
            },
            false,
            Progress => {
                if(Progress.upload.lengthComputable){
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
    OpenFileDialog.addEventListener("change", () => Array.from(OpenFileDialog.files).forEach(this.Install));
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
     * The search button of the Update Administration.
     * @type {HTMLButtonElement}
     */
    const Search = document.createElement("button");
    Search.className = "Button Icon SearchUpdates";
    Search.style.backgroundImage = `url("${vDesk.Visual.Icons.Updates.Search}")`;
    Search.disabled = !vDesk.Security.User.Current.Permissions.InstallUpdate;
    Search.textContent = vDesk.Locale.Updates.Search;
    Search.addEventListener("click", OnClickSearch, false);
    Controls.appendChild(Search);

    /**
     * The download button of the Update Administration.
     * @type {HTMLButtonElement}
     */
    const Download = document.createElement("button");
    Download.className = "Button Icon DownloadUpdate";
    Download.style.backgroundImage = `url("${vDesk.Visual.Icons.Updates.Download}")`;
    Download.disabled = true;
    Download.textContent = vDesk.Locale.Updates.Download;
    Download.addEventListener("click", OnClickDownload, false);
    Controls.appendChild(Download);

    /**
     * The install button of the Update Administration.
     * @type {HTMLButtonElement}
     */
    const Install = document.createElement("button");
    Install.className = "Button Icon Updates";
    Install.style.backgroundImage = `url("${vDesk.Visual.Icons.Packages.Install}")`;
    Install.disabled = true;
    Install.textContent = vDesk.Locale.Packages.Install;
    Install.addEventListener("click", OnClickInstall, false);
    Controls.appendChild(Install);

    /**
     * The install button of the Update Administration.
     * @type {HTMLButtonElement}
     */
    const Deploy = document.createElement("button");
    Deploy.className = "Button Icon Updates";
    Deploy.style.backgroundImage = `url("${vDesk.Visual.Icons.Updates.Upload}")`;
    Deploy.disabled = !vDesk.Security.User.Current.Permissions.InstallUpdate
    Deploy.textContent = vDesk.Locale.Updates.Deploy;
    Deploy.addEventListener("click", () => OpenFileDialog.click(), false);
    Controls.appendChild(Deploy);

};

vDesk.Configuration.Remote.Plugins.UpdateAdministration = vDesk.Updates.Administration;