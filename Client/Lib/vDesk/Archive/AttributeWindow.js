"use strict";
/**
 * Initializes a new instance of the AttributeWindow class.
 * @class Window for displaying the atrributes, AccessControlList and metainformations of an element.
 * @param {vDesk.Archive.Element} Element The element to display the attributes of.
 * @augments vDesk.Controls.Window
 * @memberOf vDesk.Archive
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Archive.AttributeWindow = function(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    this.Extends(vDesk.Controls.Window);

    /**
     * The TabControl of the AttributeWindow.
     * @type vDesk.Controls.TabControl
     */
    const TabControl = new vDesk.Controls.TabControl();
    TabControl.Control.classList.add("Attributes");

    //Lel..
    Object.values(vDesk.Archive.Attributes)
        .filter(IAttribute => IAttribute.Permission())
        .map(IAttribute => new IAttribute(Element))
        .forEach(IAttribute => TabControl.Create(IAttribute.Title, IAttribute.Control));

    this.Icon = Element.Thumbnail || vDesk.Visual.Icons.Archive[Element.Extension || "Folder"];
    this.Title = `${vDesk.Locale["Archive"]["AttributeWindowTitle"]} ${Element.Name + ((Element.Type === vDesk.Archive.Element.File) ? "." + Element.Extension : "")}`;
    this.Width = 750;
    this.Content.appendChild(TabControl.Control);
};