"use strict";
/**
 * Fired if the TabItem has been selected.
 * @event vDesk.Controls.TabControl.TabItem#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Controls.TabControl.TabItem} detail.sender The current instance of the TabItem.
 */
/**
 * Initializes a new instance of the TabItem class.
 * @class Represents a TabItem for a TabControl.
 * @param {String} [Title] The title of the TabItem.
 * @param {Node} [Content] Sets the content of the TabItem.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets or sets the title of the TabItem.
 * @property {Node} Content Gets or sets the content of the TabItem.
 * @property {Boolean} Selected Gets or sets a value indicating whether the TabItem is selected.
 * @memberOf vDesk.Controls.TabControl
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.TabControl.TabItem = function TabItem(Title = "", Content = document.createDocumentFragment()) {
    Ensure.Parameter(Title, Type.String, "Title");
    Ensure.Parameter(Content, Node, "Content");

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Title:    {
            enumerable: true,
            get:        () => Control.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                Control.textContent = Value;
            }
        },
        Content:  {
            enumerable: true,
            get:        () => Content,
            set:        Value => {
                Ensure.Property(Value, Node, "Content");
                Content = Value;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Current", Value);
                Control.classList.toggle("Dark", Value);
                Control.classList.toggle("Disabled", !Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Controls.TabControl.TabItem#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);

    /**
     * Flag indicating whether the TabItem is selected.
     * @type {Boolean}
     */
    let Selected = false;

    /**
     * The underlying DOM-Node.
     * @type {HTMLButtonElement}
     */
    const Control = document.createElement("button");
    Control.className = "TabItem Font Disabled BorderDark Background";
    Control.textContent = Title;
    Control.addEventListener("click", OnClick, false);

};