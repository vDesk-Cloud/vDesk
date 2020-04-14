"use strict";
/**
 * Fired if further navigation would reach a date within the previous time range the Month will display.
 * @event vDesk.Controls.Calendar.View.Month#previous
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'previous' event.
 * @property {vDesk.Controls.Calendar.View.Month} detail.sender The current instance of the Month.
 * @property {Date} detail.date The previous date to display.
 */
/**
 * Fired if further navigation would reach a date within the next time range the Month will display.
 * @event vDesk.Controls.Calendar.View.Month#next
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'next' event.
 * @property {vDesk.Controls.Calendar.View.Month} detail.sender The current instance of the Month.
 * @property {Date} detail.date The next date to display.
 */
/**
 * Fired if the current date of the Month has been changed.
 * @event vDesk.Controls.Calendar.View.Month#datechanged
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'datechanged' event.
 * @property {vDesk.Controls.Calendar.View.Month} detail.sender The current instance of the Month.
 * @property {Date} detail.date The new date of the Month.
 */
/**
 * Initializes a new instance of the Decade class.
 * @class Represents a calendar-view for displaying the days of a month.
 * @param {Adaptor} Adaptor The adaptor to the calendar within the view is hosted.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets or sets the title of the month.
 * @property {Date} Date Gets or sets the current (displayed) date of the month.
 * @property {vDesk.Controls.Calendar.Cell} Selected Gets or sets the current selected cell of the month.
 * @property {vDesk.Controls.Calendar.Cell} Now Gets or sets the cell of the month representing the current day.
 * @property {Array<vDesk.Controls.Calendar.Cell>} Cells Gets the cells of the month.
 * @implements {vDesk.Controls.Calendar.IView}
 * @memberOf vDesk.Controls.Calendar.View
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.View.Month = function Month(Adaptor) {

    /**
     * The title of the Month.
     * @type null|String
     */
    let Title = null;

    /**
     * The current date of the month.
     * @type Date
     */
    let Date = null;

    /**
     * The currently selected cell of the month.
     * @type vDesk.Controls.Calendar.Cell
     */
    let Selected = null;

    /**
     * The cell of the month representing the current day.
     * @type vDesk.Controls.Calendar.Cell
     */
    let Now = null;

    /**
     * The cells of the month.
     * @type Array<vDesk.Controls.Calendar.Cell>
     */
    const Cells = [];

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Title:    {
            enumerable: true,
            get:        () => Title,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                Title = Value;
            }
        },
        Date:     {
            enumerable: true,
            get:        () => Date,
            set:        Value => {
                Ensure.Property(Value, window.Date, "Title");
                Date = Value;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Calendar.Cell, "Selected");
                Selected.Selected = false;
                Selected = Value;
                Selected.Selected = true;
            }
        },
        Now:      {
            enumerable: true,
            get:        () => Now,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Calendar.Cell, "Now");
                Now.Now = false;
                Now = Value;
                Now.Now = true;
            }
        },
        Cells:    {
            enumerable: true,
            get:        () => Cells
        }
    });

    /**
     * Creates a title according to a specified date.
     * @param {Date} Date The date to create a title from.
     * @return {String} The formatted title according to the specified date.
     */
    const CreateTitle = Date => `${Adaptor.MonthAsString(Date.getMonth())} ${Date.getFullYear()}`;

    /**
     * Displays the days of a specified month.
     * @param {Date} Month The month to show the days of.
     * @fires vDesk.Controls.Calendar.IView#datechanged
     */
    this.Show = function(Month) {
        Ensure.Parameter(Month, window.Date, "Month");

        Date = Month.clone();

        //Reset selection.
        Selected.Selected = false;
        Selected = null;

        //Reset today.
        if(Now !== null) {
            Now.Now = false;
            Now = null;
        }

        //Week 2 - 5
        Month.setDate(1);
        Month.setHours(0);
        Month.setMinutes(0);
        Month.setSeconds(0);
        Month.setMilliseconds(0);
        if(Month.getISODay() > 0) {
            Month.setDate(Month.getDate() - Month.getISODay());
        } else if(Month.getISODay() === 0) {
            Month.setDate(Month.getDate() - 7);
        }

        Cells.forEach(Cell => {
            //Update the date of the cell.
            Cell.Date = Month.clone();
            Cell.Text = Month.getDate().toString();

            //Check if the cell's date matches the current day.
            if(vDesk.Controls.Calendar.Today.getDate() === Month.getDate()
               && vDesk.Controls.Calendar.Today.getMonth() === Month.getMonth()
               && vDesk.Controls.Calendar.Today.getFullYear() === Month.getFullYear()) {
                Now = Cell;
                Now.Now = true;
            }

            //Select the given day.
            if(Date.getDate() === Month.getDate() && Date.getMonth() === Month.getMonth()) {
                Selected = Cell;
                Selected.Selected = true;
            }

            //Check if the cells date is whether in the previous or next month.
            Cell.Outer = Month.getMonth() !== Date.getMonth();

            //Increment day for iteration.
            Month.setDate(Month.getDate() + 1);
        });

        //Set the title of the month.
        Title = CreateTitle(Date);

        //Notify the change of the view.
        new vDesk.Events.BubblingEvent("datechanged", {
            sender: this,
            date:   Date.clone()
        }).Dispatch(Control);
    };

    /**
     * Displays and layouts all Events of the view.
     * @param {Array<vDesk.Controls.Calendar.IEvent>} [Events=[]] The Events to display.
     * @todo Store Events in "Rows" array and check for intersection.
     */
    this.Display = function(Events = []) {
        window.requestAnimationFrame(() => {
            const Rows = [
                [],
                [],
                [],
                [],
                [],
            ];
            Cells.forEach(Cell => {
                while(Cell.Control.lastChild.nodeType !== Node.TEXT_NODE) {
                    Cell.Control.removeChild(Cell.Control.lastChild);
                }
            });
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
                //Group the Events according to their start- and end date.
                .forEach(Event => {
                    let Offset = 20;
                    Rows.some((Row, Index) => {
                        if(!Row.some(PlacedEvent => PlacedEvent.CollidesWith(Event))) {
                            Offset = (Index + 1) * 20;
                            Row.push(Event);
                            return true;
                        }
                        return false;
                    });

                    let Placed = false;
                    const Events = [];
                    Cells.filter(
                        Cell => Event.Start.getMonth() === Cell.Date.getMonth()
                                && Event.Start.getDate() === Cell.Date.getDate()
                                || Event.Start <= Cell.Date
                    )
                        .filter(Cell => Event.End >= Cell.Date)
                        .forEach(Cell => {
                            //Check for non colliding Events.
                            const Length = Cell.Control.childNodes.length * 20;
                            if(Length > Offset) {
                                Offset = Length;
                            }
                            const FakeEvent = document.createElement("div");
                            FakeEvent.className = "Event";
                            FakeEvent.addEventListener(
                                "contextmenu",
                                E => {
                                    E.preventDefault();
                                    E.stopPropagation();
                                    new vDesk.Events.BubblingEvent(
                                        "context",
                                        {
                                            sender: Event,
                                            x:      E.pageX,
                                            y:      E.pageY
                                        }
                                    ).Dispatch(Control);
                                }
                            );
                            FakeEvent.addEventListener(
                                "click",
                                e => {
                                    e.stopPropagation();
                                    new vDesk.Events.BubblingEvent("select", {sender: Event}).Dispatch(Control);
                                }
                            );
                            //Apply the title only on the first fake Event.
                            if(!Placed) {
                                FakeEvent.textContent = Event.Title;
                                Placed = true;
                            }
                            FakeEvent.title = `${Event.Title} - ${Event.Control.title}`;
                            FakeEvent.style.backgroundColor = Event.Color;
                            Cell.Control.appendChild(FakeEvent);
                            Events.push(FakeEvent);
                        });
                    Events.forEach(FakeEvent => FakeEvent.style.top = `${Offset}px`);
                });
        });
    };

    /**
     * Displays the days of the next month.
     */
    this.Forward = function() {
        Date.setDate(1);
        Date.setMonth(Date.getMonth() + 1);
        this.Show(Date);
    };

    /**
     * Displays the days of the previous month.
     */
    this.Backward = function() {
        Date.setDate(1);
        Date.setMonth(Date.getMonth() - 1);
        this.Show(Date);
    };

    /**
     * Navigates one cell up within the month.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Up = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the current selected cell is at the top border.
        if((Index - 6) <= 0) {
            Cells[Index].Date.setMonth(Cells[Index].Date.getMonth() - 1);
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Cells[Index].Date.clone()
            }).Dispatch(Control);
        }
        //Check if the upper cell is within the previous month
        else if(Cells[Index].Date.getMonth() !== Cells[Index - 7].Date.getMonth()) {
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Cells[Index - 7].Date.clone()
            }).Dispatch(Control);
        } else {
            Selected.Selected = false;
            Selected = Cells[Index - 7];
            Selected.Selected = true;
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * Navigates one cell down within the month.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Down = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the current selected cell is at the bottom border.
        if((Index + 6) >= Cells.length - 1) {
            Cells[Index].Date.setMonth(Cells[Index].Date.getMonth() + 1);
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Cells[Index].Date.clone()
            }).Dispatch(Control);
        }
        //Check if the lower cell is within the next month
        else if(Cells[Index].Date.getMonth() !== Cells[Index + 7].Date.getMonth()) {
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Cells[Index + 7].Date.clone()
            }).Dispatch(Control);
        } else {
            Selected.Selected = false;
            Selected = Cells[Index + 7];
            Selected.Selected = true;
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * Navigates one cell left within the month.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Left = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the previous cell is in the previous month.
        if(Cells[Index].Date.getMonth() !== Cells[Index - 1].Date.getMonth()) {
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Cells[Index - 1].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the previous cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index - 1];
            Selected.Selected = true;
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * Navigates one cell right within the month.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Right = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the next cell is in the next month.
        if(Cells[Index].Date.getMonth() !== Cells[Index + 1].Date.getMonth()) {
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Cells[Index + 1].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the next cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index + 1];
            Selected.Selected = true;
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Month";

    /**
     * The header row of the Month representing the days of the current displayed month.
     * @type {HTMLUListElement}
     */
    const HeaderRow = document.createElement("ul");
    HeaderRow.className = "Header Foreground BorderLight";

    //Setup cells for monday to saturday.
    for(let i = 1; i <= 6; i++) {
        const HeaderCell = document.createElement("li");
        HeaderCell.textContent = Adaptor.DayAsString(i);
        HeaderCell.className = "Cell Font Light BorderLight";
        HeaderRow.appendChild(HeaderCell);
    }
    //Setup cell for sunday.
    const HeaderCell = document.createElement("li");
    HeaderCell.textContent = Adaptor.DayAsString(0);
    HeaderCell.className = "Cell Font Light BorderLight";
    HeaderRow.appendChild(HeaderCell);
    Control.appendChild(HeaderRow);

    //Setup cells.
    for(let i = 0; i <= 5; i++) {
        const Row = document.createElement("ul");
        Row.className = "Row BorderLight";
        for(let a = 0; a <= 6; a++) {
            const Cell = new vDesk.Controls.Calendar.Cell();
            Cells.push(Cell);
            Cell.Type = "day";
            Row.appendChild(Cell.Control);
        }
        Control.appendChild(Row);
    }
    //Mark the first cell as today and selected initially.
    Selected = Cells[0];
    Cells[0].Selected = true;
    Now = Cells[0];
    Cells[0].Now = true;
};

vDesk.Controls.Calendar.View.Month.Implements(vDesk.Controls.Calendar.IView);