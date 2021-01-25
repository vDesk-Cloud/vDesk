"use strict";
/**
 * Fired if the PackageList has been selected.
 * @event vDesk.Packages.PackageList#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Packages.PackageList} detail.sender The current instance of the PackageList.
 * @property {vDesk.Packages.PackageList.Item} detail.item The selected Item.
 * @property {vDesk.Packages.Package} detail.user The Package of the selected Item.
 */

/**
 * Initializes a new instance of the PackageList class.
 * @class Represents a list of all installed Packages.
 * @param {Array<vDesk.Packages.PackageList.Item>} [Items=[]] Initializes the PackageList with the specified set of Items.
 * @param {Boolean} [Enabled=true] Flag indicating whether the PackageList is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Packages.PackageList.Item>} Items Gets the Items of the PackageList.
 * @property {vDesk.Packages.PackageList.Item} Selected Gets or sets the current selected Item of the PackageList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the PackageList is enabled.
 * @memberOf vDesk.Packages
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Packages.PackageList = function PackageList(Items = [], Enabled = true) {
    Ensure.Parameter(Items, Array, "Items");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The current selected Item of the PackageList.
     * @type {null|vDesk.Packages.PackageList.Item}
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
                    Ensure.Parameter(Item, vDesk.Packages.PackageList.Item, "Item");
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Packages.PackageList.Item, "Selected", true);
                if(Selected !== null) {
                    Selected.Selected = false;
                }
                Selected = Value;
                if(Value !== null) {
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
     * @listens vDesk.Packages.PackageList.Item#event:select
     * @fires vDesk.Packages.PackageList#select
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
            sender:  this,
            item:    Event.detail.sender,
            package: Event.detail.package
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect, true);
    };

    /**
     * Searches the PackageList for an Item by a specified Package name.
     * @param {String} Name The name of the Package of the Item to find.
     * @return {vDesk.Packages.PackageList.Item|null} The found Item; otherwise, null.
     */
    this.Find = function(Name) {
        Ensure.Parameter(Name, Type.String, "Name");
        return Items.find(Item => Item.Package.Name === Name) ?? null;
    };

    /**
     * Adds an Item to the PackageList.
     * @param {vDesk.Packages.PackageList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Packages.PackageList.Item, "Item");
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Removes an Item from the PackageList.
     * @param {vDesk.Packages.PackageList.Item|Null} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Packages.PackageList.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * Removes every Item from the PackageList.
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
    Control.className = "PackageList BorderLight";
    Control.addEventListener("select", OnSelect, true);

    //Fill PackageList.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.Packages.PackageList.Item, "Item");
        Control.appendChild(Item.Control);
    });
};

/**
 * Factory method that creates a PackageList containing every existing user.
 * @param {Boolean} [View=true] Flag indicating whether the PackageList will contain only reduced data views.
 * @return {vDesk.Packages.PackageList} A PackageList containing every existing Package.
 */
vDesk.Packages.PackageList.FromPackages = function(View = true) {
    if(View) {
        return new vDesk.Packages.PackageList(
            vDesk.Packages.Packages.map(Package => new vDesk.Packages.PackageList.Item(Package))
        );
    }

    const Response = vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Packages",
                Command:    "Installed",
                Parameters: {View: false},
                Ticket:     vDesk.User.Ticket
            }
        )
    );
    if(Response.Status) {
        return new vDesk.Packages.PackageList(
            Response.Data.map(Package => new vDesk.Packages.PackageList.Item(vDesk.Packages.Package.FromDataView(Package)))
        );
    } else {
        alert(Response.Data);
    }

};