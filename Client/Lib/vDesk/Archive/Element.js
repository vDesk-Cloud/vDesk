"use strict";
/**
 * Fired if the Element has been selected.
 * @event vDesk.Archive.Element#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 */
/**
 * Fired if the Element has been opened.
 * @event vDesk.Archive.Element#open
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'open' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 */
/**
 * Fired if the Element has been right clicked on.
 * @event vDesk.Archive.Element#context
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'context' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 * @property {Number} detail.x The horizontal position where the right click has appeared.
 * @property {Number} detail.y The vertical position where the right click has appeared.
 */
/**
 * @todo Change event to 'rename'.
 * Fired if the Element has been renamed.
 * @event vDesk.Archive.Element#renamed
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'renamed' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 * @property {Object} detail.name The name of the Element.
 * @property {String} detail.name.new The new name of the Element.
 * @property {String} detail.name.old The new name of the Element.
 */
/**
 * @todo Change event to 'renamecancel'.
 * Fired if a renaming attempt of the Element has been canceled.
 * @event vDesk.Archive.Element#renamecanceled
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'renamecanceled' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 */
/**
 * Fired if an Element has been dropped on the current Element.
 * @event vDesk.Archive.Element#elementdrop
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'elementdrop' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 * @property {vDesk.Archive.Element} detail.element The dropped Element.
 */
/**
 * Fired if the Element is a folder and the user dropped any files on the Element.
 *
 * @event vDesk.Archive.Element#filedrop
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'filedrop' event.
 * @property {vDesk.Archive.Element} detail.sender The current instance of the Element.
 * @property {FileList} detail.files The files that have been dropped on the Element.
 */
/**
 * Initializes a new instance of the Element class.
 * @class Represents an element within the archive which acts either as a folder or a file.
 * @param {?Number} [ID=null] Initializes the Element with the specified ID.
 * @param {?vDesk.Security.User} [Owner=vDesk.Security.User.Current] Initializes the Element with the specified owner.
 * @param {String} [Name=""] Initializes the Element with the specified name.
 * @param {Number} [Type=vDesk.Archive.Element.Folder] Initializes the Element with the specified type.
 * @param {?Number} [Parent=null] Initializes the Element with the specified parent Element.
 * @param {Date} [CreationTime=new Date] Initializes the Element with the specified creation time.
 * @param {String} [Guid=""] Initializes the Element with the specified Guid.
 * @param {?String} [Extension=null] Initializes the Element with the specified extension.
 * @param {?String} [File=null] Initializes the Element with the specified file name.
 * @param {Number} [Size=0] Initializes the Element with the specified size.
 * @param {?String} [Thumbnail=null] Initializes the Element with the specified thumbnail.
 * @param {vDesk.Security.AccessControlList} [AccessControlList=vDesk.Security.AccessControlList] Initializes the Element with the specified AccessControlList.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {Number|null} ID Gets or sets the ID of the Element.
 * @property {vDesk.Security.User} Owner Gets or sets the owner of the Element.
 * @property {vDesk.Archive.Element|null} Parent Gets or sets the parent Element of the Element.
 * @property {String|null} Name Gets or sets the name of the Element.
 * @property {Number} Type Gets or sets the type of the Element.
 * @property {Date} CreationTime Gets or sets the creation time of the Element.
 * @property {String} Guid Gets or sets the Guid of the Element.
 * @property {?String} Extension Gets or sets the extension of the Element.
 * @property {?String} File Gets or sets the file name of the Element.
 * @property {Number} Size Gets or sets the size of the Element.
 * @property {?String} Thumbnail Gets or sets the thumbnail of the Element.
 * @property {vDesk.Security.AccessControlList} AccessControlList Gets or sets the AccessControlList of the Element.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Element has been selected.
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.Element = function Element(
    ID                = null,
    Owner             = vDesk.Security.User.Current,
    Parent            = null,
    Name              = "",
    Type              = vDesk.Archive.Element.Folder,
    CreationTime      = new Date(),
    Guid              = "",
    Extension         = null,
    File              = null,
    Size              = 0,
    Thumbnail         = null,
    AccessControlList = new vDesk.Security.AccessControlList()
) {
    Ensure.Parameter(ID, vDesk.Struct.Type.Number, "ID", true);
    Ensure.Parameter(Owner, vDesk.Security.User, "Owner");
    Ensure.Parameter(Parent, vDesk.Archive.Element, "Parent", true);
    Ensure.Parameter(Name, vDesk.Struct.Type.String, "Name");
    Ensure.Parameter(Type, vDesk.Struct.Type.Number, "Type");
    Ensure.Parameter(CreationTime, Date, "CreationTime");
    Ensure.Parameter(Guid, vDesk.Struct.Type.String, "Guid");
    Ensure.Parameter(Extension, vDesk.Struct.Type.String, "Extension", true);
    Ensure.Parameter(Thumbnail, vDesk.Struct.Type.String, "Thumbnail", true);
    Ensure.Parameter(AccessControlList, vDesk.Security.AccessControlList, "AccessControlList", true);

    /**
     * Flag indicating whether the Element is selected.
     * @type {Boolean}
     */
    let Selected = false;

    /**
     * The dragcounter indicating drag-operations for childcontrols.
     * @type {Number}
     */
    let DragCounter = 0;

    Object.defineProperties(this, {
        Control:           {
            enumerable: true,
            get:        () => Control
        },
        ID:                {
            enumerable: true,
            get:        () => ID,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "ID", true);
                ID = Value;
                if(Value === vDesk.Archive.Element.Root){
                    TextArea.textContent = vDesk.Locale.Archive.Module;
                }
            }
        },
        Owner:             {
            enumerable: true,
            get:        () => Owner,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "Owner", true);
                Owner = Value;
            }
        },
        Parent:            {
            enumerable: true,
            get:        () => Parent,
            set:        Value => {
                Ensure.Property(Value, vDesk.Archive.Element, "Parent");
                Parent = Value;
            }
        },
        Name:              {
            enumerable: true,
            get:        () => Name,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Name");
                TextArea.textContent = Value;
                Name = Value;
            }
        },
        Type:              {
            enumerable: true,
            get:        () => Type,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "Type");
                Type = Value;
            }
        },
        CreationTime:      {
            enumerable: true,
            get:        () => CreationTime,
            set:        Value => {
                Ensure.Property(Value, Date, "CreationTime", true);
                CreationTime = Value;
            }
        },
        Guid:              {
            enumerable: true,
            get:        () => Guid,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Guid", true);
                Guid = Value;
            }
        },
        Extension:         {
            enumerable: true,
            get:        () => Extension,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Extension", true);
                Extension = Value;
            }
        },
        File:              {
            enumerable: true,
            get:        () => File,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "File", true);
                File = Value;
            }
        },
        Size:              {
            enumerable: true,
            get:        () => Size,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Number, "Size", true);
                Size = Value;
            }
        },
        Thumbnail:         {
            enumerable: true,
            get:        () => Thumbnail,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.String, "Thumbnail", true);
                Thumbnail = Value;
                Icon.src = Value ?? vDesk.Visual.Icons.Archive?.[Extension ?? "Folder"] ?? vDesk.Visual.Icons.Archive.Folder;
            }
        },
        AccessControlList: {
            enumerable: true,
            get:        () => AccessControlList,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.AccessControlList, "AccessControlList");
                AccessControlList = Value;
            }
        },
        Selected:          {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Struct.Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event and emits the 'select' event.
     * @fires vDesk.Archive.Element#select
     * @param {MouseEvent} Event
     */
    const OnClick = Event => {
        Event.stopPropagation();
        new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
    };

    /**
     * Eventhandler that listens on the 'dblclick' event and emits the 'open' event.
     * @fires vDesk.Archive.Element#open
     */
    const OnDoubleClick = () => {
        if(ID !== null){
            new vDesk.Events.BubblingEvent("open", {sender: this}).Dispatch(Control);
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
        if(ID !== null){
            new vDesk.Events.BubblingEvent("context", {
                sender: this,
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
        Event.dataTransfer.effectAllowed = "move";
        Event.dataTransfer.setReference(this);
        if(ID !== null && !Selected){
            new vDesk.Events.BubblingEvent("select", {sender: this}).Dispatch(Control);
        }
        return false;
    };

    /**
     * Eventhandler that listens on the 'dragenter' event and adds hover effects.
     * @return {Boolean}
     */
    const OnDragEnter = () => {
        DragCounter++;
        this.Selected = true;
        return false;
    };

    /**
     * Eventhandler that listens on the 'drageleave' event and removes hover effects.
     * @return {Boolean}
     */
    const OnDragLeave = () => {
        DragCounter--;
        if(DragCounter === 0){
            this.Selected = false;
        }
        return false;
    };

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
        this.Selected = false;

        let Target = Type === vDesk.Archive.Element.Folder ? this : Parent;

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
            if(DroppedElement.ID !== Target.ID || DroppedElement.Parent.ID !== Target.ID){
                new vDesk.Events.BubblingEvent("elementdrop", {
                    sender:  Target,
                    element: DroppedElement
                }).Dispatch(Control);
            }
        }

    };

    /**
     * Eventhandler that listens on the 'keypress' event.
     * @fires vDesk.Archive.Element#renamed
     * @param {KeyboardEvent} Event
     */
    const OnKeyDown = Event => {
        switch(Event.key){
            case "Enter":
                if(TextArea.textContent !== Name){
                    Event.stopPropagation();
                    new vDesk.Events.BubblingEvent("renamed", {
                        sender: this,
                        name:   {
                            new: TextArea.textContent,
                            old: Name
                        }
                    }).Dispatch(Control);
                    this.CancelEdit();
                }
                break;
            case "Escape":
                this.CancelEdit();
                break;
            default:
                return;
        }
    };

    /**
     * Eventhandler that listens on the 'click' event while the Element is in the edit state.
     * @fires vDesk.Archive.Element#renamed
     * @fires vDesk.Archive.Element#renamecanceled
     * @param {MouseEvent} Event
     * @return {Boolean} Enables selection functionality on the textarea.
     */
    const OnClickWhileEdit = Event => {
        if(Event.target !== TextArea){
            //Check if the name has been changed.
            if(TextArea.textContent !== Name){
                new vDesk.Events.BubblingEvent("renamed", {
                    sender: this,
                    name:   {
                        new: TextArea.textContent,
                        old: Name
                    }
                }).Dispatch(Control);
            }else{
                new vDesk.Events.BubblingEvent("renamecanceled", {sender: this}).Dispatch(Control);
            }
            this.CancelEdit();
        }
        return true;
    };

    /**
     * Eventhandler that listens on the 'doubleclick' event and selects if captured the whole text within the textarea.
     */
    const OnDoubleClickTextArea = () => {
        //Create a textrange with the contents of the textarea.
        const Range = document.createRange();
        Range.selectNodeContents(TextArea);

        //Replace any selection with the created range.
        const Selection = window.getSelection();
        Selection.removeAllRanges();
        Selection.addRange(Range);
    };

    /**
     * Enables the textbox of the Element for editing the name.
     */
    this.InvokeEdit = function() {
        TextArea.contentEditable = true.toString();
        TextArea.classList.add("TextBox");
        Control.classList.add("Selected");
        Control.draggable = false;
        OnDoubleClickTextArea();
        TextArea.focus();
        TextArea.addEventListener("dblclick", OnDoubleClickTextArea);
        window.addEventListener("keydown", OnKeyDown, true);
        window.addEventListener("click", OnClickWhileEdit, true);
        Control.removeEventListener("dragstart", OnDragStart);
        Control.removeEventListener("click", OnClick);
        Control.removeEventListener("dblclick", OnDoubleClick);
    };
    /**
     * Disables the textbox the Element for editing the name.
     */
    this.CancelEdit = function() {
        TextArea.contentEditable = false.toString();
        TextArea.classList.remove("TextBox");
        Control.classList.remove("Selected");
        Control.draggable = true;
        TextArea.removeEventListener("dblclick", OnDoubleClickTextArea);
        window.removeEventListener("keypress", OnKeyDown, true);
        window.removeEventListener("click", OnClickWhileEdit, true);
        Control.addEventListener("dragstart", OnDragStart);
        Control.addEventListener("click", OnClick);
        Control.addEventListener("dblclick", OnDoubleClick);
        TextArea.textContent = Name;
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Element";
    Control.draggable = true;

    /**
     * The preview icon of the Element.
     * @type {HTMLImageElement}
     */
    const Icon = document.createElement("img");
    Icon.className = "Icon";
    Icon.draggable = false;
    Icon.src = Thumbnail ?? vDesk.Visual.Icons.Archive?.[Extension ?? "Folder"] ?? vDesk.Visual.Icons.Archive.Folder;

    /**
     * The name label textarea of the Element.
     * @type {HTMLDivElement}
     */
    const TextArea = document.createElement("div");
    TextArea.className = "Name Font Dark";
    TextArea.contentEditable = false.toString();
    TextArea.draggable = false;
    if(ID === vDesk.Archive.Element.Root){
        Name = vDesk.Locale.Archive.Module;
    }
    TextArea.textContent = Name;

    //Add eventlisteners to the Element.
    Control.addEventListener("click", OnClick, false);
    Control.addEventListener("dblclick", OnDoubleClick, false);
    Control.addEventListener("contextmenu", OnContextMenu, false);
    Control.addEventListener("dragstart", OnDragStart, false);
    Control.addEventListener("dragenter", OnDragEnter, false);
    Control.addEventListener("dragleave", OnDragLeave, false);
    Control.addEventListener("drop", OnDrop, false);
    Control.appendChild(Icon);
    Control.appendChild(TextArea);

};

/**
 * Factory method that creates an Element from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Element.
 * @return {vDesk.Archive.Element} An Element filled with the provided data.
 */
vDesk.Archive.Element.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, vDesk.Struct.Type.Object, "DataView");
    return new vDesk.Archive.Element(
        DataView?.ID ?? null,
        vDesk.Security.Users.find(User => User.ID === DataView?.Owner?.ID) ?? vDesk.Security.User.FromDataView(DataView?.Owner ?? {}),
        new vDesk.Archive.Element(DataView?.Parent ?? null),
        DataView?.Name ?? "",
        DataView?.Type ?? vDesk.Archive.Element.Folder,
        DataView?.CreationTime ?? new Date(),
        DataView?.Guid ?? "",
        DataView?.Extension ?? null,
        DataView?.File ?? null,
        DataView?.Size ?? 0,
        DataView?.Thumbnail ?? null,
        vDesk.Security.AccessControlList.FromDataView(DataView?.AccessControlList ?? {})
    );
};
/**
 * The ID of the root Element of the Archive.
 * @type {Number}
 * @constant
 */
vDesk.Archive.Element.Root = 1;

/**
 * Describes the 'folder'-type of an Element.
 * @constant
 * @type {Number}
 * @name vDesk.Archive.Element.Folder
 */
vDesk.Archive.Element.Folder = 0;

/**
 * Describes the 'file'-type of an Element.
 * @constant
 * @type {Number}
 * @name vDesk.Archive.Element.File
 */
vDesk.Archive.Element.File = 1;