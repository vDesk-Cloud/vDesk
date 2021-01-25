window.addEventListener("load", () => {
    const Contact = document.getElementById("Contact");
    Contact.innerText = "";
    Contact.appendChild(Form.Control);
});

//Behaviour driven spam protection.
let NameInteraction = 0, TopicInteraction = 0, EmailInteraction = 0, MessageInteraction = 0;

/**
 * The topic EditControl of the Contact form.
 * @type {vDesk.Controls.EditControl}
 */
const Name = new vDesk.Controls.EditControl(
    "What's your name?",
    null,
    Type.String,
    null,
    {Min: 3},
    true
);
Name.Control.addEventListener("input", () => NameInteraction++, true);
Name.Control.addEventListener("update", () => NameInteraction++, true);
Name.Control.addEventListener("focus", () => NameInteraction++, true);

/**
 * The topic EditControl of the Contact form.
 * @type {vDesk.Controls.EditControl}
 */
const Topic = new vDesk.Controls.EditControl(
    "Topic",
    null,
    Extension.Type.Suggest,
    "I have a question",
    [
        "I have a question",
        "I want to tell you something",
        "I found a bugg",
        "I have an idea",
        "while(true) {sleep();}"
    ]
);
Topic.Control.addEventListener("input", () => TopicInteraction++, true);
Topic.Control.addEventListener("update", () => TopicInteraction++, true);
Topic.Control.addEventListener("focus", () => TopicInteraction++, true);

/**
 * The contact email EditControl of the Contact form.
 * @type {vDesk.Controls.EditControl}
 */
const Email = new vDesk.Controls.EditControl(
    "How can i contact you?",
    "Please provide an E-Mail address!",
    Extension.Type.Email,
    null,
    null,
    true
);
Email.Control.addEventListener("input", () => EmailInteraction++, true);
Email.Control.addEventListener("update", () => EmailInteraction++, true);
Email.Control.addEventListener("focus", () => EmailInteraction++, true);

/**
 * The message EditControl of the Contact form.
 * @type {vDesk.Controls.EditControl}
 */
const Message = new vDesk.Controls.EditControl(
    "What do you want to tell me?",
    null,
    Extension.Type.Text
);
Message.Control.addEventListener("input", () => MessageInteraction++, true);
Message.Control.addEventListener("update", () => MessageInteraction++, true);
Message.Control.addEventListener("focus", () => MessageInteraction++, true);

/**
 * The send Button of the Contact form.
 * @type {HTMLButtonElement}
 */
const Button = document.createElement("button");
Button.textContent = "Send";
Button.addEventListener("click", () => {
    if(NameInteraction < 4 && EmailInteraction < 3 && MessageInteraction < 4) {
        if(confirm("Are you a spambot?")){
            return;
        }
    }

    //Submit form.
    if(Name.Valid && Topic.Valid && Email.Valid && Message.Valid) {
        const Values = new FormData();
        Values.append("Name", Name.Value);
        Values.append("Topic", Topic.Value);
        Values.append("Email", Email.Value);
        Values.append("Message", Message.Value);
        fetch(
            "./Contact/Send",
            {
                method: "POST",
                body:   Values
            }
        )
            .then(Response => Response.text())
            .then(Response => alert(Response))
            .catch(() => alert("Couldn't send message!"));
    }
});

/**
 * The GroupBox of he Contact form.
 * @type {vDesk.Controls.GroupBox}
 */
const Form = new vDesk.Controls.GroupBox(
    "Leave a message",
    [
        Name.Control,
        Topic.Control,
        Email.Control,
        Message.Control,
        Button
    ]
);
