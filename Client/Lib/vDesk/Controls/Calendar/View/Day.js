"use strict";
/**
 * Fired if further navigation would reach a date within the previous time range the Day will display.
 * @event vDesk.Controls.Calendar.View.Day#previous
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'previous' event.
 * @property {vDesk.Controls.Calendar.View.Day} detail.sender The current instance of the Day.
 * @property {Date} detail.date The previous date to display.
 */
/**
 * Fired if further navigation would reach a date within the next time range the Day will display.
 * @event vDesk.Controls.Calendar.View.Day#next
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'next' event.
 * @property {vDesk.Controls.Calendar.View.Day} detail.sender The current instance of the Day.
 * @property {Date} detail.date The next date to display.
 */
/**
 * Fired if the current date of the Day has been changed.
 * @event vDesk.Controls.Calendar.View.Day#datechanged
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'datechanged' event.
 * @property {vDesk.Controls.Calendar.View.Day} detail.sender The current instance of the Day.
 * @property {Date} detail.date The new date of the Day.
 */
/**
 * Initializes a new instance of the Decade class.
 * @class Represents a calendar-view for displaying the hours of a day.
 * @param {Adaptor} Adaptor The adaptor to the calendar within the view is hosted.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets or sets the title of the day.
 * @property {Date} Date Gets or sets the current (displayed) date of the day.
 * @property {vDesk.Controls.Calendar.Cell} Selected Gets or sets the current selected cell of the day.
 * @property {vDesk.Controls.Calendar.Cell} Now Gets or sets the cell of the day representing the current half-hour.
 * @property {Array<vDesk.Controls.Calendar.Cell>} Cells Gets the cells of the day.
 * @property {Array<vDesk.Controls.Calendar.IEvent>} Events Gets or sets the events of the day.
 * @property {Number} PixelPerHour Gets the ratio of pixels per hour. This value is used for determining the height of IEvents.
 * @property {Number} PixelPerMinute Gets the ratio of pixels per minute. This value is used for determining the height of IEvents.
 * @implements {vDesk.Controls.Calendar.IView}
 * @memberOf vDesk.Controls.Calendar.View
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.View.Day = function Day(Adaptor) {

    //using
    let Calendar = vDesk.Controls.Calendar;

    /**
     * The title of the day.
     * @type {null|String}
     */
    let Title = null;

    /**
     * The current date of the day.
     * @type {Date}
     */
    let Date = null;

    /**
     * The currently selected cell of the day.
     * @type {null|vDesk.Controls.Calendar.Cell}
     */
    let Selected = null;

    /**
     * The cell of the day representing the current half-hour.
     * @type {null|vDesk.Controls.Calendar.Cell}
     */
    let Now = null;

    /**
     * The computed width of the hour table of the day.
     * @type {null|Number}
     */
    let Width = null;

    Object.defineProperties(this, {
        Control:        {
            enumerable: true,
            get:        () => Control
        },
        Title:          {
            enumerable: true,
            get:        () => Title,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                Title = Value;
            }
        },
        Date:           {
            enumerable: true,
            get:        () => Date,
            set:        Value => {
                Ensure.Property(Value, window.Date, "Title");
                Date = Value;
            }
        },
        Selected:       {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Calendar.Cell, "Selected");
                Selected.Selected = false;
                Selected = Value;
                Selected.Selected = true;
            }
        },
        Now:            {
            enumerable: true,
            get:        () => Now,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Calendar.Cell, "Now");
                Now.Now = false;
                Now = Value;
                Now.Now = true;
            }
        },
        Cells:          {
            enumerable: true,
            get:        () => Cells
        },
        PixelPerHour:   {
            enumerable: true,
            value:      40
        },
        PixelPerMinute: {
            enumerable: true,
            get:        () => 60 / this.PixelPerHour
        }
    });

    /**
     * Displays the hours of a specified day.
     * @param {Date} Day The day to show the hours of.
     * @fires vDesk.Controls.Calendar.IView#datechanged
     */
    this.Show = function(Day) {
        Ensure.Parameter(Day, window.Date, "Day");
        Date = Day.clone();

        //Reset selection.
        Selected.Selected = false;
        Selected = null;

        //Reset today.
        if(Now !== null) {
            Now.Now = false;
            Now = null;
        }

        //Reset time.
        Day.setSeconds(0);
        Day.setMinutes(0);
        Day.setHours(0);
        Day.setMinutes(Date.getMinutes > 30 ? 30 : 0);
        Date.setMinutes(Day.getMinutes());
        const CurrentMinutes = Calendar.Today.getMinutes > 30 ? 30 : 0;

        //Loop trough cells and update their dates.
        Cells.forEach(Cell => {
            Cell.Date = Day.clone();
            Cell.Text = "";

            //Check if the cell's date matches the current time.
            if(Calendar.Today.getHours() === Day.getHours() && Day.getMinutes() === CurrentMinutes) {
                Now = Cell;
                Now.Now = true;
            }

            //Select the given (half)hour.
            if(Day.getHours() === Date.getHours() && Day.getMinutes() === Date.getMinutes()) {
                Selected = Cell;
                Selected.Selected = true;
            }

            //Increment time for iteration.
            Day.setMinutes(Day.getMinutes() + 30);
        });

        //Set the title of the day.
        Title = `${Date.getDate()} ${Adaptor.MonthAsString(Date.getMonth())} ${Date.getFullYear()}`;

        //Notify the change of the view.
        new vDesk.Events.BubblingEvent("datechanged", {
            sender: this,
            date:   Date.clone()
        }).Dispatch(Control);
    };

    /**
     * Displays the hours of the next day.
     */
    this.Forward = function() {
        Date.setDate(Date.getDate() + 1);
        this.Show(Date);
    };

    /**
     * Displays the hours of the previous day.
     */
    this.Backward = function() {
        Date.setDate(Date.getDate() - 1);
        this.Show(Date);
    };

    /**
     * Displays and layouts all Events of the view.
     * @todo Store Events in "Rows" array and check for intersection.
     */
    this.Display = function(Events = []) {
        window.requestAnimationFrame(() => {
            while(FulltimeEventList.hasChildNodes()) {
                FulltimeEventList.removeChild(FulltimeEventList.lastChild);
            }

            EventNodes.forEach(Control => EventColumn.removeChild(Control));
            EventNodes = [];

            Width = Width ?? Number.parseInt(window.getComputedStyle(EventColumn).width);
            let Columns = [];
            let LastEvent = null;
            const EventFragment = document.createDocumentFragment();
            const FullTimeEventFragment = document.createDocumentFragment();

            //Sort the Events by starting time, and then by ending time.
            Events.sort((First, Second) => {
                if(First.Start < Second.Start) {
                    return -1;
                }
                if(First.Start > Second.Start) {
                    return 1;
                }
                if(First.End < Second.End) {
                    return -1;
                }
                if(First.End > Second.End) {
                    return 1;
                }
                return 0;
            })
                //Filter for only none full time Events.
                .filter(Event => !Event.FullTime)
                //Group the Events according to their start- and end date.
                .forEach(Event => {
                    //Check if a column needs to be started.
                    if(LastEvent !== null && Event.Start >= LastEvent.End) {
                        AlignEvents(Columns, Width);
                        Columns = [];
                        LastEvent = null;
                    }

                    //If event doesn't fit in any existing column, create a new column for the current event.
                    if(!Columns.some(Column => {
                        //Check if the last event of the column doesn't intersect with the current event.
                        if(!Column[Column.length - 1].CollidesWith(Event)) {
                            Column.push(Event);
                            return true;
                        }
                        return false;
                    })) {
                        Columns.push([Event]);
                    }

                    //Remember the latest event end time of the current group.
                    if(LastEvent === null || Event.End > LastEvent.End) {
                        LastEvent = Event;
                    }

                    EventFragment.appendChild(Event.Control);
                    EventNodes.push(Event.Control);
                });

            //Check if any columns exist.
            if(Columns.length > 0) {
                AlignEvents(Columns, Width);
            }

            //Append fulltime events to the fulltimeeventlist.
            Events.filter(Event => Event.FullTime).forEach(Event => FullTimeEventFragment.appendChild(Event.Control));

            //Append events to the eventcolumn.
            EventColumn.appendChild(EventFragment);
            FulltimeEventList.appendChild(FullTimeEventFragment);
        });

    };

    /**
     * Aligns all events according to their start- and enddate within the eventcolumn.
     * @param {Array<Array<vDesk.Controls.Calendar.IEvent>>} Columns The grouped columns of events to align.
     * @param {Number} Width The Width of the control within the events will be displayed.
     */
    const AlignEvents = (Columns, Width) => {
        const WidthPerColumn = Width / Columns.length;
        Columns.forEach((Column, Index) => {
            Column.forEach(Event => {
                Event.Left = (WidthPerColumn * Index) + 5;
                Event.Width = (WidthPerColumn * ExpandEvent(Event, Index, Columns)) - 10;
                SetHeight(Event);
                if(Event.Start.getDate() < Date.getDate()) {
                    Event.Top = 0;
                    Event.Movable = false;
                } else {
                    Event.Top = (Event.Start.getHours() * this.PixelPerHour) + (Event.Start.getMinutes() / this.PixelPerMinute);
                    Event.Movable = true;
                }
            });
        });
    };

    /**
     * Expand events at the far right to use up any remaining space.
     * Checks how many columns the event can expand into, without colliding with other events.
     * @param {vDesk.Controls.Calendar.IEvent} Event The event to expand.
     * @param {Number} Index
     * @param {Array} Columns
     * @return {Number} The amount of columns the event can span over.
     */
    const ExpandEvent = (Event, Index, Columns) => {
        let ColumnSpan = 1;
        //Loop through following columns.
        for(let Column = Index + 1, ColumnCount = Columns.length; Column < ColumnCount; Column++) {
            //Loop through events of following columns and check if they collide.
            for(let EventIndex = 0, EventCount = Columns[Column].length; EventIndex < EventCount; EventIndex++) {
                //Return the amount of possible columns the event can span over 
                //if it reaches an event of a following column that collides with.
                if(Event.CollidesWith(Columns[Column][EventIndex])) {
                    return ColumnSpan;
                }
            }
            ColumnSpan++;
        }
        return ColumnSpan;
    };

    /**
     * Calculates the height of a specified event according to its duration.
     * @param {vDesk.Controls.Calendar.IEvent} Event The event to calculate the eheight of.
     */
    const SetHeight = Event => {
        //Check if the event starts further and ends later, spans over the whole day.
        if(Event.End > Date && Event.Start < Date) {
            Event.Height = 24 * this.PixelPerHour;
            Event.ModifyEnd = false;
            Event.ModifyStart = false;
            return;
        }
        //Check if the event ends on another future day.
        if(Event.End.getDate() > Date.getDate()) {
            if(Event.Duration <= 24) {
                Event.Height = Event.Duration * this.PixelPerHour;
                Event.ModifyEnd = false;
            } else {
                Event.Height = 24 * this.PixelPerHour;
            }
            Event.ModifyStart = true;
            return;
        }
        //Check if the Event starts on a further past day.
        if(Event.Start.getDate() < Date.getDate()) {
            Event.Height = (Event.End.getHours() * this.PixelPerHour) + (Event.End.getMinutes() / this.PixelPerMinute);
            Event.ModifyStart = false;
            Event.ModifyEnd = true;
            return;
        }
        Event.Height = Event.Duration * this.PixelPerHour;
        Event.ModifyStart = true;
        Event.ModifyEnd = true;
    };

    /**
     * Displays the hours of the next day.
     */
    this.Forward = function() {
        Date.setDate(Date.getDate() + 1);
        this.Show(Date);
    };

    /**
     * Displays the hours of the previous day.
     */
    this.Backward = function() {
        Date.setDate(Date.getDate() - 1);
        this.Show(Date);
    };

    /**
     * Navigates one cell up within the day.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Up = function() {
        const Index = Cells.indexOf(Selected);
        if(Index === 0) {
            const Date = Cells[Cells.length - 1].Date.clone();
            Date.setDate(Date.getDate() - 1);
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Date
            }).Dispatch(Control);
        } else {
            Selected.Selected = false;
            Selected = Cells[Index - 1];
            Selected.Selected = true;
        }
    };

    /**
     * Navigates one cell down within the day.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Down = function() {
        const Index = Cells.indexOf(Selected);
        if(Index === Cells.length - 1) {
            const Date = Cells[0].Date.clone();
            Date.setDate(Date.getDate() + 1);
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Date
            }).Dispatch(Control);
        } else {
            Selected.Selected = false;
            Selected = Cells[Index + 1];
            Selected.Selected = true;
        }
    };

    /**
     * Navigates one cell left within the day.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Left = function() {
        const Date = Selected.Date.clone();
        Date.setDate(Date.getDate() - 1);
        new vDesk.Events.BubblingEvent("previous", {
            sender: this,
            date:   Date
        }).Dispatch(Control);
    };

    /**
     * Navigates one cell right within the day.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Right = function() {
        const Date = Selected.Date.clone();
        Date.setDate(Date.getDate() + 1);
        new vDesk.Events.BubblingEvent("next", {
            sender: this,
            date:   Date
        }).Dispatch(Control);
    };

    /**
     * The cells of the Day.
     * @type {Array<vDesk.Controls.Calendar.Cell>}
     */
    const Cells = [];

    /**
     * The Events of the Day.
     * @type {Array<vDesk.Controls.Calendar.IEvent>}
     */
    let Events = [];

    /**
     * Array of references to the controls of every Event of the Day.
     * @type {Array<HTMLElement>}
     */
    let EventNodes = [];

    /**
     * Array of references to the controls of every fulltime Event of the Day.
     * @type {Array<HTMLElement>}
     */
    let FullTimeEventNodes = [];

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Day";

    /**
     * The fulltime Event table of the Day.
     * @type {HTMLDivElement}
     */
    const FullTimeEventTable = document.createElement("div");
    FullTimeEventTable.className = "FullTime";

    /**
     * The fulltime Event list of the Day.
     * @type {HTMLDivElement}
     */
    const FulltimeEventList = document.createElement("div");
    FulltimeEventList.className = "List BorderLight";

    FullTimeEventTable.appendChild(FulltimeEventList);

    /**
     * The hour table of the Day.
     * @type {HTMLDivElement}
     */
    const HourTable = document.createElement("div");
    HourTable.className = "Time";

    /**
     * The hour column of the hourtable of the Day.
     * @type {HTMLUListElement}
     */
    const HourColumn = document.createElement("ul");
    HourColumn.className = "Hours BorderLight";

    /**
     * The event column of the hourtable of the Day.
     * @type {HTMLUListElement}
     */
    const EventColumn = document.createElement("ul");
    EventColumn.className = "HalfHours";

    //Fill hour table.
    for(let i = 0; i < 24; i++) {
        const Hour = document.createElement("li");
        Hour.className = "Cell Font Dark BorderLight";
        Hour.textContent = i.toString();
        Hour.title = i + " - " + (i + 1);
        HourColumn.appendChild(Hour);

        //Create for each hour a full hour cell.
        const FullHour = new vDesk.Controls.Calendar.Cell();
        FullHour.Type = "hour";

        EventColumn.appendChild(FullHour.Control);
        Cells.push(FullHour);

        //Create for each hour a half hour cell.
        const HalfHour = new vDesk.Controls.Calendar.Cell();
        HalfHour.Type = "hour";

        EventColumn.appendChild(HalfHour.Control);
        Cells.push(HalfHour);

        //Check if the current hour equals the iterations.
        if(Calendar.Today.getHours() === i) {

            //Check if the minutes of the current time are beneath 30.
            if(Calendar.Today.getMinutes() < 30) {

                //Mark the full hour as current.
                Selected = FullHour;
                Selected.Selected = true;
                Now = FullHour;
                Now.Now = true;
            } else {

                //Otherwise mark the half hour as current.
                Selected = HalfHour;
                Selected.Selected = true;
                Now = HalfHour;
                Now.Now = true;
            }
            Hour.classList.add("Now");
        }
    }

    HourTable.appendChild(HourColumn);
    HourTable.appendChild(EventColumn);

    Control.appendChild(FullTimeEventTable);
    Control.appendChild(HourTable);
};

vDesk.Controls.Calendar.View.Day.Implements(vDesk.Controls.Calendar.IView);