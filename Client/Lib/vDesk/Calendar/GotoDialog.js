"use strict";
/**
 * Initializes a new instance of the GotoDialog class.
 * @class Dialog for quickly navigating through the calendar to a specified date.
 * @memberOf vDesk.Calendar
 * @extends vDesk.Controls.Window
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Calendar
 */
vDesk.Calendar.GotoDialog = function GotoDialog() {
    this.Extends(vDesk.Controls.Window);

    /**
     * The DatePicker of the GotoDialog.
     * @type {vDesk.Controls.DatePicker}
     */
    const DatePicker = new vDesk.Controls.DatePicker();
    DatePicker.Control.style.height = "180px";

    /**
     * The submit button of the GotoDialog.
     * @type {HTMLButtonElement}
     */
    const SubmitButton = document.createElement("button");
    SubmitButton.textContent = "OK";
    SubmitButton.className = "Button BorderDark Font Dark";
    SubmitButton.style.position = "absolute";
    SubmitButton.style.left = "42%";
    SubmitButton.addEventListener(
        "click",
        () => new vDesk.Events.BubblingEvent("submit", {
            date: DatePicker.SelectedDate
        }).Dispatch(SubmitButton),
        false
    );

    this.MinimumHeight = 100;
    this.Width = 212;
    this.Height = 250;
    this.Resizable = false;
    this.Modal = true;

    this.Content.appendChild(DatePicker.Control);
    this.Content.appendChild(SubmitButton);
};

