"use strict";
/**
 * Initializes a new instance of the Archive class.
 * @module Archive
 * @class Archive Module.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Name Gets the name of the Archive.
 * @property {String} Title Gets the localized name of the Archive.
 * @property {String} Icon Gets the icon of the Archive.
 * @property {vDesk.Controls.ContextMenu} ContextMenu Gets the ContextMenu of the Archive.
 * @implements vDesk.Modules.IVisualModule
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
Modules.Archive = function Archive() {

    /**
     * The current name-sortorder of the Elements in the FolderView of the Archive module.
     * @type {Boolean}
     */
    let NameSortOrder = false;

    /**
     * The current type-sortorder of the Elements in the FolderView of the Archive module.
     * @type {Boolean}
     */
    let TypeSortOrder = false;

    Object.defineProperties(this, {
        Control:     {
            enumerable: true,
            get:        () => Control
        },
        Name:        {
            enumerable: true,
            value:      "Archive"
        },
        Title:       {
            enumerable: true,
            value:      vDesk.Locale.Archive.Module
        },
        Icon:        {
            enumerable: true,
            value:      vDesk.Visual.Icons.Archive.Module
        },
        GoToID:      {
            enumerable: true,
            get:        () => GoToID
        },
        ContextMenu: {
            enumerable: true,
            get:        () => ContextMenu
        }
    });

    /**
     * Listens on the filedrop or change event and uploads any dropped or selected files to the Archive module.
     * @listens vDesk.Archive.FolderView#event:filedrop
     * @param {CustomEvent} Event
     */
    const OnFileDrop = Event => {

        //Loop through dropped files.
        Event.detail.files.forEach(File => {
            const Element = new vDesk.Archive.Element(
                null,
                vDesk.Security.User.Current,
                Event.detail.sender,
                File.name,
                vDesk.Archive.Element.File,
                new Date(),
                "",
                File.name.substring(File.name.lastIndexOf(".") + 1),
                File.name,
                File.size
            );
            ElementCache.Add(Element);
            if(Event.detail.sender.ID === FolderView.CurrentFolder.ID){
                FolderView.Add(Element);
            }
            Uploader.Upload(File, Element, TreeView.Add(Element));
        });

    };

    /**
     * Eventhandler that listens on the 'change' event.
     */
    const OnChange = () => {
        //Loop through dropped files.
        Array.from(OpenFileDialog.files)
            .forEach(File => {
                const Element = new vDesk.Archive.Element(
                    null,
                    vDesk.Security.User.Current,
                    FolderView.CurrentFolder,
                    File.name
                );
                ElementCache.Add(Element);
                FolderView.Add(Element);
                Uploader.Upload(File, Element, TreeView.Add(Element));
            });
    };

    /**
     * Listens on the open event. Fetches the children if the Element is a folder; otherwise, displays the file of the Element.
     * @listens vDesk.Archive.Element#event:open
     * @param {CustomEvent} Event
     */
    const OnOpen = Event => {
        if(Event.detail.sender.Type === vDesk.Archive.Element.Folder){
            //Fetch and display the children of the Element if its a folder.
            ElementCache.FetchElements(
                Event.detail.sender,
                Elements => window.requestAnimationFrame(() => {
                    //Display Elements.
                    FolderView.Elements = Elements;
                    Elements.forEach(Element => TreeView.Add(Element));
                })
            );

            FolderView.CurrentFolder = Event.detail.sender;
            Title.textContent = FolderView.CurrentFolder.Name;
            History.Add(Event.detail.sender);
        }else{
            new vDesk.Archive.Element.Viewer.Window(Event.detail.sender).Show()
        }

    };

    /**
     * Displays the content of a file or the children of a folder.
     * @param {vDesk.Archive.Element} Element The element to show the content of.
     */
    const Open = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        if(Element.Type === vDesk.Archive.Element.Folder){
            FolderView.CurrentFolder = Element;
            History.Add(Element);
            Title.textContent = Element.Name;
            //Fetch and display the children of the Element if its a folder.
            ElementCache.FetchElements(
                Element,
                Elements => window.requestAnimationFrame(() => {
                    FolderView.Elements = Elements;
                    Elements.forEach(Element => TreeView.Add(Element));
                })
            );

        }else{
            new vDesk.Archive.Element.Viewer.Window(Element).Show();
        }
    };

    /**
     * Eventhandler that listens on the 'expand' event of a treeviewitem, fetchng its children and appending it to the item.
     * @param {CustomEvent} Event
     */
    const OnExpand = Event => ElementCache.FetchElements(
        Event.detail.sender.Element,
        Elements => window.requestAnimationFrame(() => Elements.forEach(Element => TreeView.Add(Element)))
    );

    /**
     * Creates a temporary folder.
     */
    const AddFolder = function() {
        const Element = new vDesk.Archive.Element(
            null,
            vDesk.Security.User.Current,
            FolderView.CurrentFolder,
            vDesk.Locale.Archive.NewFolder,
            vDesk.Archive.Element.Folder,
            new Date(),
            "",
            null,
            null,
            0,
            null,
            FolderView.CurrentFolder.AccessControlList
        );
        Element.Selected = true;
        TreeView.Add(Element);
        FolderView.Add(Element);
        ElementCache.Add(Element);
        Element.InvokeEdit();
    };

    /**
     * Adds a recently created folder to the Archive module.
     * @param {vDesk.Archive.Element} Element The folder Element to add.
     */
    const CreateFolder = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "CreateFolder",
                    Parameters: {
                        Parent: Element.Parent.ID,
                        Name:   Element.Name
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(Response.Status){
                    Element.ID = Response.Data.ID;
                    Element.AccessControlList = vDesk.Security.AccessControlList.FromDataView(Response.Data.AccessControlList);
                    TreeView.Find(Element).Element = Element;
                }else{
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Displays the content of a file or the children of a folder.
     * @param {vDesk.Archive.Element} Element The element to show the content of.
     */
    const Edit = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        new vDesk.Archive.Element.Editor.Window(Element).Show();
    };

    /**
     * Deletes an Element and any children from the Archive module.
     * @param {vDesk.Archive.Element} Element
     */
    const DeleteElement = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "DeleteElements",
                    Parameters: {Elements: [Element.ID]},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(Response.Status){
                    TreeView.Remove(Element);
                    FolderView.Remove(Element);
                    ElementCache.Remove(Element);
                }else{
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Deletes any selected Elements from the Archive module.
     */
    const DeleteSelectedElements = function() {

        const Elements = FolderView.Selected.filter(Element => Element.AccessControlList.Delete);

        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "DeleteElements",
                    Parameters: {Elements: Elements.map(Element => Element.ID)},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(Response.Status){
                    Elements.forEach(Element => {
                        TreeView.Remove(Element);
                        FolderView.Remove(Element);
                        ElementCache.Remove(Element);
                    });
                }else{
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Displays the Attributes of an Element.
     * @param {vDesk.Archive.Element} Element The Element to show the Attributes of.
     */
    const ShowAttributes = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        new vDesk.Archive.AttributeWindow(Element).Show();
    };

    /**
     * Refetches and replaces the Elements of the current displayed folder.
     */
    const Refresh = function() {
        FolderView.Elements.forEach(Element => TreeView.Remove(Element));
        ElementCache.FetchElements(
            FolderView.CurrentFolder,
            Elements => window.requestAnimationFrame(() => {
                FolderView.Elements = Elements;
                Elements.forEach(Element => TreeView.Add(Element));
            }),
            true
        );
    };

    /**
     * Navigates through the archive to a target element.
     * @param {Number} ID The ID of the element to seek.
     */
    const GoToID = function(ID) {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "GetBranch",
                    Parameters: {ID: ID},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(Response.Status){
                    //Loop through branch and fetch its children.
                    Response.Data.forEach(ID => {
                        ElementCache.FetchElements(
                            new vDesk.Archive.Element(ID),
                            Elements => window.requestAnimationFrame(
                                () => Elements.forEach(Element => TreeView.Add(Element))
                            )
                        );
                    });
                    //Display/open the target element.
                    Open(ElementCache.GetElement(Response.Data[Response.Data.length - 1]));
                }
            }
        );
    };

    /**
     * Moves on or more dropped elements into the target destination.
     * @param {CustomEvent} Event
     * @listens vDesk.Archive.Element#event:elementdrop
     */
    const OnElementDrop = Event => {
        //Check if more than 1 element has been selected before.
        if(FolderView.Selected.length > 1){
            //Exclude the target if its in the selection.
            MoveTo(ElementCache.GetElement(Event.detail.sender.ID), FolderView.Selected.filter(Element => Element.ID !== Event.detail.sender.ID));
        }else{
            MoveTo(ElementCache.GetElement(Event.detail.sender.ID), [Event.detail.element]);
        }
    };

    /**
     * Renames an Element.
     * @param {vDesk.Archive.Element} Element The Element to edit.
     */
    const Rename = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        window.removeEventListener("click", OnClickDeselect);
        window.removeEventListener("keydown", OnKeyDown);
        if(!Element.Selected){
            Element.Selected = true;
        }
        if(FolderView.CurrentFolder.ID !== Element.Parent.ID){
            Open(Element.Parent);
        }
        FolderView.Enabled = false;
        Element.InvokeEdit();
    };

    /**
     * Listens on the rename event.
     * If the target is a newly created folder, it will be added to the archive, else the name will be changed.
     * @param {CustomEvent} Event
     */
    const OnRenamed = Event => {
        //Check if the sender is a newly created folder.
        if(Event.detail.sender.ID === null && Event.detail.sender.Type === vDesk.Archive.Element.Folder){
            Event.detail.sender.Name = Event.detail.name.new;
            CreateFolder(Event.detail.sender);
        }else{
            //Else rename the existing element.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Archive",
                        Command:    "Rename",
                        Parameters: {
                            ID:   Event.detail.sender.ID,
                            Name: Event.detail.name.new
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Event.detail.sender.Name = Event.detail.name.new;
                        TreeView.Find(Event.detail.sender).Element = Event.detail.sender;
                    }else{
                        alert(Response.Data);
                    }
                }
            );
        }
        OnClickDeselect();
        FolderView.Enabled = true;
        window.addEventListener("click", OnClickDeselect);
        window.addEventListener("keydown", OnKeyDown);
    };

    /**
     * Listens on the 'renamecanceled' event.
     * If the target is a newly created folder, it will be added to the archive, else any changes will be discarded.
     * @param {CustomEvent} Event
     */
    const OnRenameCanceled = Event => {
        if(Event.detail.sender.ID === null && Event.detail.sender.Type === vDesk.Archive.Element.Folder){
            CreateFolder(Event.detail.sender);
        }
        OnClickDeselect();
        FolderView.Enabled = true;
        window.addEventListener("click", OnClickDeselect);
        window.addEventListener("keydown", OnKeyDown);
    };

    /**
     * Eventhandler that listens on the 'keydown' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyDown = Event => {
        switch(Event.key){
            case "Enter":
                if(FolderView.Selected.length > 1){
                    FolderView.Selected.filter(Element => Element.Type === vDesk.Archive.Element.File).forEach(Element => Open(Element));
                }else{
                    Open(FolderView.Selected[0]);
                }
                break;
            case "Delete":
                if(!(Event.target instanceof HTMLSpanElement) && FolderView.Selected.length > 0){
                    DeleteSelectedElements();
                }
                break;
            case "Home":
                History.Clear();
                Open(RootElement);
                break;
            default:
                if(Event.ctrlKey){
                    switch(Event.key){
                        case "c":
                            CopyToClipboard();
                            break;
                        case "a":
                            Event.preventDefault();
                            SelectAll();
                            break;
                        case "x":
                            CutToClipboard();
                            break;
                        case "v":
                            PasteFromClipboard();
                            break;
                    }
                }
        }

    };

    /**
     * Unsets the modifierflag if the controlkey has been released.
     * @param {KeyboardEvent} Event
     */
    const OnKeyUp = Event => ControlKeyPressed = Event.ctrlKey;

    /**
     * Listens on the select event.
     * Adds emitting elements to the selection.
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        RenameToolBarItem.Enabled = Event.detail.elements.length === 1 && Event.detail.elements[0].AccessControlList.Write;
        DeleteToolBarItem.Enabled = Event.detail.elements.some(Element => Element.AccessControlList.Delete);
        CopyToolBarItem.Enabled = true;
        CutToolBarItem.Enabled = Event.detail.elements.some(Element => Element.AccessControlList.Write);
        ViewToolBarItem.Enabled = Event.detail.elements.length > 0;
        AttributesToolBarItem.Enabled = Event.detail.elements.length > 0;
        ContextMenu.Hide();
    };

    /**
     * Deselects an element or deselects all elements if the control key has been released.
     */
    const OnClickDeselect = () => {
        FolderView.Selected = [];
        //Disable related ToolBar Items.
        RenameToolBarItem.Enabled = false;
        DeleteToolBarItem.Enabled = false;
        CopyToolBarItem.Enabled = false;
        CutToolBarItem.Enabled = false;
        ViewToolBarItem.Enabled = false;
        AttributesToolBarItem.Enabled = false;
    };

    /**
     * Selects all current displayed elements.
     */
    const SelectAll = function() {
        FolderView.Elements.forEach(Element => Element.Selected = true);

        //Enable related ToolBar Items.
        RenameToolBarItem.Enabled = false;
        DeleteToolBarItem.Enabled = true;
        CopyToolBarItem.Enabled = true;
        CutToolBarItem.Enabled = true;
        ViewToolBarItem.Enabled = false;
        AttributesToolBarItem.Enabled = false;
        ContextMenu.Hide();
    };

    /**
     * Copies all selected elements to the clipboard.
     */
    const CopyToClipboard = function() {
        Clipboard.Clear();
        FolderView.Selected.filter(Element => Element.AccessControlList.Write).forEach(Element => Clipboard.Add(Element));
        PasteToolBarItem.Enabled = true;
        Clipboard.LastOperation = vDesk.Archive.Clipboard.Operations.Copy;
    };

    /**
     * Cuts all selected elements to the clipboard.
     */
    const CutToClipboard = function() {
        Clipboard.Clear();
        FolderView.Selected.filter(Element => Element.AccessControlList.Write).forEach(Element => Clipboard.Add(Element));
        PasteToolBarItem.Enabled = true;
        Clipboard.LastOperation = vDesk.Archive.Clipboard.Operations.Cut;
    };

    /**
     * Pastes all previous copied or cut elements from the clipboard.
     * @type Function
     */
    const PasteFromClipboard = function() {

        if(Clipboard.ContainsElements){
            switch(Clipboard.LastOperation){
                case vDesk.Archive.Clipboard.Operations.Copy:
                    CopyTo(FolderView.CurrentFolder, Clipboard.Elements);
                    break;
                case vDesk.Archive.Clipboard.Operations.Cut:
                    MoveTo(FolderView.CurrentFolder, Clipboard.Elements);
                    break;
            }
        }
        PasteToolBarItem.Enabled = false;
        Clipboard.LastOperation = vDesk.Archive.Clipboard.Operations.Paste;
        Clipboard.Clear();
    };

    /**
     * Moves a given set of Elements to another folder Element.
     * @param {vDesk.Archive.Element} Target The target folder Element.
     * @param {Array<vDesk.Archive.Element>} Elements The Elements to move.
     */
    const MoveTo = function(Target, Elements) {
        Ensure.Parameter(Target, vDesk.Archive.Element, "Target");
        Ensure.Parameter(Elements, Array, "Elements");

        //Check if the User is allowed to add Elements to the target.
        if(Target.Type === vDesk.Archive.Element.Folder && Target.AccessControlList.Write){

            //Filter Elements the User can edit.
            Elements = Elements.filter(Element => Element.Parent.ID !== Target.ID && Element.ID !== Target.ID && Element.AccessControlList.Write);

            //Send command to the server.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Archive",
                        Command:    "Move",
                        Parameters: {
                            Target:   Target.ID,
                            Elements: Elements.map(Element => Element.ID)
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Elements.forEach(Element => {
                            TreeView.Remove(Element);
                            Element.Parent = Target;
                            TreeView.Add(Element);
                            FolderView.Remove(Element);
                        });
                        if(FolderView.CurrentFolder.ID === Target.ID){
                            Elements.forEach(Element => FolderView.Add(Element));
                        }
                    }else{
                        alert(Response.Data);
                    }
                }
            );
        }
    };

    /**
     * Copies a given set of elements to another folder.
     * @param {vDesk.Archive.Element} Target The targetfolder.
     * @param {Array<vDesk.Archive.Element>} Elements The elements to move.
     */
    const CopyTo = function(Target, Elements) {
        Ensure.Parameter(Target, vDesk.Archive.Element, "Target");
        Ensure.Parameter(Elements, Array, "Elements");

        //Check if the user is allowed to add elements to the target.
        if(Target.AccessControlList.Write){
            //Send command to the server.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Archive",
                        Command:    "Copy",
                        Parameters: {
                            Target:   Target.ID,
                            Elements: Elements.filter(Element => Element.AccessControlList.Write).map(Element => Element.ID)
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Refresh();
                    }else{
                        alert(Response.Data);
                    }
                }
            );
        }
    };

    /**
     * Eventhandler that listens on the 'click' event and hides the ContextMenu of the Archive.
     */
    const OnClick = () => ContextMenu.Hide();

    /**
     * Eventhandler that listens on the 'context' event.
     * @listens vDesk.Archive.Element#event:context
     * @param {CustomEvent} Event
     */
    const OnContext = Event => {
        if(ContextMenu.Visible){
            ContextMenu.Hide();
        }
        if(Event.detail.sender instanceof vDesk.Archive.FolderView){
            ContextMenu.Show(FolderView.CurrentFolder, Event.detail.x, Event.detail.y);
        }else if(Event.detail.sender instanceof vDesk.Archive.Element){
            ContextMenu.Show(Event.detail.sender, Event.detail.x, Event.detail.y);
        }
    };

    /**
     * Eventhandler that listens on the 'submit' event.
     * @listens vDesk.Controls.ContextMenu#event:submit
     * @param {CustomEvent} Event
     */
    const OnSubmit = Event => {
        switch(Event.detail.action){
            case "open":
                History.Add(Event.detail.target);
                Open(Event.detail.target);
                break;
            case "Edit":
                Edit(Event.detail.target);
                break;
            case "save":
                Downloader.Download(Event.detail.target);
                break;
            case "delete":
                if(ContextMenu.Target.ID > 1){
                    DeleteElement(Event.detail.target);
                }
                break;
            case "rename":
                Rename(Event.detail.target);
                break;
            case "addfolder":
                AddFolder();
                break;
            case "addfile":
                OpenFileDialog.click();
                break;
            case "refresh":
                Refresh();
                break;
            case "Copy":
                Event.detail.target.Selected = true;
                CopyToClipboard();
                break;
            case "Cut":
                Event.detail.target.Selected = true;
                CutToClipboard();
                break;
            case "Paste":
                PasteFromClipboard();
                break;
            case "attributes":
                ShowAttributes(Event.detail.target);
                break;
        }
        ContextMenu.Hide();
        Event.stopPropagation();
    };

    /**
     * Eventhandler that listens on the vDesk.Archive.Element.Created event.
     * @param {MessageEvent} Event
     */
    const OnElementCreated = Event => {
        //Check if the event hasn't occurred before.
        if(ElementCache.Find(Number.parseInt(Event.data)) === null){
            ElementCache.FetchElement(Number.parseInt(Event.data), Element => {
                if(Element !== null){
                    if(FolderView.CurrentFolder.ID === Element.Parent.ID){
                        FolderView.Add(Element);
                    }
                    TreeView.Add(Element);
                }
            });
        }
    };
    vDesk.Events.Stream.addEventListener("vDesk.Archive.Element.Created", OnElementCreated, false);

    /**
     * Eventhandler that listens on the global 'vDesk.Archive.Element.Renamed' event.
     * @param {MessageEvent} Event
     */
    const OnElementRenamed = Event => {
        const Element = ElementCache.Find(Number.parseInt(Event.data));
        if(Element !== null){
            ElementCache.FetchElement(Element.ID, Renamed => Element.Name = Renamed.Name, true);
            (TreeView.Find(Element)).Element = Element;
        }
    };
    vDesk.Events.Stream.addEventListener("vDesk.Archive.Element.Renamed", OnElementRenamed, false);

    /**
     * Eventhandler that listens on the global 'vDesk.Archive.Element.Moved' event.
     * @param {MessageEvent} Event
     */
    const OnElementMoved = Event => {
        const Element = ElementCache.Find(Number.parseInt(Event.data)) ?? ElementCache.GetElement(Number.parseInt(Event.data));
        TreeView.Remove(Element);
        if(FolderView.CurrentFolder.ID === Element.Parent.ID){
            FolderView.Add(Element);
        }else{
            FolderView.Remove(Element);
        }
        TreeView.Add(Element);
    };
    vDesk.Events.Stream.addEventListener("vDesk.Archive.Element.Moved", OnElementMoved, false);

    /**
     * Eventhandler that listens on the global 'vDesk.Archive.Element.Deleted' event.
     * @param {MessageEvent} Event
     */
    const OnElementDeleted = Event => {
        const Element = ElementCache.Find(Number.parseInt(Event.data));
        if(Element !== null){
            FolderView.Remove(Element);
            TreeView.Remove(Element);
            ElementCache.Remove(Element);
        }
    };
    vDesk.Events.Stream.addEventListener("vDesk.Archive.Element.Deleted", OnElementDeleted, false);

    /**
     * Removes the Elements and according Items of Elements whose upload has failed.
     * @param {CustomEvent} Event
     */
    const OnUploadFailed = Event => {
        ElementCache.Remove(Event.detail.element);
        FolderView.Remove(Event.detail.element);
        TreeView.Remove(Event.detail.element);
    };


    /**
     * Closes the contextmenu if a global click has been captured.
     */
    const OnClickCloseContextMenu = () => ContextMenu.Hide();

    /**
     * Flag that indicates if the control-key is being pressed.
     * @type {Boolean}
     */
    let ControlKeyPressed = false;

    /**
     * The underlying dom node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Archive";
    Control.addEventListener("context", OnContext, false);
    Control.addEventListener("filedrop", OnFileDrop, false);
    Control.addEventListener("open", OnOpen, false);
    Control.addEventListener("expand", OnExpand, false);
    Control.addEventListener("elementdrop", OnElementDrop, false);
    Control.addEventListener("select", OnSelect, false);
    Control.addEventListener("renamed", OnRenamed, false);
    Control.addEventListener("renamecanceled", OnRenameCanceled, false);

    /**
     * The header of the Archive module.
     * @type {HTMLDivElement}
     */
    const Header = document.createElement("div");
    Header.className = "Header Font Dark BorderLight";

    /**
     * The History of the Archive module.
     * @type vDesk.Archive.History
     */
    const History = new vDesk.Archive.History();
    History.Control.addEventListener("previous", Event => Open(Event.detail.element), false);
    History.Control.addEventListener("next", Event => Open(Event.detail.element), false);
    History.Control.addEventListener("parent", Event => Open(Event.detail.element), false);
    Header.appendChild(History.Control);

    /**
     * The title of the Archive module.
     * @type {HTMLSpanElement}
     */
    const Title = document.createElement("span");
    Title.className = "Title Font";
    Header.appendChild(Title);

    /**
     * The sort buttons of the Archive.
     * @type {HTMLDivElement}
     */
    const Sort = document.createElement("div");
    Sort.className = "Sort";
    Header.appendChild(Sort);
    Control.appendChild(Header);

    /**
     * the "sortbyname" button of the Archive module.
     * @type {HTMLButtonElement}
     */
    const ByName = document.createElement("button");
    ByName.className = "Button Button UI";
    ByName.style.backgroundImage = `url("${vDesk.Visual.Icons.Archive.SortABCAsc}")`;
    ByName.addEventListener("click", () => {
        FolderView.Sort(NameSortOrder ? vDesk.Archive.FolderView.Sort.NameDescending : vDesk.Archive.FolderView.Sort.NameAscending);
        ByName.style.backgroundImage = `url("${(NameSortOrder ? vDesk.Visual.Icons.Archive.SortABCAsc : vDesk.Visual.Icons.Archive.SortABCDesc)}")`;
        NameSortOrder = !NameSortOrder;
    }, false);
    Sort.appendChild(ByName);

    /**
     * The "sortbytype" button of the Archive module.
     * @type {HTMLButtonElement}
     */
    const ByType = document.createElement("button");
    ByType.className = "Button Button UI";
    ByType.style.backgroundImage = `url("${vDesk.Visual.Icons.Archive.SortTypeAsc}")`;
    ByType.addEventListener("click", () => {
        FolderView.Sort(TypeSortOrder ? vDesk.Archive.FolderView.Sort.TypeDescending : vDesk.Archive.FolderView.Sort.TypeAscending);
        ByType.style.backgroundImage = `url("${(TypeSortOrder ? vDesk.Visual.Icons.Archive.SortTypeAsc : vDesk.Visual.Icons.Archive.SortTypeDesc)}")`;
        TypeSortOrder = !TypeSortOrder;
    }, false);
    Sort.appendChild(ByType);

    /**
     * The TreeView of the Archive module.
     * @type {vDesk.Archive.TreeView}
     */
    const TreeView = new vDesk.Archive.TreeView();

    /**
     * The FolderView of the Archive module.
     * @type {vDesk.Archive.FolderView}
     */
    const FolderView = new vDesk.Archive.FolderView();

    /**
     * The Resizer of the Archive.
     * @type {vDesk.Controls.Resizer}
     */
    const Resize = new vDesk.Controls.Resizer(
        vDesk.Controls.Resizer.Direction.Horizontal,
        TreeView.Control,
        FolderView.Control
    );
    Control.appendChild(TreeView.Control);
    Control.appendChild(Resize.Control);
    Control.appendChild(FolderView.Control);

    /**
     * The open file dialog of the Archive module.
     * @type {HTMLInputElement}
     */
    const OpenFileDialog = document.createElement("input");
    OpenFileDialog.type = "file";
    OpenFileDialog.style.cssText = "display: none;";
    OpenFileDialog.multiple = true;
    OpenFileDialog.addEventListener("change", OnChange, false);
    Control.appendChild(OpenFileDialog);

    /**
     * The Uploader of the Archive module.
     * @type {vDesk.Archive.Uploader}
     */
    const Uploader = new vDesk.Archive.Uploader();

    /**
     * The Downloader of the Archive module.
     * @type {vDesk.Archive.Downloader}
     */
    const Downloader = new vDesk.Archive.Downloader();

    /**
     * The Clipboard of the Archive module.
     * @type {vDesk.Archive.Clipboard}
     */
    const Clipboard = new vDesk.Archive.Clipboard();

    /**
     * The ElementCache of the Archive module.
     * @type {vDesk.Archive.Element.Cache}
     */
    const ElementCache = new vDesk.Archive.Element.Cache();

    /**
     * The root Element of the Archive module.
     * @type {vDesk.Archive.Element}
     * @todo Make this async.
     */
    const RootElement = ElementCache.GetElement(1);
    TreeView.RootElement = new vDesk.Archive.TreeView.Item(RootElement);
    History.Add(RootElement);
    FolderView.CurrentFolder = RootElement;
    Title.textContent = RootElement.Name;

    ElementCache.FetchElements(
        RootElement,
        Elements => {
            window.requestAnimationFrame(() => {
                FolderView.Elements = Elements;
                Elements.forEach(Element => TreeView.Add(Element));
            });
        }
    );
    TreeView.RootElement.Expanded = true;

    /**
     * The ContextMenu of the Archive module.
     * @type {vDesk.Controls.ContextMenu}
     */
    const ContextMenu = new vDesk.Controls.ContextMenu([
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Open,
            "open",
            vDesk.Visual.Icons.View,
            Element => Element !== FolderView.CurrentFolder
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Edit,
            "Edit",
            vDesk.Visual.Icons.Edit,
            Element => Object.values(vDesk.Archive.Element.Edit)
                .some(Plugin => ~Plugin.Extensions.indexOf(Element.Extension))
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Save,
            "save",
            vDesk.Visual.Icons.Save,
            Element => Element !== FolderView.CurrentFolder && Element.Type === vDesk.Archive.Element.File
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Copy,
            "Copy",
            vDesk.Visual.Icons.Copy,
            Element => Element !== FolderView.CurrentFolder && Element.AccessControlList.Write
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Cut,
            "Cut",
            vDesk.Visual.Icons.Cut,
            Element => Element !== FolderView.CurrentFolder && Element.AccessControlList.Write
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Paste,
            "Paste",
            vDesk.Visual.Icons.Paste,
            () => Clipboard.ContainsElements
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.Archive.Rename,
            "rename",
            vDesk.Visual.Icons.Edit,
            Element => Element !== FolderView.CurrentFolder && Element.AccessControlList.Write
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Delete,
            "delete",
            vDesk.Visual.Icons.Delete,
            Element => Element !== FolderView.CurrentFolder && Element.AccessControlList.Delete
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.Archive.AddFile,
            "addfile",
            vDesk.Visual.Icons.Archive.AddFile,
            Element => Element === FolderView.CurrentFolder && Element.AccessControlList.Write
        ),
        new vDesk.Controls.ContextMenu.Group(
            vDesk.Locale.vDesk.New,
            vDesk.Visual.Icons.TriangleRight,
            Element => Element === FolderView.CurrentFolder && Element.AccessControlList.Write,
            [
                new vDesk.Controls.ContextMenu.Item(
                    vDesk.Locale.Archive.Folder,
                    "addfolder",
                    vDesk.Visual.Icons.Archive.NewFolder,
                    Element => Element.Type === vDesk.Archive.Element.Folder
                )
            ]
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.Archive.Refresh,
            "refresh",
            vDesk.Visual.Icons.Refresh
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.Archive.Attributes,
            "attributes",
            vDesk.Visual.Icons.Archive.Attributes,
            () => vDesk.Security.User.Current.Permissions.ReadAttributes
        )
    ]);
    ContextMenu.Control.addEventListener("submit", OnSubmit);

    /**
     * The ToolBar Item for navigating to the root Element.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ArchiveEntryToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Archive.Entry,
        vDesk.Visual.Icons.Archive.Home,
        true,
        () => {
            History.Clear();
            Open(RootElement);
        }
    );

    /**
     * The ToolBar Item for creating a new folder.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const AddFolderToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Archive.NewFolder,
        vDesk.Visual.Icons.Archive.NewFolder,
        true,
        AddFolder
    );

    /**
     * The ToolBar Item for adding a new file to the Archive module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const AddFileToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Archive.AddFile,
        vDesk.Visual.Icons.Archive.AddFile,
        true,
        () => OpenFileDialog.click()
    );

    /**
     * The ToolBar Item for refreshing the current view.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const RefreshToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Archive.Refresh,
        vDesk.Visual.Icons.Refresh,
        true,
        Refresh
    );

    /**
     * The ToolBar Group containing ToolBar Items for navigating and adding files/folders to the Archive module.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const ArchiveToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.Archive.Module,
        [
            ArchiveEntryToolBarItem,
            AddFolderToolBarItem,
            AddFileToolBarItem,
            RefreshToolBarItem
        ]
    );

    /**
     * The ToolBar Item for renaming the current selected element.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const RenameToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Archive.Rename,
        vDesk.Visual.Icons.Edit,
        false,
        Event => {
            Event.stopPropagation();
            Rename(FolderView.Selected[0]);
        }
    );

    /**
     * The ToolBar Item for deleting the current selected element/s.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Delete,
        vDesk.Visual.Icons.Delete,
        false,
        DeleteSelectedElements
    );

    /**
     * The Toolbar Group containing ToolBar Items for renaming and deleting the current selected item/s.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const ElementToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.Archive.Element,
        [
            RenameToolBarItem,
            DeleteToolBarItem
        ]
    );

    /**
     * The ToolBar Item for copying the current selected Element/s.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const CopyToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Copy,
        vDesk.Visual.Icons.Copy,
        false,
        CopyToClipboard
    );

    /**
     * The Toolbar Item for cutting the current selected Element/s.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const CutToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Cut,
        vDesk.Visual.Icons.Cut,
        false,
        CutToClipboard
    );

    /**
     * The ToolBar Item for pasting recently copied or cut out Element/s.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const PasteToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Paste,
        vDesk.Visual.Icons.Paste,
        false,
        PasteFromClipboard
    );

    /**
     * The ToolBar Group containing ToolBar Items for copying, cutting and pasting of the Clipboard.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const ClipboardToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.Archive.Clipboard,
        [
            CopyToolBarItem,
            CutToolBarItem,
            PasteToolBarItem
        ]
    );

    /**
     * The ToolBar Item for viewing/opening the current selected Element.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ViewToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.vDesk.Open,
        vDesk.Visual.Icons.View,
        false,
        () => {
            if(FolderView.Selected.length > 1){
                FolderView.Selected.filter(Element => Element.Type === vDesk.Archive.Element.File).forEach(Element => Open(Element));
            }else{
                Open(FolderView.Selected[0]);
            }
        }
    );

    /**
     * The Toolbar Item for viewing the attributes of the current selected element.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const AttributesToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale.Archive.Attributes,
        vDesk.Visual.Icons.Archive.Attributes,
        false,
        () => FolderView.Selected.forEach(Element => ShowAttributes(Element))
    );

    /**
     * The ToolBar Group containing ToolBar Items for viewing a file or its metadata.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const ViewToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale.vDesk.View,
        [
            ViewToolBarItem,
            AttributesToolBarItem
        ]
    );

    /**
     * Adds all required eventhandler.
     */
    this.Load = function() {
        History.Enabled = true;
        FolderView.Enabled = true;
        window.addEventListener("click", OnClickCloseContextMenu);
        window.addEventListener("click", OnClickDeselect);
        window.addEventListener("keydown", OnKeyDown);
        window.addEventListener("keyup", OnKeyUp);
        window.addEventListener("uploadfailed", OnUploadFailed);
        window.addEventListener("click", OnClick);
        vDesk.Header.ToolBar.Groups = [ArchiveToolBarGroup, ElementToolBarGroup, ClipboardToolBarGroup, ViewToolBarGroup];
    };

    /**
     * Removes all eventhandlers of the Archive module.
     */
    this.Unload = function() {
        History.Enabled = false;
        FolderView.Enabled = false;
        window.removeEventListener("click", OnClickCloseContextMenu);
        window.removeEventListener("click", OnClickDeselect);
        window.removeEventListener("keydown", OnKeyDown);
        window.removeEventListener("keyup", OnKeyUp);
        window.removeEventListener("uploadfailed", OnUploadFailed);
        window.removeEventListener("click", OnClick);
    };
};

Modules.Archive.Implements(vDesk.Modules.IVisualModule);