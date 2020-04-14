"use strict";
/**
 * @typedef {Object} User Represents the data of an user.
 * @property {Number} ID Gets the ID of the user.
 * @property {String} Name Gets or sets the name of the user.
 * @property {String} Email Gets or sets the email-address of the user.
 * @property {Boolean} Active Gets or sets a value indicating whether the account of the user is active.
 * @property {Number} FailedLoginCount Gets or sets the amount of failed login attempts of the user.
 * @property {Array<Number>} Memberships Gets or sets the IDs of the groups the user is a member of.
 */
/**
 * Initializes a new instance of the UserAccountControl class.
 * @class Represents a plugin for administrating user-accounts and their group-based permissions.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the useraccountvontrol-plugin.
 * @memberOf vDesk.Security.User
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.User.Administration = function Administration() {

    /**
     * The users of the UserAccountControl plugin.
     * @type{ Array<User>}
     */
    let Users = [];

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale["Security"]["Users"]
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Security.UserList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        UserEditor.User = Event.detail.item.User;
        MembershipEditor.User = Event.detail.item.User;
        Delete.disabled = Event.detail.item.User.ID === vDesk.Security.User.System;
        if(UserEditor.Enabled) {
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel || vDesk.Visual.Icons.Unknown}")`;
            EditSave.textContent = vDesk.Locale["vDesk"]["Cancel"];
            Reset.disabled = true;
        }
    };

    /**
     * Saves possible made changes.
     */
    const OnClickEditSave = () => {
        if(UserEditor.Enabled && MembershipEditor.Enabled) {
            if(UserEditor.Changed) {
                UserEditor.Save();
            }
            UserEditor.Enabled = false;
            if(MembershipEditor.Changed) {
                MembershipEditor.Save();
            }
            MembershipEditor.Enabled = false;
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
            EditSave.textContent = vDesk.Locale["vDesk"]["Edit"];
        } else {
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel || vDesk.Visual.Icons.Unknown}")`;
            EditSave.textContent = vDesk.Locale["vDesk"]["Cancel"];
            UserEditor.Enabled = true;
            MembershipEditor.Enabled = true;
        }

    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickReset = () => {
        UserEditor.Reset();
        MembershipEditor.Reset();
        if(UserEditor.Enabled || MembershipEditor.Enabled) {
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel || vDesk.Visual.Icons.Unknown}")`;
            EditSave.textContent = vDesk.Locale["vDesk"]["Cancel"];
        } else {
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
            EditSave.textContent = vDesk.Locale["vDesk"]["Edit"];
        }
        EditSave.disabled = false;
        Reset.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'change' event.
     * @listens vDesk.Security.User.Editor#event:change
     * @listens vDesk.Security.User.MembershipEditor#event:change
     */
    const OnChange = () => {
        EditSave.disabled = false;
        Reset.disabled = !UserEditor.Changed && !MembershipEditor.Changed;

        if(UserEditor.Changed || MembershipEditor.Changed) {
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Save || vDesk.Visual.Icons.Unknown}")`;
            EditSave.textContent = vDesk.Locale["vDesk"]["Save"];
        }
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Security.User.Editor#event:create
     * @param {CustomEvent} Event
     */
    const OnCreate = Event => {
        if(MembershipEditor.Changed) {
            MembershipEditor.User.ID = Event.detail.user.ID;
            MembershipEditor.Save();
        }
        UserList.Find(Event.detail.user.ID).User = Event.detail.user;
        vDesk.Security.Users.push(Event.detail.user);
        Delete.disabled = !vDesk.User.Permissions["DeleteUser"];
        Reset.disabled = true;

        UserList.Add(
            new vDesk.Security.UserList.Item(
                new vDesk.Security.User(
                    null,
                    vDesk.Locale["Security"]["NewUser"]
                )
            )
        );
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Security.User.Editor#event:delete
     * @param {CustomEvent} Event
     */
    const OnDelete = Event => {
        Users.splice(Users.indexOf(Users.find(User => User.ID === Event.detail.user.ID)), 1);
        UserList.Remove(UserList.Find(Event.detail.user.ID));
        vDesk.Security.Users.splice(vDesk.Security.Users.indexOf(vDesk.Security.Users.find(User => User.ID === Event.detail.user.ID)), 1);
        UserList.Selected = UserList.Items[0];
        MembershipEditor.User = UserEditor.User = UserList.Selected.User;

        //Check if the User has deleted himself.
        if(Event.detail.user.ID === vDesk.User.ID) {
            vDesk.Stop();
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "UserAdministration";
    Control.addEventListener("change", OnChange, false);

    /**
     * The UserList of the UserAccountControl plugin.
     * @type {vDesk.Security.UserList}
     */
    const UserList = new vDesk.Security.UserList.FromUsers(false);
    UserList.Control.addEventListener("select", OnSelect, false);
    UserList.Selected = UserList.Items[0];
    if(vDesk.User.Permissions["CreateUser"]) {
        UserList.Add(
            new vDesk.Security.UserList.Item(
                new vDesk.Security.User(
                    null,
                    vDesk.Locale["Security"]["NewUser"]
                )
            )
        );
    }
    Control.appendChild(UserList.Control);

    /**
     * The UserEditor of the UserAccountControl plugin.
     * @type {vDesk.Security.User.Editor}
     */
    const UserEditor = new vDesk.Security.User.Editor(UserList.Selected.User);
    UserEditor.Control.addEventListener("create", OnCreate, false);
    UserEditor.Control.addEventListener("delete", OnDelete, false);
    Control.appendChild(UserEditor.Control);

    /**
     * The MembershipEditor of the UserAccountControl plugin.
     * @type {vDesk.Security.User.MembershipEditor}
     */
    const MembershipEditor = new vDesk.Security.User.MembershipEditor(UserList.Selected.User);
    Control.appendChild(MembershipEditor.Control);

    /**
     * The edit/save button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const EditSave = document.createElement("button");
    EditSave.className = "Button Icon Save";
    EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit || vDesk.Visual.Icons.Unknown}")`;
    EditSave.textContent = vDesk.Locale["vDesk"]["Edit"];
    EditSave.disabled = !vDesk.User.Permissions["UpdateUser"];
    EditSave.addEventListener("click", OnClickEditSave, false);

    /**
     * The reset button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const Reset = document.createElement("button");
    Reset.className = "Button Icon Reset";
    Reset.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    Reset.disabled = true;
    Reset.textContent = vDesk.Locale["vDesk"]["ResetChanges"];
    Reset.addEventListener("click", OnClickReset, false);

    /**
     * The delete button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const Delete = document.createElement("button");
    Delete.className = "Button Icon Reset";
    Delete.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete || vDesk.Visual.Icons.Unknown}")`;
    Delete.disabled = !vDesk.User.Permissions["DeleteUser"] || UserList.Selected.User.ID === vDesk.Security.User.System;
    Delete.textContent = vDesk.Locale["vDesk"]["Delete"];
    Delete.addEventListener("click", () => UserEditor.Delete(), false);

    /**
     * The controls of the Administration.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Controls.appendChild(EditSave);
    Controls.appendChild(Reset);
    Controls.appendChild(Delete);
    Control.appendChild(Controls);
};

vDesk.Configuration.Remote.Plugins.UserAdministration = vDesk.Security.User.Administration;