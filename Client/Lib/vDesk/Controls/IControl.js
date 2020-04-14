"use strict";
/**
 * Initializes a new instance of the IControl class.
 * @interface
 * @memberOf vDesk.Controls
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.IControl = function IControl() {

};
vDesk.Controls.IControl.prototype = {

    /**
     * @type {HTMLElement}
     */
    Control: Interface.FieldNotImplemented,

    /**
     * Adds an eventlistener to the IControl.
     * @param Type
     * @param Callback
     * @param UseCapture
     */
    addEventListener: function(Type, Callback, UseCapture = false) {
        this.Control.addEventListener(Type, Callback, UseCapture);
    },

    /**
     * Removes an eventlistener from the IControl.
     * @param Type
     * @param Callback
     * @param UseCapture
     */
    removeEventListener(Type, Callback, UseCapture = false) {
        this.Control.removeEventListener(Type, Callback, UseCapture);
    },
};