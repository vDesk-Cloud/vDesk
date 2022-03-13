"use strict";
/**
 * Initializes a new instance of the Settings class.
 * @class Represents plugin for modifying server side configuration values.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the settingseditor-plugin.
 * @memberOf vDesk.Configuration.Remote.Plugins
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Configuration
 */
vDesk.Configuration.Remote.Plugins.Settings = function Settings() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => SettingsGroupBox.Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Configuration.Module
        }
    });

    /**
     * The settings GroupBox of the Settings plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const SettingsGroupBox = new vDesk.Controls.GroupBox(vDesk.Locale.Configuration.Settings);
    SettingsGroupBox.Control.classList.add("Settings");

    //Fetch settings from the server.
    vDesk.Connection.Send(
        new vDesk.Modules.Command(
            {
                Module:     "Configuration",
                Command:    "GetSettings",
                Parameters: {All: true},
                Ticket:     vDesk.Security.User.Current.Ticket
            }
        ),
        Response => {
            if(Response.Status){
                const Fragment = document.createDocumentFragment();

                //Loop through domains
                for(const Domain in Response.Data){

                    //Create a GroupBox for each domain.
                    const DomainGroupBox = new vDesk.Controls.GroupBox(Domain);
                    DomainGroupBox.Control.addEventListener(
                        "save",
                        Event => {
                            vDesk.Connection.Send(
                                new vDesk.Modules.Command(
                                    {
                                        Module:     "Configuration",
                                        Command:    "UpdateSetting",
                                        Parameters: {
                                            Domain: Event.detail.sender.Domain,
                                            Tag:    Event.detail.sender.Tag,
                                            Value:  Event.detail.sender.Value
                                        },
                                        Ticket:     vDesk.Security.User.Current.Ticket
                                    }
                                ),
                                Response => {
                                    if(!Response.Status){
                                        alert(Response.Data);
                                    }
                                }
                            );
                        },
                        false
                    );

                    //Loop through settings of each domain.
                    Response.Data[Domain].forEach(Setting => {

                        //Create an EditControl for each setting.
                        const SettingControl = new vDesk.Configuration.Setting(
                            Domain,
                            Setting.Tag,
                            vDesk.Locale.Settings[`${Domain}:${Setting.Tag}`],
                            Setting.Type,
                            Setting.Value,
                            Setting?.Validator ?? {},
                            false,
                            vDesk.Security.User.Current.Permissions.UpdateSettings
                        );

                        DomainGroupBox.Add(SettingControl.Control);
                    });

                    Fragment.appendChild(DomainGroupBox.Control);
                }

                SettingsGroupBox.Add(Fragment);

            }else{
                alert(Response.Data);
            }
        }
    );
};