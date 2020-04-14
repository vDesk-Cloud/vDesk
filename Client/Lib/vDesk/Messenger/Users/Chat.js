"use strict";
/**
 * Fired if the Chat has been selected.
 * @event vDesk.Messenger.Users.Chat.Chat#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Messenger.Users.Chat} detail.sender The current instance of the Chat.
 * @property {vDesk.Security.User} detail.user The recipient of the Chat.
 */
/**
 * Initializes a new instance of the Chat class.
 * @class Represents a private Chat.
 * @param {vDesk.Security.User} Sender Initializes the Chat with the specified sender.
 * @param {Number} Unread Initializes the Chat with the specified amount of unread Messages.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Chat is enabled.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.User} Sender Gets or sets the sender of the Chat.
 * @property {Number} Unread Gets or sets the amount of unread Messages of the Chat.
 * @property {vDesk.Messenger.Users.Chat.Conversation} Conversation Gets the Conversation of the Chat.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Chat is enabled.
 * @property {Boolean} Selected Gets or sets a value indicating whether the Chat is selected.
 * @extends vDesk.Security.UserList.Item
 * @memberOf vDesk.Messenger.Users
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Messenger.Users.Chat = function Chat(Sender, Unread = 0, Enabled = true) {
    Ensure.Parameter(Sender, vDesk.Security.User, "User");
    Ensure.Parameter(Unread, Type.Number, "Unread");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * Flag indicating whether the Chat is selected.
     * @type {Boolean}
     */
    let Selected = false;

    /**
     * The Conversation of the Chat.
     * @type {null|vDesk.Messenger.Users.Chat.Conversation}
     */
    let Conversation = null;

    Object.defineProperties(this, {
        Control:      {
            enumerable: true,
            get:        () => Control
        },
        Sender:         {
            enumerable: true,
            get:        () => Sender,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "Sender");
                Sender = Value;
                Name.textContent = Value.Name;
                Conversation = null;
            }
        },
        Unread:       {
            enumerable: true,
            get:        () => Unread,
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Unread");
                Unread = Value;
                UnreadMessages.textContent = Value > 0 ? `(${Value})` : "";
            }
        },
        Conversation: {
            enumerable: true,
            get:        () => {
                if(Conversation === null){
                    Conversation = vDesk.Messenger.Users.Chat.Conversation.FromSender(Sender);
                }
                return Conversation;
            }
        },
        Selected:     {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Selected");
                Selected = Value;
                Control.classList.toggle("Selected", Value);
            }
        },
        Enabled:      {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                if(Value) {
                    Control.addEventListener("click", OnClick, false);
                } else {
                    Control.removeEventListener("click", OnClick, false);
                }
                Control.classList.toggle("Disabled", !Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Messenger.Users.Chat#select
     */
    const OnClick = () => new vDesk.Events.BubblingEvent("select", {
        sender: this,
        user:   Sender
    }).Dispatch(Control);

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = "Chat Font Dark BorderLight";
    Control.classList.toggle("Disabled", !Enabled);
    if(Enabled) {
        Control.addEventListener("click", OnClick, false);
    }

    /**
     * The name span of the Chat.
     * @type {HTMLSpanElement}
     */
    const Name = document.createElement("span");
    Name.className = "Name";
    Name.textContent = Sender.Name;
    Control.appendChild(Name);

    /**
     * The unread Messages span of the Chat.
     * @type {HTMLSpanElement}
     */
    const UnreadMessages = document.createElement("span");
    UnreadMessages.className = "Unread";
    UnreadMessages.textContent = Unread > 0 ? `(${Unread})` : "";
    Control.appendChild(UnreadMessages);
};