"use strict";
/**
 * Initializes a new instance of the Window class.
 * @class Represents an window containing a composition of editors for modifying the contents and permissions of an event.
 * @param {vDesk.Calendar.Event} Event The event to edit.
 * @extends vDesk.Controls.Window
 * @memberOf vDesk.Calendar.Event.Editor
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Calendar.Event.Editor.Window = function Window(Event) {
    Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");

    this.Extends(vDesk.Controls.Window);

    /**
     * Eventhandler that listens on the 'change' event.
     * @listens vDesk.Calendar.Event.Editor#event:change
     * @listens vDesk.Security.AccessControlList.Editor#event:change
     */
    const OnChange = () => {
        ResetItem.Enabled = true;
        SaveItem.Enabled = true;
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Calendar.Event.Editor#event:create
     */
    const OnCreate = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        DeleteItem.Enabled = true;
        this.Title = vDesk.Locale.Contacts.EditContact;
        EventEditor.Event.AccessControlList.Fill(AccessControlList => {
            AccessControlListEditor.Merge(AccessControlList);
            AccessControlListEditor.Save();
        });
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Calendar.Event.Editor#event:update
     * @listens vDesk.Security.AccessControlList.Editor#event:update
     */
    const OnUpdate = Event => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        if(Event.detail.accesscontrollist !== undefined) {
            Event.stopPropagation();
        }
    };

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Calendar.Event.Editor#event:delete
     */
    const OnDelete = () => {
        ResetItem.Enabled = false;
        SaveItem.Enabled = false;
        DeleteItem.Enabled = false;
        this.Title = vDesk.Locale.Contacts.NewContact;
        EventEditor.Event = new vDesk.Calendar.Event();
        AccessControlListEditor.AccessControlList = new vDesk.Security.AccessControlList();
    };

    /**
     * The save Item of the Window.
     */
    const SaveItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Save,
        vDesk.Visual.Icons.Save,
        false,
        () => {
            if(EventEditor.Changed) {
                EventEditor.Save();
            }
            if(ParticipantEditor.Changed) {
                //ParticipantEditor.Save();
            }
            if(AccessControlListEditor.Changed) {
                AccessControlListEditor.Save();
            }
        }
    );

    /**
     * The reset ToolBar Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ResetItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.ResetChanges,
        vDesk.Visual.Icons.Refresh,
        false,
        () => {
            EventEditor.Reset();
            AccessControlListEditor.Reset();
            ResetItem.Enabled = false;
        }
    );

    /**
     * The delete ToolBar Item of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Delete,
        vDesk.Visual.Icons.Delete,
        Event.ID !== null && Event.AccessControlList.Delete /*&& vDesk.User.Permissions.DeleteEvent*/,
        () => {
            EventEditor.Delete();
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Calendar",
                        Command:    "DeleteEvent",
                        Parameters: {
                            ID: Event.ID
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        this.Close();
                    }
                }
            );
        }
    );

    /**
     * Button for displaying the EventEditor of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const EventItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Event,
        vDesk.Visual.Icons.Calendar.Module,
        true,
        () => {
            if(CurrentEditor !== EventEditor) {
                this.Content.replaceChild(EventEditor.Control, CurrentEditor.Control);
                CurrentEditor = EventEditor;
                EventItem.Selected = true;
                MeetingItem.Selected = false;
                AccessItem.Selected = false;
            }
        }
    );
    EventItem.Selected = true;

    /**
     * Button for displaying the ParticipantEditor of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const MeetingItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Meeting,
        vDesk.Visual.Icons.Security.Group,
        true,
        () => {
            if(CurrentEditor !== ParticipantEditor) {
                this.Content.replaceChild(ParticipantEditor.Control, CurrentEditor.Control);
                CurrentEditor = ParticipantEditor;
                EventItem.Selected = false;
                MeetingItem.Selected = true;
                AccessItem.Selected = false;
            }
        }
    );

    /**
     * Button for displaying the AccessControlListEditor of the Window.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const AccessItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Security.Visibility,
        vDesk.Visual.Icons.Security.Lock,
        true,
        () => {
            if(CurrentEditor !== AccessControlListEditor) {
                this.Content.replaceChild(AccessControlListEditor.Control, CurrentEditor.Control);
                CurrentEditor = AccessControlListEditor;
                EventItem.Selected = false;
                MeetingItem.Selected = false;
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
                vDesk.Locale.Calendar.Event,
                [
                    SaveItem,
                    ResetItem,
                    DeleteItem
                ]
            ),
            new vDesk.Controls.ToolBar.Group(
                vDesk.Locale.vDesk.View,
                [
                    EventItem,
                    MeetingItem,
                    AccessItem
                ]
            )
        ]
    );

    /**
     * The EventEditor of the Window.
     * @type {vDesk.Calendar.Event.Editor}
     */
    const EventEditor = new vDesk.Calendar.Event.Editor(Event, true);
    EventEditor.Control.addEventListener("change", OnChange);
    EventEditor.Control.addEventListener("create", OnCreate);
    EventEditor.Control.addEventListener("update", OnUpdate);
    EventEditor.Control.addEventListener("delete", OnDelete);
    /**
     * The Participant.Editor of the Window.
     * @type {vDesk.Calendar.Event.Participant.Editor}
     */
    const ParticipantEditor = new vDesk.Calendar.Event.Participant.Editor(Event);

    /**
     * The AccessControlListEditor of the Window.
     * @type {vDesk.Security.AccessControlList.Editor}
     */
    const AccessControlListEditor = new vDesk.Security.AccessControlList.Editor(Event.AccessControlList);
    Event.AccessControlList.Fill(AccessControlList => AccessControlListEditor.AccessControlList = AccessControlList);
    AccessControlListEditor.Control.addEventListener("change", OnChange, false);
    AccessControlListEditor.Control.addEventListener("update", OnUpdate, false);

    /**
     * The currently displayed editor of the Window.
     * @type {vDesk.Calendar.Event.Editor|vDesk.Calendar.Participant.Editor|vDesk.Security.AccessControlList.Editor}
     */
    let CurrentEditor = EventEditor;

    this.Title = Event.ID !== null ? vDesk.Locale.Calendar.EditEvent : vDesk.Locale.Calendar.NewEvent;
    this.Icon = vDesk.Visual.Icons.Calendar.Module;
    this.Content.appendChild(ToolBar.Control);
    this.Content.appendChild(CurrentEditor.Control);
    this.Control.classList.add("EventEditorWindow");
};