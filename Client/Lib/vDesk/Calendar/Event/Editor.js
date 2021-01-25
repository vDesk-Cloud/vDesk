"use strict";
/**
 * Fired if the current edited Event of the Editor has been changed.
 * @event vDesk.Calendar.Event.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Calendar.Event.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a new Event has been created.
 * @event vDesk.Calendar.Event.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.Calendar.Event.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Calendar.Event} detail.event The newly created Event.
 */
/**
 * Fired if the current edited Event of the Editor has been updated.
 * @event vDesk.Calendar.Event.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Calendar.Event.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Calendar.Event} detail.event The updated Event.
 */
/**
 * Fired if the current edited Event of the Editor has been deleted.
 * @event vDesk.Calendar.Event.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Calendar.Event.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Calendar.Event} detail.event The deleted Event.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for viewing or editing the contents of an Event.
 * @param {vDesk.Calendar.Event} Event Initializes the Editor with the specified Event to edit.
 * @param {Boolean} [Enabled = true] Flag indicating whether the Editor is enabled.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Calendar.Event} Event Gets or sets the current edited Event of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether current edited Event of the Editor has been changed.
 * @memberOf vDesk.Calendar.Event
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Implement controls for changing/setting repeatamount and -interval.
 */
vDesk.Calendar.Event.Editor = function Editor(Event, Enabled = true) {
    Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The previous state of the current edited Event of the Editor.
     * @type {vDesk.Calendar.Event}
     */
    let Previous = vDesk.Calendar.Event.FromDataView(Event);

    /**
     * Flag indicating whether the Event of the Editor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => GroupBox.Control
        },
        Event:   {
            enumerable: true,
            get:        () => Event,
            set:        Value => {
                Ensure.Property(Value, vDesk.Calendar.Event, "Event");
                Event = Value;
                Owner.textContent = `${vDesk.Locale.Security.Owner}: ${Value.Owner.Name}`;
                Previous = vDesk.Calendar.Event.FromDataView(Value);
                Title.Value = Value.Title;
                Color.Value = vDesk.Media.Drawing.Color.FromRGBString(Value.Color);
                Start.Value = Value.Start;
                End.Value = Value.End;
                FullTime.Value = Value.FullTime;
                Content.Value = Value.Content;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Title.Enabled = Value;
                Color.Enabled = Value;
                Start.Enabled = Value;
                End.Enabled = Value;
                FullTime.Enabled = Value;
                Content.Enabled = Value;
            }
        },
        Changed: {
            get: () => Changed
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @fires vDesk.Calendar.Event.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(GroupBox.Control);
    };

    /**
     * Saves possible changes or creates a new database-entry.
     * @return {Boolean} True if the changes have been sucessfully saved; otherwise, false.
     */
    this.Save = function() {
        if(Event.ID === null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Calendar",
                        Command:    "CreateEvent",
                        Parameters: {
                            Start:          Start.Value,
                            End:            End.Value,
                            FullTime:       FullTime.Value,
                            RepeatAmount:   0,
                            RepeatInterval: 0,
                            Title:          Title.Value,
                            Color:          Color.Value.ToRGBString(),
                            Content:        Content.Value
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        this.Event = vDesk.Calendar.Event.FromDataView(Response.Data);
                        Changed = false;
                        new vDesk.Events.BubblingEvent("create", {
                            sender: this,
                            event:  Event
                        }).Dispatch(GroupBox.Control);
                    }
                }
            );
        } else {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Calendar",
                        Command:    "UpdateEvent",
                        Parameters: {
                            ID:             Event.ID,
                            Start:          Start.Value,
                            End:            End.Value,
                            FullTime:       FullTime.Value,
                            RepeatAmount:   0,
                            RepeatInterval: 0,
                            Title:          Title.Value,
                            Color:          Color.Value.ToRGBString(),
                            Content:        Content.Value
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        Event.Start = Start.Value;
                        Event.End = End.Value;
                        Event.FullTime = FullTime.Value;
                        Event.Title = Title.Value;
                        Event.Color = Color.Value.ToRGBString();
                        Event.Content = Content.Value;
                        Previous = vDesk.Calendar.Event.FromDataView(Event);
                        Changed = false;
                        new vDesk.Events.BubblingEvent("update", {
                            sender: this,
                            event:  Event
                        }).Dispatch(GroupBox.Control);
                    }
                }
            );
        }
    };

    /**
     * Deletes the current edited Event.
     * @fires vDesk.Calendar.Event.Editor#delete
     */
    this.Delete = function() {
        if(Event.ID !== null) {
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
                        new vDesk.Events.BubblingEvent("delete", {
                            sender: this,
                            event:  Event
                        }).Dispatch(GroupBox.Control);
                        this.Event = new vDesk.Calendar.Event();
                    }
                }
            );
        }
    };

    /**
     * Resets the current edited Event to its original state.
     */
    this.Reset = () => this.Event = Previous;

    /**
     * The owner row of the Editor.
     * @type {HTMLLIElement}
     */
    const Owner = document.createElement("li");
    Owner.className = "Owner Font Dark BorderLight";
    Owner.textContent = `${vDesk.Locale.Security.Owner}: ${Event.Owner.Name}`;

    /**
     * The title EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Title = new vDesk.Controls.EditControl(
        vDesk.Locale.vDesk.Title,
        null,
        Type.String,
        Event.Title,
        null,
        true,
        Enabled
    );
    Title.Control.classList.add("Title", "BorderLight");

    /**
     * The color EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Color = new vDesk.Controls.EditControl(
        vDesk.Locale.Colors.Color,
        null,
        Extension.Type.Color,
        vDesk.Media.Drawing.Color.FromRGBString(Event.Color),
        null,
        true,
        Enabled
    );
    Color.Control.classList.add("Color", "BorderLight");

    /**
     * The start DateTime EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Start = new vDesk.Controls.EditControl(
        vDesk.Locale.Calendar.Start,
        null,
        Extension.Type.DateTime,
        Event.Start,
        null,
        true,
        Enabled
    );
    Start.Control.classList.add("Start");

    /**
     * The end DateTime EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const End = new vDesk.Controls.EditControl(
        vDesk.Locale.Calendar.End,
        null,
        Extension.Type.DateTime,
        Event.End,
        null,
        true,
        Enabled
    );
    End.Control.classList.add("End", "BorderLight");

    /**
     * The full time EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const FullTime = new vDesk.Controls.EditControl(
        vDesk.Locale.Calendar.FullTime,
        null,
        Type.Boolean,
        Event.FullTime,
        null,
        false,
        Enabled
    );
    FullTime.Control.classList.add("FullTime");

    /**
     * The content EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Content = new vDesk.Controls.EditControl(
        "",
        null,
        Extension.Type.Text,
        Event.Content,
        null,
        false,
        Enabled
    );
    Content.Control.classList.add("Content");

    /**
     * The GroupBox of the Editor.
     * @type {vDesk.Controls.GroupBox}
     */
    const GroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale.Calendar.Event,
        [
            Owner,
            Title.Control,
            Color.Control,
            Start.Control,
            End.Control,
            FullTime.Control,
            Content.Control
        ]
    );
    GroupBox.Control.classList.add("EventEditor");
    GroupBox.Content.addEventListener("update", OnUpdate, false);
};