"use strict";
/**
 * Fired if the user dropped any files on the FolderView
 * @event vDesk.Archive.FolderView#filedrop
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'filedrop' event.
 * @property {vDesk.Archive.FolderView} detail.sender The current instance of the FolderView.
 * @property {vDesk.Archive.Element} detail.target The target folder the files have been dropped on.
 * @property {FileList} detail.files The files that have been dropped on the FolderView.
 */
/**
 * Fired if the FolderView has been right clicked on.
 * @event vDesk.Archive.FolderView#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.Archive.FolderView} detail.sender The current instance of the FolderView.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * Fired if any Elements of the FolderView have been selected.
 * @event vDesk.Archive.FolderView#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Archive.FolderView} detail.sender The current instance of the FolderView.
 * @property {Array<vDesk.Archive.Element>} detail.elements The selected Elements of the FolderView.
 */
/**
 * Initializes a new instance of the FolderView class.
 * @class Represents the main workspace for the archive module. Provides functionality for displaying Elements.
 * @param {vDesk.Archive.Element} [CurrentFolder=null] Initializes the FolderView with the specified current folder Element.
 * @param {Array<vDesk.Archive.Element>} Elements Initializes the FolderView with the specified set of Elements.
 * @param {Boolean} [Enabled=true] Flag indicating whether the FolderView is enabled.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Archive.Element} CurrentFolder Gets or sets the current folder Element of the FolderView.
 * @property {Array<vDesk.Archive.Element>} Elements Gets or sets the Elements of the FolderView.
 * @property {Array<vDesk.Archive.Element>} Selected Gets or sets the selected Elements of the FolderView. Note: This is a computed property.
 * @property {Boolean} Selected Gets or sets a value indicating whether the FolderView is enabled.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.FolderView = function FolderView(CurrentFolder = null, Elements = [], Enabled = true) {
    Ensure.Parameter(CurrentFolder, vDesk.Archive.Element, "CurrentFolder", true);
    Ensure.Parameter(Elements, Array, "Elements");
    Ensure.Parameter(Enabled, Type.Boolean, "Elements");

    /**
     * The initial vertical position of the mousepointer when an mousedown-event has occurred.
     * @type {Number}
     */
    let VerticalStartPosition = 0;

    /**
     * The initial horizontal position of the mousepointer when an mousedown-event has occurred.
     * @type {Number}
     */
    let HorizontalStartPosition = 0;

    /**
     * The current vertical position of the mousepointer when an mousemove-event has occurred.
     * @type {Number}
     */
    let VerticalPosition = 0;

    /**
     * The current horizontal position of the mousepointer when an mousemove-event has occurred.
     * @type {Number}
     */
    let HorizontalPosition = 0;

    /**
     * The current top and left offset of the FolderView.
     * @type {null|Object}
     */
    let Offset = null;

    /**
     * The current top offset in relation to the scroll amount of the FolderView.
     * @type {null|Number}
     */
    let ScrollOffset = null;

    /**
     * Flag that indicates if the control-key is being pressed.
     * @type {Boolean}
     */
    let ControlKeyPressed = false;

    Object.defineProperties(this, {
        Control:       {
            enumerable: true,
            get:        () => Control
        },
        CurrentFolder: {
            enumerable: true,
            get:        () => CurrentFolder,
            set:        Value => {
                Ensure.Property(Value, vDesk.Archive.Element, "CurrentFolder");
                CurrentFolder = Value;
            }
        },
        Elements:      {
            enumerable: true,
            get:        () => Elements,
            set:        Value => {
                Ensure.Property(Value, Array, "Elements");
                //Remove Elements.
                Elements.forEach(Element => Control.removeChild(Element.Control));

                //Clear array
                Elements = [];

                //Append new Elements.
                const Fragment = document.createDocumentFragment();

                Value.forEach(Element => {
                    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
                    Elements.push(Element);
                    Fragment.appendChild(Element.Control);
                });

                Control.appendChild(Fragment);
            }
        },
        Selected:      {
            enumerable: true,
            get:        () => Elements.filter(Element => Element.Selected),
            set:        Value => {
                Ensure.Property(Value, Array, "Elements");
                //Reset Elements.
                Elements.forEach(Element => Element.Selected = false);

                //Select Elements.
                Elements.filter(Element => Value.some(Selected => Selected.ID === Element.ID))
                    .forEach(Element => Element.Selected = true);
            }
        },
        Enabled:       {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                if(Value){
                    window.addEventListener("keydown", OnKeyDown);
                    Control.addEventListener("mousedown", OnMouseDown);
                }else{
                    window.removeEventListener("keydown", OnKeyDown);
                    Control.removeEventListener("mousedown", OnMouseDown);
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Archive.Element#event:select
     * @fires vDesk.Archive.FolderView#select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        Event.stopPropagation();
        //Reset Elements.
        if(!ControlKeyPressed){
            Elements.forEach(Element => Element.Selected = false);
        }
        Event.detail.sender.Selected = !Event.detail.sender.Selected;

        Control.removeEventListener("select", OnSelect);
        new vDesk.Events.BubblingEvent("select", {
            sender:   this,
            elements: this.Selected
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect);
    };

    /**
     * Eventhandler that listens on the 'contextmenu' event.
     * @fires vDesk.Archive.FolderView#context
     * @param {MouseEvent} Event
     */
    const OnContextMenu = Event => {
        Event.stopPropagation();
        Event.preventDefault();
        new vDesk.Events.BubblingEvent("context", {
            sender: this,
            x:      Event.pageX,
            y:      Event.pageY
        }).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'keydown' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyDown = Event => {
        if(
            Event.key === "ArrowLeft"
            || Event.key === "ArrowRight"
            || Event.key === "ArrowUp"
            || Event.key === "ArrowDown"
        ){
            const Selected = this.Selected;

            if(Selected.length > 0){
                let Element;
                let Index;
                let RowLength;
                switch(Event.key){
                    case "ArrowLeft":
                        Index = Elements.indexOf(Selected[0]);
                        if(Index > 0){
                            Element = Elements[--Index];
                        }else{
                            Element = Selected.shift();
                        }
                        break;
                    case "ArrowRight":
                        Index = Elements.indexOf(Selected[Selected.length - 1]);
                        if(Index < Elements.length - 1){
                            Element = Elements[++Index];
                        }else{
                            Element = Selected.pop();
                        }
                        break;
                    case "ArrowUp":
                        Index = Elements.indexOf(Selected[0]);
                        RowLength = Math.floor(Control.offsetWidth / 100);
                        if(Index >= RowLength){
                            Element = Elements[Index - RowLength];
                        }else{
                            Element = Selected.shift();
                        }
                        break;
                    case "ArrowDown":
                        Index = Elements.indexOf(Selected[0]);
                        RowLength = Math.floor(Control.offsetWidth / 100);
                        if(Index + RowLength < Elements.length){
                            Element = Elements[Index + RowLength];
                        }else{
                            Element = Elements[Elements.length - 1];
                        }
                        break;
                }

                Selected.forEach(Element => Element.Selected = false);
                Element.Selected = true;

                Control.removeEventListener("select", OnSelect);
                new vDesk.Events.BubblingEvent("select", {
                    sender:   this,
                    elements: [Element]
                }).Dispatch(Control);
                Control.addEventListener("select", OnSelect);

            }
        }
    };
    if(Enabled){
        window.addEventListener("keydown", OnKeyDown);
    }

    /**
     * Eventhandler that listens on the 'dragover' event and enables drop functionality on the FolderView.
     * @param {MouseEvent} Event
     */
    const OnDragOver = Event => Event.preventDefault();

    /**
     * Eventhandler that listens on the 'drop' event and validates any dropped files.
     * @fires vDesk.Archive.FolderView#filedrop
     * @param {DragEvent} Event
     */
    const OnDrop = Event => {
        Event.preventDefault();
        Event.stopPropagation();
        if(Event.dataTransfer.files !== undefined && Event.dataTransfer.files.length > 0){
            new vDesk.Events.BubblingEvent("filedrop", {
                sender: CurrentFolder,
                files:  Array.from(Event.dataTransfer.files)
            }).Dispatch(Control);
        }else{
            const DroppedElement = Event.dataTransfer.getReference();
            if(DroppedElement.ID !== CurrentFolder.ID || DroppedElement.Parent.ID !== CurrentFolder.ID){
                new vDesk.Events.BubblingEvent("elementdrop", {
                    sender:  CurrentFolder,
                    element: DroppedElement
                }).Dispatch(Control);
            }
        }
    };

    /**
     * Eventhandler that listens on the 'mousedown' event and initializes the selection rectangle.
     * @param {MouseEvent} Event
     */
    const OnMouseDown = Event => {

        if(Event.target === Control){
            // Event.preventDefault();

            //Display rectangle.
            SelectionRectangle.style.display = "block";

            Offset = vDesk.Visual.TreeHelper.GetOffset(Control);
            ScrollOffset = Control.scrollTop;

            //Start position of the mouse.
            VerticalStartPosition = Event.pageY - Offset.top + ScrollOffset;
            HorizontalStartPosition = Event.pageX - Offset.left;

            VerticalPosition = VerticalStartPosition;
            HorizontalPosition = HorizontalStartPosition;

            Control.addEventListener("mousemove", OnMouseMove);
            Control.addEventListener("mouseup", OnMouseUp);
            Control.removeEventListener("mousedown", OnMouseDown);

            window.requestAnimationFrame(UpdateRectangle);
        }
    };

    /**
     * Eventhandler that listens on the 'mousemove' event and draws the selection-rectangle.
     * @param {MouseEvent} Event
     */
    const OnMouseMove = Event => {
        Event.preventDefault();
        VerticalPosition = Event.pageY - Offset.top + ScrollOffset;
        HorizontalPosition = Event.pageX - Offset.left;
        window.requestAnimationFrame(UpdateRectangle);
    };

    /**
     * Eventhandler that listens on the 'mouseup' event and hides the selection rectangle and performs an hittest on every element of the FolderView.
     * @fires vDesk.Archive.FolderView#select
     */
    const OnMouseUp = Event => {
        Event.stopPropagation();

        //Reset Elements if a normal selection occurred.
        if(!Event.ctrlKey){
            Elements.forEach(Element => Element.Selected = false);
        }

        Elements.filter(Element => vDesk.Visual.TreeHelper.HitTest(Element.Control, SelectionRectangle))
            .forEach(Element => Element.Selected = true);

        Control.removeEventListener("select", OnSelect);
        new vDesk.Events.BubblingEvent("select", {
            sender:   this,
            elements: this.Selected
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect);

        //Reset selection rectangle.
        SelectionRectangle.style.display = "none";
        Control.addEventListener("mousedown", OnMouseDown);
        Control.removeEventListener("mousemove", OnMouseMove);
        Control.removeEventListener("mouseup", OnMouseUp);
        SelectionRectangle.style.height = "0px";
        SelectionRectangle.style.width = "0px";
        return false;
    };

    /**
     * Updates the dimensions of the selection-rectangle according to the mouse-position.
     */
    const UpdateRectangle = () => {
        const LowerY = Math.min(VerticalStartPosition, VerticalPosition);
        const HigherY = Math.max(VerticalStartPosition, VerticalPosition);

        const LowerX = Math.min(HorizontalStartPosition, HorizontalPosition);
        const HigherX = Math.max(HorizontalStartPosition, HorizontalPosition);

        SelectionRectangle.style.top = `${LowerY}px`;
        SelectionRectangle.style.left = `${LowerX}px`;
        SelectionRectangle.style.width = `${HigherX - LowerX}px`;
        SelectionRectangle.style.height = `${HigherY - LowerY}px`;
    };

    /**
     * Eventhandler that listens on the 'scroll' event and updates the scrolloffset.
     */
    const OnScroll = () => ScrollOffset = Control.scrollTop;

    /**
     * Clears the FolderView of all current displayed Elements.
     */
    this.Clear = function() {
        Elements.forEach(Element => Control.removeChild(Element.Control));
        Elements = [];
    };

    /**
     * Adds an Element to the FolderView.
     * @param {vDesk.Archive.Element} Element The Element to add.
     */
    this.Add = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        //Check if the Element is not already in the list.
        if(Element.ID === null || !Elements.some(Current => Current.ID === Element.ID)){
            Control.appendChild(Element.Control);
            Elements.push(Element);
        }
    };

    /**
     * Removes an Element from the FolderView.
     * @param {vDesk.Archive.Element} Element The Element to remove.
     */
    this.Remove = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

        //Check if the element is currently being displayed.
        const Index = Elements.indexOf(Element);
        if(~Index){
            Control.removeChild(Element.Control);
            Elements.splice(Index, 1);
        }
        //Otherwise check if an similar element with the same ID is currently being displayed.
        else{
            //Get the Element.
            const FoundElement = Elements.find(DisplayedElement => DisplayedElement.ID === Element.ID);
            //Remove the Element.
            if(FoundElement !== undefined){
                Control.removeChild(FoundElement.Control);
                Elements.splice(Elements.indexOf(FoundElement), 1);
            }
        }
    };

    /**
     * Sorts the Elements of the FolderView according to a specified predicate.
     * @param {Function} Predicate The function to determine the sort order.
     */
    this.Sort = function(Predicate) {
        Ensure.Parameter(Predicate, Type.Function, "Predicate");
        window.requestAnimationFrame(() => {
            //Remove Elements.
            Elements.forEach(Element => Control.removeChild(Element.Control));

            //Sort and reappend Elements.
            const Fragment = document.createDocumentFragment();
            Elements.sort(Predicate).forEach(Element => Fragment.appendChild(Element.Control));
            Control.appendChild(Fragment);
        });
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "FolderView";

    //Setup listeners.
    window.addEventListener("keydown", Event => ControlKeyPressed = Event.ctrlKey);
    window.addEventListener("keyup", Event => ControlKeyPressed = Event.ctrlKey);
    Control.addEventListener("click", Event => Event.stopPropagation());
    Control.addEventListener("select", OnSelect);
    Control.addEventListener("contextmenu", OnContextMenu);
    Control.addEventListener("dragover", OnDragOver);
    Control.addEventListener("drop", OnDrop);
    Control.addEventListener("mousedown", OnMouseDown);
    Control.addEventListener("scroll", OnScroll);

    /**
     * The selection rectangle of the FolderView.
     * @type {HTMLDivElement}
     */
    const SelectionRectangle = document.createElement("div");
    SelectionRectangle.style.display = "none";
    SelectionRectangle.className = "SelectionRectangle";
    Control.appendChild(SelectionRectangle);

};

/**
 * Enumeration of predefined predicates for sorting the Elements of the FolderView.
 * @enum {Function}
 */
vDesk.Archive.FolderView.Sort = {
    /**
     * Predicate for sorting Elements based on their name according in an ascending alphabetical order.
     * @constant
     * @type Function
     */
    NameAscending: (Collator => (A, B) => Collator.compare(A.Name, B.Name))
                   (new Intl.Collator(
                       vDesk.Security.User.Current.Locale.toLowerCase(), {
                           sensitivity: "base",
                           numeric:     true
                       }
                   )),

    /**
     * Predicate for sorting Elements based on their name according in an descending alphabetical order.
     * @constant
     * @type Function
     */
    NameDescending: (Collator => (A, B) => Collator.compare(A.Name, B.Name) * -1)
                    (new Intl.Collator(
                        vDesk.Security.User.Current.Locale.toLowerCase(), {
                            sensitivity: "base",
                            numeric:     true
                        }
                    )),

    /**
     * Predicate for sorting Elements based on their type according in an ascending order. (Folder < File).
     * @constant
     * @type Function
     */
    TypeAscending: (A, B) => A.Type - B.Type,

    /**
     * Predicate for sorting Elements based on their type according in an descending order. (Folder > File).
     * @constant
     * @type Function
     */
    TypeDescending: (A, B) => B.Type - A.Type
};