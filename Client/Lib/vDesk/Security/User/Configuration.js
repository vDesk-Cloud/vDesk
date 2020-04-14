"use strict";
/**
 * Initializes a new instance of the Configuration class.
 * @class Plugin for editing the attributes of an Users account.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the display-name of the account-plugin.
 * @memberOf vDesk.Configuration.Local.Plugins
 * @implements vDesk.Configuration.IPlugin
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Security.User.Configuration = function Configuration() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale["Security"]["UserConfiguration"]
        }
    });


    /**
     * Eventhandler that listens on the 'save' event.
     * @listens vDesk.Configuration.Setting#save
     */
    const OnSaveEmail = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "UpdateEmail",
                    Parameters: {Email: Email.Value},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Email.Enabled = false;
                }
            }
        );
    };

    /**
     * Eventhandler that listens on the 'save' event.
     * @listens vDesk.Configuration.Setting#save
     */
    const OnSaveLocale = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "UpdateLocale",
                    Parameters: {Locale: Locale.Value},
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    Locale.Enabled = false;
                    alert("Successfully changed locale. You must restart the client until the change takes effect.")
                }
            }
        );
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     */
    const OnUpdate = () => ResetPassword.disabled = !(OldPassword.Valid && NewPassword.Valid);

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClick = () => {
        vDesk.Connection.Send(
            new vDesk.Modules.Command(
                {
                    Module:     "Security",
                    Command:    "ResetPassword",
                    Parameters: {
                        Old: OldPassword.Value,
                        New: NewPassword.Value,
                    },
                    Ticket:     vDesk.User.Ticket
                }
            ),
            Response => {
                if(Response.Status) {
                    ResetPassword.disabled = true;
                    alert("Successfully reset password!")
                }
            }
        );
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "UserConfiguration";

    /**
     * The email Setting of the Configuration.
     * @type {vDesk.Configuration.Setting}
     */
    const Email = new vDesk.Configuration.Setting(
        "",
        vDesk.Locale["Security"]["Email"],
        null,
        Extension.Type.Email,
        vDesk.User.Email,
        null,
        false,
        true
    );
    Email.Control.addEventListener("save", OnSaveEmail);

    /**
     * The locale Setting of the Configuration.
     * @type {vDesk.Configuration.Setting}
     */
    const Locale = new vDesk.Configuration.Setting(
        "",
        vDesk.Locale["vDesk"]["Language"],
        null,
        Extension.Type.Enum,
        vDesk.User.Locale,
        vDesk.Locale.Locales,
        false,
        true
    );
    Locale.Control.addEventListener("save", OnSaveLocale);

    /**
     * The settings GroupBox of the Account plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const SettingsGroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale["Configuration"]["Settings"],
        [
            Email.Control,
            Locale.Control
        ]
    );
    Control.appendChild(SettingsGroupBox.Control);

    /**
     * The old password EditControl of the Account plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const OldPassword = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Security"]["OldPassword"]}:`,
        null,
        Extension.Type.Password,
        null,
        null,
        true
    );

    /**
     * The old password EditControl of the Account plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const NewPassword = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Security"]["NewPassword"]}:`,
        null,
        Extension.Type.Password,
        null,
        null,
        true
    );

    /**
     * The reset password button of the Account plugin.
     * @type {HTMLButtonElement}
     */
    const ResetPassword = document.createElement("button");
    ResetPassword.className = "Button Icon BorderDark Font Dark";
    ResetPassword.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    ResetPassword.textContent = vDesk.Locale["Security"]["ResetPassword"];
    ResetPassword.disabled = true;
    ResetPassword.addEventListener("click", OnClick);

    /**
     * The reset password GroupBox of the Account plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const ResetPasswordGroupBox = new vDesk.Controls.GroupBox(
        vDesk.Locale["Security"]["Password"],
        [
            OldPassword.Control,
            NewPassword.Control,
            ResetPassword
        ]
    );
    ResetPasswordGroupBox.Control.addEventListener("update", OnUpdate);
    Control.appendChild(ResetPasswordGroupBox.Control);

};

vDesk.Security.User.Configuration.Implements(vDesk.Configuration.IPlugin);
vDesk.Configuration.Local.Plugins.UserConfiguration = vDesk.Security.User.Configuration;