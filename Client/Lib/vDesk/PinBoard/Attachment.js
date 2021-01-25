/**
 * Fired if the Attachment has been selected.
 * @event vDesk.PinBoard.Attachment#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.PinBoard.Attachment} detail.sender The current instance of the Attachment.
 */
/**
 * Fired if the Attachment has been opened.
 * @event vDesk.PinBoard.Attachment#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.PinBoard.Attachment} detail.sender The current instance of the Attachment.
 */
/**
 * Fired if the Attachment has been right clicked on.
 * @event vDesk.PinBoard.Attachment#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.PinBoard.Attachment} detail.sender The current instance of the Attachment.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Initializes a new instance of the Attachment class.
 * @class Represents an Attachment of an {@link vDesk.Archive.Element|Element} on the PinBoard.
 * @param {?Number} [ID=null] The ID of the Attachment.
 * @param {Number} [Y=0] The vertical position of the Attachment.
 * @param {Number} [X=0] The horizontal position of the Attachment.
 * @param {vDesk.Archive.Element} [Element=null] The ElementID of the Attachment.
 * @param {Object<Number>} [BoundingSphere=vDesk.PinBoard.Attachment.BoundingSphere] Initializes the Attachment with the specified BoundingSphere.
 * @param {Boolean} [Selected=false] Flag indicating whether the Attachment is selected.
 * @property {Number} ID Gets or sets the id of the Attachment.
 * @property {Number} Y Gets or sets the top offset of the Attachment.
 * @property {Number} X Gets or sets the left offset of the Attachment.
 * @property {vDesk.Archive.Element} Element Gets or sets the Element of the Attachment.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Attachment is selected.
 * @memberOf vDesk.PinBoard
 * @augments vDesk.Controls.FloatingBox
 */
vDesk.PinBoard.Attachment = function Attachment(
    ID             = null,
    Y              = 0,
    X              = 0,
    Element        = null,
    BoundingSphere = vDesk.PinBoard.Attachment.BoundingSphere,
    Selected       = false
) {
    Ensure.Parameter(ID, vDesk.Struct.Type.Number, "ID", true);
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
    Ensure.Parameter(Selected, vDesk.Struct.Type.Boolean, "Selected");
    this.Extends(vDesk.Controls.FloatingBox, null, Y, X, BoundingSphere);

    /**
     * The ID of the timeout after a moved event has been captured.
     * @type {Number}
     */
    let Delay = null;

    Object.defineProperties(this, {
        ID:         {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "ID", true);
                ID = Value;
            }
        },
        X:          {
            enumerable: true,
            get:        () => this.Left,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "X");
                this.Left = Value;
            }
        },
        Y:          {
            enumerable: true,
            get:        () => this.Top,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "Y");
                this.Top = Value;
            }
        },
        Element:    {
            enumerable: true,
            get:        () => Element,
            set:        Value => {
                Ensure.Property(Value, vDesk.Archive.Element, "Element");
                Element = Value;
                NameSpan.textContent = Element.Name;
                Icon.src = vDesk.Visual.Icons.Archive?.[Value?.Extension ?? "Folder"] ?? vDesk.Visual.Icons.Archive.Folder;
            }
        },
        Selected:   {
            get: () => Selected,
            set: Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Selected");
                Selected = Value;
                this.Control.classList.toggle("Selected", Value);
                NameSpan.classList.toggle("Selected", Value);
            }
        },
        //Override parents StackOrder.
        StackOrder: {
            enumerable: true,
            get:        () => this.Parent.StackOrder,
            set:        Value => {
                this.Parent.StackOrder = Value;
                Icon.style.zIndex = ++Value;
                NameSpan.style.zIndex = Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'mousedown' event and sets the stackorder temporarly to 1000;
     */
    const OnMouseDown = () => this.StackOrder = 10000;

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.PinBoard.Attachment#select Fired if the user clicked on the attachment.
     * @param {MouseEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(this.Control);
    };

    /**
     * Eventhandler that listens on the 'dblclick' event.
     * @fires vDesk.PinBoard.Attachment#open
     */
    const OnDoubleClick = () => new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(this.Control);

    /**
     * Eventhandler that listens on the 'contextmenu' event.
     * @fires vDesk.PinBoard.Attachment#context
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
     * Eventhandler that listens on the 'moved' event.
     * @listens vDesk.Controls.FloatingBox#event:moved
     * @fires vDesk.PinBoard.Attachment#moved
     * @param {CustomEvent} Event
     */
    const OnMoved = Event => {
        Event.stopPropagation();
        window.clearTimeout(Delay);
        Delay = setTimeout(() => {
            Event.detail.sender = this;
            this.Control.removeEventListener("moved", OnMoved, false);
            new vDesk.Events.BubblingEvent("moved", Event.detail).Dispatch(this.Control);
            this.Control.addEventListener("moved", OnMoved, false);
        }, 3000);
    };

    //Setup control.
    this.Control.className = "Attachment";
    this.Control.addEventListener("click", OnClick, false);
    this.Control.addEventListener("dblclick", OnDoubleClick, false);
    this.Control.addEventListener("contextmenu", OnContextMenu, false);
    this.Control.addEventListener("moved", OnMoved, false);
    this.Control.addEventListener("mousedown", OnMouseDown, false);
    this.Control.classList.toggle("Selected", Selected);

    /**
     * The icon of the associated vDesk.Archive.Element.
     * @type {HTMLImageElement}
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.draggable = false;
    Icon.src = Element?.Thumbnail ?? vDesk.Visual.Icons.Archive?.[Element?.Extension ?? "Folder"] ?? vDesk.Visual.Icons.Archive.Folder;

    /**
     * The name label textarea of the associated vDesk.Archive.Element.
     * @type {HTMLSpanElement}
     */
    const NameSpan = document.createElement("span");
    NameSpan.className = "Name Font Dark";
    NameSpan.draggable = false;
    NameSpan.textContent = Element.Name;
    NameSpan.classList.toggle("Selected", Selected);

    this.Control.appendChild(Icon);
    this.Control.appendChild(NameSpan);
};

/**
 * Factory method that creates an Attachment from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Attachment.
 * @param {Object<Number>} [BoundingSphere=vDesk.PinBoard.Attachment.BoundingSphere] The Optional BoundingSphere to use to create an instance of the Attachment.
 * @return {vDesk.PinBoard.Attachment} An Attachment filled with the provided data.
 */
vDesk.PinBoard.Attachment.FromDataView = function(DataView, BoundingSphere = vDesk.PinBoard.Attachment.BoundingSphere) {
    Ensure.Parameter(DataView, "object", "DataView");
    DataView.Element.Owner = vDesk.User;
    return new vDesk.PinBoard.Attachment(
        DataView?.ID ?? null,
        DataView?.Y ?? 0,
        DataView?.X ?? 0,
        vDesk.Archive.Element.FromDataView(DataView?.Element ?? {}),
        DataView?.BoundingSphere ?? BoundingSphere
    );
};

/**
 * Default BoundingSphere of Attachments.
 * @enum {Number}
 */
vDesk.PinBoard.Attachment.BoundingSphere = {
    Left:   10,
    Top:    10,
    Right:  10,
    Bottom: 10
};