"use strict";
/**
 * Fired if the Entry has been selected.
 * @event vDesk.Security.AccessControlList.Entry#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Security.AccessControlList.Entry} detail.sender The current instance of the Entry.
 */
/**
 * Fired if the Entry has been updated.
 * @event vDesk.Security.AccessControlList.Entry#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Security.AccessControlList.Entry} detail.sender The current instance of the Entry.
 */
/**
 * Fired if the Entry has been deleted.
 * @event vDesk.Security.AccessControlList.Entry#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Security.AccessControlList.Entry} detail.sender The current instance of the Entry.
 */
/**
 * Initializes a new instance of the Entry class.
 * @class Represents an Entry of an AccessControlList.
 * @param {Number} [ID=null] Initializes the Entry with the specified ID.
 * @param {vDesk.Security.Group} [Group] Initializes the Entry with the specified Group.
 * @param {vDesk.Security.User} [User] Initializes the Entry with the specified User.
 * @param {Boolean} [Read=false] Initializes the Entry with the specified Read permission.
 * @param {Boolean} [Write=false] Initializes the Entry with the specified Write permission.
 * @param {Boolean} [Delete=false] Initializes the Entry with the specified Delete permission.
 * @param {Boolean} [Enabled=false] Initializes the Entry with the specified Delete permission.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {?Number} ID Gets or sets the ID of the Entry.
 * @property {vDesk.Security.Group} Group Gets or sets the Group the Entry belongs to.
 * @property {vDesk.Security.User} User Gets or sets the User the Entry belongs to.
 * @property {String} Name Gets or sets the name of the user or group the Entry belongs to.
 * @property {Boolean} Read Gets or sets a value indicating whether the belonging user or group has reading permissions.
 * @property {Boolean} Write Gets or sets a value indicating whether the belonging user or group has writing permissions.
 * @property {Boolean} Delete Gets or sets a value indicating whether the belonging user or group has deleting permissions.
 * @property {Boolean} Required Gets or sets a value indicating whether the Entry is required.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Entry is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Entry is selected.
 * @fires select Fired if the user clicked on the Entry.
 * @memberOf vDesk.Security.AccessControlList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.AccessControlList.Entry = function Entry(
    ID      = null,
    Group   = new vDesk.Security.Group(),
    User    = new vDesk.Security.User(),
    Read    = false,
    Write   = false,
    Delete  = false,
    Enabled = true
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Group, vDesk.Security.Group, "Group");
    Ensure.Parameter(User, vDesk.Security.User, "User");
    Ensure.Parameter(Read, Type.Boolean, "Read");
    Ensure.Parameter(Write, Type.Boolean, "Write");
    Ensure.Parameter(Delete, Type.Boolean, "Delete");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Entry is selected.
     * @type {Boolean}
     * @ignore
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control:  {
            get: () => Control
        },
        ID:       {
            get: () => ID,
            set: Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        User:     {
            get: () => User,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "User");
                User = Value;
                Group = new vDesk.Security.Group();
                Icon.src = vDesk.Visual.Icons.Security.User;
                Name.textContent = Value.Name || Group.Name;
            }
        },
        Group:    {
            get: () => Group,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.Group, "Group");
                Group = Value;
                User = new vDesk.Security.User();
                Icon.src = vDesk.Visual.Icons.Security.Group;
                Name.textContent = Value.Name || User.Name;
            }
        },
        Name:     {
            get: () => Name.textContent
        },
        Read:     {
            get: () => ReadCheckBox.checked,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Read");
                ReadCheckBox.checked = Value;
            }
        },
        Write:    {
            get: () => WriteCheckBox.checked,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Write");
                WriteCheckBox.checked = Value;
            }
        },
        Delete:   {
            get: () => DeleteCheckBox.checked,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Delete");
                DeleteCheckBox.checked = Value;
            }
        },
        Enabled:  {
            get: () => Enabled,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = User.ID === vDesk.Security.User.System ? false : Value;
                ReadCheckBox.disabled = !Value;
                WriteCheckBox.disabled = !Value;
                DeleteCheckBox.disabled = !Value;
                DeleteButton.disabled = this.Required ? true : !Value;
            }
        },
        Required: {
            get: () => User.ID === vDesk.Security.User.System || Group.ID === vDesk.Security.Group.Everyone
        },
        Selected: {
            get: () => Selected,
            set: Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.AccessControlList.Entry#select
     */
    const OnClick = () => {
        if(!this.Required) {
            new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.AccessControlList.Entry#update
     */
    const OnClickCheckBox = () => new vDesk.Events.BubblingEvent("update", {sender: this}).Dispatch(Control);

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.AccessControlList.Entry#delete
     */
    const OnClickDeleteButton = () => new vDesk.Events.BubblingEvent("delete", {sender: this}).Dispatch(Control);

    Enabled = User.ID === vDesk.Security.User.System ? false : Enabled;

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Entry BorderDark";
    Control.addEventListener("click", OnClick, false);

    /**
     * The icon of the Entry.
     * @type {HTMLImageElement}
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.src = User.ID !== null
               ? vDesk.Visual.Icons.Security.User
               : Group.ID !== null
                 ? vDesk.Visual.Icons.Security.Group
                 : vDesk.Visual.Icons.Unknown;

    /**
     * The name span of the Entry.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.className = "Name Font Dark";
    Name.textContent = Group.Name || User.Name;

    /**
     * The checkbox of the Entry indicating 'read'-permissions.
     * @type {HTMLInputElement}
     */
    const ReadCheckBox = document.createElement("input");
    ReadCheckBox.className = "CheckBox BorderDark Background Font Dark";
    ReadCheckBox.type = "checkbox";
    ReadCheckBox.title = vDesk.Locale.Security.Read;
    ReadCheckBox.disabled = !Enabled;
    ReadCheckBox.addEventListener("click", OnClickCheckBox, false);

    /**
     * The checkbox of the Entry indicating 'write'-permissions.
     * @type {HTMLInputElement}
     */
    const WriteCheckBox = document.createElement("input");
    WriteCheckBox.className = "CheckBox BorderDark";
    WriteCheckBox.type = "checkbox";
    WriteCheckBox.title = vDesk.Locale.Security.Write;
    WriteCheckBox.disabled = !Enabled;
    WriteCheckBox.addEventListener("click", OnClickCheckBox, false);

    /**
     * The checkbox of the Entry indicating 'delete'-permissions.
     * @type {HTMLInputElement}
     */
    const DeleteCheckBox = document.createElement("input");
    DeleteCheckBox.className = "CheckBox BorderDark";
    DeleteCheckBox.type = "checkbox";
    DeleteCheckBox.title = vDesk.Locale.vDesk.Delete;
    DeleteCheckBox.disabled = !Enabled;
    DeleteCheckBox.addEventListener("click", OnClickCheckBox, false);

    /**
     * The delete button of the Entry.
     * @type {HTMLButtonElement}
     */
    const DeleteButton = document.createElement("button");
    DeleteButton.className = "Button Font Light";
    DeleteButton.textContent = "×";
    DeleteButton.title = vDesk.Locale.Security.DeleteEntry;
    DeleteButton.disabled = this.Required ? true : !Enabled;
    DeleteButton.addEventListener("click", OnClickDeleteButton, false);

    //Set checkbox state.
    ReadCheckBox.checked = Read;
    WriteCheckBox.checked = Write;
    DeleteCheckBox.checked = Delete;

    Control.appendChild(Icon);
    Control.appendChild(Name);
    Control.appendChild(ReadCheckBox);
    Control.appendChild(WriteCheckBox);
    Control.appendChild(DeleteCheckBox);
    Control.appendChild(DeleteButton);
};

/**
 * Factory method that creates a new instance of the Entry class representing the specified permissions of the specified User.
 *
 * @param {vDesk.Security.User|null}     [User=null]   The User of the Entry.
 * @param {Boolean}                      [Read=true]   Flag indicating whether the specified User has read permissions.
 * @param {Boolean}                      [Write=true]  Flag indicating whether the specified User has write permissions.
 * @param {Boolean}                      [Delete=true] Flag indicating whether the specified User has delete permissions.
 *
 * @return {vDesk.Security.AccessControlList.Entry} The Entry representing the specified permissions of the specified User.
 */
vDesk.Security.AccessControlList.Entry.FromUser = function(User = null, Read = true, Write = true, Delete = true) {
    return new vDesk.Security.AccessControlList.Entry(
        null,
        new vDesk.Security.Group(),
        User
        ?? vDesk.Security.Users.find(ExistingUser => ExistingUser.ID === vDesk.Security.User.System)
        ?? new vDesk.Security.User(vDesk.Security.User.System),
        Read,
        Write,
        Delete
    );
};

/**
 * Factory method that creates a new instance of the Entry class representing the specified permissions of the specified Group.
 *
 * @param {vDesk.Security.Group|null}    [Group=null]  The Group of the Entry.
 * @param {Boolean}                      [Read=true]   Flag indicating whether the specified Group has read permissions.
 * @param {Boolean}                      [Write=true]  Flag indicating whether the specified Group has write permissions.
 * @param {Boolean}                      [Delete=true] Flag indicating whether the specified Group has delete permissions.
 *
 * @return {vDesk.Security.AccessControlList.Entry} The Entry representing the specified permissions of the specified Group.
 */

vDesk.Security.AccessControlList.Entry.FromGroup = function(Group = null, Read = true, Write = true, Delete = true) {
    return new vDesk.Security.AccessControlList.Entry(
        null,
        Group
        ?? vDesk.Security.Groups.find(ExistingGroup => ExistingGroup.ID === vDesk.Security.Group.Everyone)
        ?? new vDesk.Security.Group(vDesk.Security.Group.Everyone),
        new vDesk.Security.User(),
        Read,
        Write,
        Delete
    );
};

/**
 * Factory method that creates an Entry from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Entry.
 * @return {vDesk.Security.AccessControlList.Entry} An Entry filled with the provided data.
 */
vDesk.Security.AccessControlList.Entry.FromDataView = function(DataView) {
    return new vDesk.Security.AccessControlList.Entry(
        DataView?.ID ?? null,
        vDesk.Security.Groups.find(Group => Group.ID === DataView?.Group?.ID) ?? new vDesk.Security.Group(),
        vDesk.Security.Users.find(User => User.ID === DataView?.User?.ID) ?? new vDesk.Security.User(),
        DataView?.Read ?? false,
        DataView?.Write ?? false,
        DataView?.Delete ?? false
    );
};