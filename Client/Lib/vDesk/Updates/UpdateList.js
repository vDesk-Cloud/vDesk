"use strict";
/**
 * Fired if the UpdateList has been selected.
 * @event vDesk.Updates.UpdateList#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Updates.UpdateList} detail.sender The current instance of the UpdateList.
 * @property {vDesk.Updates.UpdateList.Item} detail.item The selected Item.
 * @property {vDesk.Updates.Update} detail.user The Update of the selected Item.
 */
/**
 * Initializes a new instance of the UpdateList class.
 * @class Represents a list of  Updates.
 * @param {Array<vDesk.Updates.UpdateList.Item>} [Items=[]] Initializes the UpdateList with the specified set of Items.
 * @param {Boolean} [Enabled=true] Flag indicating whether the UpdateList is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Updates.UpdateList.Item>} Items Gets the Items of the UpdateList.
 * @property {vDesk.Updates.UpdateList.Item} Selected Gets or sets the current selected Item of the UpdateList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the UpdateList is enabled.
 * @memberOf vDesk.Updates
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Updates
 */
vDesk.Updates.UpdateList = function UpdateList(Items = [], Enabled = true) {
    Ensure.Parameter(Items, Array, "Items");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The current selected Item of the UpdateList.
     * @type {null|vDesk.Updates.UpdateList.Item}
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
                this.Clear();
                Items = Value;
                //Append new Items.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Updates.UpdateList.Item, "Item");
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Updates.UpdateList.Item, "Selected", true);
                if(Selected !== null){
                    Selected.Selected = false;
                }
                Selected = Value;
                if(Value !== null){
                    Selected.Selected = true;
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
     * @listens vDesk.Updates.UpdateList.Item#event:select
     * @fires vDesk.Updates.UpdateList#select
     */
    const OnSelect = Event => {
        Event.stopPropagation();

        if(Selected !== null){
            Selected.Selected = false;
        }
        Selected = Event.detail.sender;
        Selected.Selected = true;

        Control.removeEventListener("select", OnSelect, true);
        new vDesk.Events.BubblingEvent("select", {
            sender: this,
            item:   Event.detail.sender,
            update: Event.detail.package
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect, true);
    };

    /**
     * Searches the UpdateList for an Item by a specified Update hash.
     * @param {String} Hash The hash of the Update of the Item to find.
     * @return {vDesk.Updates.UpdateList.Item|null} The found Item; otherwise, null.
     */
    this.Find = function(Hash) {
        Ensure.Parameter(Hash, Type.String, "Hash");
        return Items.find(Item => Item.Update.Hash === Hash) ?? null;
    };

    /**
     * Adds an Item to the UpdateList.
     * @param {vDesk.Updates.UpdateList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Updates.UpdateList.Item, "Item");
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Removes an Item from the UpdateList.
     * @param {vDesk.Updates.UpdateList.Item|Null} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Updates.UpdateList.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index){
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Removes every Item from the UpdateList.
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
    Control.className = "UpdateList BorderLight";
    Control.addEventListener("select", OnSelect, true);

    //Fill UpdateList.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.Updates.UpdateList.Item, "Item");
        Control.appendChild(Item.Control);
    });
};

/**
 * Factory method that creates a UpdateList containing every existing user.
 * @return {vDesk.Updates.UpdateList} A UpdateList containing every existing Update.
 */
vDesk.Updates.UpdateList.FromUpdates = function() {
    const Response = vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:  "Updates",
                Command: "Hosted",
                Ticket:  vDesk.Security.User.Current.Ticket
            }
        )
    );
    if(Response.Status){
        return new vDesk.Updates.UpdateList(
            Response.Data.map(Update => new vDesk.Updates.UpdateList.Item(vDesk.Updates.Update.FromDataView(Update)))
        );
    }else{
        alert(Response.Data);
    }
};