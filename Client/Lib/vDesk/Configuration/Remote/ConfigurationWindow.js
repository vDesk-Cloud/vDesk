"use strict";
/**
 * Initializes a new instance of the ConfigurationWindow class.
 * @class Represents a dialog containing controls for managing system settings.
 * @extends vDesk.Controls.Window
 * @memberOf vDesk.Configuration.Remote
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Configuration.Remote.ConfigurationWindow = function ConfigurationWindow() {

    this.Extends(vDesk.Controls.Window);

    /**
     * The TabControl of the ConfigurationWindow.
     * @type {vDesk.Controls.TabControl}
     */
    const TabControl = new vDesk.Controls.TabControl();

    //Initialize plugins.
    for(const Plugin in vDesk.Configuration.Remote.Plugins) {
        const Instance = new vDesk.Configuration.Remote.Plugins[Plugin];
        TabControl.Create(Instance.Title, Instance.Control);
    }

    this.Title = vDesk.Locale.Configuration.SystemSettings;
    this.Modal = true;
    this.Width = 900;
    this.Content.appendChild(TabControl.Control);
    this.Icon = vDesk.Visual.Icons.Configuration.Administration;
};
