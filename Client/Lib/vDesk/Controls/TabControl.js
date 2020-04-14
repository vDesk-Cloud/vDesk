"use strict";
/**
 * Initializes a new instance of the TabControl class.
 * @class Represents a tabbed content control.
 * @param {Array<vDesk.Controls.TabControl.TabItem>} [TabItems=[]] Initializes the TabControl with the specified set of TabItems.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Controls.TabControl.TabItem>} TabItems Gets or sets the TabItems of the TabControl.
 * @property {vDesk.Controls.TabControl.TabItem} CurrentTabItem Gets or sets the current displayed TabItem of the TabControl.
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.TabControl = function TabControl(TabItems = []) {
    Ensure.Parameter(TabItems, Array, "TabItems");

    /**
     * The current selected TabItem of the TabControl.
     * @type {null|vDesk.Controls.TabControl.TabItem}
     */
    let CurrentTabItem = null;

    Object.defineProperties(this, {
        Control:        {
            enumerable: true,
            get:        () => Control
        },
        TabItems:       {
            enumerable: true,
            get:        () => TabItems,
            set:        Value => {
                Ensure.Property(Value, Array, "TabItems");

                //Remove TabItems.
                TabItems.forEach(TabItem => Header.removeChild(TabItem.Control));
                Container.removeChild(CurrentTabItem.Content);

                //Clear array.
                TabItems = Value;

                //Append new TabItems.
                Value.foreach(TabItem => {
                    Ensure.Parameter(TabItem, vDesk.Controls.TabControl.TabItem, "TabItem");
                    Header.appendChild(TabItem.Control);
                });

                if(Value.length > 0) {
                    CurrentTabItem = Value[0];
                    CurrentTabItem.Selected = true;
                    Container.appendChild(CurrentTabItem.Content);
                }
            }
        },
        CurrentTabItem: {
            enumerable: true,
            get:        () => CurrentTabItem,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.TabControl.TabItem, "CurrentTabItem");
                CurrentTabItem.Selected = false;
                Container.replaceChild(Value.Content, CurrentTabItem.Content);
                CurrentTabItem = Value;
                CurrentTabItem.Selected = true;
            }
        },
        Selected:       {
            enumerable: true,
            get:        () => this.CurrentTabItem,
            set:        Value => this.CurrentTabItem = Value
        }
    });

    /**
     * Eventhandler that listens on the 'select' event and displays the selected TabItem.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.TabControl.TabItem#event:select
     */
    const OnSelect = Event => {
        Event.stopPropagation();
        if(Event.detail.sender !== CurrentTabItem) {
            CurrentTabItem.Selected = false;
            Container.replaceChild(Event.detail.sender.Content, CurrentTabItem.Content);
            CurrentTabItem = Event.detail.sender;
            CurrentTabItem.Selected = true;
        }
    };

    /**
     * Creates and adds a new TabItem to the TabControl.
     * @param {String} Title The title of the tabitem to add.
     * @param {Node} Content The content of the TabItem to add.
     * @return {vDesk.Controls.TabControl.TabItem} The created TabItem.
     */
    this.Create = function(Title, Content) {
        Ensure.Parameter(Title, Type.String, "Title");
        Ensure.Parameter(Content, Node, "Content");
        const TabItem = new vDesk.Controls.TabControl.TabItem(Title, Content);
        Header.appendChild(TabItem.Control);
        TabItems.push(TabItem);
        if(CurrentTabItem === null) {
            CurrentTabItem = TabItem;
            CurrentTabItem.Selected = true;
            Container.appendChild(CurrentTabItem.Content);
        }

        return TabItem;
    };

    /**
     * Adds a new TabItem to the TabControl.
     * @param {vDesk.Controls.TabControl.TabItem} TabItem The TabItem to add.
     */
    this.Add = function(TabItem) {
        Ensure.Parameter(TabItem, vDesk.Controls.TabControl.TabItem, "TabItem");

        Header.appendChild(TabItem.Control);
        TabItems.push(TabItem);

        if(CurrentTabItem === null) {
            CurrentTabItem = TabItem;
            CurrentTabItem.Selected = true;
            Container.appendChild(CurrentTabItem.Content);
        }

    };

    /**
     * Removes a TabItem from the TabControl.
     * @param {vDesk.Controls.TabControl.TabItem} TabItem The TabItem to remove.
     */
    this.Remove = function(TabItem) {
        const Item = TabItems.find(Item => Item.Title === TabItem.Title);
        if(Item !== undefined) {
            Header.removeChild(Item.Control);
            TabItems.splice(TabItems.indexOf(Item), 1);
            CurrentTabItem.Selected = false;
            Container.replaceChild(TabItems[0].Content, CurrentTabItem.Content);
            CurrentTabItem = TabItems[0];
            CurrentTabItem.Selected = true;
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "TabControl";

    /**
     * The header of the TabControl.
     * @type  {HTMLDivElement}
     */
    const Header = document.createElement("div");
    Header.className = "Header BorderDark";
    Header.addEventListener("select", OnSelect, false);

    /**
     * The container of the TabControl.
     * @type {HTMLDivElement}
     */
    const Container = document.createElement("div");
    Container.className = "Container";

    if(TabItems.length > 0) {
        TabItems.forEach(TabItem => Header.appendChild(TabItem.Control));
        CurrentTabItem = TabItems[0];
        CurrentTabItem.Selected = true;
        Container.appendChild(CurrentTabItem.Content);
    }

    Control.appendChild(Header);
    Control.appendChild(Container);

};