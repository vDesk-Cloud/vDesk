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
            value:      vDesk.Locale["Packages"]["Packages"]
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.GroupList#event:select
     */
    const OnSelect = Event => Description.replaceChild(Event.detail.package.Control, Description.lastChild);

    /**
     * Eventhandler that listens on the 'change' event.
     */
    const OnChange = () => {
        //Loop through dropped files.
        Array.from(OpenFileDialog.files)
            .forEach(File => {
                vDesk.Connection.Send(
                    new vDesk.Modules.Command(
                        {
                            Module:     "Packages",
                            Command:    "InstallPackage",
                            Parameters: {Package: File},
                            Ticket:     vDesk.User.Ticket
                        }
                    ),
                    Response => {
                        if(!Response.Status) {
                            alert(Response.Data);
                            return;
                        }
                        Packages.Add(
                            new vDesk.Packages.PackageList.Item(
                                vDesk.Packages.Package.FromDataView(Response.Data)
                            )
                        );
                    }
                );
            });
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClick = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Packages",
                    Command:    "UninstallPackage",
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
     * The Packages of the Administration.
     * @type {vDesk.Packages.PackageList}
     */
    const Packages = new vDesk.Packages.PackageList();
    Control.appendChild(Packages.Control);

    /**
     * The description list of the Administration.
     * @type {HTMLDivElement}
     */
    const Description = document.createElement("div");
    Description.className = "Description";
    Control.appendChild(Description);

    /**
     * The open file dialog of the Archive module.
     * @type {HTMLInputElement}
     */
    const OpenFileDialog = document.createElement("input");
    OpenFileDialog.type = "file";
    OpenFileDialog.style.cssText = "display: none;";
    OpenFileDialog.multiple = true;
    OpenFileDialog.accept = ".phar";
    OpenFileDialog.addEventListener("change", OnChange, false);
    Control.appendChild(OpenFileDialog);

    /**
     * The install button of the Administration.
     * @type {HTMLButtonElement}
     */
    const Install = document.createElement("button");
    Install.className = "Button Icon Packages";
    Install.style.backgroundImage = `url("${vDesk.Visual.Icons.Save || vDesk.Visual.Icons.Unknown}")`;
    Install.disabled = !vDesk.User.Permissions["InstallPackage"];
    Install.textContent = vDesk.Locale["Packages"]["Install"];
    Install.addEventListener("click", () => OpenFileDialog.click(), false);

    /**
     * The uninstall button of the Administration.
     * @type {HTMLButtonElement}
     */
    const Uninstall = document.createElement("button");
    Uninstall.className = "Button Icon Uninstall";
    Uninstall.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete || vDesk.Visual.Icons.Unknown}")`;
    Uninstall.disabled = !vDesk.User.Permissions["UninstallPackage"];
    Uninstall.textContent = vDesk.Locale["Packages"]["Uninstall"];
    Uninstall.addEventListener("click", OnClick, false);

    /**
     * The controls of the Administration.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Controls.appendChild(Install);
    Controls.appendChild(Uninstall);
    Control.appendChild(Controls);

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Packages",
                Command:    "GetPackages",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            if(Response.Status) {
                Packages.Items = Response.Data.map(Package => new vDesk.Packages.PackageList.Item(vDesk.Packages.Package.FromDataView(Package)));
                Packages.Selected = Packages.Items[0];
                Description.appendChild(Packages.Selected.Package.Control)
            } else {
                alert(Response.Data);
            }
        }
    );

};

vDesk.Configuration.Remote.Plugins.PackageAdministration = vDesk.Packages.Administration;