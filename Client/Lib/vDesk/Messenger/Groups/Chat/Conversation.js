"use strict";
/**
 * Initializes a new instance of the Conversation class.
 * @class Represents a private Conversation between 2 Groups.
 * @param {vDesk.Security.Group} [Group=new vDesk.Security.Group()] Initializes the Conversation with the specified Group.
 * @param {Array<vDesk.Messenger.Groups.Message>} [Messages=[]] Initializes the Conversation with the specified collection of Messages.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.Group} Sender Gets or sets the Group of the Conversation.
 * @property {Array<vDesk.Messenger.Groups.Message>} Messages Gets or sets the Messages of the Conversation.
 * @property {Number} UnreadMessages Gets or sets the amount of unread Messages of the Conversation.
 * @memberOf vDesk.Messenger.Groups.Chat
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Messenger
 */
vDesk.Messenger.Groups.Chat.Conversation = function Conversation(Group = new vDesk.Security.Group(), Messages = []) {
    Ensure.Parameter(Group, vDesk.Security.Group, "Group");
    Ensure.Parameter(Messages, Array, "Messages");

    Object.defineProperties(this, {
        Control:  {
            get: () => Control
        },
        Group:    {
            get: () => Group,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.Group, "Group");
                Group = Value;
            }
        },
        Messages: {
            get: () => Messages,
            set: Value => {
                Ensure.Property(Value, Array, "Messages");
                window.requestAnimationFrame(() => {
                    while(MessageList.hasChildNodes()){
                        MessageList.removeChild(MessageList.lastChild);
                    }
                    Messages = Value;
                    const Fragment = document.createDocumentFragment();
                    Value.forEach(Message => Fragment.appendChild(Message.Control));
                    MessageList.appendChild(Fragment);
                    this.Scroll();
                });
            }
        }
    });

    /**
     * Eventhandler that listens on the 'sent' event.
     * @listens vDesk.Messenger.Groups.Message.Editor#event:sent
     * @param {CustomEvent} Event
     */
    const OnSent = Event => {
        window.requestAnimationFrame(() => {
            while(MessageList.hasChildNodes()){
                MessageList.removeChild(MessageList.lastChild);
            }
            Messages.push(Event.detail.message);
            const Fragment = document.createDocumentFragment();
            Messages.forEach(Message => Fragment.appendChild(Message.Control));
            MessageList.appendChild(Fragment);
            this.Scroll();
        });
    };

    /**
     * Eventhandler that listens on the 'scroll' event.
     */
    const OnScroll = () => {
        if(MessageList.scrollTop === 0){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Messenger",
                        Command:    "GetGroupMessages",
                        Parameters: {
                            Group:  Group.ID,
                            Date:   Messages[0].Date,
                            Amount: 10
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        if(Response.Data.length < 10){
                            MessageList.removeEventListener("scroll", OnScroll, false);
                        }
                        window.requestAnimationFrame(() => {
                            const Height = MessageList.scrollHeight;
                            while(MessageList.hasChildNodes()){
                                MessageList.removeChild(MessageList.lastChild);
                            }
                            Messages.unshift(...Response.Data.map(Message => vDesk.Messenger.Groups.Message.FromDataView(Message)));
                            const Fragment = document.createDocumentFragment();
                            Messages.forEach(Message => Fragment.appendChild(Message.Control));
                            MessageList.appendChild(Fragment);
                            MessageList.scrollTop = MessageList.scrollHeight - Height;
                        });
                    }
                }
            );
        }
    };

    /**
     * Adds a set of Messages to the Chat.
     * @param {vDesk.Messenger.Groups.Message} NewMessages The Messages to add.
     */
    this.AddMessages = function(...NewMessages) {
        window.requestAnimationFrame(() => {
            while(MessageList.hasChildNodes()){
                MessageList.removeChild(MessageList.lastChild);
            }
            Messages.push(...NewMessages);
            const Fragment = document.createDocumentFragment();
            Messages.forEach(Message => Fragment.appendChild(Message.Control));
            MessageList.appendChild(Fragment);
        });
    };

    /**
     * Scrolls the Messages of the Conversation to the bottom.
     * @param {Boolean} [Top=false] Flag indicating whether to scroll to the top instead of the bottom.
     */
    this.Scroll = function(Top = false) {
        if(Top){
            MessageList.scrollTop = 0;
        }else{
            MessageList.scrollTop = MessageList.scrollHeight;
        }
    };

    /**
     * The underlying DOMNode.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Groups Conversation";

    /**
     * The Message list of the Conversation.
     * @type {HTMLUListElement}
     */
    const MessageList = document.createElement("ul");
    MessageList.className = "Messages";
    Messages.forEach(Message => MessageList.appendChild(Message.Control));
    MessageList.addEventListener("scroll", OnScroll, false);
    Control.appendChild(MessageList);

    /**
     * The Message Editor of the Conversation.
     * @type {vDesk.Messenger.Groups.Message.Editor}
     */
    const Editor = new vDesk.Messenger.Groups.Message.Editor(Group);
    Editor.Control.addEventListener("sent", OnSent, false);
    Control.appendChild(Editor.Control);
};

/**
 * Factory method that creates a Conversation from a specified sender and recipient.
 * @param {vDesk.Security.Group} Group The sender of the Conversation.
 * @return {vDesk.Messenger.Groups.Chat.Conversation} A Conversation filled with the provided data.
 */
vDesk.Messenger.Groups.Chat.Conversation.FromGroup = function(Group) {
    const Conversation = new vDesk.Messenger.Groups.Chat.Conversation(Group);
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Messenger",
                Command:    "GetGroupMessages",
                Parameters: {
                    Group:  Group.ID,
                    Date:   new Date(),
                    Amount: 10
                },
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Response => {
            if(Response.Status){
                Conversation.Messages = Response.Data.map(Message => vDesk.Messenger.Groups.Message.FromDataView(Message));
            }
        }
    );
    return Conversation;
};