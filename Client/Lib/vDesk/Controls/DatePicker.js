"use strict";
/**
 * Fired if the value of the DatePicker has been updated.
 * @event vDesk.Controls.DatePicker#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.DatePicker} detail.sender The current instance of the DatePicker.
 * @property {Object} detail.date The date of the DatePicker.
 * @property {Date} detail.date.previous The date of the DatePicker before the 'update' event has occurred.
 * @property {Date} detail.date.current The date of the DatePicker after the 'update' event has occurred.
 */
/**
 * Fired if the value of the DatePicker has been cleared.
 * @event vDesk.Controls.DatePicker#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.DatePicker} detail.sender The current instance of the DatePicker.
 */
/**
 * Initializes a new instance of the DatePicker class.
 * @class Represents a DatePicker control.
 * @param {Date} [Date=Date] Initializes the DatePicker with the specified date.
 * @param {Boolean} [Enabled=true] Flag indicating whether the DatePicker is enabled.
 * @property {HTMLElement} Control Returns the underlying DOM-Node.
 * @property {Date} Date Gets or sets the Date of the DatePicker.
 * @property {Date} Value Gets or sets the Date value of the DatePicker.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the DatePicker is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.DatePicker = function DatePicker(Date = new window.Date(), Enabled = true) {
    Ensure.Parameter(Date, window.Date, "Date");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the calendar of the DatePicker is displayed.
     * @type {Boolean}
     */
    let Expanded = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Date:    {
            enumerable: true,
            get:        () => Date,
            set:        Value => {
                Ensure.Property(Value, window.Date, "Date");
                Date = Value;
                Calendar.Show(Value, vDesk.Controls.Calendar.View.Month);
                TextBox.value = Value.toLocaleDateString(vDesk.User.Locale);
            }
        },
        Value:   {
            enumerable: true,
            get:        () => this.Date,
            set:        Value => this.Date = Value
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Button.disabled = !Value;
                TextBox.disabled = !Value;
                if(!Value) {
                    window.removeEventListener("click", OnClick, false);
                    vDesk.Visual.Animation.FadeOut(Calendar.Control, 150, () => Calendar.Control.style.display = "none");
                    Button.textContent = "▼";
                    Expanded = false;
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and hides the DatePicker if a click occurred outside of the DatePicker.
     * @param {CustomEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        ToggleCalendar();
    };

    /**
     * Eventhandler that listens on the 'input' event and validates the value of the textbox for a valid datestring.
     * @fires vDesk.Controls.DatePicker#update
     * @fires vDesk.Controls.DatePicker#clear
     */
    const OnInput = () => {
        if(TextBox.value.length > 0) {
            //Check if the entered value matches the date pattern and differs from the original date.
            const Timestamp = window.Date.parse(TextBox.value);
            if(!Number.isNaN(Timestamp)) {
                const Previous = Date.clone();
                //Update selected date.
                Date.setTime(Timestamp);
                Calendar.Show(Date, vDesk.Controls.Calendar.View.Month);

                //Inform about update.
                if(
                    Date.getDate() !== Previous.getDate()
                    || Date.getMonth() !== Previous.getMonth()
                    || Date.getFullYear() !== Previous.getFullYear()
                ) {
                    new vDesk.Events.BubblingEvent("update", {
                        sender: this,
                        date:   {
                            previous: Previous,
                            current:  Date
                        }
                    }).Dispatch(Control);
                }
                TextBox.classList.remove("Error");
            } else {
                TextBox.classList.add("Error");
            }
        } else {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            TextBox.classList.remove("Error");
        }
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Controls.Calendar.Cell#event:select
     * @fires vDesk.Controls.DatePicker#update
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        Event.stopPropagation();
        //Check if the selected cell of the calendar, is a daycell.
        if(Event.detail.sender.Type === "day") {
            TextBox.value = Event.detail.sender.Date.toLocaleDateString();
            TextBox.className = "TextBox BorderDark Font";
            //Check if a different Date has been selected.
            if(
                Date.getDate() !== Event.detail.sender.Date.getDate()
                || Date.getMonth() !== Event.detail.sender.Date.getMonth()
                || Date.getFullYear() !== Event.detail.sender.Date.getFullYear()
            ) {
                const Previous = Date.clone();

                //Update selected date.
                Date.setTime(Event.detail.sender.Date.getTime());

                //Inform about update.
                new vDesk.Events.BubblingEvent("update", {
                    sender: this,
                    date:   {
                        previous: Previous,
                        current:  Date
                    }
                }).Dispatch(Control)
            }
            ToggleCalendar();
        } else {
            Calendar.Downward();
        }
    };

    /**
     * Toggles the visibility of the Calendar of the DatePicker.
     */
    const ToggleCalendar = function() {
        if(Expanded) {
            vDesk.Visual.Animation.FadeOut(Calendar.Control, 150, () => Calendar.Control.style.display = "none");
            window.removeEventListener("click", OnClick, false);
            Button.textContent = "▼";
            Expanded = false;
        } else {
            vDesk.Visual.Animation.FadeIn(Calendar.Control, 150, () => Calendar.Control.style.display = "block");
            Calendar.Control.style.display = "";
            Button.textContent = "▲";
            Expanded = true;
            window.addEventListener("click", OnClick, false);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "DatePicker Font";
    Control.addEventListener("click", Event => Event.stopPropagation(), false);

    /**
     * The TextBox of the DatePicker.
     * @type {HTMLInputElement}
     */
    const TextBox = document.createElement("input");
    TextBox.className = "TextBox";
    TextBox.type = "text";
    TextBox.value = Date.toLocaleDateString(vDesk.User.Locale);
    TextBox.addEventListener("input", OnInput, false);
    TextBox.disabled = !Enabled;

    /**
     * The button of the DatePicker.
     * @type {HTMLButtonElement}
     */
    const Button = document.createElement("button");
    Button.className = "Toggle Button";
    Button.value = "▼";
    Button.textContent = "▼";
    Button.addEventListener("click", ToggleCalendar, false);
    Button.disabled = !Enabled;

    /**
     * The Calendar of the DatePicker.
     * @type {vDesk.Controls.Calendar}
     */
    const Calendar = new vDesk.Controls.Calendar(
        Date,
        [
            vDesk.Controls.Calendar.View.Month,
            vDesk.Controls.Calendar.View.Year,
            vDesk.Controls.Calendar.View.Decade
        ],
        vDesk.Controls.Calendar.View.Month,
        vDesk.Controls.DatePicker.Days,
        vDesk.Controls.DatePicker.Months,
        false
    );
    Calendar.Control.className = "Picker";
    Calendar.Control.style.display = "none";
    Calendar.Show(Date, vDesk.Controls.Calendar.View.Month);
    Calendar.Control.addEventListener("select", OnSelect, false);

    Control.appendChild(TextBox);
    Control.appendChild(Button);
    Control.appendChild(Calendar.Control);
};

/**
 * RegEx checking for non numeric characters.
 * @type {RegExp}
 * @constant
 */
vDesk.Controls.DatePicker.NonDigitRegex = /([^0-9.\/\|]+)/;

Object.defineProperties(vDesk.Controls.DatePicker, {
    /**
     * Enumeration of locale-specific daynames.
     * @constant
     * @type {Array<String>}
     * @name vDesk.Controls.DatePicker.Days
     */
    Days:   {
        enumerable: true,
        get:        () => [
            vDesk.Locale["Calendar"]["SundayShort"],
            vDesk.Locale["Calendar"]["MondayShort"],
            vDesk.Locale["Calendar"]["TuesdayShort"],
            vDesk.Locale["Calendar"]["WednesdayShort"],
            vDesk.Locale["Calendar"]["ThursdayShort"],
            vDesk.Locale["Calendar"]["FridayShort"],
            vDesk.Locale["Calendar"]["SaturdayShort"]
        ]
    },
    /**
     * Enumeration of locale-specific monthnames.
     * @constant
     * @type {Array<String>}
     * @name vDesk.Controls.DatePicker.Months
     */
    Months: {
        enumerable: true,
        get:        () => [
            vDesk.Locale["Calendar"]["JanuaryShort"],
            vDesk.Locale["Calendar"]["FebruaryShort"],
            vDesk.Locale["Calendar"]["MarchShort"],
            vDesk.Locale["Calendar"]["AprilShort"],
            vDesk.Locale["Calendar"]["MayShort"],
            vDesk.Locale["Calendar"]["JuneShort"],
            vDesk.Locale["Calendar"]["JulyShort"],
            vDesk.Locale["Calendar"]["AugustShort"],
            vDesk.Locale["Calendar"]["SeptemberShort"],
            vDesk.Locale["Calendar"]["OctoberShort"],
            vDesk.Locale["Calendar"]["NovemberShort"],
            vDesk.Locale["Calendar"]["DecemberShort"]
        ]
    }
});