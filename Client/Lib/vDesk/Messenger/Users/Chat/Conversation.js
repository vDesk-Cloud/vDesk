"use strict";
/**
 * Initializes a new instance of the Conversation class.
 * @class Represents a private Conversation between 2 Users.
 * @param {vDesk.Security.User} [Sender=new vDesk.Security.User()] Initializes the Conversation with the specified sender.
 * @param {Array<vDesk.Messenger.Users.Message>} [Messages=[]] Initializes the Conversation with the specified collection of Messages.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.User} Sender Gets or sets the sender of the Conversation.
 * @property {Array<vDesk.Messenger.Users.Message>} Messages Gets or sets the Messages of the Conversation.
 * @property {Number} UnreadMessages Gets or sets the amount of unread Messages of the Conversation.
 * @memberOf vDesk.Messenger.Users.Chat
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Messenger
 */
vDesk.Messenger.Users.Chat.Conversation = function Conversation(Sender = new vDesk.Security.User(), Messages = []) {
    Ensure.Parameter(Sender, vDesk.Security.User, "Sender");
    Ensure.Parameter(Messages, Array, "Messages");

    Object.defineProperties(this, {
        Control:  {
            get: () => Control
        },
        Sender:   {
            get: () => Sender,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "Sender");
                Sender = Value;
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
     * @listens vDesk.Messenger.Users.Message.Editor#event:sent
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
                        Command:    "GetUserMessages",
                        Parameters: {
                            Sender: Sender.ID,
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
                            Messages.unshift(...Response.Data.map(Message => vDesk.Messenger.Users.Message.FromDataView(Message)));
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
     * @param {vDesk.Messenger.Users.Message} NewMessages The Messages to add.
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
    Control.className = "Users Conversation";

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
     * @type {vDesk.Messenger.Users.Message.Editor}
     */
    const Editor = new vDesk.Messenger.Users.Message.Editor(Sender);
    Editor.Control.addEventListener("sent", OnSent, false);
    Control.appendChild(Editor.Control);
};

/**
 * Factory method that creates a Conversation from a specified sender and recipient.
 * @param {vDesk.Security.User} Sender The sender of the Conversation.
 * @return {vDesk.Messenger.Users.Chat.Conversation} A Conversation filled with the provided data.
 */
vDesk.Messenger.Users.Chat.Conversation.FromSender = function(Sender) {
    const Conversation = new vDesk.Messenger.Users.Chat.Conversation(Sender);
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Messenger",
                Command:    "GetUserMessages",
                Parameters: {
                    Sender: Sender.ID,
                    Date:   new Date(),
                    Amount: 10
                },
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Response => {
            if(Response.Status){
                Conversation.Messages = Response.Data.map(Message => vDesk.Messenger.Users.Message.FromDataView(Message));
            }
        }
    );
    return Conversation;
};