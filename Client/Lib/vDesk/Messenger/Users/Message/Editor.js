"use strict";
/**
 * Fired if a new Message has been sent.
 * @event vDesk.Messenger.Users.Message.Editor#sent
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'sent' event.
 * @property {vDesk.Messenger.Users.Message.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.Messenger.Users.Message} detail.message The sent Message.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Class that represents an Editor for sending new Messages.
 * @param {vDesk.Security.User} Recipient Initializes the Editor with the specified Recipient.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.User} Recipient Gets or sets the Recipient of the Editor.
 * @memberOf vDesk.Messenger.Users.Message
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Messenger.Users.Message.Editor = function Editor(Recipient) {

    Object.defineProperties(this, {
        Control:   {
            get: () => Control
        },
        Recipient: {
            get: () => Recipient,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "Recipient");
                Recipient = Value;
            }
        }
    });

    /**
     * Sends a new Message to the Recipient of the Editor.
     * @fires vDesk.Messenger.Users.Message.Editor#sent
     */
    this.Send = function() {
        if(Text.value.length > 0) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Messenger",
                        Command:    "SendUserMessage",
                        Parameters: {
                            Recipient: Recipient.ID,
                            Text:      Text.value
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        new vDesk.Events.BubblingEvent("sent", {
                            sender:  this,
                            message: vDesk.Messenger.Users.Message.FromDataView(Response.Data)
                        }).Dispatch(Control);
                        Text.value = "";
                    }
                }
            );
        }
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Users MessageEditor";

    /**
     * The Message text textarea of the Editor.
     * @type {HTMLTextAreaElement}
     */
    const Text = document.createElement("textarea");
    Text.className = "Text TextBox";
    Text.minLength = 1;
    Text.maxLength = 65535;
    Text.addEventListener("input", () => Send.disabled = !Text.validity.valid);
    Text.addEventListener("keydown", Event => {
        if(Event.ctrlKey && Event.key === "Enter") {
            this.Send();
        }
    }, false);
    Control.appendChild(Text);

    /**
     * The EmojiPicker of the Editor.
     * @type {vDesk.Messenger.EmojiPicker}
     */
    const EmojiPicker = new vDesk.Messenger.EmojiPicker();
    EmojiPicker.Control.addEventListener("select", Event => Text.value = Text.value + Event.detail.emoji);

    /**
     * The send button of the Editor.
     * @type {HTMLButtonElement}
     */
    const Send = document.createElement("button");
    Send.className = "Send Button Icon";
    Send.style.backgroundImage = `url("${vDesk.Visual.Icons.Messenger.Send}")`;
    Send.textContent = "Send";
    Send.addEventListener("click", this.Send, false);


    /**
     * The controls container of the Editor.
     * @type {HTMLDivElement}
     */
    const Controls = document.createElement("div");
    Controls.className = "Controls";
    Controls.appendChild(EmojiPicker.Control);
    Controls.appendChild(Send);
    Control.appendChild(Controls);
};
