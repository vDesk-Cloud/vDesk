"use strict";
/**
 * Fired if the Machine has been selected.
 * @event vDesk.Machines.Machine#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Machines.Machine} detail.sender The current instance of the Machine.
 */
/**
 * Initializes a new instance of the Machine class.
 * @class Class that represents a running Machine.
 *
 * @param {Number} [ID=null] Initializes the Machine with the specified process ID name.
 * @param {vDesk.Security.User} Owner Initializes the Machine with the specified name.
 * @param {String} [Guid=null] Initializes the Machine with the specified guid.
 * @param {String} [Name=""] Initializes the Machine with the specified name.
 * @param {Number} [TimeStamp=null] Initializes the Machine with the specified timestamp.
 * @param {String} [Status=vDesk.Machines.Machine.Virtual] Initializes the Machine with the specified status.
 * @property {Number} ID Gets or sets the name of the Machine.
 * @property {vDesk.Security.User} Owner Gets or sets the owner of the Machine.
 * @property {String} Guid Gets or sets the guid of the Machine.
 * @property {Number} TimeStamp Gets or sets the timestamp of the Machine.
 * @property {String} Status Gets or sets the status of the Machine.
 * @property {String} Name Gets or sets the name of the Machine.
 * @property {Number} Index Gets or sets the current index of the Machine.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Machine is selected.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Machine is enabled.
 * @memberOf vDesk.Machines
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
vDesk.Machines.Machine = function Machine(
    ID        = null,
    Owner     = vDesk.User,
    Guid      = null,
    TimeStamp = 0,
    Status    = vDesk.Machines.Machine.Virtual,
    Name      = ""
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Owner, vDesk.Security.User, "Owner");
    Ensure.Parameter(Guid, Type.String, "Guid", true);
    Ensure.Parameter(TimeStamp, Type.Number, "TimeStamp");
    Ensure.Parameter(Status, Type.String, "Status");
    Ensure.Parameter(Name, Type.String, "Name");

    /**
     * The index of the Machine.
     * @type {Number}
     */
    let Index = 0;

    /**
     * Flag indicating whether the Machine is selected.
     * @type {Boolean}
     */
    let Selected = false;

    /**
     * Flag indicating whether the Machine is enabled.
     * @type {Boolean}
     */
    let Enabled = true;

    Object.defineProperties(this, {
        Control:   {
            enumerable: false,
            get:        () => Control

        },
        ID:        {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Owner:     {
            enumerable: true,
            get:        () => Owner,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "Owner", true);
                Owner = Value;
            }
        },
        Guid:      {
            enumerable: true,
            get:        () => GuidCell.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Guid");
                GuidCell.textContent = Value;
            }
        },
        TimeStamp: {
            enumerable: true,
            get:        () => TimeStamp,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "TimeStamp");
                TimeStampCell.textContent = Value;
            }
        },
        Status:    {
            enumerable: true,
            get:        () => Status,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Status");
                Status = Value;
                StatusCell.textContent = Value === vDesk.Machines.Machine.Running ? vDesk.Locale.Machines.Running : vDesk.Locale.Machines.Suspended;
            }
        },
        Name:      {
            enumerable: true,
            get:        () => NameCell.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Name");
                NameCell.textContent = Value;
            }
        },
        Index:     {
            enumerable: true,
            get:        () => Index,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Index");
                Index = Value;
            }
        },
        Selected:  {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled:   {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Machines.Machine#select
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLTableRowElement}
     */
    const Control = document.createElement("tr");
    Control.className = "Row Machine";
    Control.addEventListener("click", OnClick, false);

    /**
     * The name cell of the Machine.
     * @type {HTMLTableCellElement}
     */
    const NameCell = document.createElement("td");
    NameCell.className = "Cell BorderLight Font Dark";
    NameCell.textContent = Name;
    Control.appendChild(NameCell);

    /**
     * The name cell of the Machine.
     * @type {HTMLTableCellElement}
     */
    const IDCell = document.createElement("td");
    IDCell.className = "Cell BorderLight Font Dark";
    IDCell.textContent = ID;
    Control.appendChild(IDCell);

    /**
     * The name cell of the Machine.
     * @type {HTMLTableCellElement}
     */
    const StatusCell = document.createElement("td");
    StatusCell.className = "Cell BorderLight Font Dark";
    StatusCell.textContent = Status === vDesk.Machines.Machine.Running ? vDesk.Locale.Machines.Running : vDesk.Locale.Machines.Suspended;
    Control.appendChild(StatusCell);

    /**
     * The name cell of the Machine.
     * @type {HTMLTableCellElement}
     */
    const GuidCell = document.createElement("td");
    GuidCell.className = "Cell BorderLight Font Dark";
    GuidCell.textContent = Guid;
    Control.appendChild(GuidCell);

    /**
     * The guid cell of the Machine.
     * @type {HTMLTableCellElement}
     */
    const OwnerCell = document.createElement("td");
    OwnerCell.className = "Cell BorderLight Font Dark";
    OwnerCell.textContent = Owner.Name;
    Control.appendChild(OwnerCell);

    /**
     * The timestamp cell of the Machine.
     * @type {HTMLTableCellElement}
     */
    const TimeStampCell = document.createElement("td");
    TimeStampCell.className = "Cell BorderLight Font Dark";
    TimeStampCell.textContent = new Date(TimeStamp).toLocaleTimeString();
    Control.appendChild(TimeStampCell);
};

/**
 * Factory method that creates a Machine from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Machine.
 * @return {vDesk.Machines.Machine} A Machine filled with the provided data.
 */
vDesk.Machines.Machine.FromDataView = function(DataView) {
    return new vDesk.Machines.Machine(
        DataView?.ID ?? null,
        vDesk.Security.Users.find(User => User.ID === DataView?.Owner?.ID) ?? vDesk.Security.User.FromDataView(DataView?.Owner ?? {}),
        DataView?.Guid ?? "",
        DataView?.TimeStamp ?? null,
        DataView?.Status ?? vDesk.Machines.Machine.Virtual,
        DataView?.Name ?? ""
    );
};

vDesk.Machines.Machine.Virtual = "0";
vDesk.Machines.Machine.Running = "1";
vDesk.Machines.Machine.Suspended = "2";

vDesk.Machines.Machine.Implements(vDesk.Controls.Table.IRow);