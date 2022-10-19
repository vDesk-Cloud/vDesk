"use strict";
/**
 * Initializes a new instance of the ViewerWindow class.
 * @class Window for displaying the file of an element.
 * @param {vDesk.Archive.Element} Element The element to display the content of.
 * @memberOf vDesk.Archive.Element.Editor
 * @extends vDesk.Controls.Window
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Archive
 */
vDesk.Archive.Element.Editor.Window = function ViewerWindow(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element", false);
    this.Extends(vDesk.Controls.Window);
    this.Icon = Element?.Thumbnail ?? vDesk.Visual.Icons.Archive[Element?.Extension ?? "Folder"];
    this.Title = Element.Name + "." + Element.Extension;
    this.Content.appendChild(new vDesk.Archive.Element.Editor(Element).Control);
};