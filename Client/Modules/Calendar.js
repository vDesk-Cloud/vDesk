"use strict";
/**
 * Initializes a new instance of the Calendar class.
 * @module Calendar
 * @class The calendar module.
 * Provides functionality for adding and organizing events and meetings as well als setting reminders.
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
Modules.Calendar = function Calendar() {

    /**
     * The current selected event of the Calendar module.
     * @type vDesk.Calendar.Event
     * @ignore
     */
    let SelectedEvent = null;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Name:     {
            enumerable: true,
            value:      "Calendar"
        },
        Title:    {
            enumerable: true,
            value:      vDesk.Locale.Calendar.Module
        },
        Icon:     {
            enumerable: true,
            value:      vDesk.Visual.Icons.Calendar.Module
        },
        Calendar: {
            enumerable: true,
            get:        () => Calendar
        }
    });

    /**
     * Creates a new Event.
     */
    this.CreateEvent = () => this.EditEvent(new vDesk.Calendar.Event());

    /**
     * Edits an Event.
     * @param {vDesk.Calendar.Event} Event The event to edit.
     */
    this.EditEvent = function(Event) {
        Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
        const Window = new vDesk.Calendar.Event.Editor.Window(Event);
        Window.Control.addEventListener("create", OnCreate);
        Window.Control.addEventListener("update", OnUpdate);
        Window.Control.addEventListener("delete", OnDelete);
        Window.Show();
    };

    /**
     * Deletes an Event.
     * @param {vDesk.Calendar.Event} Event The Event to delete.
     */
    this.DeleteEvent = function(Event) {
        Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
        const Editor = new vDesk.Calendar.Event.Editor(Event);
        Editor.Control.addEventListener("delete", OnDelete);
        Editor.Delete();
    };

    /**
     * Updates the displayed events of the current view of the Calendar module.
     */
    const UpdateCalendar = function() {
        if(Calendar.CurrentView instanceof vDesk.Controls.Calendar.View.Day) {
            EventCache.FetchDay(
                Calendar.CurrentView.Date,
                //@todo Make "Calendar.CurrentView.Show"
                Calendar.CurrentView.Display,
                false
            );
        }
        if(Calendar.CurrentView instanceof vDesk.Controls.Calendar.View.Month) {
            EventCache.FetchMonth(Calendar.CurrentView.Date, Calendar.CurrentView.Display, false);
        }
    };

    /**
     * Eventhandler that listens on the 'select' event and marks an Event as selected and enables according toolbaritems.
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        if(Event.detail.sender instanceof vDesk.Calendar.Event) {
            SelectedEvent = Event.detail.sender;
            OpenEventToolBarItem.Enabled = true;
            EditEventToolBarItem.Enabled = true;
            DeleteEventToolBarItem.Enabled = true;
        }
    };

    /**
     * Eventhandler that listens on the 'click' event and deselects any current selected Event and disables according toolbaritems and hides the ContextMenu aswell.
     */
    const OnClick = () => {
        SelectedEvent = null;
        OpenEventToolBarItem.Enabled = false;
        EditEventToolBarItem.Enabled = false;
        DeleteEventToolBarItem.Enabled = false;
        ContextMenu.Hide();
    };

    /**
     * Eventhandler that listens on the 'open' event and shows the data of the opened Event.
     * @listens vDesk.Calendar.Event#event:open
     * @param {CustomEvent} Event
     */
    const OnOpen = Event => {
        if(Event.detail.sender instanceof vDesk.Calendar.Event) {
            new vDesk.Calendar.Event.Viewer.Window(Event.detail.sender).Show();
        }
    };

    /**
     * Eventhandler that listens on the 'datechanged' event and fetches and displays any existing Events within the Calendar of the current date.
     * @listens vDesk.Controls.Calendar.IView#event:datechanged
     * @param {CustomEvent} Event
     */
    const OnDateChanged = Event => {
        //Check if currently the day-view is active and fetch events of the displayed day.
        if(Calendar.CurrentView instanceof vDesk.Controls.Calendar.View.Day) {
            EventCache.FetchDay(
                Event.detail.date,
                Calendar.CurrentView.Display,
                false
            );
        }
        //Otherwise check if currently the month-view is active and fetch events of the displayed month.
        else if(Calendar.CurrentView instanceof vDesk.Controls.Calendar.View.Month) {
            //@todo Fetch Events according the dates of the first and last Cell of the Month-view.
            EventCache.FetchMonth(Event.detail.date, Calendar.CurrentView.Display, false);
        }
    };

    /**
     * Eventhandler that listens on the 'resized' event and updates the enddate of the resized Event.
     * @listens vDesk.Calendar.Event#event:resized
     * @param {CustomEvent} Event
     */
    const OnResized = Event => {

        const Start = Event.detail.sender.Start.clone();
        const End = Event.detail.sender.End.clone();
        const Difference = ((Event.detail.height.previous - Event.detail.height.current) * Calendar.CurrentView.PixelPerMinute) * 60000;

        //Check if the position has been changed.
        if(Event.detail.top.current !== Event.detail.top.previous) {
            Start.setTime(Start.getTime() + Difference);
        } else {
            End.setTime(End.getTime() - Difference);
        }

        //Save changes.
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Calendar",
                    Command:    "UpdateEventDate",
                    Parameters: {
                        ID:    Event.detail.sender.ID,
                        Start: Start,
                        End:   End
                    },
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Event.detail.sender.End = End;
                    Event.detail.sender.Start = Start;
                    Event.detail.sender.SetTooltip();
                }
                UpdateCalendar();
            }
        );
    };

    /**
     * Updates the start- and enddate of a moved event.
     * @listens vDesk.Calendar.Event#event:moved
     * @param {CustomEvent} Event
     */
    const OnMoved = Event => {

        //Calculate new start- and enddate.
        const Start = Event.detail.sender.Start.clone();
        const End = Event.detail.sender.End.clone();
        const Difference = ((Event.detail.top.current - Event.detail.top.previous) * Calendar.CurrentView.PixelPerMinute) * 60000;
        Start.setTime(Start.getTime() + Difference);
        End.setTime(End.getTime() + Difference);

        //Save changes.
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Calendar",
                    Command:    "UpdateEventDate",
                    Parameters: {
                        ID: Event.detail.sender.ID,
                        Start,
                        End
                    },
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Event.detail.sender.Start = Start;
                    Event.detail.sender.End = End;
                    Event.detail.sender.SetTooltip();
                }
                UpdateCalendar();
            }
        );
    };

    /**
     * Eventhandler that listens on the 'create' event.
     * @listens vDesk.Calendar.Event.Editor#event:create
     * @param {CustomEvent} Event
     */
    const OnCreate = Event => {
        EventCache.Add(Event.detail.event);
        UpdateCalendar();
    };

    /**
     * Eventhandler that listens on the 'vDesk.Calendar.Event.Created' event.
     * @param {MessageEvent} Event
     */
    const OnCalendarEventCreated = Event => {
        if(EventCache.Find(Number.parseInt(Event.data)) === null) {
            EventCache.FetchEvent(Number.parseInt(Event.data), Event => {
                if(Event !== null) {
                    EventCache.Add(Event);
                    UpdateCalendar();
                }
            });
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Calendar.Event.Created", OnCalendarEventCreated, false);

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Calendar.Event.Editor#event:update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        EventCache.Update(Event.detail.event);
        UpdateCalendar();
    };

    /**
     * Eventhandler that listens on the 'vDesk.Calendar.Event.Updated' event.
     * @param {MessageEvent} Event
     */
    const OnCalendarEventUpdated = Event => {
        if(EventCache.Find(Number.parseInt(Event.data)) !== null) {
            EventCache.FetchEvent(Number.parseInt(Event.data), Event => {
                if(Event !== null) {
                    EventCache.Update(Event);
                    UpdateCalendar();
                }
            });
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Calendar.Event.Updated", OnCalendarEventUpdated, false);

    /**
     * Eventhandler that listens on the 'delete' event.
     * @listens vDesk.Calendar.Event.Editor#event:delete
     * @param {CustomEvent} Event
     */
    const OnDelete = Event => {
        EventCache.Remove(Event.detail.event);
        UpdateCalendar();
    };

    /**
     * Eventhandler that listens on the 'vDesk.Calendar.Event.Deleted' event.
     * @param {MessageEvent} Event
     */
    const OnCalendarEventDeleted = Event => {
        if(EventCache.Find(Number.parseInt(Event.data)) !== null) {
            EventCache.FetchEvent(Number.parseInt(Event.data), Event => {
                if(Event !== null) {
                    EventCache.Remove(Event);
                    UpdateCalendar();
                }
            });
        }
    };
    vDesk.Events.EventDispatcher.addEventListener("vDesk.Calendar.Event.Deleted", OnCalendarEventDeleted, false);

    /**
     * Displays the ContextMenu of the Calendar Module.
     * @param {CustomEvent} Event
     */
    const OnContext = Event => {
        ContextMenu.Show(Event.detail.sender, Event.detail.x, Event.detail.y);
        SelectedEvent = null;
        OpenEventToolBarItem.Enabled = false;
        EditEventToolBarItem.Enabled = false;
        DeleteEventToolBarItem.Enabled = false;
    };

    /**
     * Eventhandler that listens on the 'submit' event.
     * @listens vDesk.Controls.ContextMenu#event:submit
     * @param {CustomEvent} Event
     */
    const OnSubmit = Event => {
        switch(Event.detail.action) {
            case "open":
                new vDesk.Calendar.Event.Viewer.Window(ContextMenu.Target).Show();
                break;
            case "Edit":
                this.EditEvent(ContextMenu.Target);
                break;
            case "add":
                const CalendarEvent = new vDesk.Calendar.Event();
                CalendarEvent.Start = ContextMenu.Target.Date.clone();
                CalendarEvent.End = ContextMenu.Target.Date.clone();
                CalendarEvent.End.setHours(CalendarEvent.End.getHours() + 2);
                new vDesk.Calendar.Event.Editor.Window(CalendarEvent).Show();
                break;
            case "delete":
                this.DeleteEvent(ContextMenu.Target);
                break;
        }
        ContextMenu.Hide();
    };

    /**
     * Loads the Calendar Module.
     */
    this.Load = function() {
        vDesk.Header.ToolBar.Groups = [NewGroup, GoToGroup, ViewToolBarGroup, SelectedEventToolBarGroup];
        Calendar.CaptureKeys = true;
        Control.addEventListener("context", OnContext);
        Control.addEventListener("select", OnSelect);
        window.addEventListener("click", OnClick,);
        Control.addEventListener("open", OnOpen);
        Control.addEventListener("datechanged", OnDateChanged);
        Control.addEventListener("resized", OnResized);
        Control.addEventListener("moved", OnMoved);
    };

    /**
     * Unloads the Calendar Module.
     */
    this.Unload = function() {
        Calendar.CaptureKeys = false;
        Control.removeEventListener("context", OnContext);
        Control.removeEventListener("select", OnSelect);
        window.removeEventListener("click", OnClick);
        Control.removeEventListener("open", OnOpen);
        Control.removeEventListener("datechanged", OnDateChanged);
        Control.removeEventListener("resized", OnResized);
        Control.removeEventListener("moved", OnMoved);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Calendar";

    /**
     * The ContextMenu of the Calendar module.
     * @type {vDesk.Controls.ContextMenu}
     */
    const ContextMenu = new vDesk.Controls.ContextMenu(
        [
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.Calendar.GoToDate,
                "goto",
                vDesk.Visual.Icons.Calendar.DateTo
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.vDesk.Open,
                "open",
                vDesk.Visual.Icons.View,
                Event => Event instanceof vDesk.Calendar.Event
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.vDesk.Edit,
                "Edit",
                vDesk.Visual.Icons.Edit,
                Event => Event instanceof vDesk.Calendar.Event && Event.AccessControlList.Write
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale.vDesk.Delete,
                "delete",
                vDesk.Visual.Icons.Delete,
                Event => Event instanceof vDesk.Calendar.Event && Event.AccessControlList.Delete
            ),
            new vDesk.Controls.ContextMenu.Group(
                vDesk.Locale.vDesk.New,
                vDesk.Visual.Icons.TriangleRight,
                Cell => Cell instanceof vDesk.Controls.Calendar.Cell && (Cell.Type === "day" || Cell.Type === "hour"),
                [
                    new vDesk.Controls.ContextMenu.Item(
                        vDesk.Locale.Calendar.Event,
                        "add",
                        vDesk.Visual.Icons.Calendar.AddEvent,
                        () => true
                    )
                ]
            )
        ]
    );
    ContextMenu.Control.addEventListener("submit", OnSubmit);

    /**
     * The ToolBar Item for creating a new Event.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NewEventItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.NewEvent,
        vDesk.Visual.Icons.Calendar.AddEvent,
        true,
        () => this.CreateEvent()
    );

    /**
     * The ToolBar Group containing ToolBar Items for creating new Events.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const NewGroup = new vDesk.Controls.ToolBar.Group(vDesk.Locale.Calendar.Event, [NewEventItem]);

    /**
     * The Toolbar Item for navigating to the actual day.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const GoToTodayItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Today,
        vDesk.Visual.Icons.Calendar.Today,
        true,
        () => Calendar.Show(vDesk.Controls.Calendar.Today, vDesk.Controls.Calendar.View.Day)
    );

    /**
     * The ToolBar Item for navigating to a designated date.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const GoToDateItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Date,
        vDesk.Visual.Icons.Calendar.DateTo,
        true,
        () => {
            const Dialog = new vDesk.Calendar.GotoDialog();
            Dialog.Control.addEventListener(
                "submit",
                Event => {
                    Dialog.Close();
                    Calendar.Show(Event.detail.date.clone(), vDesk.Controls.Calendar.View.Day);
                },
                false
            );
            Dialog.Show();
        }
    );

    /**
     * The Toolbar Group containing ToolBar Items for navigating to a designed date.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const GoToGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.Calendar.GoTo,
        [
            GoToTodayItem,
            GoToDateItem
        ]
    );

    /**
     * The ToolBar Item for displaying the day of the current date.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ShowDayItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Day,
        vDesk.Visual.Icons.Calendar.DayView,
        true,
        () => Calendar.Show(Calendar.CurrentView.Date.clone(), vDesk.Controls.Calendar.View.Day)
    );

    /**
     * The ToolBar Item for displaying the month of the current date.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ShowMonthItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Month,
        vDesk.Visual.Icons.Calendar.MonthView,
        true,
        () => Calendar.Show(Calendar.CurrentView.Date.clone(), vDesk.Controls.Calendar.View.Month)
    );

    /**
     * The Toolbar Item for displaying the year of the current date.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ShowYearItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Calendar.Year,
        vDesk.Visual.Icons.Calendar.YearView,
        true,
        () => Calendar.Show(Calendar.CurrentView.Date.clone(), vDesk.Controls.Calendar.View.Year)
    );

    /**
     * The ToolBar Group containing ToolBar Items for switching the appearance of the calendar control.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const ViewToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.vDesk.View,
        [
            ShowDayItem,
            ShowMonthItem,
            ShowYearItem
        ]
    );

    /**
     * The ToolBar Item for displaying the data of the current selected Event.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const OpenEventToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Open,
        vDesk.Visual.Icons.View,
        false,
        () => {
            new vDesk.Calendar.Event.Viewer.Window(SelectedEvent).Show();
            SelectedEvent = null;
            OpenEventToolBarItem.Enabled = false;
            EditEventToolBarItem.Enabled = false;
            DeleteEventToolBarItem.Enabled = false;
        }
    );

    /**
     * The ToolBar Item for editing the data of the current selected Event.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const EditEventToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Edit,
        vDesk.Visual.Icons.Edit,
        false,
        () => {
            this.EditEvent(SelectedEvent);
            SelectedEvent = null;
            OpenEventToolBarItem.Enabled = false;
            EditEventToolBarItem.Enabled = false;
            DeleteEventToolBarItem.Enabled = false;
        }
    );

    /**
     * The ToolBar Item for deleting the current selected Event.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteEventToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Delete,
        vDesk.Visual.Icons.Delete,
        false,
        () => {
            this.DeleteEvent(SelectedEvent);
            SelectedEvent = null;
            OpenEventToolBarItem.Enabled = false;
            EditEventToolBarItem.Enabled = false;
            DeleteEventToolBarItem.Enabled = false;
        }
    );

    /**
     * The ToolBar Group containing view, edit and delete Toolbar Items for the current selected Event.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const SelectedEventToolBarGroup = new vDesk.Controls.ToolBar.Group(vDesk.Locale.Calendar.Event, [
        OpenEventToolBarItem,
        EditEventToolBarItem,
        DeleteEventToolBarItem
    ]);

    /**
     * The EventCache of the Calendar module.
     * @type vDesk.Calendar.Event.Cache
     */
    const EventCache = new vDesk.Calendar.Event.Cache(3, true);

    /**
     * The Calendar of the Calendar module.
     * @type vDesk.Controls.Calendar
     */
    const Calendar = new vDesk.Controls.Calendar(
        vDesk.Controls.Calendar.Today.clone(),
        vDesk.Controls.Calendar.DefaultViews,
        vDesk.Controls.Calendar.View.Month,
        [
            vDesk.Locale.Calendar.Sunday,
            vDesk.Locale.Calendar.Monday,
            vDesk.Locale.Calendar.Tuesday,
            vDesk.Locale.Calendar.Wednesday,
            vDesk.Locale.Calendar.Thursday,
            vDesk.Locale.Calendar.Friday,
            vDesk.Locale.Calendar.Saturday
        ],
        [
            vDesk.Locale.Calendar.January,
            vDesk.Locale.Calendar.February,
            vDesk.Locale.Calendar.March,
            vDesk.Locale.Calendar.April,
            vDesk.Locale.Calendar.May,
            vDesk.Locale.Calendar.June,
            vDesk.Locale.Calendar.July,
            vDesk.Locale.Calendar.August,
            vDesk.Locale.Calendar.September,
            vDesk.Locale.Calendar.October,
            vDesk.Locale.Calendar.November,
            vDesk.Locale.Calendar.December
        ]
    );

    UpdateCalendar();

    Control.appendChild(Calendar.Control);

};

Modules.Calendar.Implements(vDesk.Modules.IVisualModule);