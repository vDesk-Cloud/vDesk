"use strict";
/**
 * Fired if an Item of the MaskList has been selected.
 * @event vDesk.MetaInformation.MaskList#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.MetaInformation.MaskList} detail.sender The current instance of the MaskList.
 * @property {Mask} detail.mask The Mask instance of the Item.
 * @property {vDesk.MetaInformation.MaskList.Item} detail.item The Item of the according Mask.
 */
/**
 * Initializes a new instance of the MaskList class.
 * @class Represents collection of all masks.
 * @param {Array<vDesk.MetaInformation.MaskList.Item>} [Items=[]] Initializes the MaskList with the specified set of Items.
 * @param {Boolean} [Enabled=true] Flag indicating whether the MaskList is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.MetaInformation.MaskList.Item>} Items Gets the mMaskListItems of the MaskList.
 * @property {vDesk.MetaInformation.MaskList.Item} Selected Gets or sets the mMaskList.Item of the current displayed mask.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the MaskList is enabled.
 * @memberOf vDesk.MetaInformation
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\MetaInformation
 */
vDesk.MetaInformation.MaskList = function MaskList(Items = [], Enabled = true) {

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

                Items.forEach(Item => Control.removeChild(Item.Control));

                Items = Value;

                const Fragment = document.createDocumentFragment();
                Items.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.MetaInformation.MaskList.Item, "Item");
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.MetaInformation.MaskList.Item, "Selected", true);
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
     * @fires vDesk.MetaInformation.MaskList#select
     */
    const OnSelect = Event => {
        Event.stopPropagation();
        if(Selected !== null){
            Selected.Selected = false;
        }
        //Mark the current selected item as the selected item.
        Selected = Event.detail.sender;
        Selected.Selected = true;

        Control.removeEventListener("select", OnSelect, true);
        new vDesk.Events.BubblingEvent("select", {
            sender: this,
            mask:   Event.detail.sender.Mask,
            item:   Event.detail.sender
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect, true);
    };

    /**
     * Searches the MaskList for an Item by a specified ID.
     * @param {Number} ID The ID of the Item to find.
     * @return {vDesk.MetaInformation.MaskList.Item|Null} The found Item, else null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "ID", true);
        return Items.find(Item => Item.Mask.ID === ID) ?? null;
    };

    /**
     * Adds an Item to the MaskList.
     * @param {vDesk.MetaInformation.MaskList.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.MetaInformation.MaskList.Item, "Item");
        //Check if an item with given ID already exists.
        if(this.Find(Item.Mask.ID) !== null){
            throw new ArgumentError(`Item with ID: '${Item.Mask.ID}' already exists!`);
        }
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Removes an Item from the MaskList.
     * @param {vDesk.MetaInformation.MaskList.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.MetaInformation.MaskList.Item, "Item");
        //Check if the row is in the mask and remove it.
        const FoundItem = this.Find(Item.Mask.ID);
        if(FoundItem !== null){
            Control.removeChild(FoundItem.Control);
            Items.splice(Items.indexOf(FoundItem), 1);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "MaskList BorderLight";
    Control.addEventListener("select", OnSelect, true);

    //Fill MaskList.
    Items.forEach(Item => {
        Ensure.Parameter(Item, vDesk.MetaInformation.MaskList.Item, "Item");
        Control.appendChild(Item.Control);
    });

    /**
     * The Item of the current displayed Mask.
     * @type {null|vDesk.MetaInformation.MaskList.Item}
     */
    let Selected = Items?.[0] ?? null;
};

/**
 * Factorymethod that creates a MaskList from every existing Mask.
 * @return {vDesk.MetaInformation.MaskList} A MaskList containing every existing Mask.
 */
vDesk.MetaInformation.MaskList.FromMasks = function() {
    return new vDesk.MetaInformation.MaskList(vDesk.MetaInformation.Masks.map(Mask => new vDesk.MetaInformation.MaskList.Item(Mask)));
};