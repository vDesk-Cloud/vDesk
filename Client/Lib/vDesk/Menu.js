"use strict";
/**
 * Initializes a new instance of the Menu class.
 * @class Represents the Menu of the Client.
 * @param {Array<vDesk.Menu.Item>} [Items=[]] The items of the Menu.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Menu.Item>} Items Gets or sets the Items of the Menu.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Menu = function Menu(Items = []) {
    Ensure.Parameter(Items, Array, "Items");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Items:   {
            enumerable: true,
            get:        () => Items,
            set:        Value => {
                Ensure.Property(Value, Array, "Items");
                this.Clear();
                Items = Value;
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Menu.Item, "Item");
                    Control.appendChild(Item.Control);
                });
            }
        }
    });

    /**
     * Adds an Item to the Menu.
     * @param {vDesk.Menu.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Menu.Item, "Item");
        Control.appendChild(Item.Control);
        Items.push(Item);
    };

    /**
     * Removes an Item from the Menu.
     * @param {vDesk.Menu.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Menu.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Clears the Menu.
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
    Control.className = "Menu";

    //Append passed items to the Menu.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.Menu.Item, "Item");
        Control.appendChild(Item.Control);
    });

};
