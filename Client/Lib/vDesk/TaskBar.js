"use strict";
/**
 * Initializes a new instance of the TaskBar class.
 * @constructor
 * @class Represents the TaskBar of the Client
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.TaskBar.Item>} Items Gets the Items of the TaskBar.
 * @property {vDesk.TaskBar.Item} Selected Gets the current selected Item of the TaskBar.
 * @memberOf vDesk
 */
vDesk.TaskBar = function TaskBar() {

    /**
     * The items of the TaskBar.
     * @type {Array<vDesk.TaskBar.Item>}
     */
    let Items = [];

    /**
     * The currently focused item of the TaskBar.
     * @type {vDesk.TaskBar.Item}
     */
    let FocusedItem = {Focus: false};

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Items:    {
            enumerable: true,
            get:        () => Items
        },
        Selected: {
            enumerable: true,
            get:        () => Items
        }
    });

    /**
     * Eventhandler that listens on the 'show' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Window#event:show
     */
    const OnShow = Event => {
        if(Event.detail.sender instanceof vDesk.Controls.Window) {
            const Item = new vDesk.TaskBar.Item(Event.detail.sender);
            FocusedItem.Focus = false;
            FocusedItem = Item;
            FocusedItem.Focus = true;
            Items.push(Item);
            Control.appendChild(Item.Control);
            window.addEventListener("click", OnClick, {once: true});
        }
    };

    /**
     * Eventhandler that listens on the 'focus' event and sets the focused Window on top.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Window#event:focus
     */
    const OnFocus = Event => {
        FocusedItem.Focus = false;
        FocusedItem = Event.detail.sender;
        FocusedItem.Focus = true;
        Items.sort((a, b) => a.Window.StackOrder - b.Window.StackOrder)
            .reduce((StackOrder, Item) => Item.Window.StackOrder = StackOrder + 10, 500);
        window.addEventListener("click", OnClick, {once: true});
    };

    /**
     * Removes the TaskBar Item of a Window that has been closed.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Window#event:closed
     */
    const OnClosed = Event => {
        Control.removeChild(Event.detail.sender.Control);
        Items.splice(Items.indexOf(Event.detail.sender), 1);
    };

    /**
     * Removes the focus of eventual focused TaskBar Item of a Window that has been minimized.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Window#event:minimized
     */
    const OnMinimized = Event => {
        if(Event.detail.sender === FocusedItem) {
            FocusedItem.Focus = false;
            FocusedItem = {Focus: false};
        }
    };

    /**
     * Removes the focus from the current focus window.
     */
    const OnClick = function() {
        FocusedItem.Focus = false;
        FocusedItem = {Focus: false};
    };

    /**
     * Minimizes and hides all current selected Windows of the TaskBar.
     */
    const HideAll = function() {
        Items.forEach(Item => {
            Item.Focus = false;
            Item.Window.Minimize();
        });
    };

    /**
     * Adds an Item to the TaskBar.
     * @param {vDesk.TaskBar.Item} Item The Item to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.TaskBar.Item, "Item");
        Items.push(Item);
        Control.appendChild(Item.Control);
    };

    /**
     * Removes an Item from the TaskBar.
     * @param {vDesk.TaskBar.Item} Item The Item to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.TaskBar.Item, "Item");
        const Index = Items.indexOf(Item);
        if(~Index) {
            Items.splice(Index, 1);
            Control.removeChild(Item.Control);
        }
    };

    /**
     * Clears the TaskBar.
     */
    this.Clear = function() {
        Items.forEach(Item => {
            Item.Window.Close();
            Control.removeChild(Item.Control);
        });
        Items = [];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "TaskBar";
    window.addEventListener("show", OnShow, false);
    Control.addEventListener("closed", OnClosed, false);
    Control.addEventListener("focused", OnFocus, false);
    Control.addEventListener("minimized", OnMinimized, false);

    /**
     * The hide all button of the TaskBar.
     * @type {HTMLButtonElement}
     */
    const HideAllButton = document.createElement("button");
    HideAllButton.className = "HideAll Button BorderLight";
    HideAllButton.addEventListener("click", HideAll, false);

    Control.appendChild(HideAllButton);

    /**
     * Minimizes and hides all current selected windows of the TaskBar.
     */
    this.HideAll = function() {
        HideAll();
    };
};