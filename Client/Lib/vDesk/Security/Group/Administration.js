"use strict";
/**
 * Initializes a new instance of the Administration class.
 * @class Represents a plugin for administrating Groups and their permissions.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the Administration-plugin.
 * @memberOf vDesk.Security.Group
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Security
 */
vDesk.Security.Group.Administration = function Administration() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Security.Groups
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.GroupList#event:select
     */
    const OnSelect = Event => {
        if(Event.detail.item.Group.ID !== GroupEditor.Group.ID){

            //Check if the "new group"-entry has been selected.
            if(Event.detail.item.Group.ID === null){
                Reset.disabled = true;
                Delete.disabled = true;
                EditSave.disabled = true;
                GroupEditor.Enabled = true;
            }else{
                //Reset controls.
                EditSave.disabled = false;
                Delete.disabled = Event.detail.item.Group.ID === vDesk.Security.Group.Everyone || !vDesk.Security.User.Current.Permissions.DeleteGroup;
                GroupEditor.Enabled = false;
                EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
                EditSave.textContent = vDesk.Locale.vDesk.Edit;
                Reset.disabled = true;
            }

            //Display the selected Group.
            GroupEditor.Group = Event.detail.item.Group;

        }
    };

    /**
     * Eventhandler that listens on the 'change' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Group.Editor#event:change
     */
    const OnChange = Event => {

        EditSave.disabled = Event.detail.group.Name.length === 0 && !GroupEditor.Changed;
        Reset.disabled = !GroupEditor.Changed;

        if(GroupEditor.Changed){
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Save}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Save;
        }

    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Group.Editor#event:create
     */
    const OnCreate = Event => {
        GroupList.Find(null).Group = Event.detail.group;
        vDesk.Security.Groups.push(Event.detail.group);
        Delete.disabled = !vDesk.Security.User.Current.Permissions.DeleteGroup;
        Reset.disabled = true;

        GroupList.Add(
            new vDesk.Security.GroupList.Item(
                new vDesk.Security.Group(
                    null,
                    vDesk.Locale.Security.NewGroup,
                    GroupEditor.Permissions
                )
            )
        );

    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Group.Editor#event:update
     */
    const OnUpdate = Event => {
        GroupList.Find(Event.detail.group.ID).Group = Event.detail.group;
        vDesk.Security.Groups.find(Group => Group.ID === Event.detail.group.ID).Name = Event.detail.group.Name;
        Reset.disabled = true;
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.Group.Editor#event:delete
     */
    const OnDelete = Event => {
        vDesk.Security.Groups.splice(
            vDesk.Security.Groups.indexOf(vDesk.Security.Groups.find(Group => Group.ID === Event.detail.group.ID)),
            1
        );
        GroupList.Remove(GroupList.Selected);

        GroupList.Selected = GroupList.Items[0];
        GroupEditor.Group = GroupList.Items[0].Group;
        GroupEditor.Enabled = false;
        Delete.disabled = GroupEditor.Group.ID === vDesk.Security.Group.Everyone;
        EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
        EditSave.textContent = vDesk.Locale.vDesk.Edit;
        EditSave.disabled = !vDesk.Security.User.Current.Permissions.UpdateGroup;
        Reset.disabled = true;
    };

    /**
     * Saves possible made changes.
     */
    const OnClickEditSaveButton = () => {

        if(GroupEditor.Enabled){
            if(GroupEditor.Changed){
                GroupEditor.Save();
            }
            GroupEditor.Enabled = false;
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Edit;
        }else{
            //Enable GroupEditor.
            EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Cancel}")`;
            EditSave.textContent = vDesk.Locale.vDesk.Cancel;
            GroupEditor.Enabled = true;
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickResetButton = () => {
        GroupEditor.Reset();
        if(GroupEditor.Enabled){
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
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickDeleteButton = () => {
        if(GroupEditor.Group.ID !== null && confirm(vDesk.Locale.Security.GroupEditorDeleteGroup)){
            GroupEditor.Delete();
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "GroupAdministration";
    Control.addEventListener("select", OnSelect, false);
    Control.addEventListener("change", OnChange, false);
    Control.addEventListener("create", OnCreate, false);
    Control.addEventListener("update", OnUpdate, false);
    Control.addEventListener("delete", OnDelete, false);

    /**
     * The GroupList of the Administration plugin.
     * @type {vDesk.Security.GroupList}
     */
    const GroupList  = new vDesk.Security.GroupList();
    GroupList.Control.addEventListener("select", OnSelect, false);

    /**
     * The GroupEditor of the Administration plugin.
     * @type {vDesk.Security.Group.Editor}
     */
    const GroupEditor = new vDesk.Security.Group.Editor(new vDesk.Security.Group(), false);

    //Fetch Groups.
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Security",
                Command:    "GetGroups",
                Parameters: {View: false},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Response => {
            if(Response.Status){
                Response.Data.map(Group => vDesk.Security.Group.FromDataView(Group))
                    .forEach(Group => GroupList.Add(new vDesk.Security.GroupList.Item(Group)));
                GroupList.Selected = GroupList.Items[0];
                GroupEditor.Permissions = GroupList.Selected.Group.Permissions;
                GroupEditor.Group = GroupList.Selected.Group;
                Delete.disabled = !vDesk.Security.User.Current.Permissions.DeleteGroup || GroupList.Selected.Group.ID === vDesk.Security.Group.Everyone;

                //Check if the User is allowed to create new Groups.
                if(vDesk.Security.User.Current.Permissions.CreateGroup){
                    GroupList.Add(
                        new vDesk.Security.GroupList.Item(
                            new vDesk.Security.Group(
                                null,
                                vDesk.Locale.Security.NewGroup,
                                GroupEditor.Permissions
                            )
                        )
                    );
                }
            }
        }
    );

    /**
     * The edit/save button of the Administration plugin.
     * @type {HTMLButtonElement}
     */
    const EditSave = document.createElement("button");
    EditSave.className = "Button Icon Save";
    EditSave.style.backgroundImage = `url("${vDesk.Visual.Icons.Edit}")`;
    EditSave.textContent = vDesk.Locale.vDesk.Edit;
    EditSave.disabled = !vDesk.Security.User.Current.Permissions.UpdateGroup;
    EditSave.addEventListener("click", OnClickEditSaveButton, false);

    /**
     * The reset button of the Administration plugin.
     * @type {HTMLButtonElement}
     */
    const Reset = document.createElement("button");
    Reset.className = "Button Icon Reset";
    Reset.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    Reset.disabled = true;
    Reset.textContent = vDesk.Locale.vDesk.ResetChanges;
    Reset.addEventListener("click", OnClickResetButton, false);

    /**
     * The delete button of the Administration plugin.
     * @type {HTMLButtonElement}
     */
    const Delete = document.createElement("button");
    Delete.className = "Button Icon Reset";
    Delete.style.backgroundImage = `url("${vDesk.Visual.Icons.Delete}")`;
    Delete.disabled = !vDesk.Security.User.Current.Permissions.DeleteGroup;
    Delete.textContent = vDesk.Locale.vDesk.Delete;
    Delete.addEventListener("click", OnClickDeleteButton, false);

    /**
     * The controls row of the Administration plugin.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Controls.appendChild(EditSave);
    Controls.appendChild(Reset);
    Controls.appendChild(Delete);

    Control.appendChild(GroupList.Control);
    Control.appendChild(GroupEditor.Control);
    Control.appendChild(Controls);

};

vDesk.Configuration.Remote.Plugins.GroupAdministration = vDesk.Security.Group.Administration;