"use strict";
/**
 * Fired if the Event has been selected.
 * @event vDesk.Calendar.Event#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Calendar.Event} detail.sender The current instance of the Event.
 */
/**
 * Fired if the Event has been opened.
 * @event vDesk.Calendar.Event#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Calendar.Event} detail.sender The current instance of the Event.
 */
/**
 * Fired if the Event has been right clicked on.
 * @event vDesk.Calendar.Event#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.Calendar.Event} detail.sender The current instance of the Event.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Fired after the Event has been resized.
 * @event vDesk.Calendar.Event#resized
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resized' event.
 * @property {vDesk.Calendar.Event} detail.sender The current instance of the Event.
 * @property {Object} detail.height The height of the Event.
 * @property {Number} detail.height.previous The height of the Event before the 'resized' event has occurred.
 * @property {Number} detail.height.current The height of the Event after the 'resized' event has occurred.
 * @property {Object} detail.width The width of the Event.
 * @property {Number} detail.width.previous The width of the Event before the 'resized' event has occurred.
 * @property {Number} detail.width.current The width of the Event after the 'resized' event has occurred.
 * @property {Object} detail.top The top offset of the Event.
 * @property {Number} detail.top.previous The top offset of the Event before the 'resized' event has occurred.
 * @property {Number} detail.top.current The top offset of the Event after the 'resized' event has occurred.
 * @property {Object} detail.left The left offset of the Event.
 * @property {Number} detail.left.previous The left offset of the Event before the 'resized' event has occurred.
 * @property {Number} detail.left.current The left offset of the Event after the 'resized' event has occurred.
 */
/**
 * Fired after the Event has been moved.
 * @event vDesk.Calendar.Event#moved
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'moved' event.
 * @property {vDesk.Calendar.Event} detail.sender The current instance of the Event.
 * @property {Object} detail.top The top offset of the Event.
 * @property {Number} detail.top.previous The top offset of the Event before the 'moved' event has occurred.
 * @property {Number} detail.top.current The top offset of the Event after the 'moved' event has occurred.
 * @property {Object} detail.left The left offset of the Event.
 * @property {Number} detail.left.previous The left offset of the Event before the 'moved' event has occurred.
 * @property {Number} detail.left.current The left offset of the Event after the 'moved' event has occurred.
 */
/**
 * Initializes a new instance of the Event class.
 * @class Represents an event within the calendar module.
 * @param {?Number} [ID=null] Initializes the Event with the specified ID.
 * @param {vDesk.Security.User} [Owner=new vDesk.Security.User] Initializes the Event with the specified owner.
 * @param {?Date} [Start=null] Initializes the Event with the specified start date.
 * @param {?Date} [End=null] Initializes the Event with the specified end date.
 * @param {Boolean} [FullTime=false] Flag indicating whether the event occurs over a whole day.
 * @param {Number} [RepeatAmount=0] Initializes the Event with the specified repeat amount.
 * @param {Number} [RepeatInterval=0] Initializes the Event with the specified repeat interval.
 * @param {String} [Title=""] Initializes the Event with the specified title. The title of the Event.
 * @param {String} [Color="rgb(251, 205, 81)"] Initializes the Event with the specified color. The color of the Event.
 * @param {String} [Content=""] Initializes the Event with the specified content. The content of the Event.
 * @param {vDesk.Security.AccessControlList} [AccessControlList=vDesk.Security.AccessControlList] Initializes the Event with the specified AccessControlList.
 * @property {?Number} ID Gets or sets the ID of the Event.
 * @property {vDesk.Security.User} Owner Gets or sets the ID of the owner of the Event.
 * @property {?Date} Start Gets or sets the start date of the Event.
 * @property {?Date} End Gets or sets the end date of the Event.
 * @property {Boolean} FullTime Gets or sets a value indicating whether the event occurs over a whole day.
 * @property {Boolean} Repeating Gets a value indicating whether the event occurs more than once.
 * @property {Number} RepeatAmount Gets or sets the amount the event re-occurs.
 * @property {Number} RepeatInterval Gets or sets the interval in days the event re-occurs.
 * @property {String} Title Gets or sets the title of the Event.
 * @property {String} Content Gets or sets the content of the Event.
 * @property {vDesk.Security.AccessControlList} AccessControlList Gets or sets the AccessControlList of the Event.
 * @property {Number} Duration Gets the duration in hours of the Event. This is a calculated value.
 * @implements {vDesk.Controls.Calendar.IEvent}
 * @augments vDesk.Controls.DynamicBox
 * @memberOf vDesk.Calendar
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Calendar
 */
vDesk.Calendar.Event = function Event(
    ID                = null,
    Owner             = vDesk.Security.User.Current,
    Start             = new Date(),
    End               = new Date(Date.now() + 7.2e+6),
    FullTime          = false,
    RepeatAmount      = 0,
    RepeatInterval    = 0,
    Title             = "",
    Color             = "rgb(251, 205, 81)",
    Content           = "",
    AccessControlList = new vDesk.Security.AccessControlList()
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Owner, vDesk.Security.User, "Owner");
    Ensure.Parameter(Start, Date, "Start");
    Ensure.Parameter(End, Date, "End");
    Ensure.Parameter(FullTime, Type.Boolean, "FullTime");
    Ensure.Parameter(RepeatAmount, Type.Number, "RepeatAmount");
    Ensure.Parameter(RepeatInterval, Type.Number, "RepeatInterval");
    Ensure.Parameter(Title, Type.String, "Title");
    Ensure.Parameter(Color, Type.String, "Color");
    Ensure.Parameter(Content, Type.String, "Content");
    Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList");

    this.Extends(
        vDesk.Controls.DynamicBox,
        document.createTextNode(Content),
        document.createTextNode(Title),
        200,
        200,
        0,
        10,
        {
            Left:   5,
            Top:    0,
            Right:  5,
            Bottom: -1000,
            Width:  {Min: 10},
            Height: {Min: 10}
        },
        AccessControlList.Write && !FullTime,
        AccessControlList.Write && !FullTime
    );

    this.ResizableLeft = false;
    this.ResizableRight = false;

    /**
     * The previous height of the Event after an resized event has occured.
     * @type {null|Number}
     */
    let PreviousHeight = null;

    /**
     * The previous top offset of the Event after an resized or moved event has occured.
     * @type {null|Number}
     */
    let PreviousTop = null;

    /**
     * The ID of the timeout after a resized event has been captured.
     * @type {null|Number}
     */
    let ResizedDelayID = null;

    /**
     * The ID of the timeout after a moved event has been captured.
     * @type {null|Number}
     */
    let MovedDelayID = null;

    Object.defineProperties(this, {
        ID:                {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Owner:             {
            enumerable: true,
            get:        () => Owner,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "Owner");
                Owner = Value;
            }
        },
        Start:             {
            enumerable: true,
            get:        () => Start,
            set:        Value => {
                Ensure.Property(Value, Date, "Start", true);
                Start = Value;
                if(Value !== null){
                    Start.setSeconds(0);
                    this.SetTooltip();
                }
            }
        },
        End:               {
            enumerable: true,
            get:        () => End,
            set:        Value => {
                Ensure.Property(Value, Date, "End", true);
                End = Value;
                if(Value !== null){
                    End.setSeconds(0);
                    this.SetTooltip();
                }
            }
        },
        FullTime:          {
            enumerable: true,
            get:        () => FullTime,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "FullTime");
                FullTime = Value;
                this.Movable = !FullTime;
                this.Resizable = !FullTime;
                this.ResizableLeft = false;
                this.ResizableRight = false;
                this.Control.classList.toggle("FullTime", FullTime);
            }
        },
        RepeatAmount:      {
            enumerable: true,
            get:        () => RepeatAmount,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "RepeatAmount");
                RepeatAmount = Value;
            }
        },
        RepeatInterval:    {
            enumerable: true,
            get:        () => RepeatInterval,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "RepeatInterval");
                RepeatInterval = Value;
            }
        },
        Title:             {
            enumerable: true,
            get:        () => this.Header.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                this.Header.textContent = Value;
            }
        },
        Color:             {
            enumerable: true,
            get:        () => this.Control.style.backgroundColor,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Color");
                this.Control.style.backgroundColor = Value;
            }
        },
        Content:           {
            enumerable: true,
            get:        () => this.Parent.Content.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Content");
                this.Parent.Content.textContent = Value;
            }
        },
        AccessControlList: {
            enumerable: true,
            get:        () => AccessControlList,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.AccessControlList, "AccessControlList");
                AccessControlList = Value;
            }
        },
        Height:            {
            enumerable: true,
            get:        () => this.Parent.Height,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Height");
                this.Parent.Height = Value;
                PreviousHeight = Value;
                this.Header.classList.toggle("Half", Value < 40);
            }
        },
        Top:               {
            enumerable: true,
            get:        () => this.Parent.Top,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Top");
                this.Parent.Top = Value;
                PreviousTop = Value;
            }
        },
        ModifyStart:       {
            enumerable: true,
            get:        () => this.ResizableTop,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "ModifyStart");
                this.ResizableTop = AccessControlList.Write && Value;
            }
        },
        ModifyEnd:         {
            enumerable: true,
            get:        () => this.ResizableBottom,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "ModifyEnd");
                this.ResizableBottom = AccessControlList.Write && Value;
            }
        },
        Repeating:         {
            enumerable: true,
            get:        () => RepeatAmount > 0
        },
        Duration:          {
            enumerable: true,
            get:        () => Start !== null && End !== null
                ? Number((End - Start) / 36e5).toFixed(1)
                : 0
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and emits the 'select' event.
     * @fires vDesk.Calendar.Event#select
     * @param {MouseEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(this.Control);
    };

    /**
     * Eventhandler that listens on the 'dblclick' event and emits the 'open' event.
     * @fires vDesk.Calendar.Event#open
     */
    const OnDoubleClick = () => new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(this.Control);

    /**
     * Eventhandler that listens on the 'contextmenu' event and emits the 'context' event.
     * @fires vDesk.Calendar.Event#context
     * @param {MouseEvent} Event
     */
    const OnContextMenu = Event => {
        Event.stopPropagation();
        Event.preventDefault();
        new vDesk.Events.BubblingEvent("context", {
            sender: this,
            x:      Event.pageX,
            y:      Event.pageY
        }).Dispatch(this.Control);
    };

    /**
     * Eventhandler that listens on the 'resized' event and transforms its sender after 3000 milliseconds if within the timespan no further 'resized' events have been captured.
     * @fires vDesk.Calendar.Event#resized
     * @param {CustomEvent} Event
     */
    const OnResized = Event => {
        Event.stopPropagation();
        clearTimeout(ResizedDelayID);
        ResizedDelayID = setTimeout(() => {
            Event.detail.sender = this;
            this.Control.removeEventListener("resized", OnResized, false);
            new vDesk.Events.BubblingEvent("resized", Event.detail).Dispatch(this.Control);
            this.Control.addEventListener("resized", OnResized, false);
        }, 3000);
    };
    /**
     * Eventhandler that listens on the 'moved' event and transforms its sender after 3000 milliseconds if within the timespan no further 'moved' events have been captured.
     * @fires vDesk.Calendar.Event#moved
     * @param {CustomEvent} Event
     */
    const OnMoved = Event => {
        Event.stopPropagation();
        clearTimeout(MovedDelayID);
        MovedDelayID = setTimeout(() => {
            Event.detail.sender = this;
            this.Control.removeEventListener("moved", OnMoved, false);
            new vDesk.Events.BubblingEvent("moved", Event.detail).Dispatch(this.Control);
            this.Control.addEventListener("moved", OnMoved, false);
        }, 3000);
    };

    /**
     * Eventhandler that listens on the 'move' event and resets the width of the header if the height raises above 40 px.
     * @param {CustomEvent} Event
     */
    const OnResizeBelow = Event => {
        if(Event.detail.height.current > 40){
            this.Header.classList.remove("Half");
            this.Control.removeEventListener("resize", OnResizeBelow, true);
            this.Control.addEventListener("resize", OnResizeAbove, true);
        }
    };
    /**
     * Eventhandler that listens on the 'move' event and  reduces the width of the header if the height drops below 40 px.
     * @param {CustomEvent} Event
     */
    const OnResizeAbove = Event => {
        if(Event.detail.height.current < 40){
            this.Header.classList.add("Half");
            this.Control.removeEventListener("resize", OnResizeAbove, true);
            this.Control.addEventListener("resize", OnResizeBelow, true);
        }
    };

    //Setup control
    // this.Control.className = "Event Font Dark";
    this.Control.classList.add("Event", "Font", "Dark");
    this.Color = Color;
    this.Control.classList.toggle("FullTime", FullTime);
    this.Control.addEventListener("click", OnClick, false);
    this.Control.addEventListener("dblclick", OnDoubleClick, false);
    this.Control.addEventListener("contextmenu", OnContextMenu, false);
    this.Control.addEventListener("resized", OnResized, false);
    this.Control.addEventListener("moved", OnMoved, false);

    this.ResizableLeft = false;
    this.ResizableRight = false;
    this.SetTooltip();
    this.Control.addEventListener("resize", OnResizeAbove, true);
};

vDesk.Calendar.Event.Implements(vDesk.Controls.Calendar.IEvent);

/**
 * Sets the mouseover tooltip according to the specified start and enddate of the Event.
 */
vDesk.Calendar.Event.prototype.SetTooltip = function() {
    if(this.Start !== null && this.End !== null){
        this.Control.title = `${vDesk.Locale.Calendar.Start}: ${this.Start.toLocaleString()}\r\n${vDesk.Locale.Calendar.End}: ${this.End.toLocaleString()}`;
    }
};

/**
 * Determines whether the date of a different event intersects with the date of this instance.
 * @param {vDesk.Controls.Calendar.IEvent} Event The event to check.
 * @return {Boolean} True if the date of the Event intersects with the date of this instance; otherwise, false.
 */
vDesk.Calendar.Event.prototype.CollidesWith = function(Event) {
    Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
    if(this.Start === null && this.End === null){
        return false;
    }
    //Check if the Event to compare starts within this Event.
    return (this.Start <= Event.Start && Event.Start <= this.End)
        //Check if the Event to compare ends within this Event
        || (this.End >= Event.Start && Event.End >= this.End)
        //Check if this Event starts within the Event to compare
        || (Event.Start <= this.Start && this.Start <= Event.End)
        //Check if this Event ends within the Event to compare
        || (Event.End >= this.End && this.End >= Event.Start);
};

/**
 * Factory method that creates an Event from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Event.
 * @return {vDesk.Calendar.Event} An Event filled with the provided data.
 */
vDesk.Calendar.Event.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, "object", "DataView");
    return new vDesk.Calendar.Event(
        DataView?.ID ?? null,
        vDesk.Security.Users.find(User => User.ID === DataView?.Owner?.ID) ?? vDesk.Security.User.FromDataView(DataView?.Owner ?? {}),
        DataView?.Start ?? null,
        DataView?.End ?? null,
        DataView?.FullTime ?? false,
        DataView?.RepeatAmount ?? 0,
        DataView?.RepeatInterval ?? 0,
        DataView?.Title ?? "",
        DataView?.Color ?? "rgb(251, 205, 81)",
        DataView?.Content ?? "",
        vDesk.Security.AccessControlList.FromDataView(DataView.AccessControlList ?? {})
    );
};