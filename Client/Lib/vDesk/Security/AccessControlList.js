"use strict";
/**
 * Fired if an {@link vDesk.Security.UserGroupList.Item} has been dropped on the AccessControlList.
 * @event vDesk.Security.AccessControlList#drop
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'drop' event.
 * @property {vDesk.Security.AccessControlList} detail.sender The current instance of the AccessControlList.
 * @property {vDesk.Security.UserGroupList.Item} detail.item The Item that has been dropped on the AccessControlList.
 */
/**
 * Initializes a new instance of the Entry class.
 * @class Represents a AccessControlList.
 * @param {?Number} [ID=null] The ID of the AccessControlList.
 * @param {Array<vDesk.Security.AccessControlList.Entry>} [Entries=[]] Initializes the AccessControlList with the specified set of Entries.
 * @param {Boolean} [Read=true] Flag indicating whether the current User has read permissions on the AccessControlList.
 * @param {Boolean} [Write=true] Flag indicating whether the current User has write permissions on the AccessControlList.
 * @param {Boolean} [Delete=true] Flag indicating whether the current User has delete permissions on the AccessControlList.
 * @param {Boolean} [Enabled=true] Flag indicating whether the AccessControlList is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Number} ID Gets or sets the ID of the AccessControlList.
 * @property {Array<vDesk.Security.AccessControlList.Entry>} Entries Gets or sets the Entries of the AccessControlList.
 * @property {Boolean} Read Gets or sets a value indicating whether the current User has read permissions on the AccessControlList.
 * @property {Boolean} Write Gets or sets a value indicating whether the current User has write permissions on the AccessControlList.
 * @property {Boolean} Delete Gets or sets a value indicating whether the current User has delete permissions on the AccessControlList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the AccessControlList is enabled.
 * @memberOf vDesk.Security
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.AccessControlList = function AccessControlList(
    ID      = null,
    Entries = [
        vDesk.Security.AccessControlList.Entry.FromUser(),
        vDesk.Security.AccessControlList.Entry.FromGroup(null, false, false, false),
        vDesk.Security.AccessControlList.Entry.FromUser(vDesk.User)
    ],
    Read    = true,
    Write   = true,
    Delete  = true,
    Enabled = true
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Entries, Array, "Entries");
    Ensure.Parameter(Read, Type.Boolean, "Read");
    Ensure.Parameter(Delete, Type.Boolean, "Delete");
    Ensure.Parameter(Delete, Type.Boolean, "Delete");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The amount of drag operations captured on the AccessControlList.
     * @type {Number}
     */
    let DragCount = 0;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        ID:      {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Entries: {
            enumerable: true,
            get:        () => Entries,
            set:        Value => {
                Ensure.Property(Value, Array, "Entries");

                //Remove Entries.
                Entries.forEach(Entry => Control.removeChild(Entry.Control));

                //Clear array
                Entries = [];

                //Append new entries.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Entry => {
                    Entries.push(Entry);
                    Fragment.appendChild(Entry.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Read:    {
            enumerable: true,
            get:        () => Read,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Read");
                Read = Value;
            }
        },
        Write:   {
            enumerable: true,
            get:        () => Write,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Write");
                Write = Value;
            }
        },
        Delete:  {
            enumerable: true,
            get:        () => Delete,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Delete");
                Delete = Value;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Entries");
                Enabled = Value;
                Entries.filter(Entry => Entry.ID > vDesk.Security.User.System)
                    .forEach(Entry => Entry.Enabled = Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'drop' event.
     * @fires vDesk.Security.AccessControlList#drop
     * @param {DragEvent} Event
     */
    const OnDrop = Event => {
        Event.preventDefault();
        Event.stopPropagation();
        Control.removeEventListener("drop", OnDrop, false);
        new vDesk.Events.BubblingEvent("drop", {
            sender: this,
            item:   Event.dataTransfer.getReference()
        }).Dispatch(Control);
        DragCount = 0;
        Control.classList.remove("Hover");
        Control.addEventListener("drop", OnDrop, false);
    };

    /**
     * Listens on the dragenter event and provides a hovereffect.
     * @param {DragEvent} Event
     */
    const OnDragEnter = Event => {
        Event.preventDefault();
        Control.classList.add("Hover");
        DragCount++;
    };

    /**
     * Listens on the dragleave event and removes the hovereffect.
     * @param {DragEvent} Event
     */
    const OnDragLeave = Event => {
        Event.preventDefault();
        DragCount--;
        if(DragCount === 0) {
            Control.classList.remove("Hover");
        }
    };

    /**
     * Eventhandler that listens on the dragover event and enables drop.
     * @param {DragEvent} Event
     */
    const OnDragOver = Event => Event.preventDefault();

    /**
     * Adds an Entry to the accesscontrollist.
     * @param {vDesk.Security.AccessControlList.Entry} Entry The Entry to add.
     */
    this.Add = function(Entry) {
        Ensure.Parameter(Entry, vDesk.Security.AccessControlList.Entry, "Entry");
        //Check if the entry doesn't already exist.
        if(Entry.User.ID !== null && this.FindByUser(Entry.User) === null) {
            Entries.push(Entry);
            Control.appendChild(Entry.Control);
        } else if(Entry.Group.ID !== null && this.FindByGroup(Entry.Group) === null) {
            Entries.push(Entry);
            Control.appendChild(Entry.Control);
        }
    };

    /**
     * Returns the Entry of the AccessControlList which matches the given ID.
     * @param {Number} ID The ID of the Entry to search.
     * @return {vDesk.Security.AccessControlList.Entry|null} The found Entry; otherwise, null.
     */
    this.Find = function(ID) {
        return Entries.find(Entry => Entry.ID === ID) ?? null;
    };

    /**
     * Returns the Entry of the AccessControlList which matches the specified User.
     * @param {vDesk.Security.User} User The User of the Item to search.
     * @return {vDesk.Security.UserGroupList.Item|null} The found Entry; otherwise, null.
     */
    this.FindByUser = function(User) {
        Ensure.Parameter(User, vDesk.Security.User, "User");
        return Entries.find(Entry => Entry.User.ID === User.ID) ?? null;
    };

    /**
     * Returns the Entry of the AccessControlList which matches the specified Group.
     * @param {vDesk.Security.Group} Group The Group of the Item to search.
     * @return {vDesk.Security.UserGroupList.Item|null} The found Entry; otherwise, null.
     */
    this.FindByGroup = function(Group) {
        Ensure.Parameter(Group, vDesk.Security.Group, "Group");
        return Entries.find(Entry => Entry.Group.ID === Group.ID) ?? null;
    };

    /**
     * Removes all Entries from the AccessControlList.
     */
    this.Clear = function() {
        Entries.forEach(Entry => Control.removeChild(Entry.Control));
        Entries = [];
    };

    /**
     * Removes an Entry from the AccessControlList.
     * @param {vDesk.Security.AccessControlList.Entry} Entry The Entry to remove.
     */
    this.Remove = function(Entry) {
        Ensure.Parameter(Entry, vDesk.Security.AccessControlList.Entry, "Entry");
        const Index = Entries.indexOf(Entry);
        if(~Index) {
            Control.removeChild(Entry.Control);
            Entries.splice(Index, 1);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "AccessControlList BorderDark";
    Control.addEventListener("drop", OnDrop, false);
    Control.addEventListener("dragenter", OnDragEnter, false);
    Control.addEventListener("dragleave", OnDragLeave, false);
    Control.addEventListener("dragover", OnDragOver, false);

    /**
     * The header of the AccessControlList.
     * @type {HTMLLIElement}
     */
    const Header = document.createElement("li");
    Header.className = "Header Foreground Font Light BorderDark";
    Control.appendChild(Header);

    /**
     * The name span of the AccessControlList.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.textContent = vDesk.Locale.vDesk.Name;
    Name.className = "Name";
    Header.appendChild(Name);

    /**
     * The read span of the AccessControlList.
     * @type {HTMLSpanElement}
     */
    const PermissionRead = document.createElement("span");
    PermissionRead.textContent = "R";
    PermissionRead.className = "Permission Read";
    Header.appendChild(PermissionRead);

    /**
     * The write span of the AccessControlList.
     * @type {HTMLSpanElement}
     */
    const PermissionWrite = document.createElement("span");
    PermissionWrite.textContent = "W";
    PermissionWrite.className = "Permission Write";
    Header.appendChild(PermissionWrite);

    /**
     * The delete span of the AccessControlList.
     * @type {HTMLSpanElement}
     */
    const PermissionDelete = document.createElement("span");
    PermissionDelete.textContent = "D";
    PermissionDelete.className = "Permission Delete";
    Header.appendChild(PermissionDelete);

    Entries.forEach(Entry => Control.appendChild(Entry.Control));
};

/**
 * Factory method that creates an AccessControlList from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the AccessControlList.
 * @return {vDesk.Security.AccessControlList} An AccessControlList filled with the provided data.
 */
vDesk.Security.AccessControlList.FromDataView = function(DataView) {
    return new vDesk.Security.AccessControlList(
        DataView?.ID ?? null,
        DataView?.Entries?.map(Entry => vDesk.Security.AccessControlList.Entry.FromDataView(Entry)) ?? [],
        DataView?.Read ?? true,
        DataView?.Write ?? true,
        DataView?.Delete ?? true
    );
};

/**
 * Factory method that loads an AccessControlList from the server by a specified ID.
 * @param {?Number} [ID=null] The ID of the AccessControlList to load.
 * @return {vDesk.Security.AccessControlList} An AccessControlList loaded from the specified ID; otherwise, a default AccessControlList.
 */
vDesk.Security.AccessControlList.Load = function(ID = null) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    if(ID !== null) {
        const Response = vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "GetAccessControlList",
                    Parameters: {ID: ID},
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        if(Response.Status) {
            return vDesk.Security.AccessControlList.FromDataView(Response.Data);
        }
    }
    return new vDesk.Security.AccessControlList(
        null,
        [
            vDesk.Security.AccessControlList.Entry.FromUser(),
            vDesk.Security.AccessControlList.Entry.FromGroup(null, false, false, false),
            vDesk.Security.AccessControlList.Entry.FromUser(vDesk.User)
        ]
    );

};

/**
 * Fills the AccessControlList with the data from the server.
 * @param {Function} [Callback=null] The callback to execute when the data from the server has been retrieved.
 */
vDesk.Security.AccessControlList.prototype.Fill = function(Callback = null) {
    Ensure.Parameter(Callback, Type.Function, "Callback", true);
    if(this.ID !== null) {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "GetAccessControlList",
                    Parameters: {ID: this.ID},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    this.Entries = Response.Data.Entries.map(Entry => vDesk.Security.AccessControlList.Entry.FromDataView(Entry));
                }
                if(Callback !== null) {
                    Callback(this);
                }
            }
        );
    } else {
        this.Entries = [
            vDesk.Security.AccessControlList.Entry.FromUser(),
            vDesk.Security.AccessControlList.Entry.FromGroup(null, false, false, false),
            vDesk.Security.AccessControlList.Entry.FromUser(vDesk.User)
        ];
        if(Callback !== null) {
            Callback(this);
        }
    }
    return this;
};