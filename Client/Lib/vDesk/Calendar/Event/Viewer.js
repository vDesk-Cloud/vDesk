"use strict";
/**
 * Initializes a new instance of the Viewer class.
 * @class Respresents a contentpresenter for displaying the data of an event.
 * @param {Event|vDesk.Calendar.Event} Event The event to view.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {Event|vDesk.Calendar.Event} Event Gets or sets the event of the Viewer.
 * @memberOf vDesk.Calendar.Event
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Calendar
 */
vDesk.Calendar.Event.Viewer = function Viewer(Event) {
    Ensure.Parameter(Event, vDesk.Calendar.Event, "Event");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Event:   {
            enumerable: true,
            get:        () => Event,
            set:        Value => {
                Ensure.Property(Event, vDesk.Calendar.Event, "Event");
                Event = Value;
                SetValues();
            }
        }
    });

    /**
     * Fills a number below 10 with an additional 0 digit and returns it as a string.
     * @param {Number} Value The value to fill with a zero digit.
     * @return {String} The 'zerofilled' string of the passed value.
     */
    const Zerofill = function(Value) {
        if(Value < 10){
            Value = "0" + Value;
        }
        return Value.toString();
    };

    //const Zerofill = Value => Value > 10 ? "0" + Value : Value;

    /**
     * Applies the data of an event to the viewer.
     */
    function SetValues() {
        Title.textContent = Event.Title;
        Start.textContent = Event.Start.toLocaleDateString() + " um " + Zerofill(Event.Start.getHours()) + ":" + Zerofill(
            Event.Start.getMinutes()) + " Uhr";
        /**@todod Translate.*/
        End.textContent = Event.End.toLocaleDateString() + " um " + Zerofill(Event.End.getHours()) + ":" + Zerofill(
            Event.End.getMinutes()) + " Uhr";
        /**@todod Translate.*/
        Owner.textContent = (vDesk.Security.Users.find(User => User.ID === Event.Owner.ID)).Name;
        Content.textContent = Event.Content;
    }

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "EventViewer BorderLight";

    /**
     * The title row of the Viewer.
     * @type {HTMLDivElement}
     */
    const TitleRow = document.createElement("div");
    TitleRow.className = "Row Title BorderLight";

    /**
     * The title label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const TitleLabel = document.createElement("span");
    TitleLabel.textContent = "Titel:";
    /**@todod Translate.*/
    TitleLabel.className = "Label Font Dark";

    /**
     * The title container of the Viewer.
     * @type {HTMLSpanElement}
     */
    const Title = document.createElement("span");
    Title.className = "Font Dark";

    TitleRow.appendChild(TitleLabel);
    TitleRow.appendChild(Title);

    /**
     * The start row of the Viewer.
     * @type {HTMLDivElement}
     */
    const StartRow = document.createElement("div");
    StartRow.className = "Row Start BorderLight";

    /**
     * The start label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const StartLabel = document.createElement("span");
    StartLabel.textContent = `${vDesk.Locale.Calendar.Start}:`;
    StartLabel.className = "Label Font Dark";

    /**
     * The start container of the Viewer.
     * @type {HTMLSpanElement}
     */
    const Start = document.createElement("span");
    Start.className = "Font Dark";

    StartRow.appendChild(StartLabel);
    StartRow.appendChild(Start);

    /**
     * The end row of the Viewer.
     * @type {HTMLDivElement}
     */
    const EndRow = document.createElement("div");
    EndRow.className = "Row End BorderLight";

    /**
     * The end label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const EndLabel = document.createElement("span");
    EndLabel.textContent = `${vDesk.Locale.Calendar.End}:`;
    EndLabel.className = "Label Font Dark";

    /**
     * The end container of the Viewer.
     * @type {HTMLSpanElement}
     */
    const End = document.createElement("span");
    End.className = "Font Dark";

    EndRow.appendChild(EndLabel);
    EndRow.appendChild(End);

    /**
     * The owner row of the Viewer.
     * @type {HTMLDivElement}
     */
    const OwnerRow = document.createElement("div");
    OwnerRow.className = "Row Owner BorderLight";

    /**
     * The owner label of the Viewer.
     * @type {HTMLSpanElement}
     */
    const OwnerLabel = document.createElement("span");
    OwnerLabel.textContent = `${vDesk.Locale.Security.Owner}:`;
    OwnerLabel.className = "Label Font Dark";

    /**
     * The owner container of the Viewer.
     * @type {HTMLSpanElement}
     */
    const Owner = document.createElement("span");
    Owner.className = "Font Dark";

    OwnerRow.appendChild(OwnerLabel);
    OwnerRow.appendChild(Owner);

    /**
     * The content row of the Viewer.
     * @type {HTMLDivElement}
     */
    const ContentRow = document.createElement("div");
    ContentRow.className = "Row Content";

    /**
     * The content container of the Viewer.
     * @type {HTMLParagraphElement}
     */
    const Content = document.createElement("p");
    Content.className = "Content Font Dark";

    ContentRow.appendChild(Content);

    Control.appendChild(TitleRow);
    Control.appendChild(StartRow);
    Control.appendChild(EndRow);
    Control.appendChild(OwnerRow);
    Control.appendChild(ContentRow);

    SetValues();
};