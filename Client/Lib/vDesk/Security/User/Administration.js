"use strict";
/**
 * Initializes a new instance of the Administration class.
 * @class Represents a plugin for administrating Users and their Group-based permissions.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the Administration-plugin.
 * @memberOf vDesk.Security.User
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.User.Administration = function Administration() {

    /**
     * The users of the UserAccountControl plugin.
     * @type{Array<User>}
     */
    let Users = [];

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Security.Users
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Security.UserList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        if(Event.detail.item.User.ID !== UserEditor.User.ID){

            //Check if the "new user"-entry has been selected.
            if(Event.detail.item.User.ID === null){
                Reset.disabled = true;
                Delete.disabled = true;
                EditSave.disabled = true;
                UserEditor.Enabled = true;
                MembershipEditor.Enabled = true;
            }else{
                //Reset controls.
                EditSave.disabled = false;
                Delete.disabled = Event.detail.item.User.ID === vDesk.Security.User.System || !vDesk.Security.User.Current.Permissions.DeleteUser;
                UserEditor.Enabled = false;
                MembershipEditor.Enabled = false;
                EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
                EditSave.textContent = vDesk.Locale.vDesk.Edit;
                Reset.disabled = true;
            }

            //Display the selected User.
            UserEditor.User = Event.detail.item.User;
            MembershipEditor.User = Event.detail.item.User;

        }
    };

    /**
     * Saves possible made changes.
     */
    const OnClickEditSave = () => {
        if(UserEditor.Enabled && MembershipEditor.Enabled){
            if(UserEditor.Changed){
                UserEditor.Save();
            }
            UserEditor.Enabled = false;
            if(MembershipEditor.Changed){
                MembershipEditor.Save();
            }
            MembershipEditor.Enabled = false;
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Edit;
        }else{
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Cancel;
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
        if(UserEditor.Enabled || MembershipEditor.Enabled){
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Cancel;
        }else{
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Edit;
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

        if(UserEditor.Changed || MembershipEditor.Changed){
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Save}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Save;
        }
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Security.User.Editor#event:create
     * @param {CustomEvent} Event
     */
    const OnCreate = Event => {
        if(MembershipEditor.Changed){
            MembershipEditor.User.ID = Event.detail.user.ID;
            MembershipEditor.Save();
        }
        UserList.Find(null).User = Event.detail.user;
        vDesk.Security.Users.push(Event.detail.user);
        Delete.disabled = !vDesk.Security.User.Current.Permissions.DeleteUser;
        Reset.disabled = true;

        UserList.Add(
            new vDesk.Security.UserList.Item(
                new vDesk.Security.User(
                    null,
                    vDesk.Locale.Security.NewUser
                )
            )
        );
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.User.Editor#event:update
     */
    const OnUpdate = Event => {
        UserList.Find(Event.detail.user.ID).User = Event.detail.user;
        vDesk.Security.Users.find(User => User.ID === Event.detail.user.ID).Name = Event.detail.user.Name;
        Reset.disabled = true;
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
        if(Event.detail.user.ID === vDesk.Security.User.Current.ID){
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
    Control.addEventListener("create", OnCreate, false);
    Control.addEventListener("update", OnUpdate, false);
    Control.addEventListener("delete", OnDelete, false);

    /**
     * The UserList of the UserAccountControl plugin.
     * @type {vDesk.Security.UserList}
     */
    const UserList = new vDesk.Security.UserList();
    UserList.Control.addEventListener("select", OnSelect, false);
    Control.appendChild(UserList.Control);

    //Fetch Users.
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Security",
                Command:    "GetUsers",
                Parameters: {View: false},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Response => {
            if(Response.Status){
                Response.Data.map(User => vDesk.Security.User.FromDataView(User))
                    .forEach(User => UserList.Add(new vDesk.Security.UserList.Item(User)));
                UserList.Selected = UserList.Items[0];
                UserEditor.User = UserList.Selected.User;
                MembershipEditor.User = UserList.Selected.User;
                Delete.disabled = !vDesk.Security.User.Current.Permissions.DeleteUser || UserList.Selected.User.ID === vDesk.Security.User.System;

                //Check if the User is allowed to create new Users.
                if(vDesk.Security.User.Current.Permissions.CreateUser){
                    UserList.Add(
                        new vDesk.Security.UserList.Item(
                            new vDesk.Security.User(
                                null,
                                vDesk.Locale.Security.NewUser
                            )
                        )
                    );
                }
            }
        }
    );

    /**
     * The UserEditor of the Administration plugin.
     * @type {vDesk.Security.User.Editor}
     */
    const UserEditor = new vDesk.Security.User.Editor(new vDesk.Security.User());
    Control.appendChild(UserEditor.Control);

    /**
     * The MembershipEditor of the Administration plugin.
     * @type {vDesk.Security.User.MembershipEditor}
     */
    const MembershipEditor = new vDesk.Security.User.MembershipEditor(new vDesk.Security.User());
    Control.appendChild(MembershipEditor.Control);

    /**
     * The edit/save button of the Administration plugin.
     * @type {HTMLButtonElement}
     */
    const EditSave = document.createElement("button");
    EditSave.className = "Button Icon Save";
    EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
    EditSave.textContent = vDesk.Locale.vDesk.Edit;
    EditSave.disabled = !vDesk.Security.User.Current.Permissions.UpdateUser;
    EditSave.addEventListener("click", OnClickEditSave, false);

    /**
     * The reset button of the Administration plugin.
     * @type {HTMLButtonElement}
     */
    const Reset = document.createElement("button");
    Reset.className = "Button Icon Reset";
    Reset.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    Reset.disabled = true;
    Reset.textContent = vDesk.Locale.vDesk.ResetChanges;
    Reset.addEventListener("click", OnClickReset, false);

    /**
     * The delete button of the Administration plugin.
     * @type {HTMLButtonElement}
     */
    const Delete = document.createElement("button");
    Delete.className = "Button Icon Reset";
    Delete.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete}")`;
    Delete.disabled = !vDesk.Security.User.Current.Permissions.DeleteUser;
    Delete.textContent = vDesk.Locale.vDesk.Delete;
    Delete.addEventListener("click", () => UserEditor.Delete(), false);

    /**
     * The controls of the Administration plugin.
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