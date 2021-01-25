"use strict";
/**
 * Initializes a new instance of the Text class.
 * @class Plugin for displaying and editing the contents of a text file
 * @param {vDesk.Archive.Element} Element The element to display the file of.
 * @property {HTMLDivElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.Edit
 */
vDesk.Archive.Element.Edit.Text = function Text(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickSave = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Archive",
                    Command:    "UpdateFile",
                    Parameters: {
                        ID:   Element.ID,
                        File: new Blob([TextArea.value], {type: "text/plain"})
                    },
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                Element.Size = Response.Data.Size;
                Save.disabled = true;
                Reset.disabled = true;
            }
        );
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickReset = () => {
        //Save reference to Buffer and re-append it to the textarea.
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "ElementEditor Text";

    /**
     * The textarea containing the text content of the Element to edit.
     * @type {HTMLTextAreaElement}
     */
    const TextArea = document.createElement("textarea");
    TextArea.className = "TextBox";
    TextArea.addEventListener("input", () => Save.disabled = false);
    Control.appendChild(TextArea);

    /**
     * The edit/save button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const Save = document.createElement("button");
    Save.className = "Button Icon Save";
    Save.style.backgroundImage = `url("${vDesk.Visual.Icons.Save}")`;
    Save.textContent = vDesk.Locale.vDesk.Save;
    Save.disabled = true;
    Save.addEventListener("click", OnClickSave, false);

    /**
     * The reset button of the MaskDesigner.
     * @type {HTMLButtonElement}
     */
    const Reset = document.createElement("button");
    Reset.className = "Button Icon Reset";
    Reset.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    Reset.disabled = true;
    Reset.textContent = vDesk.Locale.vDesk.ResetChanges;
    Reset.addEventListener("click", OnClickReset, false);


    /**
     * The controls of the TextEditor.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls BorderLight";
    Controls.appendChild(Save);
    Controls.appendChild(Reset);
    Control.appendChild(Controls);

    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Archive",
                Command:    "Download",
                Parameters: {ID: Element.ID},
                Ticket:     vDesk.User.Ticket
            }
        ),
        Buffer => {
            //Create an objecturl from the binarydata.
            const Reader = new FileReader();
            Reader.onload = () => TextArea.value = Reader.result;
            Reader.readAsText(new Blob([Buffer], {type: "text/plain"}));
        },
        true
    );
};

/**
 * The file extensions the plugin can handle
 * @type {Array}
 * @enum {String}
 */
vDesk.Archive.Element.Edit.Text.Extensions = ["txt", "xml", "css", "csv", "yaml", "bat", "php", "js", "sql"];
