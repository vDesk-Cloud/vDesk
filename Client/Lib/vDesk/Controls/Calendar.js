"use strict";
/**
 * @typedef {Object} Adaptor An adaptor which grants limited access to members and methods of an vDesk.Controls.Calendar.
 * @property {Array<vDesk.Controls.Calendar.IView>} Views Gets or sets the views of the calendar.
 * @property {vDesk.Controls.Calendar.IView} CurrentView Gets the currently active view of the calendar.
 * @property {DayAsString} DayAsString Gets the locale specific name of a speficied day of the week starting with sunday as the first day in the week.
 * @property {MonthAsString} MonthAsString Gets the locale specific name of a speficied month of the year starting with january as the first month in the year.
 */
/**
 * Gets the locale specific name of a speficied day of the week starting with sunday as the first day in the week.
 * @callback DayAsString
 * @param {Number} Day The day of the week to get the name of.
 * @return {String} The locale specific name of the specified day of the week.
 */
/**
 * Gets the locale specific name of a speficied month of the year starting with january as the first month in the year.
 * @callback MonthAsString
 * @param {Number} Month The month of the year to get the name of.
 * @return {String} The locale specific name of the specified month of the year.
 */
/**
 * Initializes a new instance of the Calendar class.
 * @class Represents a calendar, capable of displaying specific views and navigating through them.
 * @param {Date} [Date=vDesk.Controls.Calendar.Today] Initializes the Calendar with the specified Date.
 * @param {Array<vDesk.Controls.Calendar.IView>} [Views=vDesk.Controls.Calendar.DefaultViews] Specifies a list of IViews the calendar will display.
 * @param {vDesk.Controls.Calendar.IView} [DefaultView=vDesk.Controls.Calendar.View.Month] The default view of the calendar.
 * @param {Array<String>} [Days=vDesk.Controls.Calendar.Days] Specifies the names of the days of the calendar.
 * @param {Array<String>} [Months=vDesk.Controls.Calendar.Months] Specifies the names of the months of the calendar.
 * @param {Boolean} [CaptureKeys=true] Flag indicating whether the calendar captures keyboardinput.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Controls.Calendar.IView>} Views Gets or sets the views of the calendar.
 * @property {String} Title Gets or sets the title of the calendar.
 * @property {Date} Today Gets the date of the current day.
 * @property {vDesk.Controls.Calendar.IView} CurrentView Gets the currently active view of the calendar.
 * @property {Boolean} CaptureKeys Gets or sets a value indicating whether the calendar captures keyboardinput.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar = function Calendar(
    Date        = vDesk.Controls.Calendar.Today,
    Views       = vDesk.Controls.Calendar.DefaultViews,
    DefaultView = vDesk.Controls.Calendar.View.Month,
    Days        = vDesk.Controls.Calendar.Days,
    Months      = vDesk.Controls.Calendar.Months,
    CaptureKeys = true
) {
    Ensure.Parameter(Date, window.Date, "Date");
    Ensure.Parameter(Views, Array, "Views");
    Ensure.Parameter(DefaultView, Type.Function, "DefaultView");
    Ensure.Parameter(Days, Array, "Days");
    Ensure.Parameter(Months, Array, "Months");
    Ensure.Parameter(CaptureKeys, Type.Boolean, "CaptureKeys");

    //Using.
    const Animation = vDesk.Visual.Animation;

    /**
     * The current active view of the calendar.
     * @type {vDesk.Controls.Calendar.IView}
     */
    let CurrentView = null;

    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        Views:       {
            enumerable: true,
            get:        () => Views,
            set:        Value => {
                Ensure.Property(Value, Array, "Views");
                Views = Value;
            }
        },
        Title:       {
            enumerable: true,
            get:        () => CenterCell.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                CenterCell.textContent = Value;
            }
        },
        Today:       {
            enumerable: true,
            get:        () => vDesk.Controls.Calendar.Today
        },
        CurrentView: {
            enumerable: true,
            get:        () => CurrentView
        },
        CaptureKeys: {
            enumerable: true,
            get:        () => CaptureKeys,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "CaptureKeys");
                CaptureKeys = Value;
                if(CaptureKeys) {
                    window.addEventListener("keydown", OnKeyDown, true);
                } else {
                    window.removeEventListener("keydown", OnKeyDown, true);
                }
            }
        }
    });

    /**
     * Forwards the timerange of the current view.
     */
    const Forward = function() {
        Animation.FadeOut(CurrentView.Control, 500);
        Animation.Animate(CurrentView.Control, "margin-left", 0, -(Body.offsetWidth / 2), 300, () => {
            CurrentView.Forward();
            CenterCell.textContent = CurrentView.Title;
            Animation.FadeIn(CurrentView.Control, 500);
            Animation.Animate(CurrentView.Control, "margin-left", (Body.offsetWidth / 2), 0, 300);
        });
    };

    /**
     * Backwards the timerange of the current view.
     */
    const Backward = function() {
        Animation.FadeOut(CurrentView.Control, 500);
        Animation.Animate(CurrentView.Control, "margin-left", 0, (Body.offsetWidth / 2), 300, () => {
            CurrentView.Backward();
            CenterCell.textContent = CurrentView.Title;
            Animation.FadeIn(CurrentView.Control, 500);
            Animation.Animate(CurrentView.Control, "margin-left", -(Body.offsetWidth / 2), 0, 300);
        });
    };

    /**
     * Switches to the next upper view.
     */
    const Upward = function() {
        const Index = Views.indexOf(CurrentView);
        if(Index < Views.length - 1) {
            Animation.FadeOut(CurrentView.Control, 500);
            Animation.Animate(CurrentView.Control, "margin-top", 0, (Body.offsetHeight / 2), 300, () => {
                Body.removeChild(CurrentView.Control);
                CurrentView = Views[Index + 1];
                Body.appendChild(CurrentView.Control);
                CurrentView.Show(Views[Index].Selected.Date.clone());
                CenterCell.textContent = CurrentView.Title;
                Animation.FadeIn(CurrentView.Control, 500);
                Animation.Animate(CurrentView.Control, "margin-top", -(Body.offsetHeight / 2), 0, 300);
            });
        }
    };

    /**
     * Switches to the next lower view.
     */
    const Downward = function() {
        const Index = Views.indexOf(CurrentView);
        if(Index > 0) {
            Animation.FadeOut(CurrentView.Control, 500);
            Animation.Animate(CurrentView.Control, "margin-top", 0, -(Body.offsetHeight / 2), 300, () => {
                Body.removeChild(CurrentView.Control);
                CurrentView = Views[Index - 1];
                Body.appendChild(CurrentView.Control);
                CurrentView.Show(Views[Index].Selected.Date.clone());
                CenterCell.textContent = CurrentView.Title;
                Animation.FadeIn(CurrentView.Control, 500);
                Animation.Animate(CurrentView.Control, "margin-top", (Body.offsetHeight / 2), 0, 300);
            });
        }
    };

    /**
     * Eventhandler that listens on the 'keydown' event.
     */
    const OnKeyDown = Event => {
        switch(Event.key) {
            case "ArrowLeft":
                if(Event.ctrlKey) {
                    window.requestAnimationFrame(Backward);
                } else {
                    CurrentView.Left();
                    CenterCell.textContent = CurrentView.Title;
                }
                break;
            case "ArrowRight":
                if(Event.ctrlKey) {
                    window.requestAnimationFrame(Forward);
                } else {
                    CurrentView.Right();
                    CenterCell.textContent = CurrentView.Title;
                }
                break;
            case "ArrowUp":
                if(Event.ctrlKey) {
                    window.requestAnimationFrame(Upward);
                } else {
                    CurrentView.Up();
                    CenterCell.textContent = CurrentView.Title;
                }
                break;
            case "ArrowDown":
                if(Event.ctrlKey) {
                    window.requestAnimationFrame(Downward);
                } else {
                    CurrentView.Down();
                    CenterCell.textContent = CurrentView.Title;
                }
                break;
            case "Space":
                new vDesk.Events.BubblingEvent("open", {sender: CurrentView.Selected}).Dispatch(Control);
                window.requestAnimationFrame(Downward);
                break;
            case "Enter":
                new vDesk.Events.BubblingEvent("open", {sender: CurrentView.Selected}).Dispatch(Control);
                window.requestAnimationFrame(Downward);
                break;
        }
    };

    /**
     * Listens on the previous event.
     * @listens vDesk.Controls.Calendar.IView#event:previous
     */
    const OnPrevious = Event => {
        Animation.FadeOut(Event.detail.sender.Control, 500);
        Animation.Animate(Event.detail.sender.Control, "margin-left", 0, (Body.offsetWidth / 2), 300, () => {
            Event.detail.sender.Show(Event.detail.date);
            CenterCell.textContent = Event.detail.sender.Title;
            Animation.FadeIn(Event.detail.sender.Control, 500);
            Animation.Animate(Event.detail.sender.Control, "margin-left", -(Body.offsetWidth / 2), 0, 300);
        });
    };

    /**
     * Listens on the next event.
     * @listens vDesk.Controls.Calendar.IView#event:next
     */
    const OnNext = Event => {
        Animation.FadeOut(Event.detail.sender.Control, 500);
        Animation.Animate(Event.detail.sender.Control, "margin-left", 0, -(Body.offsetWidth / 2), 300, () => {
            Event.detail.sender.Show(Event.detail.date);
            CenterCell.textContent = Event.detail.sender.Title;
            Animation.FadeIn(Event.detail.sender.Control, 500);
            Animation.Animate(Event.detail.sender.Control, "margin-left", (Body.offsetWidth / 2), 0, 300);
        });
    };

    /**
     * Listens on the 'click' event and displays the previous timerange of the current IView.
     */
    const OnClickBackwardButton = () => window.requestAnimationFrame(Backward);

    /**
     * Listens on the 'click' event and displays the next higher IView.
     */
    const OnClickCenterCell = () => window.requestAnimationFrame(Upward);

    /**
     * Listens on the 'click' event and displays the next timerange of the current IView.
     */
    const OnClickForwardButton = () => window.requestAnimationFrame(Forward);

    /**
     * Eventhandler that listens on the 'open' event.
     * @listens vDesk.Controls.Calendar.Cell#event:open
     */
    const OnOpen = Event => {
        if(Event.detail.sender instanceof vDesk.Controls.Calendar.Cell) {
            const Index = Views.indexOf(CurrentView);
            if(Index > 0) {
                Animation.FadeOut(CurrentView.Control, 500);
                Animation.Animate(CurrentView.Control, "margin-top", 0, -(Body.offsetHeight / 2), 300, () => {
                    Body.removeChild(CurrentView.Control);
                    CurrentView = Views[Index - 1];
                    Body.appendChild(CurrentView.Control);
                    CurrentView.Show(Event.detail.sender.Date);
                    CenterCell.textContent = CurrentView.Title;
                    Animation.FadeIn(CurrentView.Control, 500);
                    Animation.Animate(CurrentView.Control, "margin-top", (Body.offsetHeight / 2), 0, 300);
                });
            }
        }
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Controls.Calendar.Cell#event:select
     */
    const OnSelect = Event => {
        if(Event.detail.sender instanceof vDesk.Controls.Calendar.Cell) {
            if(Event.detail.sender.Outer) {
                CurrentView.Show(Event.detail.sender.Date);
            } else {
                CurrentView.Selected = Event.detail.sender;
            }
        }
    };

    /**
     * Shifts the timerange back in the current displayed view.
     */
    this.Backward = () => window.requestAnimationFrame(Backward);

    /**
     * Shifts the timerange forward in the current displayed view.
     */
    this.Forward = () => window.requestAnimationFrame(Forward);

    /**
     * Switches the view to the next upper view.
     */
    this.Upward = () => window.requestAnimationFrame(Upward);

    /**
     * Switches the view to the next lower view.
     */
    this.Downward = () => window.requestAnimationFrame(Downward);

    /**
     * Loads and displays a specified date within a specified IView.
     * @param {Date} Date The date to show within the specified IView.
     * @param {?Function} [View=null] The type of the IView to load.
     * @param {?Function} [Callback=null] A callback to execute after the specified IVIew has been loaded.
     */
    this.Show = function(Date, View = null, Callback = null) {
        Ensure.Parameter(Date, window.Date, "Date");
        Ensure.Parameter(View, Type.Function, "Date", true);
        Ensure.Parameter(Callback, Type.Function, "Callback", true);

        //Check if a view has been passed.
        if(View !== null) {
            //Check if the passed view matches the current view.
            if(CurrentView instanceof View) {
                CurrentView.Show(Date.clone());
                CenterCell.textContent = CurrentView.Title;
            } else {
                const View = Views.find(CalendarView => CalendarView instanceof View);
                //Check if the specified view exists.
                if(View === undefined) {
                    throw new ArgumentError("View not found.");
                }
                const TargetIndex = Views.indexOf(View);
                const CurrentIndex = Views.indexOf(CurrentView);
                //Check if the target view is of a higher order.
                if(TargetIndex > CurrentIndex) {
                    //If true, switch the views upwards.
                    Animation.FadeOut(CurrentView.Control, 500);
                    Animation.Animate(CurrentView.Control, "margin-top", 0, Body.offsetHeight / 2, 300, () => {
                        Body.removeChild(CurrentView.Control);
                        CurrentView = Views[TargetIndex];
                        Body.appendChild(CurrentView.Control);
                        CurrentView.Show(Date.clone());
                        CenterCell.textContent = CurrentView.Title;
                        Animation.FadeIn(CurrentView.Control, 500);
                        Animation.Animate(CurrentView.Control, "margin-top", -(Body.offsetHeight / 2), 0, 300, Callback);

                    });
                } else {
                    //otherwise, switch the views downwards.
                    Animation.FadeOut(CurrentView.Control, 500);
                    Animation.Animate(CurrentView.Control, "margin-top", 0, -(Body.offsetHeight / 2), 300, () => {
                        Body.removeChild(CurrentView.Control);
                        CurrentView = Views[TargetIndex];
                        Body.appendChild(CurrentView.Control);
                        CurrentView.Show(Date.clone());
                        CenterCell.textContent = CurrentView.Title;
                        Animation.FadeIn(CurrentView.Control, 500);
                        Animation.Animate(CurrentView.Control, "margin-top", Body.offsetHeight / 2, 0, 300, Callback);
                    });
                }
            }
        } else {
            CurrentView.Show(Date.clone());
            CenterCell.textContent = CurrentView.Title;
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Calendar";

    /**
     * The header of the calendar.
     * @type {HTMLUListElement}
     */
    const Header = document.createElement("ul");
    Header.className = "Header";

    /**
     * The left header cell of the calendar.
     * @type {HTMLLIElement}
     */
    const LeftCell = document.createElement("li");
    LeftCell.className = "Cell Previous Font Dark";

    /**
     * The navigate backwards button of the calendar.
     * @type {HTMLButtonElement}
     */
    const BackwardButton = document.createElement("button");
    BackwardButton.className = "Button Arrow Left";
    BackwardButton.textContent = "🡰";

    BackwardButton.addEventListener("click", OnClickBackwardButton, false);

    LeftCell.appendChild(BackwardButton);

    /**
     * The middle header cell of the calendar.
     * @type {HTMLLIElement}
     */
    const CenterCell = document.createElement("li");
    CenterCell.textContent = "---";
    CenterCell.className = "Cell Font Dark Current";
    CenterCell.addEventListener("click", OnClickCenterCell, false);

    /**
     * The right header cell of the calendar.
     * @type {HTMLLIElement}
     */
    const RigthCell = document.createElement("li");
    RigthCell.className = "Cell Next Font Dark";

    /**
     * The navigate forwards button of the calendar.
     * @type {HTMLButtonElement}
     */
    const ForwardButton = document.createElement("button");
    ForwardButton.textContent = "🡲";
    ForwardButton.className = "Button Arrow Right";
    ForwardButton.addEventListener("click", OnClickForwardButton, false);

    RigthCell.appendChild(ForwardButton);

    Header.appendChild(LeftCell);
    Header.appendChild(CenterCell);
    Header.appendChild(RigthCell);

    /**
     * The body of the calendar.
     * @type {HTMLDivElement}
     */
    const Body = document.createElement("div");
    Body.className = "Body BorderLight";

    /**
     * The adaptor granting access to several methods of the Calendar.
     * @type {Object}
     */
    let Adaptor = {
        DayAsString:   Day => Days[Day],
        MonthAsString: Month => Months[Month],
        Views:         Views,
        CurrentView:   CurrentView
    };

    //Loop through passed views and initialize each.
    Views = Views.map(View => new View(Adaptor));
    Views.forEach(View => {
        Ensure.Parameter(View, vDesk.Controls.Calendar.IView, "View");
        if(View instanceof DefaultView) {
            CurrentView = View;
        }
    });

    if(CurrentView === null) {
        CurrentView = Views[0];
    }

    CurrentView.Show(Date.clone());
    CenterCell.textContent = CurrentView.Title;

    Body.appendChild(CurrentView.Control);

    Body.addEventListener("previous", OnPrevious, true);
    Body.addEventListener("next", OnNext, true);
    Body.addEventListener("open", OnOpen, false);
    Body.addEventListener("select", OnSelect, false);

    Control.appendChild(Header);
    Control.appendChild(Body);

    //Check if the capturekeys flag has been set.
    if(CaptureKeys) {
        window.addEventListener("keydown", OnKeyDown, true);
    }
};

Object.defineProperties(vDesk.Controls.Calendar, {
    /**
     * Enumeration of default views.
     * @constant
     * @type {Array<vDesk.Controls.Calendar.IView>}
     * @name vDesk.Controls.Calendar.DefaultViews
     */
    DefaultViews: {
        enumerable: true,
        get:        () => [
            vDesk.Controls.Calendar.View.Day,
            vDesk.Controls.Calendar.View.Month,
            vDesk.Controls.Calendar.View.Year,
            vDesk.Controls.Calendar.View.Decade
        ]
    },
    /**
     * Enumeration of locale-specific daynames.
     * @constant
     * @type {Array<String>}
     * @name vDesk.Controls.Calendar.Days
     */
    Days:         {
        enumerable: true,
        get:        () => [
            vDesk.Locale["Calendar"]["Monday"],
            vDesk.Locale["Calendar"]["Tuesday"],
            vDesk.Locale["Calendar"]["Wednesday"],
            vDesk.Locale["Calendar"]["Thursday"],
            vDesk.Locale["Calendar"]["Friday"],
            vDesk.Locale["Calendar"]["Saturday"],
            vDesk.Locale["Calendar"]["Sunday"]
        ]
    },
    /**
     * Enumeration of locale-specific monthnames.
     * @constant
     * @type {Array<String>}
     * @name vDesk.Controls.Calendar.Months
     */
    Months:       {
        enumerable: true,
        get:        () => [
            vDesk.Locale["Calendar"]["January"],
            vDesk.Locale["Calendar"]["February"],
            vDesk.Locale["Calendar"]["March"],
            vDesk.Locale["Calendar"]["April"],
            vDesk.Locale["Calendar"]["May"],
            vDesk.Locale["Calendar"]["June"],
            vDesk.Locale["Calendar"]["July"],
            vDesk.Locale["Calendar"]["August"],
            vDesk.Locale["Calendar"]["September"],
            vDesk.Locale["Calendar"]["October"],
            vDesk.Locale["Calendar"]["November"],
            vDesk.Locale["Calendar"]["December"]
        ]
    }
});

/**
 * The date of the current day.
 * @constant
 * @type {Date}
 * @name vDesk.Controls.Calendar.Today
 */
vDesk.Controls.Calendar.Today = new Date();

//Update the Date once per day.
//@todo Subtract the current amount of seconds for the timeout.
setTimeout(
    () => setInterval(() => vDesk.Controls.Calendar.Today = new Date(), 86400000),
    86400000 - vDesk.Controls.Calendar.Today.getTime()
);