"use strict";
/**
 * Interface for classes that represent an event within the calendar.
 * @interface
 * @memberOf vDesk.Controls.Calendar
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.IEvent = function IEvent() {};
/**
 * Gets the underlying DOM-Node.
 * @type HTMLElement
 */
vDesk.Controls.Calendar.IEvent.prototype.Control = null;
/**
 * Gets or sets the startdate of the IEvent.
 * @type Date
 */
vDesk.Controls.Calendar.IEvent.prototype.Start = null;
/**
 * Gets or sets the enddate of the IEvent.
 * @type Date
 */
vDesk.Controls.Calendar.IEvent.prototype.End = null;
/**
 * Gets the duration in hours of the IEvent.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.Duration = null;
/**
 * Gets or sets the title of the IEvent.
 * @type String
 */
vDesk.Controls.Calendar.IEvent.prototype.Title = null;
/**
 * Gets or sets the content of the IEvent.
 * @type String
 */
vDesk.Controls.Calendar.IEvent.prototype.Content = null;
/**
 * Gets or sets a value indicating whether the IEvent occurs over a whole day.
 * @type {Boolean}
 */
vDesk.Controls.Calendar.IEvent.prototype.FullTime = null;
/**
 * Gets or sets a value indicating whether the IEvent occurs more than once.
 * @type {Boolean}
 */
vDesk.Controls.Calendar.IEvent.prototype.Repeating = null;
/**
 * Gets or sets the interval in days the IEvent re-occurs.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.RepeatInterval = null;
/**
 * Gets or sets the amount the IEvent re-occurs.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.RepeatAmount = null;
/**
 * Gets or sets the width of the IEvent.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.Width = null;
/**
 * Gets or sets the height of the IEvent.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.Height = null;
/**
 * Gets or sets the top offset of the IEvent.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.Top = null;
/**
 * Gets or sets the left offset of the IEvent.
 * @type {Number}
 */
vDesk.Controls.Calendar.IEvent.prototype.Left = null;
/**
 * Gets or sets a value indicating if the startdate of the IEvent can be modified.
 * @type {Boolean}
 */
vDesk.Controls.Calendar.IEvent.prototype.ModifyStart = null;
/**
 * Gets or sets a value indicating if the enddate of the IEvent can be modified.
 * @type {Boolean}
 */
vDesk.Controls.Calendar.IEvent.prototype.ModifyEnd = null;
/**
 * Checks whether the date of a different IEvent intersects with the date of this instance.
 * @param {vDesk.Controls.Calendar.IEvent} Event The event to check.
 * @return {Boolean} True if the date of the IEvent intersects with the date of this instance; otherwise, false.
 */
vDesk.Controls.Calendar.IEvent.prototype.CollidesWith = function (Event) {
    throw "Not implemented!";
};