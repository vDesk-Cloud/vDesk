"use strict";
/**
 * Fired if a successfully login has been performed.
 * @event vDesk.LoginDialog#login
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'login' event.
 * @property {vDesk.LoginDialog} detail.sender The current instance of the LoginDialog.
 * @property {vDesk.Security.User} detail.user The logged in User.
 */
/**
 * Fired if an User has been logged out.
 * @event vDesk.LoginDialog#logout
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'logout' event.
 * @property {vDesk.LoginDialog} detail.sender The current instance of the LoginDialog.
 * @property {vDesk.Security.User} detail.user The logged out User.
 */
/**
 * Initializes a new instance of the LoginDialog class.
 * @class Represents a dialog for authenticating Users.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Name Gets or sets the username of the LoginDialog.
 * @property {String} Password Gets or sets the password of the LoginDialog.
 * @property {String} Server Gets or sets the Server of the LoginDialog.
 * @property {String} Status Gets or sets the status text of the LoginDialog.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Just call vDesk.Login or vDesk.LoginFromEmail from within this class.
 */
vDesk.LoginDialog = function LoginDialog() {

    Object.defineProperties(this, {
        Name:         {
            get: () => User.Value
        },
        Password:     {
            get: () => Password.Value
        },
        Server:       {
            get: () => Server.Value
        },
        KeepLoggedIn: {
            get: () => KeepLoggedIn.Value
        },
        Status:       {
            get: () => Status.textContent,
            set: Value => {
                Ensure.Property(Value, Type.String, "Status");
                Status.textContent = Value;
            }
        }
    });

    window.addEventListener(
        "ticketexpired",
        () => {
            sessionStorage.clear();
            localStorage.removeItem("KeepLoggedIn");
            new vDesk.Events.BubblingEvent("logout", {sender: this, user: vDesk.User}).Dispatch(GroupBox.Control);
            vDesk.User = {
                ID:          null,
                Name:        "",
                Locale:      "DE",
                Email:       null,
                Permissions: {}
            };
        }
    );

    /**
     * Eventhandler that listens on the 'keyup' event.
     * @param {KeyboardEvent} Event
     */
    const OnKeyUp = Event => {
        if(Event.key === "Enter") {
            this.Login();
        }
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        if(Event.detail.value && Server.Valid && User.Valid && Password.Valid) {
            localStorage.Name = User.Value;
            localStorage.Password = Password.Value;
        } else {
            localStorage.removeItem("Username");
            localStorage.removeItem("Password");
        }
        localStorage.KeepLoggedIn = Event.detail.value.toString();
    };


    /**
     * Displays the LoginDialog.
     */
    this.Show = function() {
        window.addEventListener("keyup", OnKeyUp, false);
        document.body.appendChild(Overlay);
    };

    /**
     * Performs a login with the current credentials of the LoginDialog.
     * @fires vDesk.LoginDialog#login
     */
    this.Login = function() {
        if(!vDesk.Connection.Connect(Server.Value)) {
            alert(`Cannot reach a server under the address: '${Server.Value}'`);
        }
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "Login",
                    Parameters: {
                        User:     User.Value,
                        Password: Password.Value
                    }
                }
            ),
            Response => {
                if(Response.Status) {
                    vDesk.User = vDesk.Security.User.FromDataView(Response.Data);
                    sessionStorage.Ticket = vDesk.User.Ticket;
                    new vDesk.Events.BubblingEvent("login", {sender: this, user: vDesk.User}).Dispatch(window);
                }
            }
        );
    };

    /**
     * Performs a login on a specified server with the specified ticket.
     * @fires vDesk.LoginDialog#login
     */
    this.ReLogin = function() {
        if(!vDesk.Connection.Connect(Server.Value)) {
            alert(`Cannot reach a server under the address: '${Server.Value}'`);
        }
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "ReLogin",
                    Parameters: {},
                    Ticket:     sessionStorage.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    vDesk.Security.User.Current = vDesk.User = vDesk.Security.User.FromDataView(Response.Data);
                    new vDesk.Events.BubblingEvent("login", {sender: this, user: vDesk.Security.User.Current}).Dispatch(window);
                }
            }
        );
    };

    /**
     * Performs a logout of the current logged in User.
     * @fires vDesk.LoginDialog#logout
     */
    this.Logout = function() {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "Logout",
                    Parameters: {User: vDesk.Security.User.Current.Name},
                    Ticket:     vDesk.Security.User.Current.Ticket
                }
            )
        );
        sessionStorage.clear();
        localStorage.removeItem("Username");
        localStorage.removeItem("Password");
        localStorage.removeItem("KeepLoggedIn");

        new vDesk.Events.BubblingEvent("logout", {sender: this, user: vDesk.Security.User.Current}).Dispatch(window);
        vDesk.User = {
            ID:          null,
            Name:        "",
            Locale:      "DE",
            Email:       null,
            Permissions: {}
        };
    };

    /**
     * Closes the LoginDialog
     */
    this.Remove = function() {
        window.removeEventListener("keyup", OnKeyUp, false);
        document.body.removeChild(Overlay);
    };

    /**
     * The overlay of the LoginDialog.
     * @type {HTMLDivElement}
     */
    const Overlay = document.createElement("div");
    Overlay.className = "LoginDialog Overlay";

    /**
     * The server address EditControl of the LoginDialog.
     * @type {vDesk.Controls.EditControl}
     */
    const Server = new vDesk.Controls.EditControl(
        "Server",
        null,
        Extension.Type.URL,
        window.String(window.location)
            .replace("html", "php")
            .replace("Client", "Server"),
        null,
        true
    );
    Server.Control.classList.add("Server");

    /**
     * The User name/email address EditControl of the LoginDialog.
     * @type {vDesk.Controls.EditControl}
     */
    const User = new vDesk.Controls.EditControl(
        "Name/email address",
        null,
        Type.String,
        localStorage.Name ?? null,
        null,
        true
    );
    User.Control.classList.add("Username");

    /**
     * The password EditControl of the LoginDialog.
     * @type {vDesk.Controls.EditControl}
     */
    const Password = new vDesk.Controls.EditControl(
        "Password",
        null,
        Extension.Type.Password,
        localStorage.Password ?? null,
        null,
        true
    );
    Password.Control.classList.add("Password");

    /**
     * The status span of the LoginDialog.
     * @type {HTMLSpanElement}
     */
    const Status = document.createElement("span");
    Status.className = "Status Font Dark";

    /**
     * The login button of the LoginDialog.
     * @type {HTMLButtonElement}
     */
    const Login = document.createElement("button");
    Login.textContent = "Login";
    Login.className = "Button Icon Login";
    Login.style.backgroundImage = `url("${vDesk.Visual.Icons.Logout}")`;
    Login.disabled = !Server.Valid && !User.Valid && !Password.Valid;
    Login.addEventListener("click", this.Login);

    /**
     * The keep logged in EditControl of the LoginDialog.
     * @type {vDesk.Controls.EditControl}
     */
    const KeepLoggedIn = new vDesk.Controls.EditControl(
        "KeepLoggedIn",
        "Determines whether to store the credentials in the localStorage of the current browser.",
        Type.Boolean,
        Boolean(localStorage.KeepLoggedIn)
    );
    KeepLoggedIn.Control.classList.add("KeepLoggedIn");
    KeepLoggedIn.Control.addEventListener("update", OnUpdate);

    /**
     * The GroupBox of the LoginDialog.
     * @type {vDesk.Controls.GroupBox}
     */
    const GroupBox = new vDesk.Controls.GroupBox(
        "vDesk",
        [
            Server.Control,
            User.Control,
            Password.Control,
            Status,
            Login,
            KeepLoggedIn.Control
        ]
    );
    GroupBox.Control.classList.add("LoginDialog");
    GroupBox.Control.addEventListener("update", () => Login.disabled = !Server.Valid || !User.Valid || !Password.Valid);

    /**
     * The header of the LoginDialog.
     * @type {HTMLSpanElement}
     */
    const Header = document.createElement("span");
    Header.className = "Header BorderLight Background Font Dark";
    Header.textContent = "v";

    const Char = document.createElement("span");
    Char.className = "Char";
    Char.textContent = "D";

    Header.appendChild(Char);
    Header.appendChild(document.createTextNode("esk"));

    GroupBox.Control.replaceChild(Header, GroupBox.Control.firstChild);

    Overlay.appendChild(GroupBox.Control);

    if(sessionStorage.Ticket && Server.Valid) {
        this.ReLogin();
    } else if(KeepLoggedIn.Value && Server.Valid && User.Valid && Password.Valid) {
        this.Login();
    }

};