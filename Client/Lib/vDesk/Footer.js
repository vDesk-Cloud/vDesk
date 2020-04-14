"use strict";
/**
 * Initializes a new instance of the Footer class.
 * @class Represents the footer of the client
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Content Gets or sets the content of the Footer.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Footer = (function Footer() {

    /**
     * The underlying DOM-Node.
     * @type {HTMLElement}
     */
    const Control = document.createElement("footer");
    Control.className = "Footer Foreground";

    /**
     * The TaskBar of the Footer.
     * @type {null|vDesk.TaskBar}
     */
    let TaskBar = null;

    return {
        Load() {
            if(TaskBar === null){
                TaskBar = new vDesk.TaskBar();
                Control.appendChild(TaskBar.Control);
            }
            document.body.appendChild(Control);
        },
        Clear() {
            TaskBar.Clear();
        },
        Unload() {
            this.Clear();
            document.body.removeChild(Control);
        }
    }
})();