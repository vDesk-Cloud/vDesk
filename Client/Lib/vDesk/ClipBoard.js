"use strict";
/**
 * Initializes a new instance of the Clipboard class.
 * @constructor
 * @class Provides functions for adding and receiving copy, cut and paste operations.
 * @memberOf vDesk
 */
vDesk.Clipboard = function Clipboard() {

    /**
     * The last operation which has been performed. Formerly cut or copy.
     * @type {null|String}
     */
    let LastOperation = null;

    /**
     * Flag that indicates if the clipboard captures focus and contextevents.
     * @type {Boolean}
     */
    let Enabled = null;

    /**
     * The node that has been focused.
     * @type {null|HTMLElement|HTMLTextAreaElement}
     */
    let FocusedNode = null;

    /**
     * The text of the ClipBoard.
     * @type {String}
     */
    let Text = "";

    Object.defineProperties(this, {
        LastOperation: {
            enumerable: true,
            get:        () => LastOperation,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Enabled");
                LastOperation = Value;
            }
        },
        ContainsText:  {
            enumerable: true,
            get:        () => Text.length > 0
        },
        Enabled:       {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
            }
        }
    });

    /**
     * Determines whether the specified value is an editable text Node.
     * @param {HTMLElement} Node The Node to check.
     * @return {Boolean} True if the specified value is an editable text Node; otherwise, false.
     */
    const IsEditableTextNode = Node => !Node.readOnly || !Node.disabled;

    /**
     * Determines whether the specified value is a mutable contenteditable Node.
     * @param {HTMLElement} Node The Node to check.
     * @return {Boolean} True if the specified value is a mutable contenteditable Node; otherwise, false.
     */
    const IsEditableContentNode = Node => Node.contentEditable === true.toString();

    /**
     * Determines whether the specified value is a text Node.
     * @param {HTMLElement} Node Node The Node to check.
     * @return {Boolean} True if the specified value is a text Node; otherwise, false.
     */
    const IsTextNode = Node => Node instanceof HTMLTextAreaElement || Node instanceof HTMLInputElement && Node.type === "text";

    /**
     * Determines whether the specified value is a contenteditable Node.
     * @param {HTMLElement} Node Node The Node to check.
     * @return {Boolean} True if the specified value is a contenteditable Node; otherwise, false.
     */
    const IsContentNode = Node => Node instanceof HTMLDivElement
                                  || Node instanceof HTMLSpanElement
                                  || Node instanceof HTMLParagraphElement
                                  || Node instanceof HTMLTableCellElement
                                  || Node instanceof HTMLPreElement;

    /**
     * Selects the entire text content of a specified Node.
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLDivElement|HTMLSpanElement} Node The Node to select the text content of.
     */
    this.SelectAll = function(Node) {
        FocusedNode = Node;
        let Range = null;
        if(IsTextNode(Node)) {
            Node.select();
        } else if(IsContentNode(Node)) {
            Range = document.createRange();
            Range.selectNodeContents(Node);
            const Selection = window.getSelection();
            Selection.removeAllRanges();
            Selection.addRange(Range);
        }
    };

    /**
     * Copies the selected text content of a specified Node to the ClipBoard.
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLDivElement|HTMLSpanElement} Node The Node to copy the text content of.
     */
    this.Copy = function(Node) {
        FocusedNode = Node;
        if(IsTextNode(Node)) {
            Text = Node.value.substring(Node.selectionStart, Node.selectionEnd);
        } else if(IsContentNode(Node)) {
            Text = window.getSelection().toString();
        }
    };

    /**
     * Cuts the selected text content of a specified Node to the ClipBoard.
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLDivElement|HTMLSpanElement} Node The Node to cut the text content of.
     */
    this.Cut = function(Node) {
        FocusedNode = Node;
        if(IsTextNode(Node) && IsEditableTextNode(Node)) {
            Text = Node.value.substring(Node.selectionStart, Node.selectionEnd);
            Node.value = Node.value.replace(Text, "");
        } else if(IsContentNode(Node) && IsEditableContentNode(Node)) {
            const Selection = window.getSelection();
            Text = Selection.toString();
            Selection.removeAllRanges();
            Node.textContent = Node.textContent.replace(Text, "");
        }
    };

    /**
     * Pastes the text content of the ClipBoard to a specified Node.
     * @param {HTMLInputElement|HTMLTextAreaElement|HTMLDivElement|HTMLSpanElement} Node The Node to paste the text content to.
     */
    this.Paste = function(Node) {
        Ensure.Parameter(Node, HTMLElement, "Node");
        FocusedNode = Node;
        if(IsTextNode(Node) && IsEditableTextNode(Node)) {
            Node.value = Node.value.replace(Node.value.substring(Node.selectionStart, Node.selectionEnd), Text);
        } else if(IsContentNode(Node) && IsEditableContentNode(Node)) {
            const Selection = window.getSelection();
            const SelectedText = Selection.toString();
            if(SelectedText.length > 0) {
                Node.textContent = Node.textContent.replace(SelectedText, Text);
            } else {
                Node.textContent = Node.textContent.substring(0, Selection.anchorOffset) + Text + Node.textContent.substring(Selection.anchorOffset)
            }
            Selection.removeAllRanges();
        }
    };

    /**
     * Eventhandler that listens on the 'keydown' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyDown = Event => {
        if(Event.ctrlKey) {
            switch(Event.key) {
                case "c":
                    this.Copy(FocusedNode);
                    break;
                case "a":
                    Event.preventDefault();
                    this.SelectAll(FocusedNode);
                    break;
                case "x":
                    this.Cut(FocusedNode);
                    break;
                case "v":
                    this.Paste(FocusedNode);
                    break;
                default:
                    return;
            }
        }
    };

    /**
     * Eventhandler that listens on the 'focus' event.
     * @param {KeyboardEvent} Event
     */
    const OnFocus = Event => {
        if(IsTextNode(Event.target) || IsContentNode(Event.target)) {
            FocusedNode = Event.target;
            FocusedNode.addEventListener("blur", OnBlur);
            window.addEventListener("keydown", OnKeyDown, true);
        }
    };
    /**
     * Eventhandler that listens on the 'blur' event.
     */
    const OnBlur = () => {
        FocusedNode.removeEventListener("blur", OnBlur);
        FocusedNode = null;
        window.removeEventListener("keydown", OnKeyDown, true);
    };

    window.addEventListener("focus", OnFocus, true);

    /**
     * Eventhandler that listens on the 'contextmenu' event.
     * @param {MouseEvent} Event
     */
    const OnContextMenu = Event => {
        if(
            IsTextNode(Event.target)
            && (Event.target.value.length > 0 || IsEditableTextNode(Event.target))
            || IsContentNode(Event.target)
            && (Event.target.firstChild instanceof window.Text || IsEditableContentNode(Event.target))
        ) {
            Event.preventDefault();
            Event.stopPropagation();
            FocusedNode = Event.target;
            ContextMenu.Show(FocusedNode, Event.pageX, Event.pageY);
        }
    };

    /**
     * Eventhandler that listens on the 'submit' event.
     * @listens vDesk.Controls.ContextMenu#event:submit
     * @param {CustomEvent} Event
     */
    const OnSubmit = Event => {
        switch(Event.detail.action) {
            case "Copy":
                this.Copy(ContextMenu.Target);
                break;
            case "Cut":
                this.Cut(ContextMenu.Target);
                break;
            case "Paste":
                this.Paste(ContextMenu.Target);
                break;
            case "SelectAll":
                this.SelectAll(ContextMenu.Target);
                break;
        }
        ContextMenu.Hide();
    };

    /**
     * The ContextMenu of the ClipBoard.
     * @type {vDesk.Controls.ContextMenu}
     */
    const ContextMenu = new vDesk.Controls.ContextMenu([
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Copy,
            "Copy",
            vDesk.Visual.Icons.Copy,
            () => IsTextNode(ContextMenu.Target) && ContextMenu.Target.selectionStart !== ContextMenu.Target.selectionEnd
                  || IsContentNode(ContextMenu.Target) && !window.getSelection().isCollapsed
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Cut,
            "Cut",
            vDesk.Visual.Icons.Cut,
            () => IsTextNode(ContextMenu.Target) && IsEditableTextNode(ContextMenu.Target) && ContextMenu.Target.selectionStart !== ContextMenu.Target.selectionEnd
                  || IsContentNode(ContextMenu.Target) && IsEditableContentNode(ContextMenu.Target) && !window.getSelection().isCollapsed
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.Paste,
            "Paste",
            vDesk.Visual.Icons.Paste,
            () => Text.length > 0
                  && (
                      IsTextNode(ContextMenu.Target) && IsEditableTextNode(ContextMenu.Target)
                      || IsContentNode(ContextMenu.Target) && IsEditableContentNode(ContextMenu.Target)
                  )
        ),
        new vDesk.Controls.ContextMenu.Item(
            vDesk.Locale.vDesk.SelectAll,
            "SelectAll",
            vDesk.Visual.Icons.SelectAll,
            () => IsTextNode(ContextMenu.Target) && (ContextMenu.Target.selectionStart > 0 || ContextMenu.Target.selectionEnd < ContextMenu.Target.value.length)
                  || IsContentNode(ContextMenu.Target) && ContextMenu.Target.textContent.length !== window.getSelection().toString().length
        )
    ]);
    ContextMenu.Control.addEventListener("submit", OnSubmit);
    window.addEventListener("click", () => ContextMenu.Hide());
    window.addEventListener("contextmenu", OnContextMenu);

};

