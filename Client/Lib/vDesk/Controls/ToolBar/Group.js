"use strict";
/**
 * Initializes a new instance of the Group class.
 * @class Represents a Group of the ToolBar.
 * @param {String} [Title=""] Initializes the Group with the specified title.
 * @param {Array<vDesk.Controls.ToolBar.Item>} [Items=[]] Initializes the Group with the specified set of Items.
 * @property {HTMLElement} Control Control Gets the underlying DOM-Node.
 * @property {string} Title Gets or sets the title of the toolbargroup.
 * @property {Array<vDesk.Controls.ToolBar.Item>} Items Gets or sets the toolbaritems of the toolbargroup.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.ToolBar.Group = function(Title = "", Items = []) {
    Ensure.Parameter(Title, Type.String, "Title");
    Ensure.Parameter(Items, Array, "Items");

    Object.defineProperties(this, {
        Control: {
            get: () => Control
        },
        Title:   {
            get: () => TitleLabel.textContent,
            set: Value => {
                Ensure.Property(Value, Type.String, "Title");
                TitleLabel.textContent = Title;
            }
        },
        Items:   {
            get: () => Items,
            set: Value => {
                Ensure.Property(Value, Array, "Items");

                //Remove Items.
                Items.forEach(Item => Control.removeChild(Item.Control));

                //Clear array.
                Items = [];

                //Append new Items.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Controls.ToolBar.Item, "Item");
                    Items.push(Item);
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        }
    });

    /**
     * Adds an Item to the Group.
     * @param {vDesk.Controls.ToolBar.Item} Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Controls.ToolBar.Item, "Item");
        Control.appendChild(Item.Control);
        Items.push(Item);
    };

    /**
     * Removes an Item from the Group.
     * @param {vDesk.Controls.ToolBar.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Controls.ToolBar.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Index, 1);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLElement}
     */
    const Control = document.createElement("nav");
    Control.className = "Group Foreground";

    /**
     * The title of the Group.
     * @type {HTMLSpanElement}
     */
    const TitleLabel = document.createElement("span");
    TitleLabel.className = "Title Font Light";
    TitleLabel.textContent = Title;

    Control.appendChild(TitleLabel);

    Items.forEach(Item => Control.appendChild(Item.Control));
};
