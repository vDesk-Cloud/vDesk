"use strict";
/**
 * Fired if the Cell has been selected.
 * @event vDesk.Controls.Calendar.Cell#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Controls.Calendar.Cell} detail.sender The current instance of the Cell.
 */
/**
 * Fired if the Cell has been opened.
 * @event vDesk.Controls.Calendar.Cell#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Controls.Calendar.Cell} detail.sender The current instance of the Cell.
 */
/**
 * Fired if the Cell has been right clicked on.
 * @event vDesk.Controls.Calendar.Cell#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.Controls.Calendar.Cell} detail.sender The current instance of the Cell.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Initializes a new instance of the Cell class.
 * @class Represents a generic calendar Cell.
 * @param {Date} [Date=new Date] Initializes the Cell with the specified date.
 * @param {String} [Text=""] Initializes the Cell with the specified text.
 * @param {String} [Type=""] Initializes the Cell with the specified type.
 * @param {Boolean} [Selected=false] Flag indicating whether the Cell is selected.
 * @param {Boolean} [Now=false] Flag indicating whether the Cell is the current date.
 * @param {Boolean} [Outer=false] Flag indicating whether the Cell is outside the current range of time.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Date} Date Gets or sets the date of the Cell.
 * @property {String} Text Gets or sets the text of the Cell.
 * @property {String} Type Gets or sets the type of the Cell.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Cell is selected.
 * @property {Boolean} Now Gets or sets a value indicating whether the date of the Cell represents the current date.
 * @property {Boolean} Outer Gets or sets a value indicating whether the date of the Cell is outside the current range of time.
 * @fires select Fired if the user clicked on the Cell.
 * @fires open Fired if the user double clicked on the Cell.
 * @fires context Fired if the user right clicked on the Cell.
 * @memberOf vDesk.Controls.Calendar
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.Cell = function Cell(
    Date     = new window.Date(),
    Text     = "",
    Type     = "",
    Selected = false,
    Now      = false,
    Outer    = false
) {
    Ensure.Parameter(Date, window.Date, "Date");
    Ensure.Parameter(Text, [vDesk.Struct.Type.String, vDesk.Struct.Type.Number], "Text");
    Ensure.Parameter(Type, vDesk.Struct.Type.String, "Type");
    Ensure.Parameter(Selected, vDesk.Struct.Type.Boolean, "Selected");
    Ensure.Parameter(Now, vDesk.Struct.Type.Boolean, "Now");
    Ensure.Parameter(Outer, vDesk.Struct.Type.Boolean, "Outer");

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Date:     {
            enumerable: true,
            get:        () => Date,
            set:        Value => {
                Ensure.Property(Value, window.Date, "Date");
                Date = Value;
            }
        },
        Text:     {
            enumerable: true,
            get:        () => Control.textContent,
            set:        Value => {
                Ensure.Property(Value, [vDesk.Struct.Type.String, vDesk.Struct.Type.Number], "Text");
                Control.textContent = Value;
            }
        },
        Type:     {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Type");
                Type = Value;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Now:      {
            enumerable: true,
            get:        () => Now,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Now");
                Now = Value;
                Control.classList.toggle("Now", Value);
            }
        },
        Outer:    {
            enumerable: true,
            get:        () => Outer,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Outer");
                Outer = Value;
                Control.classList.toggle("Outer", Value);
            }
        }
    });

    /**
     * Evenhandler that listens on the 'click' event.
     * @fires vDesk.Controls.Calendar.Cell#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);

    /**
     * Evenhandler that listens on the 'dblclick' event.
     * @fires vDesk.Controls.Calendar.Cell#open
     */
    const OnDoubleClick = () => new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(Control);

    /**
     * Evenhandler that listens on the 'contextmenu' event.
     * @fires vDesk.Controls.Calendar.Cell#context
     * @param {MouseEvent} Event
     */
    const OnContextMenu = Event => {
        Event.stopPropagation();
        Event.preventDefault();
        new vDesk.Events.BubblingEvent("context", {
            sender: this,
            x:      Event.pageX,
            y:      Event.pageY
        }).Dispatch(Control);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Cell Font Dark BorderLight";
    Control.textContent = Text;
    Control.addEventListener("click", OnClick);
    Control.addEventListener("dblclick", OnDoubleClick);
    Control.addEventListener("contextmenu", OnContextMenu);
};