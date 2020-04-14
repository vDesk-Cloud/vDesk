"use strict";
/**
 * @typedef {Object} BoundingSphere A rectangle describing the minimum offsets of a control inside its parent node.
 * @property {Number} Top The minimum top offset in pixels.
 * @property {Number} Left The minimum left offset in pixels.
 * @property {Number} Right The minimum right offset in pixels.
 * @property {Number} Bottom The minimum bottom offset in pixels.
 */
/**
 * Fired after the HeaderedResizableBox has been resized.
 * @event vDesk.Controls.HeaderedResizableBox#resized
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resized' event.
 * @property {vDesk.Controls.HeaderedResizableBox} detail.sender The current instance of the HeaderedResizableBox.
 * @property {Object} detail.height The height of the HeaderedResizableBox.
 * @property {Number} detail.height.previous The height of the HeaderedResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.height.current The height of the HeaderedResizableBox after the 'resized' event has occurred.
 * @property {Object} detail.width The width of the HeaderedResizableBox.
 * @property {Number} detail.width.previous The width of the HeaderedResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.width.current The width of the HeaderedResizableBox after the 'resized' event has occurred.
 * @property {Object} detail.top The top offset of the HeaderedResizableBox.
 * @property {Number} detail.top.previous The top offset of the HeaderedResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.top.current The top offset of the HeaderedResizableBox after the 'resized' event has occurred.
 * @property {Object} detail.left The left offset of the HeaderedResizableBox.
 * @property {Number} detail.left.previous The left offset of the HeaderedResizableBox before the 'resized' event has occurred.
 * @property {Number} detail.left.current The left offset of the HeaderedResizableBox after the 'resized' event has occurred.
 */
/**
 * Fired while the HeaderedResizableBox is being resized.
 * @event vDesk.Controls.HeaderedResizableBox#resize
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resize' event.
 * @property {vDesk.Controls.HeaderedResizableBox} detail.sender The current instance of the HeaderedResizableBox.
 * @property {Number} detail.height The current height of the HeaderedResizableBox.
 * @property {Number} detail.width The current width of the HeaderedResizableBox.
 */
/**
 * Fired after the HeaderedResizableBox has been moved.
 * @event vDesk.Controls.HeaderedResizableBox#moved
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'moved' event.
 * @property {vDesk.Controls.HeaderedResizableBox} detail.sender The current instance of the HeaderedResizableBox.
 * @property {Object} detail.top The top offset of the HeaderedResizableBox.
 * @property {Number} detail.top.previous The top offset of the HeaderedResizableBox before the 'moved' event has occurred.
 * @property {Number} detail.top.current The top offset of the HeaderedResizableBox after the 'moved' event has occurred.
 * @property {Object} detail.left The left offset of the HeaderedResizableBox.
 * @property {Number} detail.left.previous The left offset of the HeaderedResizableBox before the 'moved' event has occurred.
 * @property {Number} detail.left.current The left offset of the HeaderedResizableBox after the 'moved' event has occurred.
 */
/**
 * Fired while the HeaderedResizableBox is being moved.
 * @event vDesk.Controls.HeaderedResizableBox#move
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resize' event.
 * @property {vDesk.Controls.HeaderedResizableBox} detail.sender The current instance of the HeaderedResizableBox.
 * @property {Number} detail.top The current top offset of the HeaderedResizableBox.
 * @property {Number} detail.left The current left offset of the HeaderedResizableBox.
 */
/**
 * Initializes a new instance of the HeaderedResizableBox class.
 * @class Represents a movable and resizable control.
 * @param {Number} [Height=200] Initializes the HeaderedResizableBox with the specified initial height.
 * @param {Number} [Width=200] Initializes the HeaderedResizableBox with the specified initial width.
 * @param {Number} [Top=0] Initializes the HeaderedResizableBox with the specified initial top offset.
 * @param {Number} [Left=0] Initializes the HeaderedResizableBox with the specified initial left offset.
 * @param {Node|HTMLElement|DocumentFragment} [Content=null] Initializes the HeaderedResizableBox with the specified content.
 * @param {Node|HTMLElement|DocumentFragment} [Header=null] Initializes the HeaderedResizableBox with the specified header content.
 * @param {BoundingSphere} [BoundingSphere={Top: 100, Left: 100, Right: 100, Bottom: 100}] Initializes the HeaderedResizableBox with the specified sphere within the HeaderedResizableBox can float.
 * @param {Number} [MinimumHeight = 100] Initializes the HeaderedResizableBox with the specified minimum height.
 * @param {Number} [MinimumWidth = 100] Initializes the HeaderedResizableBox with the specified minimum width.
 * @param {Boolean} [Resizable = true]  Flag indicating whether the HeaderedResizableBox is resizable in any direction.
 * @param {Boolean} [Movable = true] Flag indicating whether the HeaderedResizableBox is movable.
 * @throws TypeError
 * @throws ArgumentError Thrown if the specified BoundingSphere is not valid.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {HTMLDivElement} Content Gets the content control of the HeaderedResizableBox.
 * @property {HTMLDivElement} Header Gets the header control of the HeaderedResizableBox.
 * @property {Number} [Height = 200] Gets or sets the height of the HeaderedResizableBox.
 * @property {Number} [Width = 200] Gets or sets the width of the HeaderedResizableBox.
 * @property {Number} [MinimumHeight = 100] Gets or sets the minimum height of the HeaderedResizableBox.
 * @property {Number} [MinimumWidth = 100] Gets or sets the minimum width of the HeaderedResizableBox.
 * @property {Number} [Top = 0] Gets or sets the top offset of the HeaderedResizableBox.
 * @property {Number} [Left = 0] Gets or sets the left offset of the HeaderedResizableBox.
 * @property {Number} StackOrder Gets or sets the z-index of the HeaderedResizableBox.
 * @property {Boolean} [Resizable = true] Gets or sets a value indicating whether the HeaderedResizableBox is resizable in any direction.
 * @property {Boolean} [ResizableTop = true] Gets or sets a value indicating whether the HeaderedResizableBox is resizable to the top.
 * @property {Boolean} [ResizableBottom = true] Gets or sets a value indicating whether the HeaderedResizableBox is resizable to the bottom.
 * @property {Boolean} [ResizableLeft = true] Gets or sets a value indicating whether the HeaderedResizableBox is resizable to the left.
 * @property {Boolean} [ResizableRight = true] Gets or sets a value indicating whether the HeaderedResizableBox is resizable to the right.
 * @property {Boolean} [Movable = true] Gets or sets a value indicating whether the HeaderedResizableBox is movable.
 * @property {BoundingSphere} [BoundingSphere = {Top: 100, Left: 100, Right: 100, Bottom: 100}] Gets or sets the sphere inside the HeaderedResizableBox can float.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.HeaderedResizableBox = function HeaderedResizableBox(
    Height         = 200,
    Width          = 200,
    Top            = 0,
    Left           = 0,
    Content        = null,
    Header         = null,
    BoundingSphere = {
        Top:    100,
        Left:   100,
        Right:  100,
        Bottom: 100
    },
    MinimumHeight  = 100,
    MinimumWidth   = 100,
    Resizable      = true,
    Movable        = true
) {
    Ensure.Parameter(Height, Type.Number, "Height");
    Ensure.Parameter(Width, Type.Number, "Width");
    Ensure.Parameter(Top, Type.Number, "Top");
    Ensure.Parameter(Left, Type.Number, "Left");
    Ensure.Parameter(Content, Node, "Content", true);
    Ensure.Parameter(Header, Node, "Header", true);
    Ensure.Parameter(BoundingSphere, Type.Object, "BoundingSphere");
    Ensure.Parameter(MinimumHeight, Type.Number, "MinimumHeight");
    Ensure.Parameter(MinimumWidth, Type.Number, "MinimumWidth");
    Ensure.Parameter(Resizable, Type.Boolean, "Resizable");
    Ensure.Parameter(Movable, Type.Boolean, "Movable");

    /**
     * The current calculated height of the HeaderedResizableBox.
     * @type {null|Number}
     */
    let CurrentHeight = null;

    /**
     * The current calculated width of the HeaderedResizableBox.
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
     * The difference between the top offset of the HeaderedResizableBox and the vertical position of the pointer.
     * @type {null|Number}
     */
    let VerticalDifference = null;

    /**
     * The difference between the left offset of the HeaderedResizableBox and the horizontal position of the pointer.
     * @type {null|Number}
     */
    let HorizontalDifference = null;

    /**
     * The current calculated vertical position of the HeaderedResizableBox.
     * @type {null|Number}
     */
    let CurrentTop = null;

    /**
     * The current calculated horizontal position of the HeaderedResizableBox.
     * @type {null|Number}
     */
    let CurrentLeft = null;

    /**
     * The vertical position of the HeaderedResizableBox at the beginning of a drag operation.
     * @type {null|Number}
     */
    let TopDragStart = null;

    /**
     * The horizontal position of the HeaderedResizableBox at the beginning of a drag operation.
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
     * Flag indicating whether the HeaderedResizableBox is resizable to the top.
     * @type {Boolean}
     */
    let ResizableTop = Resizable;

    /**
     * Flag indicating whether the HeaderedResizableBox is resizable to the bottom.
     * @type {Boolean}
     */
    let ResizableBottom = Resizable;

    /**
     * Flag indicating whether the HeaderedResizableBox is resizable to the left.
     * @type {Boolean}
     */
    let ResizableLeft = Resizable;

    /**
     * Flag indicating whether the HeaderedResizableBox is resizable to the right.
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
                Height = Value >= MinimumHeight ? Value : MinimumHeight;
                CurrentHeight = Height;
                Control.style.height = `${Height}px`;
            }
        },
        Width:           {
            enumerable:   true,
            configurable: true,
            get:          () => Width,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Width");
                Width = Value >= MinimumWidth ? Value : MinimumWidth;
                CurrentWidth = Width;
                Control.style.width = `${Width}px`;
            }
        },
        Top:             {
            enumerable:   true,
            configurable: true,
            get:          () => Top,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Top");
                Top = Value >= 0 ? Value : Top;
                CurrentTop = Top;
                Control.style.top = `${Top}px`;
            }
        },
        Left:            {
            enumerable:   true,
            configurable: true,
            get:          () => Left,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Left");
                Left = Value >= 0 ? Value : Left;
                CurrentLeft = Left;
                Control.style.left = `${Left}px`;
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
        BoundingSphere:  {
            enumerable:   true,
            configurable: true,
            get:          () => BoundingSphere,
            set:          Value => {
                Ensure.Property(Value, Type.Object, "BoundingSphere");
                if(!this.IsBoundingSphere(Value)) {
                    throw new ArgumentError("Value set to property 'BoundingSphere' is not a valid BoundingSphere.");
                }
                BoundingSphere = Value;
            }
        },
        MinimumHeight:   {
            enumerable:   true,
            configurable: true,
            get:          () => MinimumHeight,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "MinimumHeight");
                MinimumHeight = Value;
                if(Height < MinimumHeight) {
                    Height = MinimumHeight;
                    window.requestAnimationFrame(Update);
                }
            }
        },
        MinimumWidth:    {
            enumerable:   true,
            configurable: true,
            get:          () => MinimumWidth,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "MinimumWidth");
                MinimumWidth = Value;
                if(Width < MinimumWidth) {
                    Width = MinimumWidth;
                    window.requestAnimationFrame(Update);
                }
            }
        }
    });

    /**
     * Gets the dimensions of the parentnode the HeaderedResizableBox has been appended to.
     */
    const GetParentDimensions = () => {
        ParentHeight = Control.parentNode.offsetHeight;
        ParentWidth = Control.parentNode.offsetWidth;
        Control.removeEventListener("mousedown", GetParentDimensions, false);
    };

    /**
     * Enables/disables movability of the HeaderedResizableBox.
     */
    const ToggleMove = () => {
        if(Movable) {
            HeaderControl.addEventListener("mousedown", OnMouseDownHeader, false);
        } else {
            HeaderControl.removeEventListener("mousedown", OnMouseDownHeader, false);
        }
    };

    /**
     * Enables/disables the resizability of the HeaderedResizableBox.
     */
    const ToggleResize = () => {

        //Check if the HeaderedResizableBox is resizable to the top left.
        if(ResizableTop && ResizableLeft) {
            CornerTopLeft.addEventListener("mousedown", OnMouseDownCornerTopLeft, false);
            CornerTopLeft.classList.remove("NoResize");
        } else {
            CornerTopLeft.removeEventListener("mousedown", OnMouseDownCornerTopLeft, false);
            CornerTopLeft.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the top.
        if(ResizableTop) {
            BorderTop.addEventListener("mousedown", OnMouseDownBorderTop, false);
            BorderTop.classList.remove("NoResize");
        } else {
            BorderTop.removeEventListener("mousedown", OnMouseDownBorderTop, false);
            BorderTop.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the top right.
        if(ResizableTop && ResizableRight) {
            CornerTopRight.addEventListener("mousedown", OnMouseDownCornerTopRight, false);
            CornerTopRight.classList.remove("NoResize");
        } else {
            CornerTopRight.removeEventListener("mousedown", OnMouseDownCornerTopRight, false);
            CornerTopRight.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the left.
        if(ResizableLeft) {
            BorderLeft.addEventListener("mousedown", OnMouseDownBorderLeft, false);
            BorderLeft.classList.remove("NoResize");
        } else {
            BorderLeft.removeEventListener("mousedown", OnMouseDownBorderLeft, false);
            BorderLeft.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the right.
        if(ResizableRight) {
            BorderRight.addEventListener("mousedown", OnMouseDownBorderRight, false);
            BorderRight.classList.remove("NoResize");
        } else {
            BorderRight.removeEventListener("mousedown", OnMouseDownBorderRight, false);
            BorderRight.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the bottom left.
        if(ResizableBottom && ResizableLeft) {
            CornerBottomLeft.addEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
            CornerBottomLeft.classList.remove("NoResize");
        } else {
            CornerBottomLeft.removeEventListener("mousedown", OnMouseDownCornerBottomLeft, false);
            CornerBottomLeft.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the bottom.
        if(ResizableBottom) {
            BorderBottom.addEventListener("mousedown", OnMouseDownBorderBottom, false);
            BorderBottom.classList.remove("NoResize");
        } else {
            BorderBottom.removeEventListener("mousedown", OnMouseDownBorderBottom, false);
            BorderBottom.classList.add("NoResize");
        }

        //Check if the HeaderedResizableBox is resizable to the bottom right.
        if(ResizableBottom && ResizableRight) {
            CornerBottomRight.addEventListener("mousedown", OnMouseDownCornerBottomRight, false);
            CornerBottomRight.classList.remove("NoResize");
        } else {
            CornerBottomRight.removeEventListener("mousedown", OnMouseDownCornerBottomRight, false);
            CornerBottomRight.classList.add("NoResize");
        }
    };

    /**
     * Eventhandler that listens on the 'mousemove' event and changes the position of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#move
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
     * Eventhandler that listens on the 'mouseup' event emits the 'moved' event if the offset of the HeaderedResizableBox has been changed.
     * @fires vDesk.Controls.HeaderedResizableBox#moved
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
     * @fires vDesk.Controls.HeaderedResizableBox#resized
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
     * Updates the dimensions of the HeaderedResizableBox.
     */
    const Update = function() {

        if(ParentHeight === null || ParentWidth === null){
            GetParentDimensions();
        }

        //Check if the top border of the window has been reached.
        if(CurrentTop < BoundingSphere.Top) {
            CurrentTop = BoundingSphere.Top;
        }
        //Check if the bottom border of the window has been reached.
        const BottomDistance = ParentHeight - Height - BoundingSphere.Bottom;
        if(CurrentTop > BottomDistance) {
            CurrentTop = BottomDistance;
        }
        //Check if the left border of the window has been reached.
        if(CurrentLeft < BoundingSphere.Left) {
            CurrentLeft = BoundingSphere.Left;
        }
        //Check if the right border of the window has been reached.
        const RightDistance = ParentWidth - Width - BoundingSphere.Right;
        if(CurrentLeft > RightDistance) {
            CurrentLeft = RightDistance;
        }

        Control.style.height = `${CurrentHeight}px`;
        Control.style.width = `${CurrentWidth}px`;
        Control.style.top = `${CurrentTop}px`;
        Control.style.left = `${CurrentLeft}px`;
    };

    /**
     * Eventhandler that listens on the mousemove event on the top left corner of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveCornerTopLeft = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentWidth = Width - Event.clientX + HorizontalPosition;

        //Check if the calculated height is over the minimum height and update the top offset.
        if(CurrentHeight >= MinimumHeight) {
            CurrentTop = Top + Event.clientY - VerticalPosition;
        } else {
            CurrentHeight = MinimumHeight;
        }

        //Check if the calculated width is over the minimum width and update the left offset.
        if(CurrentWidth >= MinimumWidth) {
            CurrentLeft = Left + Event.clientX - HorizontalPosition;
        } else {
            CurrentWidth = MinimumWidth;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top left corner of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousemove event on the top border of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveBorderTop = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;

        //Check if the calculated height is over the minimum height and update the top offset.
        if(CurrentHeight >= MinimumHeight) {
            CurrentTop = Top + Event.clientY - VerticalPosition;
        } else {
            CurrentHeight = MinimumHeight;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top border of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousemove event on the top right corner of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveCornerTopRight = Event => {
        CurrentHeight = Height - Event.clientY + VerticalPosition;
        CurrentWidth = Width + Event.clientX - HorizontalPosition;

        //Check if the calculated width is below the minimum width.
        if(CurrentWidth <= MinimumWidth) {
            CurrentWidth = MinimumWidth;
        }

        //Check if the calculated height is over the minimum height and update the top offset.
        if(CurrentHeight >= MinimumHeight) {
            CurrentTop = Top + Event.clientY - VerticalPosition;
        } else {
            CurrentHeight = MinimumHeight;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the top right corner of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousedown event on the left border of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveBorderLeft = Event => {
        CurrentWidth = Width - Event.clientX + HorizontalPosition;

        //Check if the calculated width is over the minimum width and update the left offset.
        if(CurrentWidth >= MinimumWidth) {
            CurrentLeft = Left + Event.clientX - HorizontalPosition;
        } else {
            CurrentWidth = MinimumWidth;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the left border of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousemove event on the bottom left corner of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveCornerBottomLeft = Event => {
        CurrentWidth = Width - Event.clientX + HorizontalPosition;
        CurrentHeight = Height + Event.clientY - VerticalPosition;

        //Check if the calculated width is over the minimum width and update the left offset.
        if(CurrentWidth >= MinimumWidth) {
            CurrentLeft = Left + Event.clientX - HorizontalPosition;
        } else {
            CurrentWidth = MinimumWidth;
        }

        //Check if the calculated height is below the minimum height.
        if(CurrentHeight <= MinimumHeight) {
            CurrentHeight = MinimumHeight;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom left corner of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousemove event on the right border of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveBorderRight = Event => {
        CurrentWidth = Width + Event.clientX - HorizontalPosition;

        //Check if the calculated width is below the minimum width.
        if(CurrentWidth <= MinimumWidth) {
            CurrentWidth = MinimumWidth;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the right border of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousemove event on the bottom border of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveBorderBottom = Event => {
        CurrentHeight = (Height + Event.clientY - VerticalPosition);

        //Check if the calculated height is below the minimum height.
        if(CurrentHeight <= MinimumHeight) {
            CurrentHeight = MinimumHeight;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom border of the HeaderedResizableBox.
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
     * Eventhandler that listens on the mousemove event on the bottom right corner of the HeaderedResizableBox.
     * @param {MouseEvent} Event
     * @fires vDesk.Controls.HeaderedResizableBox#resize
     */
    const OnMouseMoveCornerBottomRight = Event => {
        CurrentHeight = Height + Event.clientY - VerticalPosition;
        CurrentWidth = Width + Event.clientX - HorizontalPosition;

        //Check if the calculated height is below the minimum height.
        if(CurrentHeight <= MinimumHeight) {
            CurrentHeight = MinimumHeight;
        }

        //Check if the calculated width is below the minimum width.
        if(CurrentWidth <= MinimumWidth) {
            CurrentWidth = MinimumWidth;
        }

        window.requestAnimationFrame(Update);
        new vDesk.Events.BubblingEvent("resize", {
            sender: this,
            height: CurrentHeight,
            width:  CurrentWidth
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the mousedown event on the bottom right corner of the HeaderedResizableBox.
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
     * Removes all eventhandlers from the HeaderedResizableBox.
     */
    this.Remove = function() {
        this.Resizable = false;
        Movable = false;
        ToggleMove();
        Remove();
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.style.position = "absolute";
    Control.style.overflow = "hidden";
    Control.addEventListener("mousedown", GetParentDimensions, true);

    this.Height = Height;
    this.Width = Width;
    this.Top = Top;
    this.Left = Left;

    /**
     * The header of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const HeaderControl = document.createElement("div");
    HeaderControl.className = "Header";
    HeaderControl.style.width = "100%";
    HeaderControl.style.top = "5px";
    HeaderControl.style.overflow = "hidden";
    HeaderControl.style.height = "30px";
    HeaderControl.style.position = "absolute";
    HeaderControl.style.cursor = "grab";
    HeaderControl.addEventListener("mousedown", OnMouseDownHeader, false);
    if(Header !== null) {
        HeaderControl.appendChild(Header);
    }

    /**
     * The contentcontainer of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const ContentControl = document.createElement("div");
    ContentControl.className = "Content";
    ContentControl.style.cssText = "position: absolute; top: 35px; left: 5px; right: 5px; bottom: 5px; overflow: auto;";
    if(Content !== null) {
        ContentControl.appendChild(Content);
    }

    //Check if a valid BoundingSphere has been passed.
    if(!this.IsBoundingSphere(BoundingSphere)) {
        throw new ArgumentError("Value of parameter 'BoundingSphere' must be a valid BoundingSphere.");
    }

    /**
     * The top left corner of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerTopLeft = document.createElement("div");
    CornerTopLeft.className = "Corner Top Left";
    CornerTopLeft.style.cssText = "top: 0px; left: 0px; cursor:nw-resize; width: 5px; height: 5px; position: absolute;";
    CornerTopLeft.addEventListener("mousedown", OnMouseDownCornerTopLeft, false);

    /**
     * The top border of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderTop = document.createElement("div");
    BorderTop.className = "Border Top";
    BorderTop.style.cssText = "float: none; top: 0px; cursor: ns-resize; width:100%; height:5px; position: absolute;";
    BorderTop.addEventListener("mousedown", OnMouseDownBorderTop, false);

    /**
     * The top right corner of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerTopRight = document.createElement("div");
    CornerTopRight.className = "Corner Top Right";
    CornerTopRight.style.cssText = "top: 0px; right: 0px; cursor:ne-resize; width: 5px; height: 5px; position: absolute;";
    CornerTopRight.addEventListener("mousedown", OnMouseDownCornerTopRight, false);

    /**
     * The left border of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderLeft = document.createElement("div");
    BorderLeft.className = "Border Left";
    BorderLeft.style.cssText = "float: left; left: 0px; cursor:  ew-resize; height: 100%; width:5px; position: absolute; top: 0px;";
    BorderLeft.addEventListener("mousedown", OnMouseDownBorderLeft, false);

    /**
     * The right border of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderRight = document.createElement("div");
    BorderRight.className = "Border Right";
    BorderRight.style.cssText = "float: right; right: 0px; cursor:  ew-resize; height: 100%; width:5px; position: absolute; top: 0px;";
    BorderRight.addEventListener("mousedown", OnMouseDownBorderRight, false);

    /**
     * The bottom left corner of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerBottomLeft = document.createElement("div");
    CornerBottomLeft.className = "Corner Bottom Left";
    CornerBottomLeft.style.cssText = "bottom: 0px; left: 0px; cursor:sw-resize; width: 5px; height: 5px; position: absolute;";
    CornerBottomLeft.addEventListener("mousedown", OnMouseDownCornerBottomLeft, false);

    /**
     * The bottom border of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const BorderBottom = document.createElement("div");
    BorderBottom.className = "Border Bottom";
    BorderBottom.style.cssText = "float: none; bottom: 0px; cursor: ns-resize; width:100%; height:5px; position: absolute;";
    BorderBottom.addEventListener("mousedown", OnMouseDownBorderBottom, false);

    /**
     * The bottom right corner of the HeaderedResizableBox.
     * @type {HTMLDivElement}
     */
    const CornerBottomRight = document.createElement("div");
    CornerBottomRight.className = "Corner Bottom Right";
    CornerBottomRight.style.cssText = "bottom: 0px; right: 0px; cursor:se-resize; width: 5px; height: 5px; position: absolute;";
    CornerBottomRight.addEventListener("mousedown", OnMouseDownCornerBottomRight, false);

    this.StackOrder = 1000;
    Control.appendChild(CornerTopLeft);
    Control.appendChild(BorderTop);
    Control.appendChild(CornerTopRight);
    Control.appendChild(BorderLeft);
    Control.appendChild(HeaderControl);
    Control.appendChild(ContentControl);
    Control.appendChild(BorderRight);
    Control.appendChild(CornerBottomLeft);
    Control.appendChild(BorderBottom);
    Control.appendChild(CornerBottomRight);

};

/**
 * Determines whether a specified object is a valid BoundingSphere.
 * @param {Object} Value The value to test.
 * @return {Boolean} True if the specified object satisfies the requirements for a BoundingSphere; otherwise, false.
 */
vDesk.Controls.HeaderedResizableBox.prototype.IsBoundingSphere = function(Value) {
    return Value.hasOwnProperty("Top")
           && Value.hasOwnProperty("Left")
           && Value.hasOwnProperty("Bottom")
           && Value.hasOwnProperty("Right")
           && Number.isFinite(Value.Top)
           && Number.isFinite(Value.Left)
           && Number.isFinite(Value.Bottom)
           && Number.isFinite(Value.Right);
};