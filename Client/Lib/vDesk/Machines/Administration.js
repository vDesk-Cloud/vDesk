"use strict";
/**
 * Initializes a new instance of the Administration class.
 * @class Class that represents a [...] for [...]. | Class providing functionality for [...].
 * @memberOf vDesk.Machines
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Machines.Administration = function Administration() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Machines.Machines
        }
    });

    /**
     * The current selected Machien of the Administration.
     * @type {null|vDesk.Machines.Machine}
     */
    let Selected = null;

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.GroupList#event:select
     */
    const OnSelect = Event => {
        if(Selected != null) {
            Selected.Selected = false;
        }
        Selected = Event.detail.sender;
        Selected.Selected = true;
        SuspendResume.disabled = false;
        if(Selected.Status === vDesk.Machines.Machine.Suspended) {
            SuspendResume.style.backgroundImage = `url("${vDesk.Visual.Icons.Archive.Refresh}")`;
            SuspendResume.textContent = vDesk.Locale.Machines.Resume;
        } else {
            SuspendResume.style.backgroundImage = `url("${vDesk.Visual.Icons.Machines.Suspend}")`;
            SuspendResume.textContent = vDesk.Locale.Machines.Suspend;
        }
        Stop.disabled = false;
        Terminate.disabled = false;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickDeselect = () => {
        if(Selected != null) {
            Selected.Selected = false;
        }
        Selected = null;
        SuspendResume.disabled = true;
        Stop.disabled = true;
        Terminate.disabled = true;
    };
    window.addEventListener("click", OnClickDeselect);

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickStart = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Machines",
                    Command:    "Start",
                    Parameters: {Name: Installed.Value},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(!Response.Status) {
                    alert(Response.Data);
                    return;
                }
                Running.Rows.Add(vDesk.Machines.Machine.FromDataView(Response.Data));
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickSuspendResume = Event => {
        Event.stopPropagation();
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Machines",
                    Command:    Selected.Status === vDesk.Machines.Machine.Running ? "Suspend" : "Resume",
                    Parameters: {Guid: Selected.Guid},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(!Response.Status) {
                    alert(Response.Data);
                    return;
                }
                if(Selected.Status === vDesk.Machines.Machine.Running) {
                    Selected.Status = vDesk.Machines.Machine.Suspended;
                    SuspendResume.style.backgroundImage = `url("${vDesk.Visual.Icons.Archive.Refresh}")`;
                    SuspendResume.textContent = vDesk.Locale.Machines.Resume;
                } else {
                    Selected.Status = vDesk.Machines.Machine.Running;
                    SuspendResume.style.backgroundImage = `url("${vDesk.Visual.Icons.Machines.Suspend}")`;
                    SuspendResume.textContent = vDesk.Locale.Machines.Suspend;
                }
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickStop = Event => {
        Event.stopPropagation();
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Machines",
                    Command:    "Stop",
                    Parameters: {Guid: Selected.Guid},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Running.Rows.Remove(Selected);
                    Selected = null;
                } else {
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickTerminate = Event => {
        Event.stopPropagation();
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Machines",
                    Command:    "Terminate",
                    Parameters: {Guid: Selected.Guid},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Running.Rows.Remove(Selected);
                    Selected = null;
                } else {
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickReap = Event => {
        Event.stopPropagation();
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:  "Machines",
                    Command: "Reap",
                    Ticket:  vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    this.Running();
                } else {
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "MachineAdministration";
    Control.addEventListener("select", OnSelect, false);

    /**
     * The running Machines Container of the Administration.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container";
    Control.appendChild(Container);

    /**
     * The current running Machines of the Administration.
     * @type {vDesk.Controls.Table}
     */
    const Running = new vDesk.Controls.Table([
        {
            Name:  "Name",
            Label: vDesk.Locale.vDesk.Name,
            Type:  Type.String
        },
        {
            Name:  "PID",
            Label: "PID",
            Type:  Type.Int
        },
        {
            Name:  "Status",
            Label: vDesk.Locale.Machines.Status,
            Type:  Type.String
        },
        {
            Name: "Guid",
            Type: Type.String
        },
        {
            Name:  "Owner",
            Label: vDesk.Locale.Security.Owner,
            Type:  Type.Int
        },

        {
            Name:  "TimeStamp",
            Label: vDesk.Locale.Machines.TimeStamp,
            Type:  Type.Int
        }
    ]);
    Container.appendChild(Running.Control);


    this.Running = function() {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Machines",
                    Command:    "Running",
                    Parameters: {},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Running.Rows = Response.Data.map(Machine => vDesk.Machines.Machine.FromDataView(Machine));
                } else {
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * The controls of the Administration.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Control.appendChild(Controls);

    /**
     * The currently installed Machines.
     * @type {vDesk.Controls.EditControl}
     */
    const Installed = new vDesk.Controls.EditControl(
        this.Title,
        null,
        Extension.Type.Enum,
        null,
    )
    Installed.Control.addEventListener("update", () => Start.disabled = !vDesk.User.Permissions.RunMachine);
    Installed.Control.addEventListener("clear", () => Start.disabled = true);
    Controls.appendChild(Installed.Control);
    this.Running();
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Machines",
                Command:    "Installed",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            if(Response.Status) {
                Installed.Validator = Response.Data;
                Installed.Enabled = true;
            } else {
                alert(Response.Data);
            }
        }
    );

    /**
     * The start button of the Administration.
     * @type {HTMLButtonElement}
     */
    const Start = document.createElement("button");
    Start.className = "Button Icon Machines";
    Start.style.backgroundImage = `url("${vDesk.Visual.Icons.Machines.Start}")`;
    Start.disabled = true;
    Start.textContent = vDesk.Locale.Machines.Start;
    Start.addEventListener("click", OnClickStart);
    Controls.appendChild(Start);

    /**
     * The suspend/resume button of the Administration.
     * @type {HTMLButtonElement}
     */
    const SuspendResume = document.createElement("button");
    SuspendResume.className = "Button Icon SuspendResume";
    SuspendResume.style.backgroundImage = `url("${vDesk.Visual.Icons.Machines.Suspend}")`;
    SuspendResume.disabled = !vDesk.User.Permissions.RunMachine;
    SuspendResume.textContent = vDesk.Locale.Machines.Suspend;
    SuspendResume.addEventListener("click", OnClickSuspendResume);
    Controls.appendChild(SuspendResume);

    /**
     * The stop button of the Administration.
     * @type {HTMLButtonElement}
     */
    const Stop = document.createElement("button");
    Stop.className = "Button Icon Stop";
    Stop.style.backgroundImage = `url("${vDesk.Visual.Icons.Logout}")`;
    Stop.disabled = !vDesk.User.Permissions.RunMachine;
    Stop.textContent = vDesk.Locale.Machines.Stop;
    Stop.addEventListener("click", OnClickStop);
    Controls.appendChild(Stop);

    /**
     * The terminate button of the Administration.
     * @type {HTMLButtonElement}
     */
    const Terminate = document.createElement("button");
    Terminate.className = "Button Icon Terminate";
    Terminate.style.backgroundImage = `url("${vDesk.Visual.Icons.Machines.Terminate}")`;
    Terminate.disabled = vDesk.User.Permissions.RunMachine;
    Terminate.textContent = vDesk.Locale.Machines.Terminate;
    Terminate.addEventListener("click", OnClickTerminate);
    Controls.appendChild(Terminate);

    /**
     * The reap button of the Administration.
     * @type {HTMLButtonElement}
     */
    const Reap = document.createElement("button");
    Reap.className = "Button Icon Reap";
    Reap.style.backgroundImage = `url("${vDesk.Visual.Icons.Machines.Reap}")`;
    Reap.disabled = !vDesk.User.Permissions.RunMachine;
    Reap.title = vDesk.Locale.Machines.Reap;
    Reap.addEventListener("click", OnClickReap);
    Controls.appendChild(Reap);

};

vDesk.Configuration.Remote.Plugins.MachineAdministration = vDesk.Machines.Administration;