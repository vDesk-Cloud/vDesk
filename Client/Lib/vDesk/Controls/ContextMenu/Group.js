"use strict";
/**
 * Initializes a new instance of the Group class.
 * @class Represents a Group of Items of a ContextMenu.
 * @param {String} Name The name of the Group.
 * @param {String} Icon The icon of the Group.
 * @param {Function} [Condition=()] The condition of the Group.
 * @param {Array<vDesk.Controls.ContextMenu.Item|vDesk.Controls.ContextMenu.Group>} Items Initializes the Group with the specified set of Items.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {?String} Name Gets or sets the name of the Group.
 * @property {?Blob} Icon Gets or sets the icon of the Group.
 * @property {Boolean} Visible Gets or sets a value indicating whether the Group is visible.
 * @property {Function} Condition Gets or sets the condition of the Group.
 * @property {Array<vDesk.Controls.ContextMenu.Item|vDesk.Controls.ContextMenu.Group>} Items Gets or sets the Items of the Group.
 * @memberOf vDesk.Controls.ContextMenu
 * @augments vDesk.Controls.ContextMenu.Item
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.ContextMenu.Group = function Group(Name, Icon, Condition, Items = []) {
    this.Extends(vDesk.Controls.ContextMenu.Item, Name, "group", Icon, Condition);
    Ensure.Parameter(Items, Array, "Items");

    Object.defineProperties(this, {
        Items:  {
            enumerable: true,
            get:        () => Items,
            set:        Value => {
                Ensure.Property(Value, Array, "Items");
                //Remove items.
                Items.forEach(Item => MenuList.removeChild(Item.Control));

                //Clear array
                Items = Value;

                //Append new entries.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, [vDesk.Controls.ContextMenu.Item, vDesk.Controls.ContextMenu.Group], "Item");
                    Fragment.appendChild(Item.Control);
                });
                MenuList.appendChild(Fragment);
            }
        },
        Action: {
            enumerable: true,
            value:      ""
        }
    });

    /**
     * Eventhandler that listens on the 'mouseenter' event and shows the Items and Groups of the Group.
     */
    const OnMouseEnter = () => {
        ContextMenu.classList.add("Visible");
        ContextMenu.classList.add(
            (window.innerWidth - this.Control.parentNode.parentNode.offsetLeft) < this.Control.offsetWidth ? "Left" : "Right");
        ContextMenu.classList.add(
            (window.innerHeight - this.Control.parentNode.parentNode.offsetTop) < this.Control.offsetHeight ? "Top" : "Right");
    };

    /**
     * Eventhandler that listens on the 'mouseleave' event and hides the Items and Groups of the Group.
     */
    const OnMouseLeave = () => {
        ContextMenu.classList.remove("Visible");
        ContextMenu.classList.remove("Left");
        ContextMenu.classList.remove("Right");
    };

    /**
     * Adds an Item or Group to the Group.
     * @param {vDesk.Controls.ContextMenu.Item|vDesk.Controls.ContextMenu.Group} Item The Item or Group to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, [vDesk.Controls.ContextMenu.Item, vDesk.Controls.ContextMenu.Group], "Item");
        Items.push(Item);
        MenuList.appendChild(Item.Control);
    };

    /**
     * Searches the Group for an Item or Group matching a specified action.
     * @param {String} Action The action of the Item or Group to search.
     * @return {vDesk.Controls.ContextMenu.Item|vDesk.Controls.ContextMenu.Group|null} The found menuitem, null if none was found.
     */
    this.Find = function(Action) {
        Ensure.Parameter(Action, Type.String, "Action");
        return Items.find(Item => Item.Action === Action) || null;
    };

    /**
     * Removes an Item or Group from the Group.
     * @param {vDesk.Controls.ContextMenu.Item} Item The Item or Group to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, [vDesk.Controls.ContextMenu.Item, vDesk.Controls.ContextMenu.Group], "Item");
        const FoundItem = this.Find(Item.Action);
        if(FoundItem !== null) {
            MenuList.removeChild(FoundItem.Control);
            Items.splice(Items.indexOf(FoundItem), 1);
        }
    };

    /**
     * Removes all Item or Groups from the Group.
     */
    this.Clear = function() {
        Items.forEach(Item => MenuList.removeChild(Item.Control));
        Items = [];
    };

    /**
     * Shows the Items of the Group according a specified target.
     * @param {*} Target The current target of the ContextMenu of the Group.
     */
    this.Show = function(Target) {
        this.Parent.Show(Target);
        Items.forEach(Item => Item.Show(Target));
    };

    /**
     * The contextmenu of the Group.
     * @type {HTMLDivElement}
     */
    const ContextMenu = document.createElement("div");
    ContextMenu.className = "Group";

    /**
     * The menu list of the Group.
     * @type {HTMLUListElement}
     */
    const MenuList = document.createElement("ul");
    MenuList.className = "Items";

    //Append new entries.
    Items.forEach(Item => {
        Ensure.Parameter(Item, [vDesk.Controls.ContextMenu.Item, vDesk.Controls.ContextMenu.Group], "Item");
        MenuList.appendChild(Item.Control);
    });

    ContextMenu.appendChild(MenuList);

    this.Control.appendChild(ContextMenu);
    this.Control.addEventListener("mouseenter", OnMouseEnter, false);
    this.Control.addEventListener("mouseleave", OnMouseLeave, false);

};