"use strict";

/**
 * Generic 
 * @param Items
 * @param Enabled
 * @constructor
 */
vDesk.Controls.List = function List(Items = [], Enabled = true) {
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
                    Ensure.Parameter(Item, vDesk.Controls.List.Item, "Item");
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

    this[Symbol.iterator] = function* () {

    }

    const Control = document.createElement("ul");
}