/**
 * Initializes a new instance of the PinBoard class.
 * @module PinBoard
 * @class The pinboard module.
 * Provides functionality for adding and organizing coloured notes and attaching elements of the archivemodule for easy access.
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Implement coordinate system and translate coordinates of the Notes and Attachments to the size of the window.
 */
Modules.PinBoard = function PinBoard() {

    /**
     * The default minimum z-index for elements on the PinBoard.
     * @type {Number}
     */
    const DefaultIndex = 500;

    /**
     * Contains all Notes and Attachments of the pinboard.
     * @type {Array<vDesk.PinBoard.Note|vDesk.PinBoard.Attachment>}
     */
    const Elements = [];

    /**
     * Any newly created Note.
     * @type {null|vDesk.PinBoard.Note}
     */
    let NewNote = null;

    /**
     * Any selected Note or Attachment.
     * @type {null|vDesk.PinBoard.Note|vDesk.PinBoard.Attachment}
     */
    let SelectedElement = null;

    Object.defineProperties(this, {
        Control:          {
            enumerable: true,
            get:        () => Control
        },
        Name:             {
            enumerable: true,
            value:      "PinBoard"
        },
        Title:            {
            enumerable: true,
            value:      vDesk.Locale["PinBoard"]["Module"]
        },
        Icon:             {
            enumerable: true,
            value:      vDesk.Visual.Icons.Pinboard.Module
        },
        CreateAttachment: {
            enumerable: true,
            get:        () => CreateAttachment
        },
        ContextMenu:      {
            enumerable: true,
            get:        () => ContextMenu
        }
    });

    /**
     * Sets the given element on top of the visual stack.
     * @param {vDesk.PinBoard.Note|vDesk.PinBoard.Attachment} Element The element to set on top.
     */
    const SetOnTop = function(Element) {
        //Set temporary stackorder
        Element.StackOrder = 10000;
        Elements.sort((First, Second) => First.StackOrder - Second.StackOrder)
            .reduce((StackOrder, Element) => Element.StackOrder = StackOrder + 10, DefaultIndex);
    };

    /**
     * Saves the new postion of a recent moved Element.
     * @listens vDesk.PinBoard.Attachment#event:moved
     * @listens vDesk.PinBoard.Note#event:moved
     * @param {CustomEvent} Event
     */
    const OnMoved = Event => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "PinBoard",
                    Command:    Event.detail.sender instanceof vDesk.PinBoard.Note
                                ? "UpdateNotePosition"
                                : Event.detail.sender instanceof vDesk.PinBoard.Attachment
                                  ? "UpdateAttachmentPosition"
                                  : "",
                    Parameters: {
                        ID: Event.detail.sender.ID,
                        X:  Event.detail.sender.X,
                        Y:  Event.detail.sender.Y
                    },
                    Ticket:     vDesk.User.Ticket
                }
            )
        );
        SetOnTop(Event.detail.sender);
    };

    /**
     * Saves the new size of a recent resized note.
     * @listens vDesk.PinBoard.Note#event:moved
     * @param {CustomEvent} Event
     */
    const OnResized = Event => {
        if(Event.detail.sender instanceof vDesk.PinBoard.Note) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "PinBoard",
                        Command:    "UpdateNoteSize",
                        Parameters: {
                            ID:     Event.detail.sender.ID,
                            X:      Event.detail.sender.X,
                            Y:      Event.detail.sender.Y,
                            Width:  Event.detail.sender.Width,
                            Height: Event.detail.sender.Height
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                )
            );
        }
        SetOnTop(Event.detail.sender);
    };

    /**
     * Saves the changed text of a note.
     * @listens vDesk.PinBoard.Note#event:contentchanged
     * @param {CustomEvent} Event
     */
    const OnContentChanged = Event => {
        if(Event.detail.sender instanceof vDesk.PinBoard.Note) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "PinBoard",
                        Command:    "UpdateNoteContent",
                        Parameters: {
                            ID:      Event.detail.sender.ID,
                            Content: Event.detail.sender.Content.textContent
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                )
            );
        }
        if(NewNote === Event.detail.sender) {
            NewNote = null;
        }
    };

    /**
     * Changes the color of a Note.
     * @param {vDesk.PinBoard.Note} Note The Note to change the color of.
     * @param {String} Color The color to set.
     */
    const ChangeColor = function(Note, Color) {
        if(Note instanceof vDesk.PinBoard.Note) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "PinBoard",
                        Command:    "UpdateNoteColor",
                        Parameters: {
                            ID:    Note.ID,
                            Color: Color
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        Note.Color = Color;
                    } else {
                        alert(Response.Data);
                    }
                }
            );
        }
    };

    /**
     * Selects a Note or Attachment and deselects any previous selected Note or Attachment.
     * @param {vDesk.PinBoard.Note|vDesk.PinBoard.Attachment} Element The Note or Attachment to select.
     */
    const Select = function(Element) {
        Ensure.Parameter(Element, [vDesk.PinBoard.Note, vDesk.PinBoard.Attachment], "Element");
        if(Element instanceof vDesk.PinBoard.Note) {
            NoteGreenToolBarItem.Enabled = true;
            NoteBlueToolBarItem.Enabled = true;
            NoteYellowToolBarItem.Enabled = true;
            NoteWhiteToolBarItem.Enabled = true;
            NoteRedToolBarItem.Enabled = true;
            NoteCustomColorToolBarItem.Enabled = true;
            ViewAttachmentToolBarItem.Enabled = false;
        } else if(Element instanceof vDesk.PinBoard.Attachment) {
            ViewAttachmentToolBarItem.Enabled = true;
            NoteGreenToolBarItem.Enabled = false;
            NoteBlueToolBarItem.Enabled = false;
            NoteYellowToolBarItem.Enabled = false;
            NoteWhiteToolBarItem.Enabled = false;
            NoteRedToolBarItem.Enabled = false;
            NoteCustomColorToolBarItem.Enabled = false;
        }
        if(SelectedElement !== null) {
            SelectedElement.Selected = false;
        }
        SelectedElement = Element;
        SelectedElement.Selected = true;
    };

    /**
     * Listens on the 'select' event.
     * Selects the emitting Note or Attachment and deselects any previous selected Note or Attachment.
     * @listens vDesk.PinBoard.Attachment#event:select
     * @listens vDesk.PinBoard.Note#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
        Select(Event.detail.sender);
        SetOnTop(Event.detail.sender);
        DeleteToolBarItem.Enabled = true;
        ContextMenu.Hide();
    };

    /**
     * Listens on the open event and displays the content of an Attachment.
     * @listens vDesk.PinBoard.Attachment#event:open
     * @param {CustomEvent} Event
     */
    const OnOpen = Event => {
        if(Event.detail.sender instanceof vDesk.PinBoard.Attachment) {
            ShowAttachment(Event.detail.sender);
        }
    };

    /**
     * Deselects any selected Note or Attachment and disables ToolBar Items and closes the ContextMenu.
     */
    const OnClick = function() {
        if(SelectedElement !== null) {
            SelectedElement.Selected = false;
        }
        ViewAttachmentToolBarItem.Enabled = false;
        NoteGreenToolBarItem.Enabled = false;
        NoteBlueToolBarItem.Enabled = false;
        NoteYellowToolBarItem.Enabled = false;
        NoteWhiteToolBarItem.Enabled = false;
        NoteRedToolBarItem.Enabled = false;
        NoteCustomColorToolBarItem.Enabled = false;
        DeleteToolBarItem.Enabled = false;
        SelectedElement = null;
        ContextMenu.Hide();
    };

    /**
     * Listens on the 'contextmenu' and 'context' event
     * and displays the the ContextMenu of this module on the estimated position.
     * @param {CustomEvent|MouseEvent} Event
     * @listens vDesk.PinBoard.Note#event:context
     * @listens vDesk.PinBoard.Attachment#event:context
     */
    const OnContext = Event => {
        if(ContextMenu.Visible) {
            ContextMenu.Hide();
        }
        if(Event.target === Control) {
            ContextMenu.Show(Control, Event.pageX, Event.pageY);
        } else if(IsNoteOrAttachment(Event.detail.sender)) {
            ContextMenu.Show(Event.detail.sender, Event.detail.x, Event.detail.y);
        }
    };

    /**
     * Eventhandler that listens on the 'submit' event.
     * @listens vDesk.Controls.ContextMenu#event:submit
     * @param {CustomEvent} Event
     */
    const OnSubmit = Event => {
        switch(Event.detail.action) {
            case "Open":
                ShowAttachment(Event.detail.target);
                break;
            case "Delete":
                DeleteElement(Event.detail.target);
                break;
            case "CreateNote":
                CreateNote();
                break;
            case "NoteBlue":
                ChangeColor(Event.detail.target, vDesk.PinBoard.Note.Blue);
                break;
            case "NoteGreen":
                ChangeColor(Event.detail.target, vDesk.PinBoard.Note.Green);
                break;
            case "NoteWhite":
                ChangeColor(Event.detail.target, vDesk.PinBoard.Note.White);
                break;
            case "NoteYellow":
                ChangeColor(Event.detail.target, vDesk.PinBoard.Note.Yellow);
                break;
            case "NoteRed":
                ChangeColor(Event.detail.target, vDesk.PinBoard.Note.Red);
                break;
            case "NoteCustom":
                SelectedElement = Event.detail.target;
                ColorPicker.Color = vDesk.Media.Drawing.Color.FromHexString(SelectedElement.Color);
                CustomColorDialog.Show();
                break;
        }
        ContextMenu.Hide();
    };

    /**
     * Eventhandler that listens on the 'submit' event.
     * @listens vDesk.Controls.ContextMenu#event:submit
     * @param {CustomEvent} Event
     */
    const OnSubmitArchive = Event => {
        if(Event.detail.action === "CreateattAchment") {
            CreateAttachment(Event.detail.target);
        }
    };

    /**
     * Creates and adds a new note to the PinBoard.
     */
    const CreateNote = function() {
        if(NewNote === null) {
            //Execute command against the server.
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "PinBoard",
                        Command:    "CreateNote",
                        Parameters: {
                            Height: 120,
                            Width:  160,
                            X:      120,
                            Y:      50,
                            Color:  vDesk.PinBoard.Note.Yellow
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        const Note = new vDesk.PinBoard.Note(
                            Response.Data,
                            160,
                            120,
                            50,
                            120,
                            vDesk.PinBoard.Note.Yellow,
                            "",
                            {
                                Left:   10,
                                Top:    10,
                                Right:  10,
                                Bottom: 10
                            }
                        );
                        Elements.push(Note);
                        NewNote = Note;
                        Control.appendChild(Note.Control);
                        SetOnTop(NewNote);
                        Select(NewNote);
                        NewNote.Content.focus();
                    } else {
                        alert(Response.Data);
                    }
                }
            );
        } else {
            SetOnTop(NewNote);
            Select(NewNote);
            NewNote.Content.focus();
        }

    };

    /**
     * Attaches a vDesk.Archive.Element on the PinBoard.
     * @param {vDesk.Archive.Element} Element The Element to add.
     * @todo Provide Interfaces and APIs to attach custom PinBoard-Elements.
     */
    const CreateAttachment = function(Element) {
        Ensure.Parameter(Element, vDesk.Archive.Element, "Element");
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "PinBoard",
                    Command:    "CreateAttachment",
                    Parameters: {
                        X:       200,
                        Y:       200,
                        Element: Element.ID
                    },
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    const Attachment = new vDesk.PinBoard.Attachment(
                        Response.Data,
                        200,
                        200,
                        Element,
                        {
                            Left:   10,
                            Top:    10,
                            Right:  10,
                            Bottom: 10
                        }
                    );
                    Elements.push(Attachment);
                    SetOnTop(Attachment);
                    Control.appendChild(Attachment.Control);
                } else {
                    alert(Response.Data);
                }
            }
        );
    };

    /**
     * Navigates in the Archive to the attached vDesk.Archive.Element and displays its content if any.
     * @param {vDesk.PinBoard.Attachment} Attachment The Attachment to show.
     */
    const ShowAttachment = function(Attachment) {
        Ensure.Parameter(Attachment, vDesk.PinBoard.Attachment, "Attachment");
        const Module = vDesk.Modules["Archive"];
        if(Attachment.Element.Type === vDesk.Archive.Element.Folder) {
            vDesk.WorkSpace.Module = Module;
        }
        Module.GoToID(Attachment.Element.ID);
    };

    /**
     * Deletes a Note or Attachment from the PinBoard.
     * @param {vDesk.PinBoard.Note|vDesk.PinBoard.Attachment} Element The Note or Attachment to delete.
     */
    const DeleteElement = function(Element) {
        Ensure.Parameter(Element, [vDesk.PinBoard.Note, vDesk.PinBoard.Attachment], "Element");
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "PinBoard",
                    Command:    Element instanceof vDesk.PinBoard.Note
                                ? "DeleteNote"
                                : Element instanceof vDesk.PinBoard.Attachment
                                  ? "DeleteAttachment"
                                  : "",
                    Parameters: {ID: Element.ID},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    if(SelectedElement === Element) {
                        SelectedElement = null;
                    }
                    if(NewNote === Element) {
                        NewNote = null;
                    }
                    Elements.splice(Elements.indexOf(Element), 1);
                    Control.removeChild(Element.Control);
                } else {
                    alert(Response.Data);
                }
            });
        ContextMenu.Hide();
    };

    /**
     * Checks whether a given object is a note.
     * @param {Object} Element The element to check.
     * @return {Boolean} True if the given element is an instance of vDesk.PinBoard.Note.
     */
    const IsNote = Element => Element instanceof vDesk.PinBoard.Note;

    /**
     * Checks whether a given object is an attachment.
     * @param {Object} Element The element to check.
     * @return {Boolean} True if the given element is an instance of vDesk.PinBoard.Attachment.
     */
    const IsAttachment = Element => Element instanceof vDesk.PinBoard.Attachment;

    /**
     * Checks whether a given object is a note or attachment.
     * @param {Object} Element The element to check.
     * @return {Boolean} True if the given element is an instance of vDesk.PinBoard.Note or vDesk.PinBoard.Attachment.
     */
    const IsNoteOrAttachment = Element => IsNote(Element) || IsAttachment(Element);

    this.Load = function() {
        vDesk.Header.ToolBar.Groups = [NoteToolBarGroup, SelectionToolBarGroup, NoteColorToolBarGroup];
        Control.addEventListener("context", OnContext, false);
        Control.addEventListener("contextmenu", OnContext, false);
        Control.addEventListener("resized", OnResized, false);
        Control.addEventListener("moved", OnMoved, false);
        Control.addEventListener("contentchanged", OnContentChanged, false);
        Control.addEventListener("open", OnOpen, false);
        Control.addEventListener("select", OnSelect, false);
        Control.addEventListener("click", OnClick, false);
    };

    this.Unload = function() {
        ContextMenu.Hide();
        Control.removeEventListener("context", OnContext, false);
        Control.removeEventListener("contextmenu", OnContext, false);
        Control.removeEventListener("resized", OnResized, false);
        Control.removeEventListener("moved", OnMoved, false);
        Control.removeEventListener("contentchanged", OnContentChanged, false);
        Control.removeEventListener("open", OnOpen, false);
        Control.removeEventListener("select", OnSelect, false);
        Control.removeEventListener("click", OnClick, false);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "PinBoard";
    Control.addEventListener("select", OnSelect);

    /**
     * The ToolBarItem for creating a new note.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const CreateNoteToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["NewNote"],
        vDesk.Visual.Icons.Pinboard.Note,
        true,
        CreateNote
    );

    /**
     * The ToolBarGroup containing ToolBarItems for creating new notes.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const NoteToolBarGroup = new vDesk.Controls.ToolBar.Group(vDesk.Locale["PinBoard"]["Notes"], [CreateNoteToolBarItem]);

    /**
     * The ToolBarItem for viewing the current selected attachment.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const ViewAttachmentToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["vDesk"]["Open"],
        vDesk.Visual.Icons.View,
        false,
        () => ShowAttachment(SelectedElement)
    );

    /**
     * The ToolBarItem for deleting the current selected attachment/note.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const DeleteToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["vDesk"]["Delete"],
        vDesk.Visual.Icons.Delete,
        false,
        () => DeleteElement(SelectedElement)
    );

    /**
     * The ToolBarGroup containing ToolBarItems for viewing attachments and deleting notes or attachments.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const SelectionToolBarGroup = new vDesk.Controls.ToolBar.Group(vDesk.Locale["vDesk"]["Selection"], [
        ViewAttachmentToolBarItem,
        DeleteToolBarItem
    ]);

    /**
     * The ToolBarItem changing the color of a selected note to Red.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NoteRedToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["Red"],
        vDesk.Visual.Icons.Pinboard.Color.Red,
        false,
        () => ChangeColor(SelectedElement, vDesk.PinBoard.Note.Red)
    );

    /**
     * The ToolBarItem changing the color of a selected note to green.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NoteGreenToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["Green"],
        vDesk.Visual.Icons.Pinboard.Color.Green,
        false,
        () => ChangeColor(SelectedElement, vDesk.PinBoard.Note.Green)
    );

    /**
     * The ToolBarItem changing the color of a selected note to blue.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NoteBlueToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["Blue"],
        vDesk.Visual.Icons.Pinboard.Color.Blue,
        false,
        () => ChangeColor(SelectedElement, vDesk.PinBoard.Note.Blue)
    );

    /**
     * The ToolBarItem changing the color of a selected note to yellow.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NoteYellowToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["Yellow"],
        vDesk.Visual.Icons.Pinboard.Color.Yellow,
        false,
        () => ChangeColor(SelectedElement, vDesk.PinBoard.Note.Yellow)
    );

    /**
     * The ToolBarItem changing the color of a selected note to white.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NoteWhiteToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["White"],
        vDesk.Visual.Icons.Pinboard.Color.White,
        false,
        () => ChangeColor(SelectedElement, vDesk.PinBoard.Note.White)
    );

    /**
     * The ColorPicker of the PinBoard.
     * @type {vDesk.Media.Drawing.ColorPicker}
     */
    const ColorPicker = new vDesk.Media.Drawing.ColorPicker();
    ColorPicker.Control.addEventListener("update", Event => SelectedElement.Color = Event.detail.color.ToHexString());

    /**
     * The custom color dialog of the PinBoard.
     * @type {vDesk.Controls.Window}
     */
    const CustomColorDialog = new vDesk.Controls.Window(vDesk.Visual.Icons.Pinboard.Color.Custom, vDesk.Locale["PinBoard"]["CustomColor"]);
    CustomColorDialog.Content.appendChild(ColorPicker.Control);
    CustomColorDialog.Control.addEventListener("show", () => ColorPicker.Color = vDesk.Media.Drawing.Color.FromHexString(SelectedElement.Color));
    CustomColorDialog.Control.addEventListener("close", () => ChangeColor(SelectedElement, ColorPicker.Color.ToHexString()));
    CustomColorDialog.Height = 302;
    CustomColorDialog.Width = 392;
    CustomColorDialog.Resizable = false;

    /**
     * The ToolBarItem changing the color of a selected note to a custom color.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const NoteCustomColorToolBarItem = new vDesk.Controls.ToolBar.Item(
        vDesk.Locale["PinBoard"]["Custom"],
        vDesk.Visual.Icons.Pinboard.Color.Custom,
        false,
        () => CustomColorDialog.Show()
    );

    /**
     * The ToolBarGroup containing ToolBarItems for changing the color of a selected Note.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const NoteColorToolBarGroup = new vDesk.Controls.ToolBar.Group(
        vDesk.Locale["PinBoard"]["Note"],
        [
            NoteRedToolBarItem,
            NoteGreenToolBarItem,
            NoteBlueToolBarItem,
            NoteYellowToolBarItem,
            NoteWhiteToolBarItem,
            NoteCustomColorToolBarItem
        ]
    );

    /**
     * The ContextMenu of the PinBoard.
     * @type {vDesk.Controls.ContextMenu}
     */
    const ContextMenu = new vDesk.Controls.ContextMenu(
        [
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["vDesk"]["Open"],
                "Open",
                vDesk.Visual.Icons.View,
                IsAttachment
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["vDesk"]["Delete"],
                "Delete",
                vDesk.Visual.Icons.Delete,
                IsNoteOrAttachment
            ),
            new vDesk.Controls.ContextMenu.Group(
                vDesk.Locale["vDesk"]["New"],
                vDesk.Visual.Icons.TriangleRight,
                () => true,
                [
                    new vDesk.Controls.ContextMenu.Item(
                        vDesk.Locale["PinBoard"]["Note"],
                        "CreateNote",
                        vDesk.Visual.Icons.Pinboard.Note
                    )
                ]
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["PinBoard"]["Red"],
                "NoteRed",
                vDesk.Visual.Icons.Pinboard.Color.Red,
                IsNote
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["PinBoard"]["Green"],
                "NoteGreen",
                vDesk.Visual.Icons.Pinboard.Color.Green,
                IsNote
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["PinBoard"]["Blue"],
                "NoteBlue",
                vDesk.Visual.Icons.Pinboard.Color.Blue,
                IsNote
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["PinBoard"]["Yellow"],
                "NoteYellow",
                vDesk.Visual.Icons.Pinboard.Color.Yellow,
                IsNote
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["PinBoard"]["White"],
                "NoteWhite",
                vDesk.Visual.Icons.Pinboard.Color.White,
                IsNote
            ),
            new vDesk.Controls.ContextMenu.Item(
                vDesk.Locale["PinBoard"]["Custom"],
                "NoteCustom",
                vDesk.Visual.Icons.Pinboard.Color.Custom,
                IsNote
            )
        ]
    );
    ContextMenu.Control.addEventListener("submit", OnSubmit);

    const Archive = vDesk.Modules["Archive"];
    Archive.ContextMenu.Add(
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale["Archive"]["PinBoard"],
            "CreateattAchment",
            vDesk.Visual.Icons.Pinboard.Pin,
            () => true//Element => Element !== FolderView.CurrentFolder
        )
    );
    Archive.ContextMenu.Control.addEventListener("submit", OnSubmitArchive);

    //Fetch all Notes and Attachments of the user from the server.
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "PinBoard",
                Command:    "GetEntries",
                Parameters: {},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            if(Response.Status) {
                let Index = DefaultIndex;
                const Fragment = document.createDocumentFragment();
                //Add Notes to the PinBoard.
                Response.Data.Notes.forEach(DataView => {
                    const Note = vDesk.PinBoard.Note.FromDataView(
                        DataView,
                        {
                            Left:   10,
                            Top:    10,
                            Right:  10,
                            Bottom: 10
                        }
                    );
                    Note.StackOrder = Index;
                    Elements.push(Note);
                    Fragment.appendChild(Note.Control);
                });
                //Add Attachments to the PinBoard.
                Response.Data.Attachments.forEach(DataView => {
                    const Attachment = vDesk.PinBoard.Attachment.FromDataView(
                        DataView,
                        {
                            Left:   10,
                            Top:    10,
                            Right:  10,
                            Bottom: 10
                        }
                    );
                    Attachment.StackOrder = Index;
                    Index += 10;
                    Elements.push(Attachment);
                    Fragment.appendChild(Attachment.Control);
                });
                Control.appendChild(Fragment);
            } else {
                alert(Response.Data);
            }
        });
};

Modules.PinBoard.Implements(vDesk.Modules.IVisualModule);