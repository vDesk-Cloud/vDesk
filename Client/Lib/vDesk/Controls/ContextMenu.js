"use strict";
/**
 * Fired if an Item of the ContextMenu has been clicked on.
 * @event vDesk.Controls.ContextMenu#submit
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'submit' event.
 * @property {vDesk.Controls.ContextMenu} detail.sender The current instance of the ContextMenu.
 * @property {String} detail.action The action of the Item that has been clicked on.
 * @property {String} detail.target The target of the ContextMenu of the Item that has been clicked on.
 */
/**
 * Initializes a new instance of the ContextMenu class.
 * @class Represents a ContextMenu with Items and Groups.
 * @param {Array<vDesk.Controls.ContextMenu.Item>} Items Initializes the ContextMenu with the specified set of Items.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Object} Target Gets the current target of the ContextMenu.
 * @property {Boolean} Visible Gets or sets a value indicating whether the ContextMenu is visible.
 * @property {Array<vDesk.Controls.ContextMenu.Item>} Items Gets or sets the Items of the ContextMenu.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.ContextMenu = function ContextMenu(Items = []) {
    Ensure.Parameter(Items, Array, "Items");

    /**
     * The current target of the ContextMenu.
     * @type {*}
     */
    let CurrentTarget = null;

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
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => Fragment.appendChild(Item.Control));
                Control.appendChild(Fragment);
            }
        },
        Target:  {
            enumerable: true,
            get:        () => CurrentTarget
        },
        Visible: {
            enumerable: true,
            get:        () => Control.style.display === "block",
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Visible");
                if(Value) {
                    Control.style.display = "block";
                } else {
                    Control.style.display = "none";
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'select' event and emits the 'submit' event if an Item has been selected.
     * @listens vDesk.Controls.ContextMenu.Item#event:select
     * @fires vDesk.Controls.ContextMenu#submit
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("submit", {
            sender: this,
            action: Event.detail.sender.Action,
            target: CurrentTarget
        }).Dispatch(Control);
    };

    /**
     * Adds an Item to the ContextMenu.
     * @param {vDesk.Controls.ContextMenu.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Controls.ContextMenu.Item, "Item");
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Searches the ContextMenu for an Item or Group matching a specified action.
     * @param {String} Action The action of the Item to search.
     * @return {vDesk.Controls.ContextMenu.Item|null} The found menuitem, null if none was found.
     */
    this.Find = function(Action) {
        Ensure.Parameter(Action, Type.String, "Action");
        return Items.find(Item => Item.Action === Action) ?? null;
    };

    /**
     * Removes all Items and Groups from the ContextMenu.
     */
    this.Clear = function() {
        Items.forEach(Item => Control.removeChild(Item.Control));
        Items = [];
    };

    /**
     * Removes an Item from the ContextMenu.
     * @param {vDesk.Controls.ContextMenu.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Controls.ContextMenu.Item, "Item");
        const FoundItem = this.Find(Item.Action);
        if(FoundItem !== null) {
            Control.removeChild(FoundItem.Control);
            Items.splice(Items.indexOf(FoundItem), 1);
        }
    };

    /**
     * Displays the ContextMenu at the specified target and location.
     * @param {Object} Target The target the ContextMenu points to.
     * @param {Number} Left The left offset of the target.
     * @param {Number} Top The top offset of the target.
     */
    this.Show = function(Target, Left, Top) {
        CurrentTarget = Target;

        //Check if the right window-border has been reached.
        if(window.innerWidth - Left < 200) {
            Left -= 200;
        }

        Items.forEach(Item => Item.Show(Target));
        Control.style.left = `${Left}px`;
        Control.style.top = `${Top}px`;
        Control.style.display = "block";
    };

    /**
     * Hides the ContextMenu.
     */
    this.Hide = function() {
        CurrentTarget = null;
        Items.forEach(Item => Item.Hide());
        Control.style.display = "none";
    };

    /**
     * Removes the ContextMenu.
     */
    this.Remove = function() {
        if(this.Visible) {
            this.Hide();
            document.body.removeChild(Control);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "ContextMenu BorderDark";
    Control.addEventListener("select", OnSelect);
    Items.forEach(Item => Control.appendChild(Item.Control));
    document.body.appendChild(Control);
};