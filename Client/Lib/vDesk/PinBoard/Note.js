/**
 * Fired after the Note has been resized.
 * @event vDesk.PinBoard.Note#resized
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resized' event.
 * @property {vDesk.PinBoard.Note} detail.sender The current instance of the Note.
 * @property {Object} detail.height The height of the Note.
 * @property {Number} detail.height.previous The height of the Note before the 'resized' event has occurred.
 * @property {Number} detail.height.current The height of the Note after the 'resized' event has occurred.
 * @property {Object} detail.width The width of the Note.
 * @property {Number} detail.width.previous The width of the Note before the 'resized' event has occurred.
 * @property {Number} detail.width.current The width of the Note after the 'resized' event has occurred.
 * @property {Object} detail.top The top offset of the Note.
 * @property {Number} detail.top.previous The top offset of the Note before the 'resized' event has occurred.
 * @property {Number} detail.top.current The top offset of the Note after the 'resized' event has occurred.
 * @property {Object} detail.left The left offset of the Note.
 * @property {Number} detail.left.previous The left offset of the Note before the 'resized' event has occurred.
 * @property {Number} detail.left.current The left offset of the Note after the 'resized' event has occurred.
 */
/**
 * Fired after the Note has been moved.
 * @event vDesk.PinBoard.Note#moved
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'moved' event.
 * @property {vDesk.PinBoard.Note} detail.sender The current instance of the Note.
 * @property {Object} detail.top The top offset of the Note.
 * @property {Number} detail.top.previous The top offset of the Note before the 'moved' event has occurred.
 * @property {Number} detail.top.current The top offset of the Note after the 'moved' event has occurred.
 * @property {Object} detail.left The left offset of the Note.
 * @property {Number} detail.left.previous The left offset of the Note before the 'moved' event has occurred.
 * @property {Number} detail.left.current The left offset of the Note after the 'moved' event has occurred.
 */
/**
 * Fired after the content of the Note has been changed.
 * @event vDesk.PinBoard.Note#contentchanged
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'contentchanged' event.
 * @property {vDesk.PinBoard.Note} detail.sender The current instance of the Note.
 * @property {String} detail.content The content of the Note.
 */
/**
 * Fired if the Note has been right clicked on.
 * @event vDesk.PinBoard.Note#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property { vDesk.PinBoard.Note} detail.sender The current instance of the Note.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Fired if the Note has been selected.
 * @event vDesk.PinBoard.Note#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.PinBoard.Note} detail.sender The current instance of the Note.
 */
/**
 * Initializes a new instance of the Note class.
 * @class Represents a Note of the PinBoard.
 * @param {?Number} [ID=null] Initializes the Note with the specified ID.
 * @param {Number} [Height=200] Initializes the Note with the specified height.
 * @param {Number} [Width=200] Initializes the Note with the specified width.
 * @param {Number} [Y=0] Initializes the Note with the specified vertical position.
 * @param {Number} [X=0] Initializes the Note with the specified horizontal position.
 * @param {String} [Color=vDesk.PinBoard.Note.Yellow] Initializes the Note with the specified color.
 * @param {String} [Content=""] Initializes the Note with the specified content.
 * @param {Object<Number>} [BoundingSphere=vDesk.PinBoard.Note.BoundingSphere] Initializes the Note with the specified BoundingSphere.
 * @param {Boolean} [Selected=false] Flag indicating whether the Note is selected.
 * @property {Number} ID Gets or sets the id of the Note.
 * @property {String} Color Gets or sets the color of the Note.
 * @property {Number} Y Gets or sets the top offset of the Note.
 * @property {Number} X Gets or sets the left offset of the Note.
 * @property {String} Color Gets or sets the color of the Note.
 * @property {String} Text Gets or sets the text of the Note.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Note is selected.
 * @memberOf vDesk.PinBoard
 * @augments vDesk.Controls.HeaderedResizableBox
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.PinBoard.Note = function Note(
    ID             = null,
    Height         = 200,
    Width          = 200,
    Y              = 0,
    X              = 0,
    Color          = vDesk.PinBoard.Note.Yellow,
    Content        = "",
    BoundingSphere = vDesk.PinBoard.Note.BoundingSphere,
    Selected       = false
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Color, Type.String, "Color");
    Ensure.Parameter(Content, Type.String, "Content");
    Ensure.Parameter(Selected, Type.Boolean, "Selected");
    this.Extends(vDesk.Controls.HeaderedResizableBox, Height, Width, Y, X, null, null, BoundingSphere);

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

    /**
     * The ID of the timeout after an input event has been captured.
     * @type {null|Number}
     */
    let InputDelayID = null;

    Object.defineProperties(this, {
        ID:       {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "ID", true);
                ID = Value;
            }
        },
        Color:    {
            enumerable: true,
            get:        () => this.Control.style.backgroundColor,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Color");
                this.Control.style.backgroundColor = Value;
            }
        },
        Text:    {
            enumerable: true,
            get:        () => this.Content.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Text");
                this.Content.textContent = Value;
            }
        },
        X:        {
            enumerable: true,
            get:        () => this.Left,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "X");
                this.Left = Value;
            }
        },
        Y:        {
            enumerable: true,
            get:        () => this.Top,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Y");
                this.Top = Value;
            }
        },
        Selected: {
            get: () => Selected,
            set: Value => {
                Selected = Value;
                Ensure.Property(Value, Type.Boolean, "Selected");
                this.Control.classList.toggle("Selected", Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'mousedown' event and sets the stackorder temporarily to 1000.
     */
    const OnMouseDown = () => this.StackOrder = 10000;

    /**
     * Eventhandler that listens on the 'click' event and emits the 'select' event.
     * @fires vDesk.PinBoard.Note#select
     * @param {CustomEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(this.Control);
    };

    /**
     * Eventhandler that listens on the 'contextmenu' event and emits the 'context' event.
     * @fires context Fired if the user right clicked on the note.
     * @param {MouseEvent} Event
     */
    const OnContextMenu = Event => {
        Event.preventDefault();
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("context", {
            sender: this,
            x:      Event.pageX,
            y:      Event.pageY
        }).Dispatch(this.Control);
    };

    /**
     * Eventhandler that listens on the 'resized' event and transforms its sender after 3000 milliseconds if within the timespan no further 'resized' events have been captured.
     * @listens vDesk.Controls.HeaderedResizableBox#event:resized
     * @fires vDesk.PinBoard.Note#resized
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
     * @listens vDesk.Controls.HeaderedResizableBox#event:moved
     * @fires vDesk.PinBoard.Note#moved
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
     * Eventhandler that listens on the 'input' event and emits the 'contentchanged' event after 3000 milliseconds if the user changed the text of the note and if within the timespan no further input events have been captured.
     * @fires vDesk.PinBoard.Note#contentchanged
     */
    const OnInput = () => {
        clearTimeout(InputDelayID);
        InputDelayID = setTimeout(() => {
            new vDesk.Events.BubblingEvent("contentchanged", {
                sender:  this,
                content: this.Content.textContent
            }).Dispatch(this.Control);
        }, 3000);
    };

    //Construct.
    this.Control.classList.add("Note", "Font", "Dark");
    this.Content.contentEditable = "true";

    this.Color = Color;
    this.Content.textContent = Content;
    this.Control.classList.toggle("Selected", Selected);

    this.MinimumHeight = 100;
    this.MinimumWidth = 160;

    this.Control.addEventListener("resized", OnResized, false);
    this.Control.addEventListener("moved", OnMoved, false);
    this.Content.addEventListener("input", OnInput, false);
    this.Control.addEventListener("click", OnClick, false);
    this.Header.addEventListener("contextmenu", OnContextMenu, false);
    this.Header.addEventListener("mousedown", OnMouseDown, false);
};

/**
 * Factory method that creates a Note from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Note.
 * @param {Object<Number>} [BoundingSphere=vDesk.PinBoard.Note.BoundingSphere] The Optional BoundingSphere to use to create an instance of the Note.
 * @return {vDesk.PinBoard.Note} A Note filled with the provided data.
 */
vDesk.PinBoard.Note.FromDataView = function(DataView, BoundingSphere = vDesk.PinBoard.Note.BoundingSphere) {
    Ensure.Parameter(DataView, "object", "DataView");
    return new vDesk.PinBoard.Note(
        DataView.ID || null,
        DataView.Height || 200,
        DataView.Width || 200,
        DataView.Y || 0,
        DataView.X || 0,
        DataView.Color || vDesk.PinBoard.Note.Yellow,
        DataView.Content || "",
        DataView.BoundingSphere || BoundingSphere
    );
};

/**
 * The default green color of Notes.
 * @type {String}
 * @constant
 */
vDesk.PinBoard.Note.Green = "#cdeb8b";

/**
 * The default blue color of Notes.
 * @type {String}
 * @constant
 */
vDesk.PinBoard.Note.Blue = "#b0d4e3";

/**
 * The default yellow color of Notes.
 * @type {String}
 * @constant
 */
vDesk.PinBoard.Note.Yellow = "#ffff88";

/**
 * The default red color of Notes.
 * @type {String}
 * @constant
 */
vDesk.PinBoard.Note.Red = "rgba(255,0,0,0.5)";

/**
 * The default white color of Notes.
 * @type {String}
 * @constant
 */
vDesk.PinBoard.Note.White = "#F9F9F4";

/**
 * Default BoundingSphere of Notes.
 * @enum {Number}
 */
vDesk.PinBoard.Note.BoundingSphere = {
    Left:   10,
    Top:    10,
    Right:  10,
    Bottom: 10
};