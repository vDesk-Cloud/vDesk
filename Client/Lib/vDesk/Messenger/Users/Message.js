"use strict";
/**
 * Initializes a new instance of the Message class.
 * @class Represents a private User message.
 * @param {Number} [ID=null] Initializes the Message with the specified ID.
 * @param {vDesk.Security.User} [Sender=new vDesk.Security.User()]
 * @param {vDesk.Security.User} [Recipient=new vDesk.Security.User()] Initializes the Message with the specified recipient.
 * @param {Number} [Status=vDesk.Messenger.Users.Message.Sent] Initializes the Message with the specified transmission status.
 * @param {Date} [Date=new Date] Initializes the Message with the specified date.
 * @param {String} Text Initializes the Message with the specified text.
 * @property {HTMLLIElement} Control Gets the underlying DOM-Node.
 * @property {Number} ID Gets or sets the ID of the Message.
 * @property {vDesk.Security.User} Sender Gets or sets the sender of the Message.
 * @property {vDesk.Security.User} Recipient Gets or sets the recipient of the Message.
 * @property {Number} Status Gets or sets the transmission status of the Message.
 * @property {Date} Date Gets or sets the date of the Message.
 * @property {String} Text Gets or sets the text of the Message.
 * @memberOf vDesk.Messenger.Users
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Messenger.Users.Message = function Message(
    ID        = null,
    Sender    = new vDesk.Security.User(),
    Recipient = new vDesk.Security.User(),
    Status    = vDesk.Messenger.Users.Message.Sent,
    Date      = new window.Date(),
    Text      = ""
) {

    Ensure.Parameter(ID, Type.Number, "ID", true);
    Ensure.Parameter(Sender, vDesk.Security.User, "Sender");
    Ensure.Parameter(Recipient, vDesk.Security.User, "Recipient");
    Ensure.Parameter(Status, Type.Number, "Status");
    Ensure.Parameter(Date, window.Date, "Date");
    Ensure.Parameter(Text, Type.String, "Text");

    Object.defineProperties(this, {
        Control:   {
            get: () => Control
        },
        ID:        {
            get: () => ID,
            set: Value => {
                Ensure.Property(Value, Type.Number, "ID");
                ID = Value;
            }
        },
        Sender:    {
            get: () => Sender,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "Sender");
                Sender = Value;
                Control.classList.toggle("Sender", Value.ID === vDesk.User.ID);
                Control.classList.toggle("Recipient", Value.ID !== vDesk.User.ID);
            }
        },
        Recipient: {
            get: () => Recipient,
            set: Value => {
                Ensure.Property(Value, vDesk.Security.User, "Recipient");
                Recipient = Value;
                Control.classList.toggle("Recipient", Value.ID === vDesk.User.ID);
                Control.classList.toggle("Sender", Value.ID !== vDesk.User.ID);
            }
        },
        Status:    {
            get: () => Status,
            set: Value => {
                Ensure.Property(Value, Type.Number, "Status");
                Status = Value;
                switch(Value) {
                    case vDesk.Messenger.Users.Message.Sent:
                        StatusText.textContent = "✓";
                        StatusText.className = "Status Sent";
                        break;
                    case vDesk.Messenger.Users.Message.Received:
                        StatusText.textContent = "✓✓";
                        StatusText.className = "Status Received";
                        break;
                    case vDesk.Messenger.Users.Message.Read:
                        StatusText.textContent = "✓✓";
                        StatusText.className = "Status Read";
                        break;
                }
            }
        },
        Date:      {
            get: () => Date,
            set: Value => {
                Ensure.Property(Value, window.Date, "Date");
                Date = Value;
                DateIcon.title = Value.toLocaleDateString();
            }
        },
        Text:      {
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
    Control.className = `Users Message ${vDesk.User.ID === Sender.ID ? "Sender" : "Recipient"} Font Dark`;
    Control.innerText = Text;

    /**
     * The Status icon of the Message.
     * @type {HTMLSpanElement}
     */
    const StatusText = document.createElement("span");
    switch(Status) {
        case vDesk.Messenger.Users.Message.Sent:
            StatusText.textContent = "✓";
            StatusText.className = "Status Sent";
            break;
        case vDesk.Messenger.Users.Message.Received:
            StatusText.textContent = "✓✓";
            StatusText.className = "Status Received";
            break;
        case vDesk.Messenger.Users.Message.Read:
            StatusText.textContent = "✓✓";
            StatusText.className = "Status Read";
            break;
    }
    Control.appendChild(StatusText);

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
 * @return {vDesk.Messenger.Users.Message} A Message filled with the provided data.
 */
vDesk.Messenger.Users.Message.FromDataView = function(DataView) {
    Ensure.Parameter(DataView, Type.Object, "DataView");
    return new vDesk.Messenger.Users.Message(
        DataView.ID || null,
        DataView.Sender.ID !== undefined
        ? vDesk.Security.Users.find(User => User.ID === DataView.Sender.ID)
        : vDesk.Security.User.FromDataView(DataView.Sender),
        DataView.Recipient.ID !== undefined
        ? vDesk.Security.Users.find(User => User.ID === DataView.Recipient.ID)
        : vDesk.Security.User.FromDataView(DataView.Recipient),
        DataView.Status || vDesk.Messenger.Users.Message.Sent,
        DataView.Date || new Date(),
        DataView.Text || ""
    );
};
vDesk.Messenger.Users.Message.Sent = 0;
vDesk.Messenger.Users.Message.Received = 1;
vDesk.Messenger.Users.Message.Read = 2;