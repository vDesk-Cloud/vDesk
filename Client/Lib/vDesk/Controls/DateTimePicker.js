"use strict";
/**
 * Fired if the value of the DateTimePicker has been updated.
 * @event vDesk.Controls.DateTimePicker#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.DateTimePicker} detail.sender The current instance of the DateTimePicker.
 * @property {Object} detail.datetime The datetime of the DateTimePicker.
 * @property {Date} detail.datetime.previous The datetime of the DateTimePicker before the 'update' event has occurred.
 * @property {Date} detail.datetime.current The datetime of the DateTimePicker after the 'update' event has occurred.
 */
/**
 * Fired if the value of the DateTimePicker has been cleared.
 * @event vDesk.Controls.DateTimePicker#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.DateTimePicker} detail.sender The current instance of the DateTimePicker.
 */
/**
 * Initializes a new instance of the DateTimePicker class.
 * @class Represents a DateTimePicker control.
 * @param {Date} [DateTime=vDesk.Controls.Calendar.Today] Initializes the DateTimePicker with the specified datetime.
 * @param {Boolean} [Enabled=true] Flag indicating whether the DateTimePicker is enabled.
 * @property {HTMLElement} Control Returns the underlying DOM-Node.
 * @property {Date} DateTime Gets or sets the selected datetime of the DateTimePicker.
 * @property {Date} Value Gets or sets the selected datetime of the DateTimePicker. This is a convenience property.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the DateTimePicker is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.DateTimePicker = function DateTimePicker(DateTime = vDesk.Controls.Calendar.Today, Enabled = true) {
    Ensure.Parameter(DateTime, Date, "DateTime");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the TextBox of the DatePicker of the DateTimePicker is empty.
     * @type {Boolean}
     */
    let DatePickerEmpty = false;

    /**
     * Flag indicating whether the TextBox of the TimePicker of the DateTimePicker is empty.
     * @type {Boolean}
     */
    let TimePickerEmpty = false;

    Object.defineProperties(this, {
        Control:  {
            get: () => Control
        },
        DateTime: {
            get: () => DateTime,
            set: Value => {
                DateTime = Value;
                DatePicker.Date = Value;
                TimePicker.Time = Value;
            }
        },
        Value:    {
            enumerable: true,
            get:        () => this.DateTime,
            set:        Value => this.DateTime = Value
        },
        Enabled:  {
            get: () => DatePicker.Enabled && TimePicker.Enabled,
            set: Value => {
                DatePicker.Enabled = Value;
                TimePicker.Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens ont the 'update' event.
     * @listens vDesk.Controls.DatePicker#event:update
     * @fires vDesk.Controls.DateTimePicker#update
     * @param {CustomEvent} Event
     */
    const OnUpdateDatePicker = Event => {
        Event.stopPropagation();
        DateTime = Event.detail.date.current;
        new vDesk.Events.BubblingEvent("update", {
            sender:   this,
            datetime: {
                previous: Event.detail.date.previous,
                current:  Event.detail.date.current
            }
        }).Dispatch(Control);
        DatePickerEmpty = false;
    };

    /**
     * Eventhandler that listens on the 'update' event
     * @listens vDesk.Controls.TimePicker#event:update
     * @fires vDesk.Controls.DateTimePicker#update
     * @param {CustomEvent} Event
     */
    const OnUpdateTimePicker = Event => {
        Event.stopPropagation();
        DateTime = Event.detail.time.current;
        new vDesk.Events.BubblingEvent("update", {
            sender:   this,
            datetime: {
                previous: Event.detail.time.previous,
                current:  Event.detail.time.current
            }
        }).Dispatch(Control);
        TimePickerEmpty = false;
    };

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.DatePicker#event:clear
     * @fires vDesk.Controls.DateTimePicker#clear
     * @param {CustomEvent} Event
     */
    const OnClearDatePicker = Event => {
        Event.stopPropagation();
        DatePickerEmpty = true;
        if(DatePickerEmpty && TimePickerEmpty) {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
        }
    };

    /**
     * Eventhandler that listens on the 'clear' event.
     * @listens vDesk.Controls.TimePicker#event:clear
     * @fires vDesk.Controls.DateTimePicker#clear
     * @param {CustomEvent} Event
     */
    const OnClearTimePicker = Event => {
        Event.stopPropagation();
        TimePickerEmpty = true;
        if(TimePickerEmpty && DatePickerEmpty) {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "DateTimePicker";

    /**
     * The DatePicker of the DateTimePicker.
     * @type {vDesk.Controls.DatePicker}
     */
    const DatePicker = new vDesk.Controls.DatePicker(DateTime, Enabled);
    DatePicker.Control.addEventListener("update", OnUpdateDatePicker, false);
    DatePicker.Control.addEventListener("clear", OnClearDatePicker, false);

    /**
     * The TimePicker of the DateTimePicker.
     * @type {vDesk.Controls.TimePicker}
     */
    const TimePicker = new vDesk.Controls.TimePicker(DateTime, Enabled);
    TimePicker.Control.addEventListener("update", OnUpdateTimePicker, false);
    TimePicker.Control.addEventListener("clear", OnClearTimePicker, false);

    Control.appendChild(DatePicker.Control);
    Control.appendChild(TimePicker.Control);
};