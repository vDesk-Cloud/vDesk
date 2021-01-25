"use strict";
/**
 * Initializes a new instance of the PackageList class.
 * @class Represents a list of Modules.
 * @param {Array<vDesk.ModuleList.Item>} [Items=[]] The Items of the PackageList.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.ModuleList.Item>} Items Gets or sets the Items of the PackageList.
 * @property {vDesk.ModuleList.Item} Selected Gets or sets the currently selected Item of the PackageList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the PackageList is enabled.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.ModuleList = function ModuleList(Items = []) {
    Ensure.Parameter(Items, Array, "Items");

    /**
     * The current selected Item of the PackageList.
     * @type {vDesk.ModuleList.Item}
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
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.ModuleList.Item, "Item");
                    Control.appendChild(Item.Control);
                });
                if(Items.length > 0) {
                    Selected = Items[0];
                    Selected.Selected = true;
                }
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.ModuleList.Item, "Selected");
                if(Selected !== null) {
                    Selected.Selected = false;
                }
                Selected = Value;
                Selected.Selected = true;
            }
        }
    });

    /**
     * Adds an Item to the Menu.
     * @param {vDesk.ModuleList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.ModuleList.Item, "Item");
        if(Items.length === 0) {
            Item.Selected = true;
            Selected = Item;
        }
        Control.appendChild(Item.Control);
        Items.push(Item);
    };

    /**
     * Searches the ModuleList for an Item with a specified Module.
     * @param {vDesk.Modules.IVisualModule} Module The Module of the Item to find,
     * @return {vDesk.ModuleList.Item|null} The found Item; otherwise, null.
     */
    this.Find = function(Module) {
        Ensure.Parameter(Module, vDesk.Modules.IVisualModule, "Module");
        return Items.find(Item => Item.Module === Module) ?? null;
    };

    /**
     * Searches the PackageList for an Item that holds an instance of a Module that matches the specified name.
     * @param {String} Name The name of the Module to find.
     * @returns {vDesk.ModuleList.Item|null} An Item containing the Module matching the specified name; otherwise, null.
     */
    this.FindByName = function(Name) {
        Ensure.Parameter(Name, "string", "Name");
        return Items.find(Item => Item.Module.Name === Name) ?? null;
    };

    /**
     * Searches the PackageList for an Item that holds an instance of a Module that matches the specified locale specific title.
     * @param {String} Title The title of the Module to find.
     * @returns {vDesk.ModuleList.Item|null} An Item containing the Module matching the specified locale specific title; otherwise, null.
     */
    this.FindByTitle = function(Title) {
        Ensure.Parameter(Title, "string", "Name");
        return Items.find(Item => Item.Module.Title === Title) ?? null;
    };

    /**
     * Removes an Item from the MenuList.
     * @param {vDesk.ModuleList.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.ModuleList.Item, "Item");

        //Check if the Item exists.
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Removes all Items from the MenuList.
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
    Control.className = "ModuleList BorderLight";

    //Append passed Items to the PackageList.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.ModuleList.Item, "Item");
        Control.appendChild(Item.Control);
    });

    if(Items.length > 0) {
        Selected = Items[0];
        Selected.Selected = true;
    }

};

/**
 * Factory method that creates a PackageList from every existing Module.
 * @return {vDesk.ModuleList} A PackageList filled with every existing Module.
 */
vDesk.ModuleList.FromModules = function() {
    return new vDesk.ModuleList(
        Object.values(vDesk.Modules.Running)
            .filter(Module => Module instanceof vDesk.Modules.IVisualModule)
            .map(Module => new vDesk.ModuleList.Item(Module))
    );
};