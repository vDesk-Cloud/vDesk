"use strict";
/**
 * Initializes a new instance of the Resizer class.
 * @class Represents a resizer control.
 * @param {Boolean} [Direction=vDesk.Controls.Resizer.Direction.Horizontal] Initializes the Resizer with the specified direction.
 * @param {HTMLElement} First Initializes the Resizer with the specified first Node to resize.
 * @param {HTMLElement} Second Initializes the Resizer with the specified second Node to resize.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Controls.TabControl.TabItem>} TabItems Gets or sets the TabItems of the TabControl.
 * @property {vDesk.Controls.TabControl.TabItem} CurrentTabItem Gets or sets the current displayed TabItem of the TabControl.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Resizer = function Resizer(
    Direction = vDesk.Controls.Resizer.Direction.Horizontal,
    First,
    Second
) {

    /**
     * The start position of the mouse according the Resizer.
     * @type {Number}
     */
    let StartPosition = 0;

    /**
     * The position of the mouse according the Resizer.
     * @type {Number}
     */
    let Position = 0;

    /**
     * The initial width of the TreeView of the Archive module.
     * @type null|Number
     */
    let InitialSizeFirst = 0;

    /**
     * The initial width of the FolderView of the Archive module.
     * @type {Number}
     */
    let InitialSizeSecond = 0;

    /**
     * The width of the resizer of the Archive module.
     * @type {Number}
     */
    let ResizerSize = 0;

    Object.defineProperties(this, {
        Control:   {
            enumerable: true,
            get:        () => Control
        },
        Direction: {
            enumerable: true,
            get:        () => Direction,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Direction");
                Direction = Value;
                Control.classList.toggle("Horizontal", !Value);
                Control.classList.toggle("Vertical", Value);
            }
        },
        First:     {
            enumerable: true,
            get:        () => First,
            set:        Value => {
                Ensure.Property(Value, HTMLElement, "First");
                First = Value;
            }
        },
        Second:    {
            enumerable: true,
            get:        () => Second,
            set:        Value => {
                Ensure.Property(Value, HTMLElement, "Second");
                Second = Value;
            }
        }
    });

    /**
     * Initializes the resizing.
     * @param {MouseEvent} Event
     */
    const OnMouseDownResizer = Event => {
        if(Direction === vDesk.Controls.Resizer.Direction.Horizontal) {
            StartPosition = Event.pageX;
            InitialSizeFirst = First.offsetWidth;
            InitialSizeSecond = Second.offsetWidth;
            ResizerSize = Control.offsetWidth;
        } else {
            StartPosition = Event.pageY;
            InitialSizeFirst = First.offsetHeight;
            InitialSizeSecond = Second.offsetHeight;
            ResizerSize = Control.offsetHeight;
        }
        Control.addEventListener("mouseup", OnMouseUpResizer, false);
        Control.addEventListener("mousemove", OnMouseMoveResizer, false);
        First.addEventListener("mousemove", OnMouseMoveResizer, false);
        Second.addEventListener("mousemove", OnMouseMoveResizer, false);
    };

    /**
     * Updates the width of the Nodes of the Resizer.
     */
    const UpdateWidth = () => {
        const Difference = StartPosition - Position;
        First.style.width = InitialSizeFirst - Difference + "px";
        Second.style.width = InitialSizeSecond + Difference - ResizerSize + "px";
    };

    /**
     * Updates the height of the Nodes of the Resizer.
     */
    const UpdateHeight = () => {
        const Difference = StartPosition - Position;
        First.style.height = InitialSizeFirst - Difference + "px";
        Second.style.height = InitialSizeSecond + Difference - ResizerSize + "px";
    };

    /**
     * Updates the dimensions of the tree- and FolderView.
     * @param {MouseEvent} Event
     */
    const OnMouseMoveResizer = Event => {
        if(Direction === vDesk.Controls.Resizer.Direction.Horizontal) {
            Position = Event.pageX;
            window.requestAnimationFrame(UpdateWidth);
        } else {
            Position = Event.pageY;
            window.requestAnimationFrame(UpdateHeight);
        }
    };

    /**
     * Ends the resizing.
     */
    const OnMouseUpResizer = () => {
        Control.removeEventListener("mouseup", OnMouseUpResizer, false);
        Control.removeEventListener("mousemove", OnMouseMoveResizer, false);
        First.removeEventListener("mousemove", OnMouseMoveResizer, false);
        Second.removeEventListener("mousemove", OnMouseMoveResizer, false);
    };

    /**
     * Resets the width of the tree- and FolderView to their original dimensions.
     */
    const OnDoubleClickResizer = () => {
        if(Direction === vDesk.Controls.Resizer.Direction.Horizontal) {
            First.style.width = "";
            Second.style.width = "";
        } else {
            First.style.height = "";
            Second.style.height = "";
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Resizer";
    Control.classList.toggle("Horizontal", !Direction);
    Control.classList.toggle("Vertical", Direction);
    Control.addEventListener("mousedown", OnMouseDownResizer, false);
    Control.addEventListener("dblclick", OnDoubleClickResizer, false);

    /**
     * The rule of the Resizer.
     * @type {HTMLDivElement}
     */
    const Rule = document.createElement("div");
    Rule.className = "Rule BorderLight";
    Control.appendChild(Rule);

};

/**
 * The possible directions of the Resizer.
 * @enum {Boolean}
 * @readonly
 */
vDesk.Controls.Resizer.Direction = {
    Horizontal: false,
    Vertical:   true
};