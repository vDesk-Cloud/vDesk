"use strict";
/**
 * Fired if the value of the TimeSpanPicker has been updated.
 * @event vDesk.Controls.TimeSpanPicker#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Controls.TimeSpanPicker} detail.sender The current instance of the TimeSpanPicker.
 * @property {Object} detail.timespan The timespan of the TimeSpanPicker.
 * @property {String} detail.timespan.previous The timespan of the TimeSpanPicker before the 'update' event has occurred.
 * @property {String} detail.timespan.current The timespan of the TimeSpanPicker after the 'update' event has occurred.
 */
/**
 * Fired if the value of the TimeSpanPicker has been cleared.
 * @event vDesk.Controls.TimeSpanPicker#clear
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'clear' event.
 * @property {vDesk.Controls.TimeSpanPicker} detail.sender The current instance of the TimeSpanPicker.
 */
/**
 * Initializes a new instance of the TimeSpanPicker class.
 * @class Represents a TimeSpanPicker control.
 * @param {String} [TimeSpan="00:00:00"] Initializes the TimeSpanPicker with the given timespan.
 * @param {Boolean} [Enabled=true] Flag indicating whether the TimeSpanPicker is enabled.
 * @property {HTMLElement} Control Returns the underlying DOM-Node.
 * @property {String} TimeSpan Gets or sets the selected timespan.
 * @property {Number} Hours Gets or sets the amount of hours of the selected timespan.
 * @property {Number} Minutes Gets or sets the amount of minutes of the selected timespan.
 * @property {Number} Seconds Gets or sets the amount of seconds of the selected timespan.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the TimeSpanPicker is enabled.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.TimeSpanPicker = function TimeSpanPicker(TimeSpan = "00:00:00", Enabled = true) {
    Ensure.Parameter(TimeSpan, Type.String, "TimeSpan");

    /**
     * The hours of the timespan.
     * @type {Number}
     */
    let Hours = null;

    /**
     * The minutes of the timespan.
     * @type {Number}
     */
    let Minutes = null;

    /**
     * The seconds of the timespan.
     * @type {Number}
     */
    let Seconds = null;

    /**
     * Regex for filtering non digit chars.
     * @type {RegExp}
     */
    const NonDigitRegex = /([^0-9:\/\|]+)/;

    /**
     * Flag indicating whether the TimeSpanPicker is expanded.
     * @type {Boolean}
     */
    let Expanded = false;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Hours:    {
            enumerable: true,
            get:        () => Hours,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Hours");
                Hours = value;
                TextBox.value = `${Zerofill(Value)}:${Zerofill(Minutes)}:${Zerofill(Seconds)}`;
                HourRow.textContent = Zerofill(Value);
            }
        },
        Minutes:  {
            enumerable: true,
            get:        () => Minutes,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Minutes");
                Minutes = Value;
                TextBox.value = `${Zerofill(Hours)}:${Zerofill(Value)}:${Zerofill(Seconds)}`;
                MinuteRow.textContent = Zerofill(Value);
            }
        },
        Seconds:  {
            enumerable: true,
            get:        () => Seconds,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Seconds");
                Seconds = Value;
                TextBox.value = `${Zerofill(Hours)}:${Zerofill(Minutes)}:${Zerofill(Value)}`;
                SecondRow.textContent = Zerofill(Value);
            }
        },
        TimeSpan: {
            enumerable: true,
            get:        () => TimeSpan,
            set:        Value => {
                Ensure.Property(Value, Type.String, "TimeSpan");
                if(vDesk.Utils.Expression.TimeSpan.test(Value)) {
                    TimeSpan = Value;
                    TextBox.value = Value;
                    const Matches = vDesk.Utils.Expression.TimeSpan.exec(Value);
                    Hours = Number.parseInt(Matches?.[1] ?? 0);
                    Minutes = Number.parseInt(Matches?.[2] ?? 0);
                    Seconds = Number.parseInt(Matches?.[3] ?? 0);
                    HourRow.textContent = Zerofill(Hours);
                    MinuteRow.textContent = Zerofill(Minutes);
                    SecondRow.textContent = Zerofill(Seconds);
                }
            }
        },
        Value:    {
            enumerable: true,
            get:        () => this.TimeSpan,
            set:        Value => this.TimeSpan = Value
        },
        Enabled:  {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                if(Value) {
                    TextBox.disabled = false;
                    Button.disabled = false;
                } else {
                    TextBox.disabled = true;
                    Button.disabled = true;
                    window.removeEventListener("click", OnClick, false);
                    vDesk.Visual.Animation.FadeOut(Picker, 150, () => Picker.style.display = "none");
                    Button.textContent = "▼";
                    Expanded = false;
                }
                Enabled = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and hides the picker if a click occurred outside of the TimeSpanPicker.
     * @param {MouseEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        TogglePicker();
    };

    /**
     * Validates the value of the textbox for a valid timestring.
     * @fires vDesk.Controls.TimeSpanPicker#update
     * @fires vDesk.Controls.TimeSpanPicker#clear
     */
    const OnInput = () => {
        //Discard non digit/dot chars.
        TextBox.value = TextBox.value.replace(NonDigitRegex, "");
        if(TextBox.value.length > 0) {
            //Check if the entered value matches the TimeSpan pattern.
            const Matches = vDesk.Utils.Expression.TimeSpan.exec(TextBox.value);
            if(Matches !== null) {
                //Update timespan.
                Hours = Number.parseInt(Matches[1]);
                Minutes = Number.parseInt(Matches[2]);
                Seconds = Number.parseInt(Matches[3]);

                //Check if the entered value differs from the original time.
                if(TextBox.value !== TimeSpan) {
                    const Previous = TimeSpan;

                    HourRow.textContent = Zerofill(Hours);
                    MinuteRow.textContent = Zerofill(Minutes);
                    SecondRow.textContent = Zerofill(Seconds);
                    TimeSpan = TextBox.value;

                    //Inform about update.
                    new vDesk.Events.BubblingEvent("update", {
                        sender:   this,
                        timespan: {
                            previous: Previous,
                            current:  TimeSpan
                        }
                    }).Dispatch(Control);
                }
            }
            TextBox.classList.toggle("Error", Matches === null);
        } else {
            new vDesk.Events.BubblingEvent("clear", {sender: this}).Dispatch(Control);
            TextBox.classList.remove("Error");
        }
    };

    /**
     * Toggles the visibility of the picker of the TimeSpanPicker.
     * @fires vDesk.Controls.TimeSpanPicker#update
     */
    const TogglePicker = () => {
        if(Expanded) {
            vDesk.Visual.Animation.FadeOut(Picker, 150, () => Picker.style.display = "none");
            window.removeEventListener("click", OnClick, false);
            Button.textContent = "▼";
            Expanded = false;
            TextBox.value = `${Zerofill(Hours)}:${Zerofill(Minutes)}:${Zerofill(Seconds)}`;
            if(TextBox.value !== TimeSpan) {
                let sPrevious = TimeSpan;

                TextBox.classList.toggle("Error", false);
                TimeSpan = TextBox.value;

                //Inform about update.
                new vDesk.Events.BubblingEvent("update", {
                    sender:   this,
                    timespan: {
                        previous: sPrevious,
                        current:  TimeSpan
                    }
                }).Dispatch(Control);
            } else {
                TextBox.classList.toggle("Error", false);
            }
        } else {
            vDesk.Visual.Animation.FadeIn(Picker, 150, () => Picker.style.display = "block");
            Picker.style.display = "";
            Button.textContent = "▲";
            Expanded = true;
            window.addEventListener("click", OnClick, false);
        }
    };

    /**
     * Increments the hour of the current selected time.
     */
    const IncrementHour = function() {
        if(Hours < 999) {
            Hours++;
        }
        HourRow.textContent = Zerofill(Hours);
    };

    /**
     * Decrements the hour of the current selected time.
     */
    const DecrementHour = function() {
        if(Hours > 0) {
            Hours--;
        }
        HourRow.textContent = Zerofill(Hours);
    };

    /**
     * Increments the minute of the current selected time.
     */
    const IncrementMinute = function() {
        if(Minutes < 59) {
            Minutes++;
        } else {
            IncrementHour();
            Minutes = 0;
        }
        MinuteRow.textContent = Zerofill(Minutes);
    };

    /**
     * Decrements the minute of the current selected time.
     */
    const DecrementMinute = function() {
        if(Minutes > 0) {
            Minutes--;
        } else {
            DecrementHour();
            Minutes = 59;
        }
        MinuteRow.textContent = Zerofill(Minutes);
    };

    /**
     * Increments the minute of the current selected time.
     */
    const IncrementSecond = function() {
        if(Seconds < 59) {
            Seconds++;
        } else {
            IncrementMinute();
            Seconds = 0;
        }
        SecondRow.textContent = Zerofill(Seconds);
    };

    /**
     * Decrements the minute of the current selected time.
     */
    const DecrementSecond = function() {
        if(Seconds > 0) {
            Seconds--;
        } else {
            DecrementMinute();
            Seconds = 59;
        }
        SecondRow.textContent = Zerofill(Seconds);
    };

    /**
     * Fills a number below 10 with an additional 0 digit and returns it as a string.
     * @param {Number} Value The value to fill with a zero digit.
     * @return {String} The 'zerofilled' string of the passed value.
     */
    const Zerofill = Value => `${Value < 10 ? "0" : ""}${Value.toString()}`;

    //Check if a valid timespan has been passed.
    if(!vDesk.Utils.Expression.TimeSpan.test(TimeSpan)) {
        throw new ArgumentError("Value passed to parameter 'TimeSpan' is not a valid timespan.");
    }

    const Matches = vDesk.Utils.Expression.TimeSpan.exec(TimeSpan);
    Hours = Number.parseInt(Matches[1]);
    Minutes = Number.parseInt(Matches[2]);
    Seconds = Number.parseInt(Matches[3]);

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "TimeSpanPicker Font";
    Control.addEventListener("click", Event => Event.stopPropagation());

    /**
     * The TextBox of the TimeSpanPicker.
     * @type {HTMLInputElement}
     */
    const TextBox = document.createElement("input");
    TextBox.className = "TextBox BorderDark Background Font Dark";
    TextBox.type = "text";
    TextBox.value = TimeSpan;
    TextBox.disabled = !Enabled;
    TextBox.addEventListener("input", OnInput, false);

    /**
     * The button of the TimeSpanPicker.
     * @type {HTMLButtonElement}
     */
    const Button = document.createElement("button");
    Button.className = "Toggle Button";
    Button.value = "▼";
    Button.textContent = "▼";
    Button.disabled = !Enabled;
    Button.addEventListener("click", TogglePicker);

    Control.appendChild(TextBox);
    Control.appendChild(Button);

    /**
     * The picker of the TimeSpanPicker.
     * @type {HTMLDivElement}
     */
    const Picker = document.createElement("div");
    Picker.className = "Picker";
    Picker.style.display = "none";

    /**
     * The hour selector of the TimeSpanPicker.
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
    HourDecrementButton.className = "Button BorderDark Font Dark";
    HourDecrementButton.addEventListener("click", DecrementHour);

    /**
     * The hour row of the hour selector.
     * @type {HTMLDivElement}
     */
    const HourRow = document.createElement("div");
    HourRow.className = "Row";
    HourRow.textContent = Zerofill(Hours);
    HourRow.addEventListener("wheel", Event => Event.deltaY > 0 ? IncrementHour() : DecrementHour(), false);

    /**
     * The increment button of the hour selector.
     * @type {HTMLButtonElement}
     */
    const HourIncrementButton = document.createElement("button");
    HourIncrementButton.textContent = "▼";
    HourIncrementButton.className = "Button BorderDark Font Dark";
    HourIncrementButton.addEventListener("click", IncrementHour);

    HourSelect.appendChild(HourDecrementButton);
    HourSelect.appendChild(HourRow);
    HourSelect.appendChild(HourIncrementButton);

    /**
     * The minute selector of the TimeSpanPicker.
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
    MinuteDecrementButton.className = "Button BorderDark Font Dark";
    MinuteDecrementButton.addEventListener("click", DecrementMinute);

    /**
     * The minute row of the minute selector.
     * @type {HTMLDivElement}
     */
    const MinuteRow = document.createElement("div");
    MinuteRow.className = "Row";
    MinuteRow.textContent = Zerofill(Minutes);
    MinuteRow.addEventListener("wheel", Event => Event.deltaY > 0 ? IncrementMinute() : DecrementMinute(), false);

    /**
     * The increment button of the minute selector.
     * @type {HTMLButtonElement}
     */
    const MinuteIncrementButton = document.createElement("button");
    MinuteIncrementButton.textContent = "▼";
    MinuteIncrementButton.className = "Button BorderDark Font Dark";
    MinuteIncrementButton.addEventListener("click", IncrementMinute);

    MinuteSelect.appendChild(MinuteDecrementButton);
    MinuteSelect.appendChild(MinuteRow);
    MinuteSelect.appendChild(MinuteIncrementButton);

    /**
     * The second selector of the TimeSpanPicker.
     * @type {HTMLDivElement}
     */
    const SecondSelect = document.createElement("div");
    SecondSelect.className = "Second";

    /**
     * The decrement button of the second selector.
     * @type {HTMLButtonElement}
     */
    const SecondDecrementButton = document.createElement("button");
    SecondDecrementButton.textContent = "▲";
    SecondDecrementButton.className = "Button BorderDark Font Dark";
    SecondDecrementButton.addEventListener("click", DecrementSecond);

    /**
     * The second row of the second selector.
     * @type {HTMLDivElement}
     */
    const SecondRow = document.createElement("div");
    SecondRow.className = "Row";
    SecondRow.textContent = Zerofill(Seconds);
    SecondRow.addEventListener("wheel", Event => Event.deltaY > 0 ? IncrementSecond() : DecrementSecond(), false);

    /**
     * The increment button of the second selector.
     * @type {HTMLButtonElement}
     */
    const SecondIncrementButton = document.createElement("button");
    SecondIncrementButton.textContent = "▼";
    SecondIncrementButton.className = "Button BorderDark Font Dark";
    SecondIncrementButton.addEventListener("click", IncrementSecond);

    SecondSelect.appendChild(SecondDecrementButton);
    SecondSelect.appendChild(SecondRow);
    SecondSelect.appendChild(SecondIncrementButton);

    Picker.appendChild(HourSelect);
    Picker.appendChild(MinuteSelect);
    Picker.appendChild(SecondSelect);
    Control.appendChild(Picker);
}
;