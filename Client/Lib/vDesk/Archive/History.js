"use strict";
/**
 * Fired if the History has been stepped back.
 * @event vDesk.Archive.History#previous
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'previous' event.
 * @property {vDesk.Archive.History} detail.sender The current instance of the History.
 * @property {vDesk.Archive.Element} detail.element The previous Element in the History.
 */
/**
 * Fired if the History has been stepped forward.
 * @event vDesk.Archive.History#next
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'next' event.
 * @property {vDesk.Archive.History} detail.sender The current instance of the History.
 * @property {vDesk.Archive.Element} detail.element The next Element in the History.
 */

/**
 * Fired if the parent button has been clicked.
 * @event vDesk.Archive.History#parent
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'parent' event.
 * @property {vDesk.Archive.History} detail.sender The current instance of the History.
 * @property {vDesk.Archive.Element} detail.element The parent Element in the History.
 */
/**
 * Initializes a new instance of the History class.
 * @class Represents the history of the archive.
 * @constructor
 * @param {Number} [Size=10] Initializes the History with the specified maximum amount of stored Elements.
 * @param {Boolean} [Enabled=true] Flag indicating whether the History is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Number} Size Gets or sets the maximum amount of Elements that can be stored in the History.
 * @property {Boolean} Selected Gets or sets a value indicating whether the History is enabled.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.History = function History(Size = 10, Enabled = true) {
    Ensure.Parameter(Size, Type.Number, "Size");

    /**
     * The elements of the history.
     * @type {Array<vDesk.Archive.Element>}
     */
    const Elements = [];

    /**
     * Pointer on the current element in the history.
     * @type {Number}
     */
    let CurrentPosition = 0;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Size:    {
            enumerable: true,
            get:        () => Size,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Size");
                Size = Value;
            }
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                if(Value){
                    window.addEventListener("keydown", OnKeyDown);
                }else{
                    window.removeEventListener("keydown", OnKeyDown);
                }
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Archive.History#previous
     */
    const OnClickPrevious = () => {
        //Check if the pointer is not at the first position.
        if(CurrentPosition !== 0){
            //Set pointer to the previous step.
            CurrentPosition--;

            //Create event to notify listeners that the backbutton has been pressed.
            new vDesk.Events.BubblingEvent("previous", {
                sender:  this,
                element: Elements[CurrentPosition]
            }).Dispatch(Control);

            //Disable the backbutton if the pointer has reached the first position.
            if(CurrentPosition === 0){
                Previous.disabled = true;
            }
            //Enable the forwardbutton (if it's disabled).
            Next.disabled = false;

            //Disable parent button if the root Element has been reached.
            Parent.disabled = Elements[CurrentPosition].ID === vDesk.Archive.Element.Root;
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Archive.History#next
     */
    const OnClickNext = () => {
        //Check if the pointer is not at the last position.
        if(CurrentPosition < Elements.length - 1){
            //Set pointer to the next step.
            CurrentPosition++;

            //Create event to notify listeners that the forwardbutton has been pressed.
            new vDesk.Events.BubblingEvent("next", {
                sender:  this,
                element: Elements[CurrentPosition]
            }).Dispatch(Control);

            //Disable the forwardbutton if the pointer has reached the last position.
            if(CurrentPosition === (Elements.length - 1)){
                Next.disabled = true;
            }

            //Enable the backbutton (if it's disabled)
            Previous.disabled = false;

            //Disable parent button if the root Element has been reached.
            Parent.disabled = Elements[CurrentPosition].ID === vDesk.Archive.Element.Root;
        }
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Archive.History#parent
     */
    const OnClickParent = () => {
        //Create event to notify listeners that the forwardbutton has been pressed.
        new vDesk.Events.BubblingEvent("parent", {
            sender:  this,
            element: Elements[CurrentPosition].Parent
        }).Dispatch(Control);
        //this.Add(Elements[CurrentPosition].Parent);
        Parent.disabled = Elements[CurrentPosition].ID === vDesk.Archive.Element.Root;
    };

    /**
     * Eventhandler that listens on the 'keydown' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyDown = Event => {
        if(Event.key === "PageUp" && Elements[CurrentPosition].ID > vDesk.Archive.Element.Root){
            OnClickParent();
        }
    };
    if(Enabled){
        window.addEventListener("keydown", OnKeyDown);
    }

    /**
     * Adds an Element to the History.
     * @param {vDesk.Archive.Element} Element The Element tot add.
     */
    this.Add = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        //Avoid duplicate Elements.
        if(
            Element !== Elements[Elements.length - 1]
            && Element !== Elements[CurrentPosition]
            && Element.Type === vDesk.Archive.Element.Folder
        ){
            //Remove the first Element if the maximum amount of steps is exceeded.
            if(Elements.length === Size){
                Elements.shift();
            }
            //Add the new Element to the History.
            Elements.push(Element);

            //Set pointer to current step.
            CurrentPosition = Elements.length - 1;
        }

        //Disable the forwardbutton if the pointer has reached the last Element.
        Next.disabled = CurrentPosition + 1 === Elements.length;

        //Enable the backbutton if the pointer is not at the first step.
        Previous.disabled = CurrentPosition <= 0;

        //Disable parent button if the root Element has been reached.
        Parent.disabled = Element.ID === vDesk.Archive.Element.Root;
    };

    /**
     * Clears the History.
     */
    this.Clear = function() {
        Elements.splice(0);
        Next.disabled = true;
        Previous.disabled = true;
        Parent.disabled = true;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "History";

    /**
     * The button for navigating forward of the history.
     * @type {HTMLButtonElement}
     */
    const Previous = document.createElement("button");
    Previous.className = "Button Arrow Previous";
    Previous.textContent = "🡰";
    Previous.disabled = true;
    Previous.addEventListener("click", OnClickPrevious);
    Control.appendChild(Previous);

    /**
     * The button for navigating backward of the history.
     * @type {HTMLButtonElement}
     */
    const Next = document.createElement("button");
    Next.className = "Button Arrow Next";
    Next.textContent = "🡲";
    Next.disabled = true;
    Next.addEventListener("click", OnClickNext);
    Control.appendChild(Next);

    /**
     * The visual spacer of the History.
     * @type {HTMLDivElement}
     */
    const Spacer = document.createElement("div");
    Spacer.className = "Spacer BorderLight";
    Control.appendChild(Spacer);

    /**
     * The step up button of the Archive module.
     * @type {HTMLButtonElement}
     */
    const Parent = document.createElement("button");
    Parent.className = "Button Arrow Parent";
    Parent.textContent = "🡱";
    Parent.disabled = true;
    Parent.addEventListener("click", OnClickParent);
    Control.appendChild(Parent);
};