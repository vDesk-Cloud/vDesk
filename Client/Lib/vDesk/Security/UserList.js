"use strict";
/**
 * Fired if the UserList has been selected.
 * @event vDesk.Security.UserList#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Security.UserList} detail.sender The current instance of the UserList.
 * @property {vDesk.Security.UserList.Item} detail.item The selected Item.
 * @property {vDesk.Security.User} detail.user The User of the selected Item.
 */
/**
 * Initializes a new instance of the UserList class.
 * @class Represents a collection of all users.
 * @param {Array<vDesk.Security.UserList.Item>} [Items=[]] Initializes the UserList with the specified set of Items.
 * @param {Boolean} [Enabled=true] Flag indicating whether the UserList is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Security.UserList.Item>} Items Gets the Items of the UserList.
 * @property {vDesk.Security.UserList.Item} Selected Gets or sets the current selected Item of the UserList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the UserList is enabled.
 * @memberOf vDesk.Security
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.UserList = function UserList(Items = [], Enabled = true) {
    Ensure.Parameter(Items, Array, "Items");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The current selected Item of the UserList.
     * @type {null|vDesk.Security.UserList.Item}
     */
    let Selected = null;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Items:    {
            enumerable: true,
            get:        () => Items,
            set:        Value => {
                Ensure.Property(Value, Array, "Items");

                //Clear list.
                Items.forEach(Item => Control.removeChild(Item.Control));

                Items = Value;

                //Append new Items.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Security.UserList.Item, "Item");
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.UserList.Item, "Selected");
                if(Selected !== null) {
                    Selected.Selected = false;
                }
                Selected = Value;
                Selected.Selected = true;
            }
        },
        Enabled:  {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Items.forEach(Item => Item.Enabled = Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Security.UserList.Item#event:select
     * @fires vDesk.Security.UserList#select
     */
    const OnSelect = Event => {
        Event.stopPropagation();

        if(Selected !== null) {
            Selected.Selected = false;
        }
        Selected = Event.detail.sender;
        Selected.Selected = true;

        Control.removeEventListener("select", OnSelect, true);
        new vDesk.Events.BubblingEvent("select", {
            sender: this,
            item:   Event.detail.sender
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect, true);
    };

    /**
     * Searches the UserList for an Item by a specified User ID.
     * @param {Number} ID The ID of the User of the Item to find.
     * @return {vDesk.Security.UserList.Item|null} The found Item; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "Item");
        return Items.find(Item => Item.User.ID === ID) || null;
    };

    /**
     * Adds an Item to the UserList.
     * @param {vDesk.Security.UserList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Security.UserList.Item, "Item");
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Removes an Item from the UserList.
     * @param {vDesk.Security.UserList.Item|Null} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Security.UserList.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Removes every Item from the UserList.
     */
    this.Clear = function() {
        Items.forEach(Item => Control.removeChild(Item.Control));
        Items = [];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "UserList BorderLight";
    Control.addEventListener("select", OnSelect, true);

    //Fill UserList.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.Security.UserList.Item, "Item");
        Control.appendChild(Item.Control);
    });
};

/**
 * Factory method that creates a UserList containing every existing user.
 * @param {Boolean} [View=true] Flag indicating whether the UserList will contain only reduced data views.
 * @return {vDesk.Security.UserList} A UserList containing every existing user.
 */
vDesk.Security.UserList.FromUsers = function(View = true) {
    if(View) {
        return new vDesk.Security.UserList(
            vDesk.Security.Users.map(User => new vDesk.Security.UserList.Item(User))
        );
    }

    const Response = vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Security",
                Command:    "GetUsers",
                Parameters: {View: false},
                Ticket:     vDesk.User.Ticket
            }
        )
    );
    if(Response.Status) {
        return new vDesk.Security.UserList(
            Response.Data.map(User => new vDesk.Security.UserList.Item(vDesk.Security.User.FromDataView(User)))
        );
    } else {
        alert(Response.Data);
    }

};