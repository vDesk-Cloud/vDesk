"use strict";
/**
 * @typedef {Object} Boundary A rectangle describing the minimum offsets of a control inside its parent node.
 * @property {Number} Top The minimum top offset in pixels.
 * @property {Number} Left The minimum left offset in pixels.
 * @property {Number} Right The minimum right offset in pixels.
 * @property {Number} Bottom The minimum bottom offset in pixels.
 */
/**
 * Fired after the DynamicBox has been resized.
 * @event vDesk.Controls.DynamicBox#resized
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resized' event.
 * @property {vDesk.Controls.DynamicBox} detail.sender The current instance of the DynamicBox.
 * @property {Object} detail.height The height of the DynamicBox.
 * @property {Number} detail.height.previous The height of the DynamicBox before the 'resized' event has occurred.
 * @property {Number} detail.height.current The height of the DynamicBox after the 'resized' event has occurred.
 * @property {Object} detail.width The width of the DynamicBox.
 * @property {Number} detail.width.previous The width of the DynamicBox before the 'resized' event has occurred.
 * @property {Number} detail.width.current The width of the DynamicBox after the 'resized' event has occurred.
 * @property {Object} detail.top The top offset of the DynamicBox.
 * @property {Number} detail.top.previous The top offset of the DynamicBox before the 'resized' event has occurred.
 * @property {Number} detail.top.current The top offset of the DynamicBox after the 'resized' event has occurred.
 * @property {Object} detail.left The left offset of the DynamicBox.
 * @property {Number} detail.left.previous The left offset of the DynamicBox before the 'resized' event has occurred.
 * @property {Number} detail.left.current The left offset of the DynamicBox after the 'resized' event has occurred.
 */
/**
 * Fired while the DynamicBox is being resized.
 * @event vDesk.Controls.DynamicBox#resize
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resize' event.
 * @property {vDesk.Controls.DynamicBox} detail.sender The current instance of the DynamicBox.
 * @property {Number} detail.height The current height of the DynamicBox.
 * @property {Number} detail.width The current width of the DynamicBox.
 */
/**
 * Fired after the DynamicBox has been moved.
 * @event vDesk.Controls.DynamicBox#moved
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'moved' event.
 * @property {vDesk.Controls.DynamicBox} detail.sender The current instance of the DynamicBox.
 * @property {Object} detail.top The top offset of the DynamicBox.
 * @property {Number} detail.top.previous The top offset of the DynamicBox before the 'moved' event has occurred.
 * @property {Number} detail.top.current The top offset of the DynamicBox after the 'moved' event has occurred.
 * @property {Object} detail.left The left offset of the DynamicBox.
 * @property {Number} detail.left.previous The left offset of the DynamicBox before the 'moved' event has occurred.
 * @property {Number} detail.left.current The left offset of the DynamicBox after the 'moved' event has occurred.
 */
/**
 * Fired while the DynamicBox is being moved.
 * @event vDesk.Controls.DynamicBox#move
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resize' event.
 * @property {vDesk.Controls.DynamicBox} detail.sender The current instance of the DynamicBox.
 * @property {Number} detail.top The current top offset of the DynamicBox.
 * @property {Number} detail.left The current left offset of the DynamicBox.
 */
/**
 * Initializes a new instance of the DynamicBox class.
 * @class Represents a headered, movable and resizable control.
 * @param {Node|HTMLElement|DocumentFragment} [Content=null] Initializes the DynamicBox with the specified content.
 * @param {Node|HTMLElement|DocumentFragment} [Header=null] Initializes the DynamicBox with the specified header content.
 * @param {Number} [Height=200] Initializes the DynamicBox with the specified initial height.
 * @param {Number} [Width=200] Initializes the DynamicBox with the specified initial width.
 * @param {Number} [Top=0] Initializes the DynamicBox with the specified initial top offset.
 * @param {Number} [Left=0] Initializes the DynamicBox with the specified initial left offset.
 * @param {Boundary} [Boundary={Top: 100, Left: 100, Right: 100, Bottom: 100}] Initializes the DynamicBox with the specified sphere within the DynamicBox can float.
 * @param {Boolean} [Resizable = true]  Flag indicating whether the DynamicBox is resizable in any direction.
 * @param {Boolean} [Movable = true] Flag indicating whether the DynamicBox is movable.
 * @throws ArgumentError Thrown if the specified Boundary is not valid.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {HTMLDivElement} Content Gets the content control of the DynamicBox.
 * @property {HTMLDivElement} Header Gets the header control of the DynamicBox.
 * @property {Number} [Height = 200] Gets or sets the height of the DynamicBox.
 * @property {Number} [Width = 200] Gets or sets the width of the DynamicBox.
 * @property {Number} [Top = 0] Gets or sets the top offset of the DynamicBox.
 * @property {Number} [Left = 0] Gets or sets the left offset of the DynamicBox.
 * @property {Number} StackOrder Gets or sets the z-index of the DynamicBox.
 * @property {Boolean} [Resizable = true] Gets or sets a value indicating whether the DynamicBox is resizable in any direction.
 * @property {Boolean} [ResizableTop = true] Gets or sets a value indicating whether the DynamicBox is resizable to the top.
 * @property {Boolean} [ResizableBottom = true] Gets or sets a value indicating whether the DynamicBox is resizable to the bottom.
 * @property {Boolean} [ResizableLeft = true] Gets or sets a value indicating whether the DynamicBox is resizable to the left.
 * @property {Boolean} [ResizableRight = true] Gets or sets a value indicating whether the DynamicBox is resizable to the right.
 * @property {Boolean} [Movable = true] Gets or sets a value indicating whether the DynamicBox is movable.
 * @property {Boundary} [Boundary = {Top: 100, Left: 100, Right: 100, Bottom: 100}] Gets or sets the sphere inside the DynamicBox can float.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.DynamicBox = function DynamicBox(
    Content   = null,
    Header    = null,
    Height    = 200,
    Width     = 200,
    Top       = 0,
    Left      = 0,
    Boundary  = {
        Top:    10,
        Left:   10,
        Right:  10,
        Bottom: 10,
        Height: {
            Min: 10,
            Max: null //Use dimensions as default?
        },
        Width:  {
            Min: 10,
            Max: null //Use dimensions as default?
        }
    },
    Resizable = true,
    Movable   = true
) {
    Ensure.Parameter(Height, Type.Number, "Height");
    Ensure.Parameter(Width, Type.Number, "Width");
    Ensure.Parameter(Top, Type.Number, "Top");
    Ensure.Parameter(Left, Type.Number, "Left");
    Ensure.Parameter(Content, Node, "Content", true);
    Ensure.Parameter(Header, Node, "Header", true);
    Ensure.Parameter(Boundary, Type.Object, "Boundary");
    Ensure.Parameter(Resizable, Type.Boolean, "Resizable");
    Ensure.Parameter(Movable, Type.Boolean, "Movable");

    /**
     * The current calculated height of the DynamicBox.
     * @type {null|Number}
     */
    let CurrentHeight = Height;

    /**
     * The current calculated width of the DynamicBox.
     * @type {null|Number}
     */
    let CurrentWidth = Width;

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
     * The difference between the top offset of the DynamicBox and the vertical position of the pointer.
     * @type {null|Number}
     */
    let VerticalDifference = null;

    /**
     * The difference between the left offset of the DynamicBox and the horizontal position of the pointer.
     * @type {null|Number}
     */
    let HorizontalDifference = null;

    /**
     * The current calculated vertical position of the DynamicBox.
     * @type {null|Number}
     */
    let CurrentTop = Top;

    /**
     * The current calculated horizontal position of the DynamicBox.
     * @type {null|Number}
     */
    let CurrentLeft = Left;

    /**
     * The vertical position of the DynamicBox at the beginning of a drag operation.
     * @type {null|Number}
     */
    let TopDragStart = null;

    /**
     * The horizontal position of the DynamicBox at the beginning of a drag operation.
     * @type {null|Number}
     */
    let LeftDragStart = null;

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
     * Flag indicating whether the DynamicBox is resizable to the top.
     * @type {Boolean}
     */
    let ResizableTop = Resizable;

    /**
     * Flag indicating whether the DynamicBox is resizable to the bottom.
     * @type {Boolean}
     */
    let ResizableBottom = Resizable;

    /**
     * Flag indicating whether the DynamicBox is resizable to the left.
     * @type {Boolean}
     */
    let ResizableLeft = Resizable;

    /**
     * Flag indicating whether the DynamicBox is resizable to the right.
     * @type {Boolean}
     */
    let ResizableRight = Resizable;

    Object.defineProperties(this, {
        Control:         {
            enumerable: true,
            get:        () => Control
        },
        Header:          {
            enumerable:   true,
            configurable: true,
            get:          () => HeaderControl
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
        Top:             {
            enumerable:   true,
            configurable: true,
            get:          () => Top,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Top");
                CurrentTop = Top = Value;
                window.requestAnimationFrame(Update);
            }
        },
        Left:            {
            enumerable:   true,
            configurable: true,
            get:          () => Left,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Left");
                CurrentLeft = Left = Value;
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
                HeaderControl.style.zIndex = ++Value;
                ContentControl.style.zIndex = Value;
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
        Movable:         {
            enumerable:   true,
            configurable: true,
            get:          () => Movable,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "Movable");
                Movable = Value;
                ToggleMove();
            }
        },
        Boundary:        {
            enumerable:   true,
            configurable: true,
            get:          () => Boundary,
            set:          Value => {
                Ensure.Property(Value, Type.Object, "Boundary", null);
                Boundary = Value;
            }
        }
    });

    /**
     * Gets the dimensions of the parentnode the DynamicBox has been appended to.
     */
    const GetParentDimensions = () => {
        ParentHeight = Control?.parentNode?.offsetHeight;
        ParentWidth = Control?.parentNode?.offsetWidth;
        Control.removeEventListener("mousedown", GetParentDimensions, false);
    };

    /**
     * Enables/disables movability of the DynamicBox.
     */
    const ToggleMove = () => {
        if(Movable) {
            HeaderControl.addEventListener("mousedown", OnMouseDownHeader, false);
        } else {
            HeaderControl.removeEventListener("mousedown", OnMouseDownHeader, false);
        }
    };

    /**
     * Enables/disables the resizability of the DynamicBox.
     */
    const ToggleResize = () => {

        //Check if the DynamicBox is resizable to the top left.
        if(ResizableTop && ResizableLeft) {
            CornerTopLeft.addEventListener("mousedown", OnMouseDownCornerTopLeft, false);
            CornerTopLeft.classList.remove("NoResize");
        } else {
            CornerTopLeft.removeEventListener("mousedown", OnMouseDownCornerTopLeft, false);
            CornerTopLeft.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the top.
        if(ResizableTop) {
            BorderTop.addEventListener("mousedown", OnMouseDownBorderTop, false);
            BorderTop.classList.remove("NoResize");
        } else {
            BorderTop.removeEventListener("mousedown", OnMouseDownBorderTop, false);
            BorderTop.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the top right.
        if(ResizableTop && ResizableRight) {
            CornerTopRight.addEventListener("mousedown", OnMouseDownCornerTopRight, false);
            CornerTopRight.classList.remove("NoResize");
        } else {
            CornerTopRight.removeEventListener("mousedown", OnMouseDownCornerTopRight, false);
            CornerTopRight.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the left.
        if(ResizableLeft) {
            BorderLeft.addEventListener("mousedown", OnMouseDownBorderLeft, false);
            BorderLeft.classList.remove("NoResize");
        } else {
            BorderLeft.removeEventListener("mousedown", OnMouseDownBorderLeft, false);
            BorderLeft.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the right.
        if(ResizableRight) {
            BorderRight.addEventListener("mousedown", OnMouseDownBorderRight, false);
            BorderRight.classList.remove("NoResize");
        } else {
            BorderRight.removeEventListener("mousedown", OnMouseDownBorderRight, false);
            BorderRight.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the bottom left.
        if(ResizableBottom && ResizableLeft) {
            CornerBottomLeft.addEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
            CornerBottomLeft.classList.remove("NoResize");
        } else {
            CornerBottomLeft.removeEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
            CornerBottomLeft.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the bottom.
        if(ResizableBottom) {
            BorderBottom.addEventListener("mousedown", OnMouseDownBorderBottom, false);
            BorderBottom.classList.remove("NoResize");
        } else {
            BorderBottom.removeEventListener("mousedown", OnMouseDownBorderBottom, false);
            BorderBottom.classList.add("NoResize");
        }

        //Check if the DynamicBox is resizable to the bottom right.
        if(ResizableBottom && ResizableRight) {
            CornerBottomRight.addEventListener("mousedown", OnMouseDownCornerBottomRight, false);
            CornerBottomRight.classList.remove("NoResize");
        } else {
            CornerBottomRight.removeEventListener("mousedown", OnMouseDownCornerBottomRight, false);
            CornerBottomRight.classList.add("NoResize");
        }
    };

    /**
     * Eventhandler that listens on the 'mousemove' event and changes the position of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#move
     */
    const OnMouseMoveHeader = Event => {
        Event.preventDefault();
        CurrentTop = Event.pageY - VerticalDifference;
        CurrentLeft = Event.pageX - HorizontalDifference;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("move", {
            sender: this,
            top:    CurrentTop,
            left:   CurrentLeft
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'mouseup' event emits the 'moved' event if the offset of the DynamicBox has been changed.
     * @fires vDesk.Controls.DynamicBox#moved
     */
    const OnMouseUpMove = () => {
        HeaderControl.style.cursor = "grab";
        //Check if the position has been changed.
        if(TopDragStart !== CurrentTop || LeftDragStart !== CurrentLeft) {
            new vDesk.Events.BubblingEvent("moved", {
                sender: this,
                top:    {
                    previous: Top,
                    current:  CurrentTop
                },
                left:   {
                    previous: Left,
                    current:  CurrentLeft
                }
            }).Dispatch(Control);
        }
        Remove();
    };

    /**
     * Eventhandler that listens on the 'mousedown' event and enables dragoperations.
     * @param {MouseEvent} Event
     */
    const OnMouseDownHeader = Event => {
        Event.preventDefault();
        HeaderControl.style.cursor = "grabbing";

        //Get starting offset on drag start.
        TopDragStart = Top;
        LeftDragStart = Left;

        //Get the start position of the mouse.
        HorizontalDifference = Event.pageX - Control.offsetLeft;
        VerticalDifference = Event.pageY - Control.offsetTop;

        //Enable drag.
        window.addEventListener("mousemove", OnMouseMoveHeader, false);
        window.addEventListener("mouseup", OnMouseUpMove, false);
    };

    /**
     * Removes all eventlisteners from the control.
     */
    const Remove = function() {
        window.removeEventListener("mouseup", OnMouseUpResize, false);
        window.removeEventListener("mouseup", OnMouseUpMove, false);
        window.removeEventListener("mousemove", OnMouseMoveHeader, false);
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
        Top = CurrentTop;
        Left = CurrentLeft;
    };

    /**
     * Eventhandler that listens on the mouseup event emitting the 'resized' event.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resized
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
            },
            top:    {
                previous: Top,
                current:  CurrentTop
            },
            left:   {
                previous: Left,
                current:  CurrentLeft
            }
        }).Dispatch(Control);
        Remove();
        Event.stopPropagation();
    };

    /**
     * Updates the dimensions of the DynamicBox.
     */
    const Update = function() {

        if(ParentHeight === null || ParentWidth === null) {
            GetParentDimensions();
        }

        CurrentTop = Math.max(
            Math.min(
                CurrentTop,
                ParentHeight - CurrentHeight - (Boundary?.Bottom ?? 10)
            ),
            Boundary?.Top ?? 10
        );
        CurrentLeft = Math.max(
            Math.min(
                CurrentLeft,
                ParentWidth - CurrentWidth - (Boundary?.Right ?? 10)
            ),
            Boundary?.Left ?? 10
        );

        CurrentHeight = Math.max(
            Math.min(
                CurrentHeight,
                Boundary?.Height?.Max ?? ParentHeight - (Boundary?.Top ?? 10) - (Boundary?.Bottom ?? 10)
            ),
            Boundary?.Height?.Min ?? 10
        );
        CurrentWidth = Math.max(
            Math.min(
                CurrentWidth,
                Boundary?.Width?.Max ?? ParentWidth - (Boundary?.Left ?? 10) - (Boundary?.Right ?? 10)
            ),
            Boundary?.Width?.Min ?? 10
        );
        Control.style.height = `${CurrentHeight}px`;
        Control.style.width = `${CurrentWidth}px`;
        Control.style.top = `${CurrentTop}px`;
        Control.style.left = `${CurrentLeft}px`;
    };

    /**
     * Eventhandler that listens on the mousemove event on the top left corner of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveCornerTopLeft = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        CurrentTop = Top + Event.clientY - VerticalPosition;
        CurrentLeft = Left + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top left corner of the DynamicBox.
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
     * Eventhandler that listens on the mousemove event on the top border of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveBorderTop = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentTop = Top + Event.clientY - VerticalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top border of the DynamicBox.
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
     * Eventhandler that listens on the mousemove event on the top right corner of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveCornerTopRight = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentWidth = Width + Event.clientX - HorizontalPosition;
        CurrentTop = Top + Event.clientY - VerticalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top right corner of the DynamicBox.
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
     * Eventhandler that listens on the mousedown event on the left border of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveBorderLeft = Event => {
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        CurrentLeft = Left + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the left border of the DynamicBox.
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
     * Eventhandler that listens on the mousemove event on the bottom left corner of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveCornerBottomLeft = Event => {
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        CurrentHeight = Height + Event.clientY - VerticalPosition;
        CurrentLeft = Left + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom left corner of the DynamicBox.
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
     * Eventhandler that listens on the mousemove event on the right border of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveBorderRight = Event => {
        CurrentWidth = Width + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the right border of the DynamicBox.
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
     * Eventhandler that listens on the mousemove event on the bottom border of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveBorderBottom = Event => {
        CurrentHeight = (Height + Event.clientY - VerticalPosition);
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom border of the DynamicBox.
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
     * Eventhandler that listens on the mousemove event on the bottom right corner of the DynamicBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.DynamicBox#resize
     */
    const OnMouseMoveCornerBottomRight = Event => {
        CurrentHeight = Height + Event.clientY - VerticalPosition;
        CurrentWidth = Width + Event.clientX - HorizontalPosition;
        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {sender: this, height: CurrentHeight, width: CurrentWidth}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom right corner of the DynamicBox.
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
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "DynamicBox";
    Control.style.position = "absolute";
    Control.style.overflow = "hidden";
    Control.style.height = `${Height}px`;
    Control.style.width = `${Width}px`;
    Control.style.top = `${Top}px`;
    Control.style.left = `${Left}px`;
    Control.addEventListener("mousedown", GetParentDimensions, true);

    /**
     * The top left corner of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const CornerTopLeft = document.createElement("div");
    CornerTopLeft.className = "Corner Top Left";
    CornerTopLeft.addEventListener("mousedown", OnMouseDownCornerTopLeft, false);
    Control.appendChild(CornerTopLeft);

    /**
     * The top border of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const BorderTop = document.createElement("div");
    BorderTop.className = "Border Top";
    BorderTop.addEventListener("mousedown", OnMouseDownBorderTop, false);
    Control.appendChild(BorderTop);

    /**
     * The top right corner of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const CornerTopRight = document.createElement("div");
    CornerTopRight.className = "Corner Top Right";
    CornerTopRight.addEventListener("mousedown", OnMouseDownCornerTopRight, false);
    Control.appendChild(CornerTopRight);

    /**
     * The left border of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const BorderLeft = document.createElement("div");
    BorderLeft.className = "Border Left";
    BorderLeft.addEventListener("mousedown", OnMouseDownBorderLeft, false);
    Control.appendChild(BorderLeft);

    /**
     * The header of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const HeaderControl = document.createElement("div");
    HeaderControl.className = "Header";
    HeaderControl.addEventListener("mousedown", OnMouseDownHeader, false);
    if(Header !== null) {
        HeaderControl.appendChild(Header);
    }
    Control.appendChild(HeaderControl);

    /**
     * The content container of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const ContentControl = document.createElement("div");
    ContentControl.className = "Content";
    if(Content !== null) {
        ContentControl.appendChild(Content);
    }
    Control.appendChild(ContentControl);

    /**
     * The right border of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const BorderRight = document.createElement("div");
    BorderRight.className = "Border Right";
    BorderRight.addEventListener("mousedown", OnMouseDownBorderRight, false);
    Control.appendChild(BorderRight);

    /**
     * The bottom left corner of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const CornerBottomLeft = document.createElement("div");
    CornerBottomLeft.className = "Corner Bottom Left";
    CornerBottomLeft.addEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
    Control.appendChild(CornerBottomLeft);

    /**
     * The bottom border of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const BorderBottom = document.createElement("div");
    BorderBottom.className = "Border Bottom";
    BorderBottom.addEventListener("mousedown", OnMouseDownBorderBottom, false);
    Control.appendChild(BorderBottom);

    /**
     * The bottom right corner of the DynamicBox.
     * @type {HTMLDivElement}
     */
    const CornerBottomRight = document.createElement("div");
    CornerBottomRight.className = "Corner Bottom Right";
    CornerBottomRight.addEventListener("mousedown", OnMouseDownCornerBottomRight, false);
    Control.appendChild(CornerBottomRight);

    this.StackOrder = 1000;
};
