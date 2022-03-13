"use strict";
/**
 * Initializes a new instance of the Administration class.
 * @class Class that represents a [...] for [...]. | Class providing functionality for [...].
 * @memberOf vDesk.Packages
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Packages.Administration = function Administration() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Packages.Packages
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.GroupList#event:select
     */
    const OnSelect = Event => Container.replaceChild(Event.detail.package.Control, Container.lastChild);

    /**
     * Install a Package on the server.
     *
     * @param {File} Package The PHAR archive of the Package to install.
     */
    this.Install = function(Package) {
        Ensure.Parameter(File, Package, "Package");

        const Item = new vDesk.Packages.PackageList.Item(new vDesk.Packages.Package("Uploading"));
        const ProgressBar = document.createElement("progress");
        ProgressBar.value = 0;
        ProgressBar.max = 100;
        ProgressBar.className = "Pending";
        Item.Control.appendChild(ProgressBar);

        Packages.Add(Item);

        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Packages",
                    Command:    "Install",
                    Parameters: {Package: Package},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    ProgressBar.className = "Finished";
                    Item.Package = vDesk.Packages.Package.FromDataView(Response.Data);
                }else{
                    ProgressBar.className = "Error";
                    alert(Response.Data);
                    Packages.Remove(Item);
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
     * Eventhandler that listens on the 'click' event.
     */
    const OnClick = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Packages",
                    Command:    "Uninstall",
                    Parameters: {Package: Packages.Selected.Package.Name},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(!Response.Status) {
                    alert(Response.Data);
                    return;
                }
                Response.Data.forEach(Package => Packages.Remove(Packages.Find(Package)));
                Packages.Selected = null;
            }
        );
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "PackageAdministration";
    Control.addEventListener("select", OnSelect, false);

    /**
     * The Packages of the Package Administration.
     * @type {vDesk.Packages.PackageList}
     */
    const Packages = new vDesk.Packages.PackageList();
    Control.appendChild(Packages.Control);

    /**
     * The Package container of the Package Administration.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container";
    Control.appendChild(Container);

    /**
     * The open file dialog of the Package Administration.
     * @type {HTMLInputElement}
     */
    const OpenFileDialog = document.createElement("input");
    OpenFileDialog.type = "file";
    OpenFileDialog.style.cssText = "display: none;";
    OpenFileDialog.multiple = true;
    OpenFileDialog.accept = ".phar";
    OpenFileDialog.addEventListener("change", () => Array.from(OpenFileDialog.files).forEach(this.Install));
    Control.appendChild(OpenFileDialog);

    /**
     * The controls of the Package Administration.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Control.appendChild(Controls);

    /**
     * The install button of the Package Administration.
     * @type {HTMLButtonElement}
     */
    const Install = document.createElement("button");
    Install.className = "Button Icon Packages";
    Install.style.backgroundImage = `url("${vDesk.Visual.Icons.Packages.Install}")`;
    Install.disabled = !vDesk.User.Permissions.InstallPackage;
    Install.textContent = vDesk.Locale.Packages.Install;
    Install.addEventListener("click", () => OpenFileDialog.click());
    Controls.appendChild(Install);

    /**
     * The uninstall button of the Package Administration.
     * @type {HTMLButtonElement}
     */
    const Uninstall = document.createElement("button");
    Uninstall.className = "Button Icon Uninstall";
    Uninstall.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete}")`;
    Uninstall.disabled = !vDesk.User.Permissions.UninstallPackage;
    Uninstall.textContent = vDesk.Locale.Packages.Uninstall;
    Uninstall.addEventListener("click", OnClick, false);
    Controls.appendChild(Uninstall);


    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Packages",
                Command:    "Installed",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            if(Response.Status) {
                Packages.Items = Response.Data.map(Package => new vDesk.Packages.PackageList.Item(vDesk.Packages.Package.FromDataView(Package)));
                Packages.Selected = Packages.Items[0];
                Container.appendChild(Packages.Selected.Package.Control)
            } else {
                alert(Response.Data);
            }
        }
    );

};

vDesk.Configuration.Remote.Plugins.PackageAdministration = vDesk.Packages.Administration;