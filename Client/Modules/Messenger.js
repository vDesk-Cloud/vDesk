"use strict";
/**
 * Initializes a new instance of the Messenger class.
 * @class Messenger Module
 * @extends vDesk.Controls.Window
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Messenger
 */
Modules.Messenger = function Messenger() {

    this.Extends(vDesk.Controls.Window, vDesk.Visual.Icons.Messenger.Message, vDesk.Locale.Messenger.Messages);

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Messenger.Users.Chats#event:select
     * @param {CustomEvent} Event
     */
    const OnSelectUserChats = Event => {
        if(UserConversation.hasChildNodes()){
            UserConversation.removeChild(UserConversation.lastChild);
        }
        if(Event.detail.chat.Unread > 0){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Messenger",
                        Command:    "GetUserMessages",
                        Parameters: {
                            Sender: Event.detail.chat.Sender.ID,
                            Date:   new Date(),
                            Amount: Event.detail.chat.Unread
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Event.detail.chat.Conversation.AddMessages(
                            ...Response.Data.map(Message => vDesk.Messenger.Users.Message.FromDataView(Message)));
                        Event.detail.chat.Unread = 0;
                        UsersTab.Title = `${vDesk.Locale.Security.Users} (${UserChats.Chats.reduce((Amount, Chat) => Amount + Chat.Unread, 0)})`;
                    }
                }
            );
        }
        UserConversation.appendChild(Event.detail.chat.Conversation.Control);
        Event.detail.chat.Conversation.Scroll();
    };

    /**
     * Eventhandler that listens on the global 'vDesk.Messenger.Users.Message.Sent' event.
     */
    const OnMessengerUsersMessageSent = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:  "Messenger",
                    Command: "GetUnreadUserMessages",
                    Ticket:  vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                Response.Data.forEach((Amount, Sender) => {
                    const Chat = UserChats.Chats.find(Chat => Chat.Sender.ID === Number(Sender));
                    if(Chat === UserChats.Selected){
                        vDesk.Connection.Send(
                            new vDesk.Modules.Command(
                                {
                                    Module:     "Messenger",
                                    Command:    "GetUserMessages",
                                    Parameters: {
                                        Sender: Chat.Sender.ID,
                                        Date:   new Date(),
                                        Amount: 1
                                    },
                                    Ticket:     vDesk.Security.User.Current.Ticket
                                }
                            ),
                            Response => {
                                if(Response.Status){
                                    Chat.Conversation.AddMessages(
                                        ...Response.Data.map(Message => vDesk.Messenger.Users.Message.FromDataView(Message))
                                    );
                                    Chat.Conversation.Scroll();
                                }
                            }
                        );
                    }else{
                        Chat.Unread = Amount;
                    }
                });

                UsersTab.Title = `${vDesk.Locale.Security.Users} (${UserChats.Chats.reduce((Amount, Chat) => Amount + Chat.Unread, 0)})`;

                vDesk.Events.Stream.addEventListener(
                    "vDesk.Messenger.Users.Message.Sent",
                    OnMessengerUsersMessageSent,
                    {once: true}
                );
            }
        );
    };

    vDesk.Events.Stream.addEventListener("vDesk.Messenger.Users.Message.Sent", OnMessengerUsersMessageSent, {once: true});

    /**
     * Eventhandler that listens on the global 'vDesk.Messenger.Users.Message.Received' event.
     * @param {MessageEvent} Event
     */
    const OnMessengerUsersMessageReceived = Event => {
        const Message = JSON.parse(Event.data);
        const Chat = UserChats.Chats.find(Chat => Chat.Sender.ID === Message.Recipient);
        if(Chat !== undefined){
            const ReceivedMessage = Chat.Conversation.Messages.find(ReceivedMessage => ReceivedMessage.ID === Message.ID);
            if(ReceivedMessage !== undefined && ReceivedMessage.Status < vDesk.Messenger.Users.Message.Received){
                ReceivedMessage.Status = vDesk.Messenger.Users.Message.Received;
            }
        }
    };

    vDesk.Events.Stream.addEventListener("vDesk.Messenger.Users.Message.Received", OnMessengerUsersMessageReceived);

    /**
     * Eventhandler that listens on the global 'vDesk.Messenger.Users.Message.Read' event.
     * @param {MessageEvent} Event
     */
    const OnMessengerUsersMessageRead = Event => {
        const Message = JSON.parse(Event.data);
        const Chat = UserChats.Chats.find(Chat => Chat.Sender.ID === Message.Recipient);
        if(Chat !== undefined){
            const ReadMessage = Chat.Conversation.Messages.find(ReadMessage => ReadMessage.ID === Message.ID);
            if(ReadMessage !== undefined){
                ReadMessage.Status = vDesk.Messenger.Users.Message.Read;
            }
        }
    };

    vDesk.Events.Stream.addEventListener("vDesk.Messenger.Users.Message.Read", OnMessengerUsersMessageRead);

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Messenger.Users.Chats#event:select
     * @param {CustomEvent} Event
     */
    const OnSelectGroupChats = Event => {
        if(GroupConversation.hasChildNodes()){
            GroupConversation.removeChild(GroupConversation.lastChild);
        }
        if(Event.detail.chat.Unread > 0){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Messenger",
                        Command:    "GetGroupMessages",
                        Parameters: {
                            Group:  Event.detail.chat.Group.ID,
                            Date:   new Date(),
                            Amount: Event.detail.chat.Unread
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        Event.detail.chat.Conversation.AddMessages(
                            ...Response.Data.map(Message => vDesk.Messenger.Groups.Message.FromDataView(Message)));
                        Event.detail.chat.Unread = 0;
                        GroupsTab.Title = `${vDesk.Locale.Security.Groups} (${GroupChats.Chats.reduce((Amount, Chat) => Amount + Chat.Unread, 0)})`;
                    }
                }
            );
        }
        GroupConversation.appendChild(Event.detail.chat.Conversation.Control);
        Event.detail.chat.Conversation.Scroll();
    };

    /**
     * Eventhandler that listens on the global 'vDesk.Messenger.Groups.Message.Sent' event.
     */
    const OnMessengerGroupsMessageSent = Event => {
        const Message = JSON.parse(Event.data);
        const Chat = GroupChats.Chats.find(Chat => Chat.Group.ID === Message.Group);
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Messenger",
                    Command:    "GetGroupMessages",
                    Parameters: {
                        Group:  Chat.Group.ID,
                        Date:   new Date(),
                        Amount: 1
                    },
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            ),
            Response => {
                if(Chat === GroupChats.Selected){
                    Chat.Conversation.AddMessages(
                        ...Response.Data.map(Message => vDesk.Messenger.Groups.Message.FromDataView(Message))
                    );
                    Chat.Conversation.Scroll();
                }else{
                    Chat.Unread += Response.Data.length;
                }

                GroupsTab.Title = `${vDesk.Locale.Security.Groups} (${GroupChats.Chats.reduce((Amount, Chat) => Amount + Chat.Unread, 0)})`;

                vDesk.Events.Stream.addEventListener(
                    "vDesk.Messenger.Groups.Message.Sent",
                    OnMessengerGroupsMessageSent,
                    {once: true}
                );
            }
        );
    };

    vDesk.Events.Stream.addEventListener("vDesk.Messenger.Groups.Message.Sent", OnMessengerGroupsMessageSent, {once: true});

    this.Content.classList.add("Messenger");

    /**
     * The User Conversations container of the Messenger.
     * @type {HTMLDivElement}
     */
    const Users = document.createElement("div");
    Users.className = "Users Conversations";

    /**
     * The private Chats of the Messenger.
     * @type {vDesk.Messenger.Users.Chats}
     */
    const UserChats = vDesk.Messenger.Users.Chats.FromUsers();
    UserChats.Control.addEventListener("select", OnSelectUserChats, false);
    Users.appendChild(UserChats.Control);

    /**
     * The Group Conversations container of the Messenger.
     * @type {HTMLDivElement}
     */
    const UserConversation = document.createElement("div");
    UserConversation.className = "Conversation";
    Users.appendChild(UserConversation);

    /**
     * The Company TabItem of the Contacts module.
     * @type vDesk.Controls.TabControl.TabItem
     */
    const UsersTab = new vDesk.Controls.TabControl.TabItem(vDesk.Locale.Security.Users, Users);

    /**
     * The Group Conversations container of the Messenger.
     * @type {HTMLDivElement}
     */
    const Groups = document.createElement("div");
    Groups.className = "Groups Conversations";

    /**
     * The Group Chats of the Messenger.
     * @type {vDesk.Messenger.Groups.Chats}
     */
    const GroupChats = vDesk.Messenger.Groups.Chats.FromGroups();
    GroupChats.Control.addEventListener("select", OnSelectGroupChats, false);
    Groups.appendChild(GroupChats.Control);

    /**
     * The Group Conversations container of the Messenger.
     * @type {HTMLDivElement}
     */
    const GroupConversation = document.createElement("div");
    GroupConversation.className = "Conversation";
    Groups.appendChild(GroupConversation);

    /**
     * The Company TabItem of the Contacts module.
     * @type vDesk.Controls.TabControl.TabItem
     */
    const GroupsTab = new vDesk.Controls.TabControl.TabItem(vDesk.Locale.Security.Groups, Groups);

    /**
     * The TabControl of the Contacts module.
     * @type vDesk.Controls.TabControl
     */
    const TabControl = new vDesk.Controls.TabControl([UsersTab, GroupsTab]);
    this.Content.appendChild(TabControl.Control);

};

Modules.Messenger.Implements(vDesk.Modules.IModule);