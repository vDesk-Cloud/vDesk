"use strict";
/**
 * Fired if the FloatingBox has been moved.
 * @event vDesk.Controls.FloatingBox#moved
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'moved' event.
 * @property {vDesk.Controls.FloatingBox} detail.sender The current instance of the FloatingBox.
 * @property {Object} detail.top The top offset of the FloatingBox.
 * @property {Number} detail.top.previous The top offset of the FloatingBox before the 'moved' event has occurred.
 * @property {Number} detail.top.current The top offset of the FloatingBox after the 'moved' event has occurred.
 * @property {Object} detail.left The left offset of the FloatingBox.
 * @property {Number} detail.left.previous The left offset of the FloatingBox before the 'moved' event has occurred.
 * @property {Number} detail.left.current The left offset of the FloatingBox after the 'moved' event has occurred.
 */
/**
 * Fired if the FloatingBox is being moved.
 * @event vDesk.Controls.FloatingBox#move
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'resize' event.
 * @property {vDesk.Controls.FloatingBox} detail.sender The current instance of the FloatingBox.
 * @property {Number} detail.top The current top offset of the FloatingBox.
 * @property {Number} detail.left The current left offset of the FloatingBox.
 */
/**
 * Initializes a new instance of the FloatingBox class.
 * @class Represents a movable control.
 * @param {Number} [Height=200] Initializes the FloatingBox with the specified height.
 * @param {Number} [Width=200] Initializes the FloatingBox with the specified width.
 * @param {Number} [Top=0] Initializes the FloatingBox with the specified top offset.
 * @param {Number} [Left=0] Initializes the FloatingBox with the specified left offset.
 * @param {HTMLElement|DocumentFragment|String} [Content=null]  Initializes the FloatingBox with the specified content.
 * @param {BoundingSphere} [Boundary={Top: 100, Left: 100, Right: 100, Bottom: 100}]  Initializes the FloatingBox with the specified bounding sphere.
 * @param {Boolean} [Movable=true] Flag indicating whether the FloatingBox is movable.
 * @property {HTMLElement} Control Gets the underlying dom node.
 * @property {Number} Height Gets or sets the height of the FloatingBox.
 * @property {Number} Width Gets or sets the width of the FloatingBox.
 * @property {Number} Top Gets or sets the top offset of the FloatingBox.
 * @property {Number} Left Gets or sets the left offset of the FloatingBox.
 * @property {Boolean} Movable Gets or sets a value indicating whether the FloatingBox is movable.
 * @property {Number} StackOrder Gets or sets the z-index of the FloatingBox.
 * @property {BoundingSphere} BoundingSphere Gets or sets the sphere inside the FloatingBox can float.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.FloatingBox = function(
    Content  = null,
    Top      = 0,
    Left     = 0,
    Boundary = {
        Top:    10,
        Left:   10,
        Right:  10,
        Bottom: 10
    },
    Movable  = true
) {
    Ensure.Parameter(Top, Type.Number, "Top");
    Ensure.Parameter(Left, Type.Number, "Left");
    Ensure.Parameter(Content, Node, "Content", true);
    Ensure.Parameter(Boundary, Type.Object, "BoundingSphere", true);
    Ensure.Parameter(Movable, Type.Boolean, "Movable");

    /**
     * The calculated height of the FloatingBox.
     * @type {null|Number}
     */
    let Height = null;

    /**
     * The calculated width of the FloatingBox.
     * @type {null|Number}
     */
    let Width = null;

    /**
     * The difference between the top offset of the box and the vertical position of the pointer.
     * @type {null|Number}
     */
    let VerticalDifference = null;

    /**
     * The difference between the left offset of the box and the horizontal position of the pointer.
     * @type {null|Number}
     */
    let HorizontalDifference = null;

    /**
     * The current calculated vertical position of the FloatingBox.
     * @type {null|Number}
     */
    let CurrentTop = null;

    /**
     * The current calculated horizontal position of the FloatingBox.
     * @type {null|Number}
     */
    let CurrentLeft = null;

    /**
     * The vertical position of the box at the beginning of a drag operation.
     * @type {null|Number}
     */
    let TopDragStart = null;

    /**
     * The horizontal position of the box at the beginning of a drag operation.
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

    Object.defineProperties(this, {
        Control:        {
            enumerable: true,
            get:        () => Control
        },
        Top:            {
            enumerable:   true,
            configurable: true,
            get:          () => Top,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Top");
                Top = Value;
                Control.style.top = `${Top}px`;
            }
        },
        Left:           {
            enumerable:   true,
            configurable: true,
            get:          () => Left,
            set:          Value => {
                Ensure.Property(Value, Type.Number, "Left");
                Left = Value;
                Control.style.left = `${Left}px`;
            }
        },
        BoundingSphere: {
            enumerable:   true,
            configurable: true,
            get:          () => Boundary,
            set:          Value => {
                Ensure.Property(Value, Type.Object, "BoundingSphere", true);
                Boundary = Value;
            }
        },
        StackOrder:     {
            enumerable:   true,
            configurable: true,
            get:          () => Number.parseInt(Control.style.zIndex),
            set:          Value => {
                Ensure.Property(Value, Type.Number, "StackOrder");
                Control.style.zIndex = Value;
            }
        },
        Movable:        {
            enumerable:   true,
            configurable: true,
            get:          () => Movable,
            set:          Value => {
                Ensure.Property(Value, Type.Boolean, "Movable");
                Movable = Value;
                ToggleMove();
            }
        }
    });

    /**
     * Gets the size of the parent node the box has been appended to.
     */
    const GetDimensions = function() {
        Width = Control.offsetWidth;
        Height = Control.offsetHeight;
        ParentHeight = Control?.parentNode?.offsetHeight;
        ParentWidth = Control?.parentNode?.offsetWidth;
        Control.removeEventListener("mousedown", GetDimensions, false);
    };

    /**
     * Enbales/disables movability of the box.
     */
    const ToggleMove = function() {
        if(Movable) {
            Control.addEventListener("mousedown", OnMouseDown, false);
        } else {
            Control.removeEventListener("mousedown", OnMouseDown, false);
        }
    };

    /**
     * Updates the dimensions of the FloatingBox.
     */
    const Update = function() {

        if(Height === null || Width === null || ParentHeight === null || ParentWidth === null) {
            GetDimensions();
        }
        CurrentTop = Math.max(
            Math.min(
                CurrentTop,
                ParentHeight - Height - Boundary?.Bottom ?? 10
            ),
            Boundary?.Top ?? 10
        );
        CurrentLeft = Math.max(
            Math.min(
                CurrentLeft,
                ParentWidth - Width - Boundary?.Right ?? 10
            ),
            Boundary?.Left ?? 10
        );
        Control.style.top = `${CurrentTop}px`;
        Control.style.left = `${CurrentLeft}px`;
    };

    /**
     * Eventhandler that listens on the 'mousemove' event and changes the position of the FloatingBox.
     * @param {MouseEvent} Event
     */
    const OnMouseMove = Event => {
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
     * Eventhandler that listens on the 'mouseup' event emits the 'moved' event if the offset of the FloatingBox has been changed.
     * @fires vDesk.Controls.FloatingBox#moved
     */
    const OnMouseUp = () => {
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
     * Listens on the mousedown event on the header of the FloatingBox and enables dragoperations.
     * @param {MouseEvent} Event
     */
    const OnMouseDown = Event => {
        Event.preventDefault();
        Control.style.cursor = "grabbing";
        //Get starting offset on drag start.
        TopDragStart = Top;
        LeftDragStart = Left;

        //Get the startposition of the mouse.
        HorizontalDifference = Event.pageX - Control.offsetLeft;
        VerticalDifference = Event.pageY - Control.offsetTop;

        //Enable drag.
        window.addEventListener("mousemove", OnMouseMove, false);
        window.addEventListener("mouseup", OnMouseUp, false);
    };

    /**
     * Removes all eventlisteners from the control.
     */
    const Remove = function() {
        window.removeEventListener("mouseup", OnMouseUp, false);
        window.removeEventListener("mousemove", OnMouseMove, false);
        Top = CurrentTop;
        Left = CurrentLeft;
    };

    /**
     * Removes all eventhandlers of the box.
     */
    this.Remove = function() {
        Movable = false;
        ToggleMove();
        Remove();
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.style.cursor = "grab";
    Control.style.position = "absolute";
    Control.style.overflow = "hidden";
    Control.className = "FloatingBox";
    Control.addEventListener("mousedown", OnMouseDown, false);
    Control.addEventListener("mousedown", GetDimensions, false);

    this.Top = Top;
    this.Left = Left;

    if(Content !== null) {
        Control.appendChild(Content);
    }
};
