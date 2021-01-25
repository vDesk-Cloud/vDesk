"use strict";
/**
 * Fired if the Window of the Item has been minimized.
 * @event vDesk.TaskBar.Item#minimized
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'minimized' event.
 * @property {vDesk.TaskBar.Item} detail.sender The current instance of the Item.
 * @property {vDesk.Controls.Window} detail.window The minimized Window of the Item.
 */
/**
 * Fired if the Window of the Item has been closed.
 * @event vDesk.TaskBar.Item#closed
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'closed' event.
 * @property {vDesk.TaskBar.Item} detail.sender The current instance of the Item.
 */
/**
 * Fired if the Window of the Item has been focused.
 * @event vDesk.TaskBar.Item#focused
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'focused' event.
 * @property {vDesk.TaskBar.Item} detail.sender The current instance of the Item.
 * @property {vDesk.Controls.Window} detail.window The focused Window of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item within a taskbar.
 * @param {?vDesk.Controls.Window} [Window=null] Initializes the Item with the given window.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Gets or sets the Icon of the Item.
 * @property {String} Title Gets or sets the title of the Item.
 * @property {vDesk.Controls.Window} Window Gets or sets the window of the Item.
 * @property {Boolean} Focus Gets or sets a value indicating whether the window of the Item has focus.
 * @memberOf vDesk.Client
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.TaskBar.Item = function Item(Window = null) {
    Ensure.Property(Window, vDesk.Controls.Window, "Window", true);

    /**
     * Flag indicating whether the Window of the Item has focus.
     * @type {Boolean}
     */
    let Focus = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Icon:    {
            enumerable: true,
            get:        () => Icon.src,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Icon");
                Icon.src = Value;
            }
        },
        Title:   {
            enumerable: true,
            get:        () => Window.Title,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                Title.textContent = Value;
                Control.title = Value;
            }
        },
        Window:  {
            enumerable: true,
            get:        () => Window,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Window, "Window");
                //Remove listeners from the previous window.
                if(Window !== null) {
                    Window.Control.removeEventListener("close", OnClose, false);
                    Window.Control.removeEventListener("focus", OnFocus, false);
                    Window.Control.removeEventListener("minimize", OnMinimize, false);
                }

                Window = Value;
                Icon.src = Window.Icon.src;
                Title.textContent = Window.Title;
                Control.title = Title.textContent;
                Window.Control.addEventListener("close", OnClose, false);
                Window.Control.addEventListener("focus", OnFocus, false);
                Window.Control.addEventListener("minimize", OnMinimize, false);
            }
        },
        Focus:   {
            enumerable: true,
            get:        () => Focus,
            set:        Value => {
                Ensure.Property(Value, "boolean", "Focus");
                Focus = Value;
                Control.classList.toggle("Focus", Value);
            }
        }
    });

    /**
     * Sets the window on top and notifies the taskbar about the focusing.
     * @listens vDesk.Controls.Window#event:focus
     * @fires @event vDesk.TaskBar.Item#focused
     */
    const OnFocus = () => {
        Window.StackOrder = 10000;
        new vDesk.Events.BubblingEvent("focused", {
            sender: this,
            window: Window
        }).Dispatch(Control);
    };

    /**
     * Unsets the window if its being closed and notifies the TaskBar about the closing.
     * @listens vDesk.Controls.Window#event:close
     * @fires @event vDesk.TaskBar.Item#closed
     */
    const OnClose = () => {
        Window.Control.removeEventListener("close", OnClose, false);
        Window.Control.removeEventListener("focus", OnFocus, false);
        Window.Control.removeEventListener("minimize", OnMinimize, false);
        Window = null;
        new vDesk.Events.BubblingEvent("closed", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'minimize' event.
     * Notifies the TaskBar that the Window of the Item has been minimized.
     * @listens vDesk.Controls.Window#event:minimize
     * @fires vDesk.TaskBar.Item#minimized
     */
    const OnMinimize = () => new vDesk.Events.BubblingEvent("minimized", {
        sender: this,
        window: Window
    }).Dispatch(Control);

    /**
     * Toggles the state of the Window of the Item.
     */
    const OnClick = Event => {
        Event.stopPropagation();
        switch(true) {
            case Focus:
                //Minimize the window if it has focus.
                Window.Minimize();
                this.Focus = false;
                break;
            case !Focus && !Window.Minimized:
                //Focus the window if it has whether focus nor it is minimized.
                OnFocus();
                break;
            case Window.Minimized:
                //Restore the window if it is minimized.
                //@todo Implement tracking of previous state, so an previously maximized window will be maximized again if it has been minimized instead of just restoring it.
                Window.Restore();
                OnFocus();
                break;
            default:
                //Minimize the window.
                Window.Minimize();
                this.Focus = false;
                break;
        }
    };

    if(Window !== null) {
        Window.Control.addEventListener("close", OnClose, false);
        Window.Control.addEventListener("focus", OnFocus, false);
        Window.Control.addEventListener("minimize", OnMinimize, false);
    }

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Item";
    Control.addEventListener("click", OnClick, false);

    /**
     * THe icon of the Item.
     * @type {HTMLImageElement}
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.src = Window.Icon;

    /**
     * The title of the Item.
     * @type {HTMLSpanElement}
     */
    const Title = document.createElement("span");
    Title.className = "Title Font Light";
    Title.textContent = (Window !== null) ? Window.Title : "";
    Control.title = Title.textContent;

    Control.appendChild(Icon);
    Control.appendChild(Title);
};
