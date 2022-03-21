"use strict";
/**
 * Fired if an Item of the ResultList has been selected.
 * @event vDesk.Search.ResultList#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Search.ResultList} detail.sender The current instance of the ResultList.
 * @property {vDesk.Search.ResultList.Item} detail.item The Item of the ResultList that has been selected.
 */
/**
 * Fired if an Item of the ResultList has been opened.
 * @event vDesk.Search.ResultList#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Search.ResultList} detail.sender The current instance of the ResultList.
 * @property {vDesk.Search.ResultList.Item} detail.item The Item of the ResultList that has been opened.
 */
/**
 * Initializes a new instance of the ResultList class.
 * @class Represents collection of ResultListItems according to a previous performed search operation.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Search.ResultList.Item>} Items Gets or sets the ResultListItems of the ResultList.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the ResultList ist enabled.
 * @memberOf vDesk.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Search.ResultList = function ResultList() {

    /**
     * The current type sort order of the ResultList.
     * @type {Boolean}
     */
    let SortOrderType = true;

    /**
     * The current name sort order of the ResultList.
     * @type {Boolean}
     */
    let SortOrderName = false;

    /**
     * The items of the ResultList.
     * @type Array<vDesk.Search.ResultList.Item>
     */
    let Items = null;

    /**
     * Flag indicating whether the ResultList is enabled.
     * @type {Boolean}
     */
    let Enabled = null;

    /**
     * The collator of the ResultList.
     * @type {Intl.Collator}
     */
    const Collator = new Intl.Collator(vDesk.Security.User.Current.Locale.toLowerCase(), {
        sensitivity: "base",
        numeric:     true
    });

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

                //Remove items.
                Items.forEach(Item => Control.removeChild(Item.Control));

                //Clear array.
                Items = Value;

                //Append new items.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Search.ResultList.Item, "Item");
                    Fragment.appendChild(Item.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Enabled: {
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
     * @listens vDesk.Search.ResultList.Item#event:select
     * @fires vDesk.Search.ResultList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        Event.stopPropagation();
        Control.removeEventListener("select", OnSelect, true);
        new vDesk.Events.BubblingEvent("select", {
            sender: this,
            item:   Event.detail.sender
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect, true);
    };

    /**
     * Eventhandler that listens on the 'open' event.
     * @listens vDesk.Search.ResultList.Item#event:open
     * @fires vDesk.Search.ResultList#event:open
     * @param {CustomEvent} Event
     */
    const OnOpen = Event => {
        Event.stopPropagation();
        Control.removeEventListener("open", OnOpen, true);
        new vDesk.Events.BubblingEvent("open", {
            sender: this,
            item:   Event.detail.sender
        }).Dispatch(Control);
        Control.addEventListener("open", OnOpen, true);
    };

    /**
     * Eventhandler that listens on the 'click' event and sorts the items of the ResultList according their type.
     */
    const OnClickTypeSortButton = () => {

        SortOrderType = !SortOrderType;
        if(SelectedSortButton === TypeSortButton) {
            Items.reverse();
        } else {
            Items.sort(SortOrderType ? TypeAscending : TypeDescending);
        }

        TypeSortButton.textContent = `${vDesk.Locale.vDesk.Type} ${SortOrderType ? "▲" : "▼"}`;
        NameSortButton.textContent = `${vDesk.Locale.vDesk.Name}   `;
        SelectedSortButton = TypeSortButton;
        window.requestAnimationFrame(Reorder);
    };

    /**
     * Eventhandler that listens on the click event and sorts the items of the ResultList according their name.
     */
    const OnClickNameSortButton = () => {

        SortOrderName = !SortOrderName;
        if(SelectedSortButton === NameSortButton) {
            Items.reverse();
        } else {
            Items.sort(SortOrderName ? NameAscending : NameDescending);
        }

        NameSortButton.textContent = `${vDesk.Locale.vDesk.Name} ${SortOrderName ? "▲" : "▼"}`;
        TypeSortButton.textContent = `${vDesk.Locale.vDesk.Type}   `;
        SelectedSortButton = NameSortButton;
        window.requestAnimationFrame(Reorder);
    };

    /**
     * Reorders the items of the ResultList.
     * @ignore
     */
    function Reorder() {
        const Fragment = document.createDocumentFragment();
        //Remove items.
        Items.forEach(Item => Control.removeChild(Item.Control));
        Items.forEach(Item => Fragment.appendChild(Item.Control));
        Control.appendChild(Fragment);
    }

    /**
     * Predicate for sorting the items of the ResultList based on their type according in an ascending alphabetical order.
     * @ignore
     */
    const TypeAscending = (A, B) => Collator.compare(A.Type, B.Type);

    /**
     * Predicate for sorting the items of the ResultList based on their type according in an descending alphabetical order.
     * @ignore
     */
    const TypeDescending = (A, B) => TypeAscending(A, B) * -1;

    /**
     * Predicate for sorting the items of the ResultList based on their name according in an ascending alphabetical order.
     * @ignore
     */
    const NameAscending = (A, B) => Collator.compare(A.Name, B.Name);

    /**
     * Predicate for sorting the items of the ResultList based on their name according in an descending alphabetical order.
     * @ignore
     */
    const NameDescending = (A, B) => NameAscending(A, B) * -1;

    Items = [];

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "ResultList";
    Control.addEventListener("select", OnSelect, true);
    Control.addEventListener("open", OnOpen, true);

    /**
     * The header of the ResultList.
     * @type {HTMLLIElement}
     */
    const Header = document.createElement("li");
    Header.className = "Header";

    /**
     * The sort by type button of the ResultList.
     * @type {HTMLButtonElement}
     */
    const TypeSortButton = document.createElement("button");
    TypeSortButton.className = "Button Type BorderDark Font Dark";
    TypeSortButton.textContent = `${vDesk.Locale.vDesk.Type} ▲`;
    TypeSortButton.addEventListener("click", OnClickTypeSortButton, false);

    /**
     * The sort by name button of the ResultList.
     * @type {HTMLButtonElement}
     */
    const NameSortButton = document.createElement("button");
    NameSortButton.className = "Button Name BorderDark Font Dark";
    NameSortButton.textContent = `${vDesk.Locale.vDesk.Name}   `;
    NameSortButton.addEventListener("click", OnClickNameSortButton, false);

    Header.appendChild(TypeSortButton);
    Header.appendChild(NameSortButton);
    Control.appendChild(Header);

    /**
     * The current selected sort button/type of the ResultList.
     * @type {HTMLButtonElement}
     */
    let SelectedSortButton = TypeSortButton;

    /**
     * Adds a ResultListItem to the ResultList.
     * @param {vDesk.Search.ResultList.Item} Item The item to add.
     */
    this.Items.Add = function(Item) {
        Ensure.Parameter(Item, vDesk.Search.ResultList.Item, "Item");
        //Check if the item is in the ResultList.
        const Index = Items.indexOf(Item);
        if(~Index) {
            Items[Index] = Item;
        } else {
            Items.push(Item);
            Control.appendChild(Item.Control);
        }
    };

    /**
     * Removes a ResultListItem from the ResultList.
     * @param {vDesk.Search.ResultList.Item} Item The item to remove.
     */
    this.Items.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Search.ResultList.Item, "Item");
        //Check if the item is in the ResultList.
        const Index = Items.indexOf(Item);
        if(~Index) {
            Control.removeChild(Item.Control);
            Items.splice(Items.indexOf(Item), 1);
        }
    };

    //Removes all Items from the ResultList.
    this.Items.Clear = function() {
        //Remove elements.
        Items.forEach(Item => Control.removeChild(Item.Control));
        //Clear array
        Items.splice(0, Items.length);
    };
};
