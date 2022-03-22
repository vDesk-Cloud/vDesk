"use strict";
/**
 * Fired if the Item has been opened.
 * @event vDesk.Archive.TreeView.Item#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Archive.TreeView.Item} detail.sender The current instance of the Item.
 */
/**
 * Fired if the Item has been expanded.
 * @event vDesk.Archive.TreeView.Item#expand
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'expand' event.
 * @property {vDesk.Archive.TreeView.Item} detail.sender The current instance of the Item.
 */
/**
 * Initializes a new instance of the Item class.
 * @class Represents an Item of the TreeView of the Archive.
 * @param {vDesk.Archive.Element} Element Initializes the item with the specified Element.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {?Number} ID Gets or sets the ID of the Item.
 * @property {?String} Name Gets or sets the name of the Item.
 * @property {Number} Type Gets or sets the type of the Item.
 * @property {?Number} Parent Gets or sets the ID of the parent of the Item.
 * @property {?String} Icon Gets or sets the icon of the Item.
 * @property {?String} Thumbnail Gets or sets the thumbnail of the Item.
 * @property {Boolean} ShowThumbnail Gets or sets a value indicating whether the thumbnail of the Item will be displayed instead of the icon.
 * @property {Boolean} Expanded Gets or sets a value indicating whether the Item is expanded.
 * @property {Array<vDesk.Archive.TreeView.Item>} Children Gets or sets the children of the Item.
 * @memberOf vDesk.Archive.TreeView
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.TreeView.Item = function(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    /**
     * Flag which determines if the Item is expanded.
     * @type {Boolean}
     */
    let Expanded = false;

    /**
     * The children of the Item.
     * @type {Array.<vDesk.Archive.TreeView.Item>}
     */
    let Children = [];

    /**
     * The dragcounter indicating drag-operations for childcontrols.
     * @type {Number}
     */
    let DragCounter = 0;

    Object.defineProperties(this, {
        Control:  {
            get: () => Control
        },
        Element:  {
            get: () => Element,
            set: Value => {
                Ensure.Property(Value, vDesk.Archive.Element, "Element");
                Element = Value;
                Title.textContent = Value.Name;
                if(Value.Type === vDesk.Archive.Element.File){
                    Expander.removeEventListener("click", OnClickExpander);
                    Expander.textContent = "";
                }else{
                    Expander.addEventListener("click", OnClickExpander);
                    Expander.textContent = "+";
                }
                Icon.src = Value?.Thumbnail ?? vDesk.Visual.Icons.Archive?.[Value?.Extension ?? "Folder"] ?? vDesk.Visual.Icons.Archive.Folder;
            }
        },
        Expanded: {
            get: () => Expanded,
            set: Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Expanded");
                Expanded = Value;
                Expander.textContent = Value ? "-" : "+";
                ChildList.classList.toggle("Expanded", Value);
            }
        },
        Children: {
            get: () => Children,
            set: Value => {
                Ensure.Property(Value, Array, "Children");

                //Remove children.
                Children.forEach(Child => ChildList.removeChild(Child.Control));

                //Clear array
                Children = [];

                //Append new Items.
                let oFragment = document.createDocumentFragment();
                Value.forEach(Item => {
                    Ensure.Parameter(Item, vDesk.Archive.TreeView.Item, "Item");
                    Children.push(Item);
                    oFragment.appendChild(Item.Control);
                });
                ChildList.appendChild(oFragment);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and emits the open event.
     * @param {MouseEvent} Event
     * @fires vDesk.Archive.Element#open
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("open", {sender: Element}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'click' event on the expander and emits the 'expand' event.
     * Expands/collapses the list of children of the Item.
     * @param {MouseEvent} Event
     * @fires vDesk.Archive.TreeView.Item#expand
     */
    const OnClickExpander = Event => {
        Event.stopPropagation();
        if(Expanded){
            Expander.textContent = "+";
            ChildList.classList.remove("Expanded");
            Expanded = false;
        }else{
            if(Children.length === 0){
                new vDesk.Events.BubblingEvent("expand", {sender: this}).Dispatch(Control);
            }
            Expander.textContent = "-";
            ChildList.classList.add("Expanded");
            Expanded = true;
        }
    };

    /**
     * Eventhandler that listens on the 'contextmenu' event.
     * @fires vDesk.Archive.Element#context
     * @param {MouseEvent} Event
     */
    const OnContextMenu = Event => {
        Event.stopPropagation();
        Event.preventDefault();
        if(Element.ID !== null){
            new vDesk.Events.BubblingEvent("context", {
                sender: Element,
                x:      Event.pageX,
                y:      Event.pageY
            }).Dispatch(Control);
        }
        return false;
    };

    /**
     * Listens on the dragstart event and sets the ID of the dragged element into the datatransfer.
     * @param {DragEvent} Event
     */
    const OnDragStart = Event => {
        Event.stopPropagation();
        Event.dataTransfer.effectAllowed = "move";
        Event.dataTransfer.setReference(Element);
        return false;
    };

    /**
     * Eventhandler that listens on the 'dragenter' event and adds hover effects.
     * @return {Boolean}
     */
    const OnDragEnter = Event => {
        Event.stopPropagation();
        DragCounter++;
        Control.classList.toggle("DropTarget");
        return false;
    };

    /**
     * Eventhandler that listens on the 'drageleave' event and removes hover effects.
     * @return {Boolean}
     */
    const OnDragLeave = Event => {
        Event.stopPropagation();
        DragCounter--;
        if(DragCounter === 0){
            Control.classList.toggle("DropTarget", false);
        }
        return false;
    };

    /**
     * Eventhandler that listens on the 'dragover' event.
     * @param {MouseEvent} Event
     */
    const OnDragOver = Event => Event.preventDefault();

    /**
     * Eventhandler that listens on the 'drop' event and emits the 'filedrop' event if the current Element is a folder
     * and any files hav been dropped on the Element or the 'elementdrop' event if another Element has been dropped onto the current Element.
     * @fires vDesk.Archive.Element#elementdrop
     * @fires vDesk.Archive.Element#filedrop
     * @param {DragEvent} Event
     */
    const OnDrop = Event => {
        Event.stopPropagation();
        Event.preventDefault();

        let Target = Element.Type === vDesk.Archive.Element.Folder ? Element : Element.Parent;

        //Check if the Element is a folder and any files have been dropped on the Element.
        if(
            Event.dataTransfer.files !== undefined
            && Event.dataTransfer.files.length > 0
        ){
            new vDesk.Events.BubblingEvent("filedrop", {
                sender: Target,
                files:  Array.from(Event.dataTransfer.files)
            }).Dispatch(Control);
        }else{
            //Get the ID of the dropped Element.
            const DroppedElement = Event.dataTransfer.getReference();

            //Don't fire event, if it's dropped on itself.
            if(DroppedElement.ID !== Target.ID && DroppedElement.Parent.ID !== Target.ID){
                new vDesk.Events.BubblingEvent("elementdrop", {
                    sender:  Target,
                    element: DroppedElement
                }).Dispatch(Control);
            }
        }

    };

    /**
     * Adds an Item or Element to the child collection of the Item.
     * @param {vDesk.Archive.TreeView.Item|vDesk.Archive.Element} Item The Item or Element to add.
     */
    this.Add = function(Item) {
        Ensure.Parameter(Item, [vDesk.Archive.TreeView.Item, vDesk.Archive.Element], "Item");

        //Check if an Element has been passed and transform it into an Item.
        if(Item instanceof vDesk.Archive.Element){
            Item = new vDesk.Archive.TreeView.Item(Item);
        }

        //Apply specified Item.
        Children.push(Item);
        ChildList.appendChild(Item.Control);
    };

    /**
     * Removes an Item or Element from the child collection of the Item.
     * @param {vDesk.Archive.TreeView.Item|vDesk.Archive.Element} Item The Item or Element to remove.
     */
    this.Remove = function(Item) {
        Ensure.Parameter(Item, vDesk.Archive.TreeView.Item, "Item");

        //Get the Item to remove.
        const Child = Children.find(Child => Child.Element.ID === Item.Element.ID);
        //Remove the control from the child list and collection.
        if(Child !== undefined){
            ChildList.removeChild(Child.Control);
            Children.splice(Children.indexOf(Child), 1);
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Item";
    Control.draggable = true;
    Control.addEventListener("click", OnClick);
    Control.addEventListener("contextmenu", OnContextMenu, false);
    Control.addEventListener("dragstart", OnDragStart, false);
    Control.addEventListener("dragover", OnDragOver, false);
    Control.addEventListener("dragenter", OnDragEnter, false);
    Control.addEventListener("dragleave", OnDragLeave, false);
    Control.addEventListener("drop", OnDrop, false);

    /**
     * The expander of the Item.
     * @type {HTMLDivElement}
     */
    const Expander = document.createElement("div");
    Expander.className = "Expander Font Dark";

    //Check if the Item represents a folder.
    if(Element.Type === vDesk.Archive.Element.Folder){
        Expander.textContent = "+";
        Expander.addEventListener("click", OnClickExpander);
    }else{
        Expander.textContent = "";
    }
    Control.appendChild(Expander);

    /**
     * The preview icon of the Item.
     * @type {HTMLImageElement}
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.src = Element?.Thumbnail ?? vDesk.Visual.Icons.Archive?.[Element?.Extension ?? "Folder"] ?? vDesk.Visual.Icons.Archive.Folder;
    Control.appendChild(Icon);

    /**
     * The title label of the Item.
     * @type {HTMLSpanElement}
     */
    const Title = document.createElement("span");
    Title.className = "Title Font Dark";
    Title.textContent = Element.Name;
    Control.appendChild(Title);

    /**
     * The list of child Elements of the Item.
     * @type {HTMLUListElement}
     */
    const ChildList = document.createElement("ul");
    ChildList.className = "ChildList";
    Control.appendChild(ChildList);

};

/**
 * Factory method that creates a new Item containing the data of a specified Element.
 * @param {vDesk.Archive.Element} Element The Element to create an Item of.
 * @return {vDesk.Archive.TreeView.Item} An Item containing the data of the specified Element.
 */
vDesk.Archive.TreeView.Item.FromElement = function(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
    return new vDesk.Archive.TreeView.Item(Element);
};