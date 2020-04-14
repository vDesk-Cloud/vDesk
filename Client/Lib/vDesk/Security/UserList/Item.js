"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.Security.UserList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Security.UserList.Item} detail.sender The current instance of the Item.
 * @property {vDesk.Security.User} detail.user The User of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item inside the UserList.
 * @param {vDesk.Security.User} User Initializes the Item with the specified User.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.User} User Gets or sets the User of the Item.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @memberOf vDesk.Security.UserList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.UserList.Item = function Item(User, Enabled = true) {
    Ensure.Parameter(User, vDesk.Security.User, "User");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Item is selected.
     * @type {Boolean}
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        User:    {
            enumerable: true,
            get:        () => User,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "User");
                User = Value;
                Control.textContent = Value.Name;
            }
        },
        Selected:  {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                if(Value) {
                    Control.addEventListener("click", OnClick, false);
                } else {
                    Control.removeEventListener("click", OnClick, false);
                }
                Control.classList.toggle("Disabled", !Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.UserList.Item#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {
        sender: this,
        user:   User
    }).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item Font Dark BorderLight";
    Control.textContent = User.Name;
    Control.classList.toggle("Disabled", !Enabled);
    if(Enabled) {
        Control.addEventListener("click", OnClick, false);
    }
};