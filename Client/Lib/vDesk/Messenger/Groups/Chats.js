"use strict";
/**
 * Fired if the Chats has been selected.
 * @event vDesk.Messenger.Groups.Chats#select
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'select' event.
 * @property {vDesk.Messenger.Groups.Chats} detail.sender The current instance of the Chats.
 * @property {vDesk.Messenger.Groups.Chats.Chat} detail.chat The selected Chat.
 */
/**
 * Initializes a new instance of the Chats class.
 * @class Represents a collection of private Chats.
 * @param {Array<vDesk.Messenger.Groups.Chat>} [Chats=[]] Initializes the Chats with the specified set of Chats.
 * @param {Boolean} [Enabled=true] Flag indicating whether the Chats is enabled.
 * @property {HTMLUListElement} Control Gets the underlying DOM-Node.
 * @property {Array<vDesk.Messenger.Groups.Chat>} Items Gets the Items of the Chats.
 * @property {vDesk.Messenger.Groups.Chat} Selected Gets or sets the current selected Chat of the Chats.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Chats is enabled.
 * @memberOf vDesk.Messenger.Groups
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Messenger
 */
vDesk.Messenger.Groups.Chats = function Chats(Chats = [], Enabled = true) {
    Ensure.Parameter(Chats, Array, "Chats");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The current selected Chat of the Chats.
     * @type {null|vDesk.Messenger.Groups.Chat}
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
                    Ensure.Parameter(Chat, vDesk.Messenger.Groups.Chat, "Chat");
                    Fragment.appendChild(Chat.Control);
                });
                Control.appendChild(Fragment);
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Messenger.Groups.Chat, "Selected");
                if(Selected !== null){
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
     * @listens vDesk.Messenger.Groups.Chat#event:select
     * @fires vDesk.Messenger.Groups.Chats#select
     */
    const OnSelect = Event => {
        Event.stopPropagation();

        if(Selected !== null){
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
     * @return {vDesk.Messenger.Groups.Chat|null} The found Chat; otherwise, null.
     */
    this.Find = function(ID) {
        Ensure.Parameter(ID, Type.Number, "Chat");
        return Chats.find(Chat => Chat.Sender.ID === ID) ?? null;
    };

    /**
     * Adds an Chat to the Chats.
     * @param {vDesk.Messenger.Groups.Chat} Chat The Chat to add.
     */
    this.Add = function(Chat) {
        Ensure.Parameter(Chat, vDesk.Messenger.Groups.Chat, "Chat");
        Chats.push(Chat);
        Control.appendChild(Chat.Control);
    };

    /**
     * Removes an Chat from the Chats.
     * @param {vDesk.Messenger.Groups.Chat|Null} Chat The Chat to remove.
     */
    this.Remove = function(Chat) {
        Ensure.Parameter(Chat, vDesk.Messenger.Groups.Chat, "Chat");
        const Index = Chats.indexOf(Chat);
        if(~Index){
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
    Control.className = "Groups Chats BorderLight";
    Control.addEventListener("select", OnSelect, true);

    //Fill Chats.
    Chats.forEach(Chat => {
        Ensure.Parameter(Chat, vDesk.Messenger.Groups.Chat, "Chat");
        Control.appendChild(Chat.Control);
    });
};

/**
 * Factory method that creates a Chats list containing every existing recipient.
 * @return {vDesk.Messenger.Groups.Chats} A Chats list containing all current Chats.
 */
vDesk.Messenger.Groups.Chats.FromGroups = function() {
    const Chats = new vDesk.Messenger.Groups.Chats(
        vDesk.Security.User.Current.Memberships
            .map(Group => vDesk.Security.Groups.find(ExistingGroup => ExistingGroup.ID === Group.ID))
            .map(Group => new vDesk.Messenger.Groups.Chat(Group))
    );
    Chats.Chats.forEach(Chat => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Messenger",
                    Command:    "GetGroupMessages",
                    Parameters: {
                        Group:  Chat.Group.ID,
                        Date:   new Date(),
                        Amount: 10
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                Chat.Conversation.AddMessages(
                    ...Response.Data.map(Message => vDesk.Messenger.Groups.Message.FromDataView(Message))
                );
                if(Chat.Selected){
                    Chat.Conversation.Scroll();
                }
            }
        );
    });
    return Chats;
};