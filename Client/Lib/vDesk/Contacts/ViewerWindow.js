"use strict";
/**
 * Initializes a new instance of the ViewerWindow class.
 * @class Window for displaying the content of a Contact or Company.
 * @param {vDesk.Contacts.Contact|vDesk.Contacts.Company} Contact The Contact or Company to display the content of.
 * @memberOf vDesk.Contacts
 * @augments vDesk.Controls.Window
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Contacts.ViewerWindow = function (Contact) {
    
    this.Extends(vDesk.Controls.Window);
    
    if (Contact instanceof vDesk.Contacts.Contact) {
        this.Icon = vDesk.Visual.Icons.Security.User;
        this.Title = Contact.Surname + ", " + Contact.Forename;
        this.Content.appendChild((new vDesk.Contacts.Contact.Viewer(Contact)).Control);
    } else if (Contact instanceof vDesk.Contacts.Company) {
        this.Icon = vDesk.Visual.Icons.Contacts.Module;
        this.Title = Contact.Name;
        this.Content.appendChild((new vDesk.Contacts.Company.Viewer(Contact)).Control);
    }
};