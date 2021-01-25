"use strict";
/**
 * Fired if the value of the TimePicker has been updated.
 * @event vDesk.Controls.TimePicker#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.TimePicker} detail.sender The current instance of the TimePicker.
 * @property {Object} detail.time The time of the TimePicker.
 * @property {Date} detail.time.previous The time of the TimePicker before the 'update' event has occurred.
 * @property {Date} detail.time.current The time of the TimePicker after the 'update' event has occurred.
 */
/**
 * Fired if the value of the TimePicker has been cleared.
 * @event vDesk.Controls.TimePicker#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.TimePicker} detail.sender The current instance of the TimePicker.
 */
/**
 * Initializes a new instance of the TimePicker class.
 * @class Represents a TimePicker control.
 * @param {Date} [Time=Date] Initializes the TimePicker with the specified time.
 * @param {Boolean} [Enabled=true] Flag indicating whether the TimePicker is enabled.
 * @property {HTMLElement} Control Returns the underlying DOM-Node.
 * @property {Date} Time Gets or sets the time of the TimePicker.
 * @property {Date} Value Gets or sets the time of the TimePicker. This is a convenience property.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the TimePicker is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.TimePicker = function TimePicker(Time = new window.Date(), Enabled = true) {
    Ensure.Parameter(Time, Date, "Time");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Regex for filtering non digit chars.
     * @type {RegExp}
     */
    const NonDigitRegex = /([^0-9:\/\|]+)/;

    /**
     * Flag indicating whether the TimePicker is expanded.
     * @type {Boolean}
     */
    let Expanded = false;

    /**
     * Flag indicating whether the time of the TimePicker has been changed..
     * @type {Boolean}
     */
    let Changed = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Time:    {
            enumerable: true,
            get:        () => Time,
            set:        Value => {
                Ensure.Property(Value, Date, "Time");
                Time = Value;
                Time = Value.clone();
                TextBox.value = `${Zerofill(Value.getHours())}:${Zerofill(Value.getMinutes())}`;
                HourRow.textContent = Zerofill(Value.getHours());
                MinuteRow.textContent = Zerofill(Value.getMinutes());
            }
        },
        Value:   {
            enumerable: true,
            get:        () => this.Time,
            set:        Value => this.Time = Value
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                TextBox.disabled = !Value;
                Button.disabled = !Value;
                if(!Value) {
                    window.removeEventListener("click", OnClick, false);
                    vDesk.Visual.Animation.FadeOut(Picker, 150, () => Picker.style.display = "none");
                    Button.textContent = "▼";
                    Expanded = false;
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and hides the picker if a click occurred outside of the TimePicker.
     * @param {MouseEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        Toggle();
    };

    /**
     * Eventhandler that listens on the 'input' event and validates the value of the TextBox for a valid time string.
     * @fires vDesk.Controls.TimePicker#update
     * @fires vDesk.Controls.TimePicker#clear
     */
    const OnInput = () => {
        //Discard non digit/dot chars.
        TextBox.value = TextBox.value.replace(NonDigitRegex, "");
        //Check if the TextBox is not empty.
        if(TextBox.value.length > 0) {
            //Check if the entered value matches the time pattern.
            const Matches = vDesk.Utils.Expression.Time.exec(TextBox.value);
            if(Matches !== null) {
                const Previous = Time.clone();
                //Update time.
                Time.setHours(Number.parseInt(Matches[1]));
                Time.setMinutes(Number.parseInt(Matches[2]));
                HourRow.textContent = Zerofill(Time.getHours());
                MinuteRow.textContent = Zerofill(Time.getMinutes());

                //Check if the entered value differs from the original time.
                if(
                    Time.getHours() !== Previous.getHours()
                    || Time.getMinutes() !== Previous.getMinutes()
                ) {
                    new vDesk.Events.BubblingEvent("update", {
                        sender: this,
                        time:   {
                            previous: Previous,
                            current:  Time
                        }
                    }).Dispatch(Control);
                }
                TextBox.classList.remove("Error");
            }
            TextBox.classList.toggle("Error", Matches === null);
        } else {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            TextBox.classList.remove("Error");
        }
    };

    /**
     * Toggles the visibility of the picker of the TimePicker.
     */
    const Toggle = function() {
        if(Expanded) {
            vDesk.Visual.Animation.FadeOut(Picker, 150, () => Picker.style.display = "none");
            window.removeEventListener("click", OnClick);
            Button.textContent = "▼";
            Expanded = false;

            TextBox.value = `${Zerofill(Time.getHours())}:${Zerofill(Time.getMinutes())}`;

            if(Changed) {
                Changed = false;
                const Previous = Time.clone();
                //Inform about update.
                new vDesk.Events.BubblingEvent("update", {
                    sender: this,
                    time:   {
                        previous: Previous,
                        current:  Time.clone()
                    }
                }).Dispatch(Control);
            }
        } else {
            vDesk.Visual.Animation.FadeIn(Picker, 150, () => Picker.style.display = "block");
            Picker.style.display = "";
            Button.textContent = "▲";
            Expanded = true;
            window.addEventListener("click", OnClick);
        }
    };

    /**
     * Increments the hour of the current selected time.
     */
    const IncrementHour = function() {
        Time.setHours(Time.getHours() + 1);
        HourRow.textContent = Zerofill(Time.getHours());
        Changed = true;
    };

    /**
     * Decrements the hour of the current selected time.
     */
    const DecrementHour = function() {
        Time.setHours(Time.getHours() - 1);
        HourRow.textContent = Zerofill(Time.getHours());
        Changed = true;
    };

    /**
     * Increments the minute of the current selected time.
     */
    const IncrementMinute = function() {
        Time.setMinutes(Time.getMinutes() === 59 ? 0 : Time.getMinutes() + 1);
        MinuteRow.textContent = Zerofill(Time.getMinutes());
        Changed = true;
    };

    /**
     * Decrements the minute of the current selected time.
     */
    const DecrementMinute = function() {
        Time.setMinutes(Time.getMinutes() === 0 ? 59 : Time.getMinutes() - 1);
        MinuteRow.textContent = Zerofill(Time.getMinutes());
        Changed = true;
    };

    /**
     * Fills a number below 10 with an additional 0 digit and returns it as a string.
     * @param {Number} Value The value to fill with a zero digit.
     * @return {String} The 'zerofilled' string of the passed value.
     */
    const Zerofill = Value => `${Value < 10 ? "0" : ""}${Value.toString()}`;

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "TimePicker Font";
    Control.addEventListener("click", Event => Event.stopPropagation(), false);

    /**
     * The TextBox of the TimePicker.
     * @type {HTMLInputElement}
     */
    const TextBox = document.createElement("input");
    TextBox.className = "TextBox";
    TextBox.type = "text";
    TextBox.value = `${Zerofill(Time.getHours())}:${Zerofill(Time.getMinutes())}`;
    TextBox.disabled = !Enabled;
    TextBox.addEventListener("input", OnInput, false);

    /**
     * The toggle button of the TimePicker.
     * @type {HTMLButtonElement}
     */
    const Button = document.createElement("button");
    Button.className = "Toggle Button";
    Button.value = "▼";
    Button.textContent = "▼";
    Button.disabled = !Enabled;
    Button.addEventListener("click", Toggle, false);

    Control.appendChild(TextBox);
    Control.appendChild(Button);

    /**
     * The picker of the TimePicker.
     * @type {HTMLDivElement}
     */
    const Picker = document.createElement("div");
    Picker.className = "Picker BorderDark Background";
    Picker.style.display = "none";

    /**
     * The hour selector of the TimePicker.
     * @type {HTMLDivElement}
     */
    const HourSelect = document.createElement("div");
    HourSelect.className = "Hour";

    /**
     * The decrement button of the hour selector.
     * @type {HTMLButtonElement}
     */
    const HourDecrementButton = document.createElement("button");
    HourDecrementButton.textContent = "▲";
    HourDecrementButton.className = "Button";
    HourDecrementButton.addEventListener("click", DecrementHour, false);

    /**
     * The hour row of the hour selector.
     * @type {HTMLDivElement}
     */
    const HourRow = document.createElement("div");
    HourRow.className = "Row";
    HourRow.textContent = Zerofill(Time.getHours());
    HourRow.addEventListener("wheel", Event => Event.deltaY > 0 ? IncrementHour() : DecrementHour(), false);

    /**
     * The increment button of the hour selector.
     * @type {HTMLButtonElement}
     */
    const HourIncrementButton = document.createElement("button");
    HourIncrementButton.textContent = "▼";
    HourIncrementButton.className = "Button";
    HourIncrementButton.addEventListener("click", IncrementHour, false);

    HourSelect.appendChild(HourDecrementButton);
    HourSelect.appendChild(HourRow);
    HourSelect.appendChild(HourIncrementButton);

    /**
     * The minute selector of the TimePicker.
     * @type {HTMLDivElement}
     */
    const MinuteSelect = document.createElement("div");
    MinuteSelect.className = "Minute";

    /**
     * The decrement button of the minute selector.
     * @type {HTMLButtonElement}
     */
    const MinuteDecrementButton = document.createElement("button");
    MinuteDecrementButton.textContent = "▲";
    MinuteDecrementButton.className = "Button";
    MinuteDecrementButton.addEventListener("click", DecrementMinute, false);

    /**
     * The minute row of the minute selector.
     * @type {HTMLDivElement}
     */
    const MinuteRow = document.createElement("div");
    MinuteRow.className = "Row";
    MinuteRow.textContent = Zerofill(Time.getMinutes());
    MinuteRow.addEventListener("wheel", Event => Event.deltaY > 0 ? IncrementMinute() : DecrementMinute(), false);

    /**
     * The increment button of the minute selector.
     * @type {HTMLButtonElement}
     */
    const MinuteIncrementButton = document.createElement("button");
    MinuteIncrementButton.textContent = "▼";
    MinuteIncrementButton.className = "Button";
    MinuteIncrementButton.addEventListener("click", IncrementMinute, false);

    MinuteSelect.appendChild(MinuteDecrementButton);
    MinuteSelect.appendChild(MinuteRow);
    MinuteSelect.appendChild(MinuteIncrementButton);

    Picker.appendChild(HourSelect);
    Picker.appendChild(MinuteSelect);
    Control.appendChild(Picker);
};