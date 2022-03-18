"use strict";
/**
 * Initializes a new instance of the TreeView class.
 * @class Represents the TreeView of the archive.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array.<vDesk.Archive.Element>} Items Gets the items within the index of the TreeView.
 * @property {vDesk.Archive.TreeView.Item} RootElement Gets or sets the root element of the archive.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.TreeView = function TreeView() {

    /**
     * The root Element of the archive.
     * @type {null|vDesk.Archive.TreeView.Item}
     */
    let RootElement = null;

    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        Items:       {
            enumerable: true,
            get:        () => Index
        },
        RootElement: {
            enumerable: true,
            get:        () => RootElement,
            set:        Value => {
                Ensure.Property(Value, vDesk.Archive.TreeView.Item, "RootElement");
                RootElement = Value;
                Control.appendChild(RootElement.Control);
                Index.push(RootElement);
            }
        }
    });

    /**
     * Gets all children of a treeviewitem.
     * @param {vDesk.Archive.TreeView.Item} Item The parent to search its children.
     * @return {Array.<vDesk.Archive.TreeView.Item>} The found children.
     */
    const GetChildren = function(Item) {
        const Children = [];
        const GetChildren = Item => {
            Item.Children.forEach(Child => {
                if(Child.Children.length > 0){
                    GetChildren(Child);
                }
                Children.push(Child);
            });
        };
        GetChildren(Item);
        return Children;
    };

    /**
     * Adds an Element to the TreeView and to the child collection of its according parent TreeView.Item.
     * @param {vDesk.Archive.Element} Element The Element to add.
     * @return {vDesk.Archive.TreeView.Item} The Item representing the added Element.
     */
    this.Add = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

        //Check if the Element is not already in the list.
        const FoundElement = this.Find(Element);
        if(FoundElement !== null){
            return FoundElement;
        }

        const Item = new vDesk.Archive.TreeView.Item(Element);

        //Add the item to the index.
        Index.push(Item);
        //Add the item to the children collection of its parent.
        const Parent = Index.find(IndexItem => IndexItem.Element.ID === Item.Element.Parent.ID);
        if(Parent !== undefined){
            Parent.Add(Item);
        }
        return Item;
    };

    /**
     * Removes an item and its children from the TreeView.
     * @param {vDesk.Archive.Element} Element The Element of the Item to remove.
     */
    this.Remove = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

        //Check if the Item is in the index; otherwise,try to find it.
        const Item = Index.find(IndexItem => IndexItem.Element.ID === Element.ID && IndexItem.Element.Name === Element.Name);
        const ParentItem = Index.find(ParentItem => ParentItem.Element.ID === Element.Parent.ID);

        if(Item !== undefined && ParentItem !== undefined){
            //Remove the Item from the parent Item.
            ParentItem.Remove(Item);
            //Remove the Item and it's children from the index.
            Index.splice(Index.indexOf(Item), 1);
            GetChildren(Item).forEach(Child => Index.splice(Index.indexOf(Child), 1));
        }
    };

    /**
     * Searches the index for an Item.
     * @param {vDesk.Archive.Element} Element The Element of the Item to find
     * @return {vDesk.Archive.TreeView.Item|null} The found Item; otherwise, null.
     */
    this.Find = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        return Index.find(IndexItem => IndexItem.Element.ID === Element.ID && IndexItem.Element.Parent.ID === Element.Parent.ID && IndexItem.Element.Name === Element.Name) ?? null;
    };

    /**
     * The index of child TreeView.Items of the TreeView.
     * @type {Array.<vDesk.Archive.TreeView.Item>}
     */
    const Index = [];

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "TreeView Background List";

};

