"use strict";
/**
 * Initializes a new instance of the ViewerWindow class.
 * @class Window for displaying the contens of an event.
 * @param {vDesk.Calendar.Event} Event The event to display the content of.
 * @memberOf vDesk.Calendar.Event.Viewer
 * @extends vDesk.Controls.Window
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Calendar.Event.Viewer.Window = function(Event) {
    Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");
    this.Extends(vDesk.Controls.Window);
    this.Icon = vDesk.Visual.Icons.Calendar.Module;
    this.Title = Event.Title;
    this.Content.appendChild((new vDesk.Calendar.Event.Viewer(Event)).Control);
};