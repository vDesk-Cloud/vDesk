"use strict";
/**
 * Fired if a value of the current edited Group of the GroupEditor has been changed.
 * @event vDesk.Security.Group.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Security.Group.Editor} detail.sender The current instance of the GroupEditor.
 * @property {vDesk.Security.Group} detail.group The changed Group of the GroupEditor.
 */
/**
 * Fired if the current a new Group has been created.
 * @event vDesk.Security.Group.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.Security.Group.Editor} detail.sender The current instance of the GroupEditor.
 * @property {vDesk.Security.Group} detail.group The created Group of the GroupEditor.
 */
/**
 * Fired if the current edited Group of the GroupEditor has been update.
 * @event vDesk.Security.Group.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Security.Group.Editor} detail.sender The current instance of the GroupEditor.
 * @property {vDesk.Security.Group} detail.group The updated Group of the GroupEditor.
 */
/**
 * Fired if the current edited Group of the GroupEditor has been deleted.
 * @event vDesk.Security.Group.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Security.Group.Editor} detail.sender The current instance of the GroupEditor.
 * @property {vDesk.Security.Group} detail.group The deleted Group of the GroupEditor.
 */
/**
 * Initializes a new instance of the GroupEditor class.
 * @class Represents an editor for creating, removing or modifying usergroups and specific permissions.
 * The GroupEditor is a plugin for remote configuration.
 * @param {vDesk.Security.Group} Group initializes the GroupEditor with the specified Group.
 * @param {Boolean} [Enabled=false] Flag indicating whether the GroupEditor is enabled.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.Group} Group Gets or sets the Group of the GroupEditor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the GroupEditor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the Group of the GroupEditor has been changed.
 * @memberOf vDesk.Security.Configuration
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.Group.Editor = function Editor(Group, Enabled = false) {
    Ensure.Parameter(Group, vDesk.Security.Group, "Group");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The Permissions of the GroupEditor.
     * @type {Array<vDesk.Security.Group.Permission>}
     */
    let Permissions = [];

    /**
     * The previous permissions of the current edited Group of the GroupEditor.
     * @type {Object<Boolean>}
     */
    let PreviousPermissions = Object.assign({}, Group.Permissions);

    /**
     * The previous name of the current edited Group of the GroupEditor.
     * @type {vDesk.Security.Group.Name|string}
     */
    let PreviousName = Group.Name;

    /**
     * The permission template of the GroupEditor.
     * @type {Object<Boolean>}
     */
    let Template = {};

    /**
     * Flag indicating whether the Group of the GroupEditor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        Group:       {
            enumerable: true,
            get:        () => Group,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.Group, "Group");
                Group = Value;
                PreviousPermissions = Object.assign({}, Value.Permissions);
                NameTextBox.value = PreviousName = Value.Name;
                Permissions.forEach(Permission => Permission.Value = Value.Permissions[Permission.Name]);
                Changed = false;
            }
        },
        Permissions: {
            enumerable: true,
            get:        () => Template,
            set:        Value => {
                Ensure.Property(Value, Type.Object, "Permissions");
                Template = Value;

                //Remove previous Permissions.
                Permissions.forEach(Permission => PermissionsList.removeChild(Permission.Control));
                Permissions = [];

                //Apply new Permissions.
                const Fragment = document.createDocumentFragment();
                for(const Name in Value){
                    const Permission = new vDesk.Security.Group.Permission(
                        Name,
                        vDesk.Locale.Permissions[Name],
                        Group.Permissions?.[Value.Name] ?? false,
                        Enabled
                    );
                    Permissions.push(Permission);
                    Fragment.appendChild(Permission.Control);
                }
                PermissionsList.appendChild(Fragment);
            }
        },
        Changed:     {
            enumerable: true,
            get:        () => Changed
        },
        Enabled:     {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                NameTextBox.disabled = !Value;
                Permissions.forEach(Permission => Permission.Enabled = Value);
            }
        }
    });

    /**
     * Saves any made changes.
     * @fires vDesk.Security.Group.Editor#create
     * @fires vDesk.Security.Group.Editor#update
     */
    this.Save = function() {

        //Check if the group is not virtual.
        if(Group.ID !== null){
            //Populate changed permissions.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "UpdateGroup",
                        Parameters: {
                            ID:          Group.ID,
                            Name:        Group.Name,
                            Permissions: Group.Permissions
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        PreviousName = Group.Name;
                        PreviousPermissions = Object.assign({}, Group.Permissions);
                        Control.removeEventListener("update", OnUpdate, false);
                        Changed = false;
                        new vDesk.Events.BubblingEvent("update", {
                            sender: this,
                            group:  Group
                        }).Dispatch(Control);
                        Control.addEventListener("update", OnUpdate, false);
                    }
                }
            );

        }else{
            //Save new group.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "CreateGroup",
                        Parameters: {
                            Name:        Group.Name,
                            Permissions: Group.Permissions
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Group.ID = Response.Data.ID;
                        PreviousName = Group.Name;
                        PreviousPermissions = Object.assign({}, Group.Permissions);
                        new vDesk.Events.BubblingEvent("create", {
                            sender: this,
                            group:  Group
                        }).Dispatch(Control);
                    }
                }
            );
        }
    };

    /**
     * Deletes the current edited Group of the GroupEditor.
     * @fires vDesk.Security.Group.Editor#delete
     */
    this.Delete = function() {
        if(Group.ID !== null){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "DeleteGroup",
                        Parameters: {ID: Group.ID},
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        new vDesk.Events.BubblingEvent("delete", {
                            sender: this,
                            group:  Group
                        }).Dispatch(Control);
                    }
                }
            );
        }
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Group.Permission#event:update
     * @fires vDesk.Security.Group.Editor#change
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Group.Permissions[Event.detail.sender.Name] = Event.detail.sender.Value;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {
            sender: this,
            group:  Group
        }).Dispatch(Control);
    };

    /**
     * Resets possible made changes.
     */
    this.Reset = function() {
        Changed = false;
        NameTextBox.value = Group.Name = PreviousName;
        Permissions.forEach(
            Permission => Permission.Value = Group.Permissions[Permission.Name] = PreviousPermissions[Permission.Name]);
    };

    /**
     * Eventhandler that listens on the 'change' event.
     * @param {Event} Event
     * @fires vDesk.Security.Group.Editor#change
     */
    const OnChange = Event => {
        Event.stopPropagation();
        if(
            NameTextBox.value.length === 0
            || Event.target === NameTextBox
            && Group.ID === null
            && vDesk.Security.Groups.find(Group => Group.Name.toLowerCase() === NameTextBox.value.toLowerCase()) !== undefined
        ){
            NameTextBox.classList.add("Error");
            return;
        }

        NameTextBox.classList.remove("Error");

        Group.Name = NameTextBox.value;
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {
            sender: this,
            group:  Group
        }).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "GroupEditor";
    Control.addEventListener("update", OnUpdate, false);

    /**
     * The name row of the GroupEditor.
     * @type {HTMLDivElement}
     */
    const Header = document.createElement("div");
    Header.className = "Header";

    /**
     * The name TextBox of the GroupEditor.
     * @type {HTMLInputElement}
     */
    const NameTextBox = document.createElement("input");
    NameTextBox.type = "text";
    NameTextBox.className = "Name TextBox BorderDark Background";
    NameTextBox.value = Group.Name;
    NameTextBox.disabled = !Enabled;
    NameTextBox.addEventListener("change", OnChange, false);

    Header.appendChild(NameTextBox);

    /**
     * The permissions list of the GroupEditor.
     * @type {HTMLUListElement}
     */
    const PermissionsList = document.createElement("ul");
    PermissionsList.className = "Permissions";
    PermissionsList.addEventListener("change", OnChange, false);

    for(const Name in Group.Permissions){
        const Permission = new vDesk.Security.Group.Permission(
            Name,
            vDesk.Locale.Permissions[Name],
            Group.Permissions[Name],
            Enabled
        );
        Permissions.push(Permission);
        PermissionsList.appendChild(Permission.Control);
    }

    Control.appendChild(Header);
    Control.appendChild(PermissionsList);

};