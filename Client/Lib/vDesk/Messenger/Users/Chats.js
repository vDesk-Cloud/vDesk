"use strict";
/**
 * Fired if the Chats has been selected.
 * @event vDesk.Messenger.Users.Chats#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Messenger.Users.Chats} detail.sender The current instance of the Chats.
 * @property {vDesk.Messenger.Users.Chats.Chat} detail.chat The selected Chat.
 */
/**
 * Initializes a new instance of the Chats class.
 * @class Represents a collection of private Chats.
 * @param {Array<vDesk.Messenger.Users.Chat>} [Chats=[]] Initializes the Chats with the specified set of Chats.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Chats is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Messenger.Users.Chat>} Items Gets the Items of the Chats.
 * @property {vDesk.Messenger.Users.Chat} Selected Gets or sets the current selected Chat of the Chats.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Chats is enabled.
 * @memberOf vDesk.Messenger.Users
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Messenger.Users.Chats = function Chats(Chats = [], Enabled = true) {
    Ensure.Parameter(Chats, Array, "Chats");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The current selected Chat of the Chats.
     * @type {null|vDesk.Messenger.Users.Chat}
     */
    let Selected = null;

    Object.defineProperties(this, {
        Control:  {
            enumerable: true,
            get:        () => Control
        },
        Chats:    {
            enumerable: true,
            get:        () => Chats,
            set:        Value => {
                Ensure.Property(Value, Array, "Chats");

                //Clear list.
                Chats.forEach(Chat => Control.removeChild(Chat.Control));

                Chats = Value;

                //Append new Chats.
                const Fragment = document.createDocumentFragment();
                Value.forEach(Chat => {
                    Ensure.Parameter(Chat, vDesk.Messenger.Users.Chat, "Chat");
                    Fragment.appendChild(Chat.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Messenger.Users.Chat, "Selected");
                if(Selected !== null) {
                    Selected.Selected = false;
                }
                Selected = Value;
                Selected.Selected = true;
            }
        },
        Enabled:  {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Chats.forEach(Chat => Chat.Enabled = Value);
            }
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @param {CustomEvent} Event
     * @listens vDesk.Messenger.Users.Chat#event:select
     * @fires vDesk.Messenger.Users.Chats#select
     */
    const OnSelect = Event => {
        Event.stopPropagation();

        if(Selected !== null) {
            Selected.Selected = false;
        }
        Selected = Event.detail.sender;
        Selected.Selected = true;

        Control.removeEventListener("select", OnSelect, true);
        new vDesk.Events.BubblingEvent("select", {
            sender: this,
            chat:   Event.detail.sender
        }).Dispatch(Control);
        Control.addEventListener("select", OnSelect, true);
    };

    /**
     * Searches the Chats for an Chat by a specified User ID.
     * @param {Number} ID The ID of the User of the Chat to find.
     * @return {vDesk.Messenger.Users.Chat|null} The found Chat; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "Chat");
        return Chats.find(Chat => Chat.Sender.ID === ID) || null;
    };

    /**
     * Adds an Chat to the Chats.
     * @param {vDesk.Messenger.Users.Chat} Chat The Chat to add.
     */
    this.Add = function(Chat) {
        Ensure.Parameter(Chat, vDesk.Messenger.Users.Chat, "Chat");
        Chats.push(Chat);
        Control.appendChild(Chat.Control);
    };

    /**
     * Removes an Chat from the Chats.
     * @param {vDesk.Messenger.Users.Chat|Null} Chat The Chat to remove.
     */
    this.Remove = function(Chat) {
        Ensure.Parameter(Chat, vDesk.Messenger.Users.Chat, "Chat");
        const Index = Chats.indexOf(Chat);
        if(~Index) {
            Control.removeChild(Chat.Control);
            Chats.splice(Index, 1);
        }
    };

    /**
     * Removes every Chat from the Chats.
     */
    this.Clear = function() {
        Chats.forEach(Chat => Control.removeChild(Chat.Control));
        Chats = [];
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLUListElement}
     */
    const Control = document.createElement("ul");
    Control.className = "Users Chats BorderLight";
    Control.addEventListener("select", OnSelect, true);

    //Fill Chats.
    Chats.forEach(Chat => {
        Ensure.Parameter(Chat, vDesk.Messenger.Users.Chat, "Chat");
        Control.appendChild(Chat.Control);
    });
};
/**
 * Factory method that creates a Chats list containing every existing recipient.
 * @return {vDesk.Messenger.Users.Chats} A Chats list containing all current Chats.
 */
vDesk.Messenger.Users.Chats.FromUsers = function() {
    const Chats = new vDesk.Messenger.Users.Chats(
        vDesk.Security.Users.map(User => new vDesk.Messenger.Users.Chat(User))
    );
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Messenger",
                Command:    "GetUnreadUserMessages",
                Ticket:     vDesk.User.Ticket
            }
        ),
        Response => {
            Response.Data.forEach((Amount, Sender) => Chats.Chats.find(Chat => Chat.Sender.ID === Number(Sender)).Unread = Amount);
        }
    );
    return Chats;
};