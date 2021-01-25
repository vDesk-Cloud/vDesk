"use strict";
/**
 * Fired if an Item of the GroupList has been selected.
 * @event vDesk.Security.GroupList#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Security.GroupList} detail.sender The current instance of the GroupList.
 * @property {vDesk.Security.GroupList.Item} detail.item The selected Item.
 */
/**
 * Fired if an Item has been dropped on the GroupList.
 * @event vDesk.Security.GroupList#drop
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'drop' event.
 * @property {vDesk.Security.GroupList} detail.sender The current instance of the GroupList.
 * @property {vDesk.Security.GroupList.Item} detail.item The Item that has been dropped on the GroupList.
 */
/**
 * Initializes a new instance of the GroupList class.
 * @class Represents a collection of all groups.
 * @param {Array} [Items=[]] Initializes the GroupList with the specified set of Groups.
 * @param {Boolean} [Drop=false]  Flag indicating whether the GroupList is a potential drop target.
 * @param {Boolean} [Enabled=true] Flag indicating whether the GroupList is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Security.GroupList.Item>} Items Gets or sets the Items of the GroupList.
 * @property {vDesk.Security.GroupList.Item} Selected Gets or sets the current selected Item of the GroupList.
 * @property {Boolean} Drop Gets or sets a value indicating whether the GroupList is a potential drop target.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the GroupList is enabled.
 * @memberOf vDesk.Security
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.GroupList = function GroupList(Items = [], Drop = false, Enabled = true) {
    Ensure.Parameter(Items, Array, "Items");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The item of the current displayed group.
     * @type {null|vDesk.Security.GroupList.Item}
     */
    let Selected = null;

    /**
     * The amount of drag operations captured on the GroupList.
     * @type {null|Number}
     */
    let DragCount = null;

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
                    Ensure.Parameter(Item, vDesk.Security.GroupList.Item, "Item");
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.GroupList.Item, "Selected", true);
                if(Selected !== null) {
                    Selected.Selected = false;
                }
                Selected = Value;
                if(Value !== null) {
                    Value.Selected = true;
                }
            }
        },
        Drop:     {
            enumerable: true,
            get:        () => Drop,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Drop = Value;
                Items.forEach(Item => Item.Draggable = Value);
                if(Value) {
                    Control.addEventListener("drop", OnDrop, false);
                    Control.addEventListener("dragenter", OnDragEnter, false);
                    Control.addEventListener("dragleave", OnDragLeave, false);
                    Control.addEventListener("dragover", OnDragOver, false);
                } else {
                    Control.removeEventListener("drop", OnDrop, false);
                    Control.removeEventListener("dragenter", OnDragEnter, false);
                    Control.removeEventListener("dragleave", OnDragLeave, false);
                    Control.removeEventListener("dragover", OnDragOver, false);
                }
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
     * @listens vDesk.Security.GroupList.Item#event:select
     * @fires vDesk.Security.GroupList#select
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
     * Eventhandler that listens on the 'dragstart' event.
     */
    const OnDragStart = () => {
        Control.removeEventListener("dragenter", OnDragEnter, false);
        Control.removeEventListener("dragleave", OnDragLeave, false);
    };

    /**
     * Eventhandler that listens on the 'dragend' event.
     */
    const OnDragEnd = () => {
        Control.addEventListener("dragenter", OnDragEnter, false);
        Control.addEventListener("dragleave", OnDragLeave, false);
    };

    /**
     * Eventhandler that listens on the 'drop' event.
     * @param {DragEvent} Event
     * @fires vDesk.Security.GroupList#drop
     */
    const OnDrop = Event => {
        Event.stopPropagation();
        //Why do i need this here, but not in the Archive? O.o
        Event.stopImmediatePropagation();
        Event.preventDefault();

        DragCount = 0;
        Control.classList.remove("Hover");

        //Check if the dropped Item is not in the GroupList.
        const Item = Event.dataTransfer.getReference();
        if(!~Items.indexOf(Item)) {
            new vDesk.Events.BubblingEvent("drop", {
                sender: this,
                item:   Item
            }).Dispatch(Control);
        }
        Control.addEventListener("drop", OnDrop, {once: true});
    };

    /**
     * Eventhandler that listens on the 'dragenter' event.
     * @param {DragEvent} Event
     */
    const OnDragEnter = Event => {
        Event.preventDefault();
        Control.classList.add("Hover");
        DragCount++;
    };

    /**
     * Eventhandler that listens on the 'dragleave' event.
     * @param {DragEvent} Event
     */
    const OnDragLeave = Event => {
        Event.preventDefault();
        DragCount--;
        if(DragCount === 0) {
            Control.classList.remove("Hover");
        }
    };

    /**
     * Eventhandler that listens on the 'dragover' event.
     * @param {DragEvent} Event
     */
    const OnDragOver = Event => Event.preventDefault();

    /**
     * Searches the GroupList for an Item by a specified Group ID.
     * @param {Number} ID The ID of the Group of the Item to find.
     * @return {vDesk.Security.GroupList.Item|Null} The found Item; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID", true);
        return Items.find(Item => Item.Group.ID === ID) ?? null;
    };

    /**
     * Adds an Item to the GroupList.
     * @param {vDesk.Security.GroupList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Security.GroupList.Item, "Item");
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Removes an Item from the GroupList.
     * @param {vDesk.Security.GroupList.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Security.GroupList.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Removes every Item from the GroupList.
     */
    this.Clear = function() {
        Items.forEach(Item => Control.removeChild(Item.Control));
        Items = [];
        this.Selected = null;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "GroupList BorderLight";
    Control.addEventListener("select", OnSelect, true);
    Control.addEventListener("drop", OnDrop, {once: true});
    Control.addEventListener("dragenter", OnDragEnter, false);
    Control.addEventListener("dragleave", OnDragLeave, false);
    Control.addEventListener("dragover", OnDragOver, false);
    Control.addEventListener("dragstart", OnDragStart, false);
    Control.addEventListener("dragend", OnDragEnd, false);

    //Fill GroupList.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.Security.GroupList.Item, "Item");
        Control.appendChild(Item.Control);
    });
};

/**
 * Factory method that creates a GroupList containing every existing group.
 * @param {Boolean} [View=true] Flag indicating whether the UserList will contain only reduced data views.
 * @return {vDesk.Security.GroupList} A GroupList containing every existing group.
 */
vDesk.Security.GroupList.FromGroups = function(View = true) {
    if(View) {
        return new vDesk.Security.GroupList(
            vDesk.Security.Groups.map(Group => new vDesk.Security.GroupList.Item(Group))
        );
    }

    const Response = vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Security",
                Command:    "GetGroups",
                Parameters: {View: false},
                Ticket:     vDesk.User.Ticket
            }
        )
    );
    if(Response.Status) {
        return new vDesk.Security.GroupList(
            Response.Data.map(Group => new vDesk.Security.GroupList.Item(vDesk.Security.Group.FromDataView(Group)))
        );
    } else {
        alert(Response.Data);
    }
};
