"use strict";
/**
 * @class PinBoard Colorpicker
 * @extends vDesk.Controls.Window
 * @memberOf vDesk.PinBoard
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\PinBoard
 */
vDesk.PinBoard.ColorPicker = function ColorPicker(Note = null) {
    this.Extends(vDesk.Controls.Window);
    /**
     * The ColorPicker of the PinBoard module.
     * @type {vDesk.Media.Drawing.ColorPicker}
     */
    const ColorPicker = new vDesk.Media.Drawing.ColorPicker();
    ColorPicker.Control.addEventListener("update", Event => SelectedElement.Color = Event.detail.color.ToHexString());
};
