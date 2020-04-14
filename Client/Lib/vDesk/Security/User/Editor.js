"use strict";
/**
 * Fired if the current edited User of the Editor has been changed.
 * @event vDesk.Security.User.Editor#change
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'change' event.
 * @property {vDesk.Security.User.Editor} detail.sender The current instance of the Editor.
 */
/**
 * Fired if a new User has been created.
 * @event vDesk.Security.User.Editor#create
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'create' event.
 * @property {vDesk.Security.User.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.MetaInformation.Mask} detail.user The newly created User.
 */
/**
 * Fired if the current edited User of the Editor has been updated.
 * @event vDesk.Security.User.Editor#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Security.User.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.MetaInformation.Mask} detail.user The updated User.
 */
/**
 * Fired if the current edited User of the Editor has been deleted.
 * @event vDesk.Security.User.Editor#delete
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'delete' event.
 * @property {vDesk.Security.User.Editor} detail.sender The current instance of the Editor.
 * @property {vDesk.MetaInformation.Mask} detail.user The deleted User.
 */
/**
 * Initializes a new instance of the Editor class.
 * @class Represents an editor for viewing or editing the data of an user.
 * @param {vDesk.Security.User} User Initializes the Editor with the specified User.
 * @param {Boolean} [Enabled=false] Flag indicating whether the Editor is enabled.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Security.User} User Gets or sets the current edited User of the Editor.
 * @property {Boolean} Enabled Gets or sets a value indicating whether the Editor is enabled.
 * @property {Boolean} Changed Gets a value indicating whether the data of the current user of the Editor has been modified.
 * @memberOf vDesk.Security.User
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.User.Editor = function Editor(User, Enabled = false) {
    Ensure.Parameter(User, vDesk.Security.User, "User");
    Ensure.Parameter(Enabled, Type.Boolean, "Enabled");

    /**
     * The initial state of the current edited User of the Editor.
     * @type {vDesk.Security.User}
     */
    let PreviousUser = vDesk.Security.User.FromDataView(User);

    /**
     * Flag indicating whether the current edited User of the Editor has been changed.
     * @type {Boolean}
     */
    let Changed = false;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        User:    {
            enumerable: true,
            get:        () => User,
            set:        Value => {
                Ensure.Property(Value, vDesk.Security.User, "User");
                User = Value;
                PreviousUser = vDesk.Security.User.FromDataView(Value);
                Name.Value = Value.Name;
                Name.Enabled = Enabled && Value.ID !== vDesk.Security.User.System;
                Password.Value = null;
                Password.Label = Value.ID !== null
                                 ? vDesk.Locale["Security"]["ResetPassword"]
                                 : vDesk.Locale["Security"]["Password"];
                Email.Value = Value.Email;
                Locale.Value = Value.Locale;
                Active.Value = Value.Active;
                FailedLogins.textContent = `${vDesk.Locale["Security"]["FailedLogins"]}: ${Value.FailedLoginCount || 0}`;
                Changed = false;
            }
        },
        Changed: {
            enumerable: true,
            get:        () => Changed
        },
        Enabled: {
            enumerable: true,
            get:        () => Enabled,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Enabled");
                Enabled = Value;
                Name.Enabled = Value && User.ID !== vDesk.Security.User.System;
                Password.Enabled = Value;
                Email.Enabled = Value;
                Locale.Enabled = Value;
                Active.Enabled = Value;
                Reset.disabled = !Value;
            }
        }
    });

    /**
     * Eventhandler that listens on the 'update' event.
     * @fires vDesk.Security.User.Editor#change
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        Changed = true;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
        //Instead of spinning around just flip the valid flag. lel.
        Name.Valid = !Name.Valid;
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * @fires vDesk.Security.User.Editor#change
     */
    const OnClickReset = () => {
        User.FailedLoginCount = 0;
        Changed = true;
        FailedLogins.textContent = `${vDesk.Locale["Security"]["FailedLogins"]}: ${User.FailedLoginCount}`;
        new vDesk.Events.BubblingEvent("change", {sender: this}).Dispatch(Control);
    };

    /**
     * Saves possible made changes.
     * @fires vDesk.Security.User.Editor#create
     * @fires vDesk.Security.User.Editor#update
     */
    this.Save = function() {
        if(User.ID === null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "CreateUser",
                        Parameters: {
                            ID:       User.ID,
                            Name:     Name.Value,
                            Password: Password.Value,
                            Email:    Email.Value,
                            Locale:   Locale.Value,
                            Active:   Active.Value
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        Changed = false;
                        this.User = vDesk.Security.User.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("create", {
                            sender: this,
                            user:   User
                        }).Dispatch(Control);
                    }
                }
            );
        } else {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "UpdateUser",
                        Parameters: {
                            ID:               User.ID,
                            Name:             Name.Value,
                            Locale:           Locale.Value,
                            Password:         Password.Value,
                            Active:           Active.Value,
                            Email:            Email.Value,
                            FailedLoginCount: User.FailedLoginCount
                        },
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        Control.removeEventListener("update", OnUpdate, false);
                        Changed = false;
                        this.User = vDesk.Security.User.FromDataView(Response.Data);
                        new vDesk.Events.BubblingEvent("update", {
                            sender: this,
                            user:   User
                        }).Dispatch(Control);
                        Control.addEventListener("update", OnUpdate, false);
                    }
                }
            );
        }
    };

    /**
     * Deletes the currently edited user from the system.
     * @fires vDesk.Security.User.Editor#delete
     */
    this.Delete = function() {
        if(User.ID !== null) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Security",
                        Command:    "DeleteUser",
                        Parameters: {ID: User.ID},
                        Ticket:     vDesk.User.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        new vDesk.Events.BubblingEvent("delete", {
                            sender: this,
                            user:   User
                        }).Dispatch(Control);
                    }
                }
            );
        }
    };

    /**
     * Resets the current edited user to its original state.
     */
    this.Reset = () => this.User = PreviousUser;

    /**
     * The name EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Name = new vDesk.Controls.EditControl(
        vDesk.Locale["vDesk"]["Name"],
        null,
        Type.String,
        User.Name,
        {Expression: `^${vDesk.Security.Users.map(User => `(?!.*${User.Name})`).join("")}.*$`},
        true,
        Enabled && User.ID !== vDesk.Security.User.System
    );

    /**
     * The password EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Password = new vDesk.Controls.EditControl(
        User.ID !== null
        ? vDesk.Locale["Security"]["ResetPassword"]
        : vDesk.Locale["Security"]["Password"],
        null,
        Extension.Type.Password,
        null,
        null,
        true,
        Enabled
    );

    /**
     * The email EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Email = new vDesk.Controls.EditControl(
        vDesk.Locale["Security"]["Email"],
        null,
        Extension.Type.Email,
        User.Email,
        null,
        true,
        Enabled
    );

    /**
     * The locale EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Locale = new vDesk.Controls.EditControl(
        vDesk.Locale["vDesk"]["Language"],
        null,
        Extension.Type.Enum,
        User.Locale,
        vDesk.Locale.Locales,
        true,
        Enabled
    );

    /**
     * The status EditControl of the Editor.
     * @type {vDesk.Controls.EditControl}
     */
    const Active = new vDesk.Controls.EditControl(
        vDesk.Locale["vDesk"]["Active"],
        null,
        Type.Boolean,
        User.Active,
        null,
        true,
        Enabled
    );

    /**
     * The failed logins of the Editor.
     * @type {HTMLDivElement}
     */
    const FailedLogins = document.createElement("div");
    FailedLogins.className = "FailedLogins Font Dark";
    FailedLogins.textContent = `${vDesk.Locale["Security"]["FailedLogins"]}: ${User.FailedLoginCount || 0}`;

    /**
     * The failed logins reset button of the Editor.
     * @type {HTMLButtonElement}
     */
    const Reset = document.createElement("button");
    Reset.className = "Button Icon Reset";
    Reset.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    Reset.disabled = !Enabled;
    Reset.addEventListener("click", OnClickReset, false);

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "UserEditor";
    Control.appendChild(Name.Control);
    Control.appendChild(Password.Control);
    Control.appendChild(Email.Control);
    Control.appendChild(Locale.Control);
    Control.appendChild(Active.Control);
    Control.appendChild(FailedLogins);
    Control.appendChild(Reset);
    Control.addEventListener("update", OnUpdate, false);

};