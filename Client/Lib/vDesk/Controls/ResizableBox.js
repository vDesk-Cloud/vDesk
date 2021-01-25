"use strict";
/**
 * Fired after the ResizableBox has been resized.
 * @event vDesk.Controls.ResizableBox#resized
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resized' event.
 * @property {vDesk.Controls.ResizableBox} detail.sender The current instance of the ResizableBox.
 * @property {Object} detail.height The height of the ResizableBox.
 * @property {Number} detail.height.previous The height of the ResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.height.current The height of the ResizableBox after the 'resized' event has occurred.
 * @property {Object} detail.width The width of the ResizableBox.
 * @property {Number} detail.width.previous The width of the ResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.width.current The width of the ResizableBox after the 'resized' event has occurred.
 * @property {Object} detail.top The top offset of the ResizableBox.
 * @property {Number} detail.top.previous The top offset of the ResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.top.current The top offset of the ResizableBox after the 'resized' event has occurred.
 * @property {Object} detail.left The left offset of the ResizableBox.
 * @property {Number} detail.left.previous The left offset of the ResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.left.current The left offset of the ResizableBox after the 'resized' event has occurred.
 */
/**
 * Fired while the ResizableBox is being resized.
 * @event vDesk.Controls.ResizableBox#resize
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resize' event.
 * @property {vDesk.Controls.ResizableBox} detail.sender The current instance of the ResizableBox.
 * @property {Number} detail.height The current height of the ResizableBox.
 * @property {Number} detail.width The current width of the ResizableBox.
 */
/**
 * Initializes a new instance of the ResizableBox class.
 * @class Represents a resizable box.
 * @param {Node|HTMLElement|DocumentFragment} [Content=null] Initializes the ResizableBox with the specified content.
 * @param {Number} [Height=200] Initializes the ResizableBox with the specified initial height.
 * @param {Number} [Width=200] Initializes the ResizableBox with the specified initial width.
 * @param {Boundary} [Boundary={Top: 100, Left: 100, Right: 100, Bottom: 100}] Initializes the DynamicBox with the specified sphere within the DynamicBox can float.
 * @param {Boolean} [Resizable = true]  Flag indicating whether the ResizableBox is resizable in any direction.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {HTMLDivElement} Content Gets the content control of the ResizableBox.
 * @property {Number} [Height = 200] Gets or sets the height of the ResizableBox.
 * @property {Number} [Width = 200] Gets or sets the width of the ResizableBox.
 * @property {Number} [MinimumHeight = 100] Gets or sets the minimum height of the ResizableBox.
 * @property {Number} [MinimumWidth = 100] Gets or sets the minimum width of the ResizableBox.
 * @property {Number} [Top = 0] Gets or sets the top offset of the DynamicBox.
 * @property {Number} [Left = 0] Gets or sets the left offset of the DynamicBox.
 * @property {Boolean} [Resizable = true] Gets or sets a value indicating whether the ResizableBox is resizable in any direction.
 * @property {Boolean} [ResizableTop = true] Gets or sets a value indicating whether the ResizableBox is resizable to the top.
 * @property {Boolean} [ResizableBottom = true] Gets or sets a value indicating whether the ResizableBox is resizable to the bottom.
 * @property {Boolean} [ResizableLeft = true] Gets or sets a value indicating whether the ResizableBox is resizable to the left.
 * @property {Boolean} [ResizableRight = true] Gets or sets a value indicating whether the ResizableBox is resizable to the right.
 * @memberOf vDesk.Controls
 */
vDesk.Controls.ResizableBox = function(
    Content        = null,
    Height         = 100,
    Width          = 100,
    Boundary = {
        Height: {
            Min: 10,
            Max: 1000
        },
        Width: {
            Min: 10,
            Max: 1000
        }
    },
    Resizable      = true
) {
    Ensure.Parameter(Content, Node, "Content", true);
    Ensure.Parameter(Height, Type.Number, "Height");
    Ensure.Parameter(Width, Type.Number, "Width");
    Ensure.Parameter(Boundary, Type.Object, "Boundary", true);
    Ensure.Parameter(Resizable, Type.Boolean, "Resizable");

    /**
     * The current calculated height of the ResizableBox.
     * @type {null|Number}
     */
    let CurrentHeight = null;

    /**
     * The current calculated width of the ResizableBox.
     * @type {null|Number}
     */
    let CurrentWidth = null;

    /**
     * The initial vertical position of the pointer after a mousedown event has occurred.
     * @type {null|Number}
     */
    let VerticalPosition = null;

    /**
     * The initial horizontal position of the pointer after a mousedown event has occurred.
     * @type {null|Number}
     */
    let HorizontalPosition = null;

    /**
     * The height of the parent.
     * @type {null|Number}
     */
    let ParentHeight = null;

    /**
     * The width of the parent.
     * @type {null|Number}
     */
    let ParentWidth = null;

    /**
     * Flag indicating whether the ResizableBox is resizable to the top.
     * @type {Boolean}
     */
    let ResizableTop = Resizable;

    /**
     * Flag indicating whether the ResizableBox is resizable to the bottom.
     * @type {Boolean}
     */
    let ResizableBottom = Resizable;

    /**
     * Flag indicating whether the ResizableBox is resizable to the left.
     * @type {Boolean}
     */
    let ResizableLeft = Resizable;

    /**
     * Flag indicating whether the ResizableBox is resizable to the right.
     * @type {Boolean}
     */
    let ResizableRight = Resizable;

    Object.defineProperties(this, {
        Control:         {
            enumerable: true,
            get:        () => Control
        },
        Content:         {
            enumerable:   true,
            configurable: true,
            get:          () => ContentControl
        },
        Height:          {
            enumerable:   true,
            configurable: true,
            get:          () => Height,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Height");
                CurrentHeight = Height = Value;
                window.requestAnimationFrame(Update);
            }
        },
        Width:           {
            enumerable:   true,
            configurable: true,
            get:          () => Width,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Width");
                CurrentWidth = Width = Value;
                window.requestAnimationFrame(Update);
            }
        },
        StackOrder:      {
            enumerable:   true,
            configurable: true,
            get:          () => Number.parseInt(Control.style.zIndex),
            set:          Value => {
                Ensure.Property(Value, Type.Number, "StackOrder");
                Control.style.zIndex = Value;
                ContentControl.style.zIndex = ++Value;
                BorderTop.style.zIndex = ++Value;
                BorderLeft.style.zIndex = Value;
                BorderRight.style.zIndex = Value;
                BorderBottom.style.zIndex = Value;
                CornerTopLeft.style.zIndex = ++Value;
                CornerTopRight.style.zIndex = Value;
                CornerBottomLeft.style.zIndex = Value;
                CornerBottomRight.style.zIndex = Value;
            }
        },
        Resizable:       {
            enumerable:   true,
            configurable: true,
            get:          () => ResizableTop && ResizableBottom && ResizableLeft && ResizableRight,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "Resizable");
                ResizableTop = Value;
                ResizableBottom = Value;
                ResizableLeft = Value;
                ResizableRight = Value;
                ToggleResize();
            }
        },
        ResizableTop:    {
            enumerable:   true,
            configurable: true,
            get:          () => ResizableTop,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "ResizableTop");
                ResizableTop = Value;
                ToggleResize();
            }
        },
        ResizableBottom: {
            enumerable:   true,
            configurable: true,
            get:          () => ResizableBottom,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "ResizableBottom");
                ResizableBottom = Value;
                ToggleResize();
            }
        },
        ResizableLeft:   {
            enumerable:   true,
            configurable: true,
            get:          () => ResizableLeft,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "ResizableLeft");
                ResizableLeft = Value;
                ToggleResize();
            }
        },
        ResizableRight:  {
            enumerable:   true,
            configurable: true,
            get:          () => ResizableRight,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "ResizableRight");
                ResizableRight = Value;
                ToggleResize();
            }
        },
        Boundary:  {
            enumerable:   true,
            configurable: true,
            get:          () => Boundary,
            set:          Value => {
                Ensure.Property(Value, Type.Object, "Boundary", true);
                Boundary = Value;
            }
        }
    });

    /**
     * Enables/disables the resizability of the ResizableBox.
     */
    const ToggleResize = () => {

        //Check if the ResizableBox is resizable to the top left.
        if(ResizableTop && ResizableLeft) {
            CornerTopLeft.addEventListener("mousedown", OnMouseDownCornerTopLeft, false);
            CornerTopLeft.classList.remove("NoResize");
        } else {
            CornerTopLeft.removeEventListener("mousedown", OnMouseDownCornerTopLeft, false);
            CornerTopLeft.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the top.
        if(ResizableTop) {
            BorderTop.addEventListener("mousedown", OnMouseDownBorderTop, false);
            BorderTop.classList.remove("NoResize");
        } else {
            BorderTop.removeEventListener("mousedown", OnMouseDownBorderTop, false);
            BorderTop.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the top right.
        if(ResizableTop && ResizableRight) {
            CornerTopRight.addEventListener("mousedown", OnMouseDownCornerTopRight, false);
            CornerTopRight.classList.remove("NoResize");
        } else {
            CornerTopRight.removeEventListener("mousedown", OnMouseDownCornerTopRight, false);
            CornerTopRight.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the left.
        if(ResizableLeft) {
            BorderLeft.addEventListener("mousedown", OnMouseDownBorderLeft, false);
            BorderLeft.classList.remove("NoResize");
        } else {
            BorderLeft.removeEventListener("mousedown", OnMouseDownBorderLeft, false);
            BorderLeft.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the right.
        if(ResizableRight) {
            BorderRight.addEventListener("mousedown", OnMouseDownBorderRight, false);
            BorderRight.classList.remove("NoResize");
        } else {
            BorderRight.removeEventListener("mousedown", OnMouseDownBorderRight, false);
            BorderRight.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the bottom left.
        if(ResizableBottom && ResizableLeft) {
            CornerBottomLeft.addEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
            CornerBottomLeft.classList.remove("NoResize");
        } else {
            CornerBottomLeft.removeEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
            CornerBottomLeft.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the bottom.
        if(ResizableBottom) {
            BorderBottom.addEventListener("mousedown", OnMouseDownBorderBottom, false);
            BorderBottom.classList.remove("NoResize");
        } else {
            BorderBottom.removeEventListener("mousedown", OnMouseDownBorderBottom, false);
            BorderBottom.classList.add("NoResize");
        }

        //Check if the ResizableBox is resizable to the bottom right.
        if(ResizableBottom && ResizableRight) {
            CornerBottomRight.addEventListener("mousedown", OnMouseDownCornerBottomRight, false);
            CornerBottomRight.classList.remove("NoResize");
        } else {
            CornerBottomRight.removeEventListener("mousedown", OnMouseDownCornerBottomRight, false);
            CornerBottomRight.classList.add("NoResize");
        }
    };


    /**
     * Gets the dimensions of the parentnode the ResizableBox has been appended to.
     */
    const GetParentDimensions = () => {
        ParentHeight = Control.parentNode.offsetHeight;
        ParentWidth = Control.parentNode.offsetWidth;
        Control.removeEventListener("mousedown", GetParentDimensions, false);
    };

    /**
     * Updates the dimensions of the ResizableBox.
     */
    const Update = function() {

        if(ParentHeight === null || ParentWidth === null) {
            GetParentDimensions();
        }
        
        CurrentHeight = Math.max(
            Math.min(
                CurrentHeight,
                Boundary?.Height?.Max ?? ParentHeight - 4
            ),
            Boundary?.Height?.Min ?? 10
        );
        CurrentWidth = Math.max(
            Math.min(
                CurrentWidth,
                Boundary?.Width?.Max ??  ParentWidth - 4
            ),
            Boundary?.Width?.Min ?? 10
        );

        Control.style.height = `${CurrentHeight}px`;
        Control.style.width = `${CurrentWidth}px`;

        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Removes all eventlisteners from the control.
     */
    const Remove = function() {
        window.removeEventListener("mouseup", OnMouseUpResize, false);
        window.removeEventListener("mousemove", OnMouseMoveCornerTopLeft, false);
        window.removeEventListener("mousemove", OnMouseMoveBorderTop, false);
        window.removeEventListener("mousemove", OnMouseMoveCornerTopRight, false);
        window.removeEventListener("mousemove", OnMouseMoveBorderLeft, false);
        window.removeEventListener("mousemove", OnMouseMoveBorderRight, false);
        window.removeEventListener("mousemove", OnMouseMoveCornerBottomLeft, false);
        window.removeEventListener("mousemove", OnMouseMoveBorderBottom, false);
        window.removeEventListener("mousemove", OnMouseMoveCornerBottomRight, false);
        Height = CurrentHeight;
        Width = CurrentWidth;
    };

    /**
     * Eventhandler that listens on the mouseup event emitting the 'resized' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resized
     */
    const OnMouseUpResize = Event => {
        new vDesk.Events.BubblingEvent("resized", {
            sender: this,
            height: {
                previous: Height,
                current:  CurrentHeight
            },
            width:  {
                previous: Width,
                current:  CurrentWidth
            }
        }).Dispatch(Control);
        Remove();
        Event.stopPropagation();
    };

    /**
     * Eventhandler that listens on the mousemove event on the top left corner of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveCornerTopLeft = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top left corner of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownCornerTopLeft = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveCornerTopLeft, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousemove event on the top border of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveBorderTop = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top border of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownBorderTop = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveBorderTop, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousemove event on the top right corner of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveCornerTopRight = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentWidth = Width + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top right corner of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownCornerTopRight = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveCornerTopRight, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousedown event on the left border of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveBorderLeft = Event => {
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the left border of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownBorderLeft = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveBorderLeft, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousemove event on the bottom left corner of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveCornerBottomLeft = Event => {
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        CurrentHeight = Height + Event.clientY - VerticalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom left corner of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownCornerBottomLeft = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveCornerBottomLeft, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousemove event on the right border of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveBorderRight = Event => {
        CurrentWidth = Width + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the right border of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownBorderRight = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveBorderRight, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousemove event on the bottom border of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveBorderBottom = Event => {
        CurrentHeight = (Height + Event.clientY - VerticalPosition);
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom border of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownBorderBottom = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveBorderBottom, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Eventhandler that listens on the mousemove event on the bottom right corner of the ResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.ResizableBox#resize
     */
    const OnMouseMoveCornerBottomRight = Event => {
        CurrentHeight = Height + Event.clientY - VerticalPosition;
        CurrentWidth = Width + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom right corner of the ResizableBox.
     * @param {MouseEvent} Event
     */
    const OnMouseDownCornerBottomRight = Event => {
        Event.preventDefault();
        VerticalPosition = Event.clientY;
        HorizontalPosition = Event.clientX;
        window.addEventListener("mousemove", OnMouseMoveCornerBottomRight, false);
        window.addEventListener("mouseup", OnMouseUpResize, false);
    };

    /**
     * Removes all eventhandlers from the ResizableBox.
     */
    this.Remove = function() {
        this.Resizable = false;
        Remove();
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "ResizableBox";
    Control.style.position = "absolute";
    Control.style.overflow = "hidden";
    Control.addEventListener("mousedown", GetParentDimensions, true);

    this.Height = Height;
    this.Width = Width;


    /**
     * The top left corner of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerTopLeft = document.createElement("div");
    CornerTopLeft.className = "Corner Top Left";
    CornerTopLeft.addEventListener("mousedown", OnMouseDownCornerTopLeft, false);
    Control.appendChild(CornerTopLeft);

    /**
     * The top border of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderTop = document.createElement("div");
    BorderTop.className = "Border Top";
    BorderTop.addEventListener("mousedown", OnMouseDownBorderTop, false);
    Control.appendChild(BorderTop);

    /**
     * The top right corner of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerTopRight = document.createElement("div");
    CornerTopRight.className = "Corner Top Right";
    CornerTopRight.addEventListener("mousedown", OnMouseDownCornerTopRight, false);
    Control.appendChild(CornerTopRight);

    /**
     * The left border of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderLeft = document.createElement("div");
    BorderLeft.className = "Border Left";
    BorderLeft.addEventListener("mousedown", OnMouseDownBorderLeft, false);
    Control.appendChild(BorderLeft);

    /**
     * The content container of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const ContentControl = document.createElement("div");
    ContentControl.className = "Content";
    if(Content !== null) {
        ContentControl.appendChild(Content);
    }
    Control.appendChild(ContentControl);

    /**
     * The right border of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderRight = document.createElement("div");
    BorderRight.className = "Border Right";
    BorderRight.addEventListener("mousedown", OnMouseDownBorderRight, false);
    Control.appendChild(BorderRight);


    /**
     * The bottom left corner of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerBottomLeft = document.createElement("div");
    CornerBottomLeft.className = "Corner Bottom Left";
    CornerBottomLeft.addEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
    Control.appendChild(CornerBottomLeft);

    /**
     * The bottom border of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderBottom = document.createElement("div");
    BorderBottom.className = "Border Bottom";
    BorderBottom.addEventListener("mousedown", OnMouseDownBorderBottom, false);
    Control.appendChild(BorderBottom);

    /**
     * The bottom right corner of the ResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerBottomRight = document.createElement("div");
    CornerBottomRight.className = "Corner Bottom Right";
    CornerBottomRight.addEventListener("mousedown", OnMouseDownCornerBottomRight, false);
    Control.appendChild(CornerBottomRight);

    this.StackOrder = 1000;


};