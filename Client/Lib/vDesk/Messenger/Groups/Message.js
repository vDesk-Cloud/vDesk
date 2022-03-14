"use strict";
/**
 * Initializes a new instance of the Message class.
 * @class Represents a private User message.
 * @param {Number} [ID=null] Initializes the Message with the specified ID.
 * @param {vDesk.Security.User} [Sender=new vDesk.Security.User()]
 * @param {vDesk.Security.User} [Group=new vDesk.Security.User()] Initializes the Message with the specified recipient.
 * @param {Date} [Date=new Date] Initializes the Message with the specified date.
 * @param {String} Text Initializes the Message with the specified text.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {Number} ID Gets or sets the ID of the Message.
 * @property {vDesk.Security.User} Sender Gets or sets the sender of the Message.
 * @property {vDesk.Security.User} Group Gets or sets the recipient of the Message.
 * @property {Date} Date Gets or sets the date of the Message.
 * @property {String} Text Gets or sets the text of the Message.
 * @memberOf vDesk.Messenger.Groups
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Messenger
 */
vDesk.Messenger.Groups.Message = function Message(
    ID     = null,
    Sender = new vDesk.Security.User(),
    Group  = new vDesk.Security.User(),
    Date   = new window.Date(),
    Text   = ""
) {
    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Sender, vDesk.Security.User, "Sender");
    Ensure.Parameter(Group, vDesk.Security.Group, "Group");
    Ensure.Parameter(Date, window.Date, "Date");
    Ensure.Parameter(Text, Type.String, "Text");

    Object.defineProperties(this, {
        Control: {
            get: () => Control
        },
        ID:      {
            get: () => ID,
            set: Value => {
                Ensure.Property(Value, Type.Number, "ID");
                ID = Value;
            }
        },
        Sender:  {
            get: () => Sender,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "Sender");
                Sender = Value;
                User.textContent = Sender.Name;
                Control.classList.toggle("Sender", Value.ID === vDesk.Security.User.Current.ID);
                Control.classList.toggle("Group", Value.ID !== vDesk.Security.User.Current.ID);
            }
        },
        Group:   {
            get: () => Group,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "Group");
                Group = Value;
                Control.classList.toggle("Group", Value.ID === vDesk.Security.User.Current.ID);
                Control.classList.toggle("Sender", Value.ID !== vDesk.Security.User.Current.ID);
            }
        },
        Date:    {
            get: () => Date,
            set: Value => {
                Ensure.Property(Value, window.Date, "Date");
                Date = Value;
                DateIcon.title = Value.toLocaleDateString();
            }
        },
        Text:    {
            get: () => Control.innerText,
            set: Value => {
                Ensure.Property(Value, Type.String, "Text");
                Control.innerText = Value;
            }
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLLIElement}
     */
    const Control = document.createElement("li");
    Control.className = `Groups Message ${vDesk.Security.User.Current.ID === Sender.ID ? "Sender" : "Group"} Font Dark`;
    Control.innerText = Text;

    /**
     * The User name of the Message.
     * @type {HTMLSpanElement}
     */
    const User = document.createElement("span");
    User.textContent = Sender.Name;
    User.className = "User Font Dark";
    Control.appendChild(User);

    /**
     * The date icon of the Message.
     * @type {HTMLImageElement}
     */
    const DateIcon = document.createElement("img");
    DateIcon.className = "Date Icon";
    DateIcon.title = Date.toLocaleString();
    DateIcon.src = vDesk.Visual.Icons.Messenger.Time;
    Control.appendChild(DateIcon);
};

/**
 * Factory method that creates a Message from a JSON-encoded representation.
 * @param {Object} DataView The Data to use to create an instance of the Message.
 * @return {vDesk.Messenger.Groups.Message} A Message filled with the provided data.
 */
vDesk.Messenger.Groups.Message.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, Type.Object, "DataView");
    return new vDesk.Messenger.Groups.Message(
        DataView?.ID ?? null,
        vDesk.Security.Users.find(User => User.ID === DataView?.Sender?.ID) ?? vDesk.Security.User.FromDataView(DataView?.Sender ?? {}),
        vDesk.Security.Groups.find(Group => Group.ID === DataView?.Group?.ID) ?? vDesk.Security.Group.FromDataView(DataView?.Group ?? {}),
        DataView?.Date ?? new Date(),
        DataView?.Text ?? ""
    );
};