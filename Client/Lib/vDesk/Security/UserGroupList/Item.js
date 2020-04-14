"use strict";
/**
 * Fired if the Item has been selected.
 * @event vDesk.Security.UserGroupList.Item#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Security.UserGroupList.Item} detail.sender The current instance of the Item.
 */
/**
 * Initializes a new instance of the UserGroupList class.
 * @class Represents an Item within a vDesk.Security.UserGroupList of either an user or group.
 * @param {?Number} [User=null] Initializes the Item with the specified user ID.
 * @param {vDesk.Security.Group} [Group] Initializes the Item with the specified Group.
 * @param {vDesk.Security.User} [User] Initializes the Item with the specified User.
 * @param {Number} [Index=0] Initializes the Item with the specified index.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Item is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.Group} Group Gets or sets the ID of the group the entry belongs to.
 * @property {vDesk.Security.User} User Gets or sets the ID of the user the entry belongs to.
 * @property {String} Name Gets or sets the name of the Item.
 * @property {Number} Index Gets or sets the index of the Item.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Item is selected.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Item is enabled.
 * @memberOf vDesk.Security.UserGroupList
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.UserGroupList.Item = function Item(Group = new vDesk.Security.Group(), User = new vDesk.Security.User(), Index = 0, Enabled = true) {
    Ensure.Parameter(Group, vDesk.Security.Group, "Group");
    Ensure.Parameter(User, vDesk.Security.User, "User");
    Ensure.Parameter(Index, Type.Number, "Index");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the entry is selected.
     * @type {Boolean}
     * @ignore
     */
    let Selected = false;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Index:    {
            enumerable: true,
            get:        () => Index,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Index");
                Index = Value;
            }
        },
        Group:    {
            enumerable: true,
            get:        () => Group,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.Group, "Group");
                Group = Value;
                User = new vDesk.Security.User();
                Icon.src = vDesk.Visual.Icons.Security.Group;
            }
        },
        User:     {
            enumerable: true,
            get:        () => User,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "User");
                User = Value;
                Group = new vDesk.Security.Group();
                Icon.src = vDesk.Visual.Icons.Security.User;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled:  {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Control.draggable = Value;
                if(Value) {
                    Control.style.cursor = "grab";
                    Control.addEventListener("click", OnClick, false);
                } else {
                    Control.style.cursor = "default";
                    Control.removeEventListener("click", OnClick, false);
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.UserGroupList.Item#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);

    /**
     * Eventhandler that listens on the 'dragstart' event.
     * @param {DragEvent} Event
     */
    const OnDragStart = Event => {
        Control.style.cursor = "grabbing";
        Event.dataTransfer.effectAllowed = "move";
        Event.dataTransfer.setReference(this);
    };

    /**
     * Eventhandler that listens on the 'dragend' event and removes visual feedback.
     */
    const OnDragEnd = () => Control.style.cursor = "grab";

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     * @ignore
     */
    const Control = document.createElement("li");
    Control.className = "Item BorderDark Background Font Dark";
    Control.addEventListener("dragstart", OnDragStart, false);
    Control.addEventListener("dragend", OnDragEnd, false);
    Control.draggable = Enabled;
    Control.style.cursor = Enabled ? "grab" : "default";
    if(Enabled) {
        Control.addEventListener("click", OnClick, false);
    }

    /**
     * The icon of the Item.
     * @type {HTMLImageElement}
     * @ignore
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.src = User.ID !== null
               ? vDesk.Visual.Icons.Security.User
               : Group.ID !== null
                 ? vDesk.Visual.Icons.Security.Group
                 : vDesk.Visual.Icons.Unknown;

    /**
     * The name span of the Item.
     * @type {HTMLSpanElement}
     * @ignore
     */
    const Name = document.createElement("span");
    Name.className = "Name";
    Name.textContent = User.Name || Group.Name;

    Control.appendChild(Icon);
    Control.appendChild(Name);
};