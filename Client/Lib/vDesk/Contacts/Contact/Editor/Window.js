"use strict";
/**
 * Initializes a new instance of the Window class.
 * @class Represents an Window for modifying or creating Contacts.
 * @param {vDesk.Contacts.Contact} Contact Initializes the Window with the specified Contact to edit.
 * @extends vDesk.Controls.Window
 * @memberOf vDesk.Contacts.Contact.Editor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
vDesk.Contacts.Contact.Editor.Window = function Window(Contact) {
    Ensure.Property(Contact, vDesk.Contacts.Contact, "Contact");

    this.Extends(vDesk.Controls.Window);

    /**
     * Eventhandler that listens on the 'change' event.
     * @listens vDesk.Contacts.Contact.Editor#event:change
     * @listens vDesk.Security.AccessControlList.Editor#event:change
     */
    const OnChange = () => {
        ResetItem.Enabled = true;
        SaveItem.Enabled = true;
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Contacts.Contact.Editor#event:create
     */
    const OnCreate = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        DeleteItem.Enabled = true;
        this.Title = vDesk.Locale.Contacts.EditContact;
        ContactEditor.Contact.AccessControlList.Fill(AccessControlList => {
            AccessControlListEditor.Merge(AccessControlList);
            AccessControlListEditor.Save();
        });
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Contacts.Contact.Editor#event:update
     * @listens vDesk.Security.AccessControlList.Editor#event:update
     */
    const OnUpdate = Event => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        if(Event.detail.sender === AccessControlListEditor){
            Event.stopPropagation();
        }
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Contacts.Contact.Editor#event:delete
     */
    const OnDelete = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        DeleteItem.Enabled = false;
        this.Title = vDesk.Locale.Contacts.NewContact;
        ContactEditor.Contact = new vDesk.Contacts.Contact();
        AccessControlListEditor.AccessControlList = new vDesk.Security.AccessControlList();
    };

    /**
     * The save Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const SaveItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Save,
        vDesk.Visual.Icons.Save,
        false,
        () => {
            if(ContactEditor.Changed){
                ContactEditor.Save();
            }
            if(AccessControlListEditor.Changed){
                AccessControlListEditor.Save();
            }
        }
    );

    /**
     * The reset Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ResetItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.ResetChanges,
        vDesk.Visual.Icons.Refresh,
        false,
        () => {
            ContactEditor.Reset();
            AccessControlListEditor.Reset();
            ResetItem.Enabled = false;
        }
    );

    /**
     * The delete Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Delete,
        vDesk.Visual.Icons.Delete,
        Contact.ID !== null && Contact.AccessControlList.Delete && vDesk.Security.User.Current.Permissions.DeleteContact,
        () => ContactEditor.Delete()
    );

    /**
     * Button for displaying the ContactEditor of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ContactItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Contacts.Contact,
        vDesk.Visual.Icons.Contacts.Module,
        true,
        () => {
            if(CurrentEditor !== ContactEditor){
                this.Content.replaceChild(ContactEditor.Control, CurrentEditor.Control);
                CurrentEditor = ContactEditor;
                ContactItem.Selected = true;
                AccessItem.Selected = false;
            }
        }
    );
    ContactItem.Control.classList.add("Selected");

    /**
     * ToolBar Item for displaying the AccessControlListEditor of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const AccessItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Security.Visibility,
        vDesk.Visual.Icons.Security.Lock,
        true,
        () => {
            if(CurrentEditor !== AccessControlListEditor){
                this.Content.replaceChild(AccessControlListEditor.Control, CurrentEditor.Control);
                CurrentEditor = AccessControlListEditor;
                ContactItem.Selected = false;
                AccessItem.Selected = true;
            }
        }
    );

    /**
     * The ToolBar of the Window.
     * @type {vDesk.Controls.ToolBar}
     */
    const ToolBar = new vDesk.Controls.ToolBar(
        [
            new vDesk.Controls.ToolBar.Group(
                vDesk.Locale.Contacts.Contact,
                [
                    SaveItem,
                    ResetItem,
                    DeleteItem
                ]
            ),
            new vDesk.Controls.ToolBar.Group(
                vDesk.Locale.vDesk.View,
                [
                    ContactItem,
                    AccessItem
                ]
            )
        ]
    );

    /**
     * The ContactEditor of the Window.
     * @type {vDesk.Contacts.Contact.Editor}
     */
    const ContactEditor = new vDesk.Contacts.Contact.Editor(Contact, true);
    ContactEditor.Control.addEventListener("change", OnChange, false);
    ContactEditor.Control.addEventListener("create", OnCreate, false);
    ContactEditor.Control.addEventListener("update", OnUpdate, false);
    ContactEditor.Control.addEventListener("delete", OnDelete, false);

    /**
     * The currently displayed editor of the Window.
     * @type {vDesk.Contacts.Contact.Editor|vDesk.Security.AccessControlList.Editor}
     */
    let CurrentEditor = ContactEditor;

    /**
     * The AccessControlListEditor of the Window.
     * @type {vDesk.Security.AccessControlList.Editor}
     */
    const AccessControlListEditor = new vDesk.Security.AccessControlList.Editor(Contact.AccessControlList, true);
    Contact.AccessControlList.Fill(AccessControlList => AccessControlListEditor.AccessControlList = AccessControlList);
    AccessControlListEditor.Control.addEventListener("change", OnChange, false);
    AccessControlListEditor.Control.addEventListener("update", OnUpdate, false);

    this.Title = Contact.ID !== null ? vDesk.Locale.Contacts.EditContact : vDesk.Locale.Contacts.NewContact;
    this.Icon = vDesk.Visual.Icons.Security.User;
    this.Content.appendChild(ToolBar.Control);
    this.Content.appendChild(CurrentEditor.Control);
    this.Control.classList.add("ContactEditorWindow");
    this.Height = 606;
};