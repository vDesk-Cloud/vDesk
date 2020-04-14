"use strict";
/**
 * Initializes a new instance of the ResizableBox class.
 * @class Represents a resizable control.
 * @param {Number} [Height = 200] The initial height of the control.
 * @param {Number} [Width = 200] The initial width of the control.
 * @param {HTMLElement} [Content] The content of the control.
 * @property {HTMLElement} Control Gets the underlying dom node.
 * @property {HTMLElement} Content Gets or sets the content of the control.
 * @property {Number} Height Gets or sets the height of the control.
 * @property {Number} Width Gets or sets the width of the control.
 * @property {Boolean} Resizable Gets or sets a value indiciating whether the control is resizable.
 * @memberOf vDesk.Controls
 */
vDesk.Controls.ResizableBox = function (Height, Width, Content) {

    /**
     * The instance itself.
     * @type vDesk.Controls.ResizableBox
     */
    let _oInstance = null;
    /**
     * The underlying DOM-Node.
     * @type HTMLElement
     */
    let _oControl = null;
    /**
     * The top border of the box..
     * @type HTMLElement
     */
    let _oBorderTop = null;
    /**
     * The left border of the box..
     * @type HTMLElement
     */
    let _oBorderLeft = null;
    /**
     * The right border of the box..
     * @type HTMLElement
     */
    let _oBorderRight = null;
    /**
     * The bottom border of the box..
     * @type HTMLElement
     */
    let _oBorderBottom = null;
    /**
     * The top left corner of the box.0
     * @type HTMLElement
     */
    let _oCornerTopLeft = null;
    /**
     * The top right corner of the box.0
     * @type HTMLElement
     */
    let _oCornerTopRight = null;
    /**
     * The bottom left corner of the box.0
     * @type HTMLElement
     */
    let _oCornerBottomLeft = null;
    /**
     * The bottom right corner of the box.0
     * @type HTMLElement
     */
    let _oCornerBottomRight = null;
    /**
     * The contentcontainer of the box.
     * @type HTMLElement
     */
    let _oContent = null;
    /**
     * The height of the box.
     * @type {Number}
     */
    let _iHeight = null;
    /**
     * The width of the box.
     * @type {Number}
     */
    let _iWidth = null;
    /**
     * The initial vertical position of the pointer after a mousedown event has occurred.
     * @type {Number}
     */
    let _iVerticalPosition = null;
    /**
     * The initial horizontal position of the pointer after a mousedown event has occurred.
     * @type {Number}
     */
    let _iHorizontalPosition = null;
    /**
     * Flag indicating whether the box is resizable.
     * @type {Boolean}
     */
    let _bResizable = null;
    /**
     * Enables/disables the resizability of the box.
     * @type Function
     */
    let _fnToggleResize = null;
    /**
     * Cleans up eventhandlers.
     * @type Function
     */
    let _fnRemove = null;
    /**
     * Eventhandler that listens on the mouseup event.
     * @type Function
     */
    let _fnOnMouseUp = null;
    /**
     * Eventhandler that listens on the mousedown event on the top border.
     * @type Function
     */
    let _fnOnMouseDownBorderTop = null;
    /**
     * Eventhandler that listens on the mousedown event on the left border.
     * @type Function
     */
    let _fnOnMouseDownBorderLeft = null;
    /**
     * Eventhandler that listens on the mousedown event on the right border.
     * @type Function
     */
    let _fnOnMouseDownBorderRight = null;
    /**
     * Eventhandler that listens on the mousedown event on the bottom border.
     * @type Function
     */
    let _fnOnMouseDownBorderBottom = null;
    /**
     * Eventhandler that listens on the mousedown event on the top left corner.
     * @type Function
     */
    let _fnOnMouseDownCornerTopLeft = null;
    /**
     * Eventhandler that listens on the mousedown event on the top right corner.
     * @type Function
     */
    let _fnOnMouseDownCornerTopRight = null;
    /**
     * Eventhandler that listens on the mousedown event on the bottom left.
     * @type Function
     */
    let _fnOnMouseDownCornerBottomLeft = null;
    /**
     * Eventhandler that listens on the mousedown event on the bottom right.
     * @type Function
     */
    let _fnOnMouseDownCornerBottomRight = null;
    /**
     * Eventhandler that listens on the mousemove event on the top border.
     * @type Function
     */
    let _fnOnMouseMoveBorderTop = null;
    /**
     * Eventhandler that listens on the mousemove event on the left border.
     * @type Function
     */
    let _fnOnMouseMoveBorderLeft = null;
    /**
     * Eventhandler that listens on the mousemove event on the right border.
     * @type Function
     */
    let _fnOnMouseMoveBorderRight = null;
    /**
     * Eventhandler that listens on the mousemove event on the bottom border.
     * @type Function
     */
    let _fnOnMouseMoveBorderBottom = null;
    /**
     * Eventhandler that listens on the mousemove event on the top left corner.
     * @type Function
     */
    let _fnOnMouseMoveCornerTopLeft = null;
    /**
     * Eventhandler that listens on the mousemove event on the top right corner.
     * @type Function
     */
    let _fnOnMouseMoveCornerTopRight = null;
    /**
     * Eventhandler that listens on the mousemove event on the bottom left.
     * @type Function
     */
    let _fnOnMouseMoveCornerBottomLeft = null;
    /**
     * Eventhandler that listens on the mousemove event on the bottom right.
     * @type Function
     */
    let _fnOnMouseMoveCornerBottomRight = null;

    Object.defineProperty(this, "Control", {
        get: function () {
            return _oControl;
        }
    });

    Object.defineProperty(this, "Content", {
        get: function () {
            return _oContent;
        },
        set: function (value) {
            if (value instanceof HTMLElement || value instanceof DocumentFragment) {
                if (_oContent.hasChildNodes()) {
                    _oContent.removeChild(_oContent.lastChild);
                }
                _oContent.appendChild(value);
                console.log("Resbox content set..");
            }
        }
    });

    Object.defineProperty(this, "Height", {
        get: function () {
            return _iHeight;
        },
        set: function (value) {
            _iHeight = (isFinite(value) && value > 100) ? value : _iHeight;
            _oControl.style.height = _iHeight + "px";
        }
    });

    Object.defineProperty(this, "Width", {
        get: function () {
            return _iWidth;
        },
        set: function (value) {
            _iWidth = (isFinite(value) && value > 100) ? value : _iWidth;
            _oControl.style.width = _iWidth + "px";
        }
    });

    Object.defineProperty(this, "Resizable", {
        get: function () {
            return _bResizable;
        },
        set: function (value) {
            _bResizable = value;
            _fnToggleResize();
        }
    });

    //Construct
    _iHeight = (isFinite(Height) && Height > 100) ? Height : 200;
    _iWidth = (isFinite(Width) && Width > 100) ? Width : 200;

    //Setup control
    _oControl = document.createElement("div");
    _oControl.style.cssText = "position: absolute; height: " + _iHeight + "px; width: " + _iWidth + "px;";

    _oContent = document.createElement("div");
    _oContent.style.cssText = "position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px; overflow: auto;";
    if (typeof Content !== "undefined") {
        _oContent.appendChild(Content);
    }

    /**
     * Enables/disables the resizability of the box.
     */
    _fnToggleResize = function () {
        if (_bResizable) {
            _oCornerTopLeft.addEventListener("mousedown", _fnOnMouseDownCornerTopLeft, true);
            _oBorderTop.addEventListener("mousedown", _fnOnMouseDownBorderTop, true);
            _oCornerTopRight.addEventListener("mousedown", _fnOnMouseDownCornerTopRight, true);
            _oBorderLeft.addEventListener("mousedown", _fnOnMouseDownBorderLeft, true);
            _oBorderRight.addEventListener("mousedown", _fnOnMouseDownBorderRight, true);
            _oCornerBottomLeft.addEventListener("mousedown", _fnOnMouseDownCornerBottomLeft, true);
            _oBorderBottom.addEventListener("mousedown", _fnOnMouseDownBorderBottom, true);
            _oCornerBottomRight.addEventListener("mousedown", _fnOnMouseDownCornerBottomRight, true);
        } else {
            _oCornerTopLeft.removeEventListener("mousedown", _fnOnMouseDownCornerTopLeft, true);
            _oBorderTop.removeEventListener("mousedown", _fnOnMouseDownBorderTop, true);
            _oCornerTopRight.removeEventListener("mousedown", _fnOnMouseDownCornerTopRight, true);
            _oBorderLeft.removeEventListener("mousedown", _fnOnMouseDownBorderLeft, true);
            _oBorderRight.removeEventListener("mousedown", _fnOnMouseDownBorderRight, true);
            _oCornerBottomLeft.removeEventListener("mousedown", _fnOnMouseDownCornerBottomLeft, true);
            _oBorderBottom.removeEventListener("mousedown", _fnOnMouseDownBorderBottom, true);
            _oCornerBottomRight.removeEventListener("mousedown", _fnOnMouseDownCornerBottomRight, true);
        }
    };

    _fnRemove = function () {
        window.removeEventListener("mouseup", _fnOnMouseUp, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveCornerTopLeft, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveBorderTop, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveCornerTopRight, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveBorderLeft, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveBorderRight, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveCornerBottomLeft, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveBorderBottom, true);
        window.removeEventListener("mousemove", _fnOnMouseMoveCornerBottomRight, true);
    };

    _fnOnMouseUp = function () {
        _iHeight = parseInt(_oControl.style.height);
        _iWidth = parseInt(_oControl.style.width);
        _fnRemove();
        new vDesk.Events.BubblingEvent("resized", {"sender": _oInstance}).Dispatch(_oControl);
    };

    //Setup top left corner .
    _fnOnMouseMoveCornerTopLeft = function (e) {
        let iHeight = (_iHeight - e.clientY + _iVerticalPosition);
        let iWidth = (_iWidth - e.clientX + _iHorizontalPosition);

        _oControl.style.cssText += "; height: " + iHeight + "px; ; width: " + iWidth + "px;";

        if (iWidth > 100) {
            _oControl.style.cssText += " left: " + e.clientX + "px;";
        }

        if (iHeight > 100) {
            _oControl.style.cssText += " top: " + (e.clientY - 100) + "px;";
        }
    };
    _fnOnMouseDownCornerTopLeft = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveCornerTopLeft, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oCornerTopLeft = document.createElement("div");
    _oCornerTopLeft.className = "Corner Top Left";
    _oCornerTopLeft.style.cssText = "top: 0px; left: 0px; cursor:nw-resize; width: 5px; height: 5px; position: absolute; z-index: 1005;";
    _oCornerTopLeft.addEventListener("mousedown", _fnOnMouseDownCornerTopLeft, false);

    //Setup top border.
    _fnOnMouseMoveBorderTop = function (e) {
        let iHeight = (_iHeight - e.clientY + _iVerticalPosition);

        _oControl.style.cssText += "; height: " + iHeight + "px;";

        if (iHeight > 100) {
            _oControl.style.cssText += " top: " + (e.clientY - 100) + "px;";
        }
    };
    _fnOnMouseDownBorderTop = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveBorderTop, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oBorderTop = document.createElement("div");
    _oBorderTop.className = "Border Top";
    _oBorderTop.style.cssText = "float: none; top: 0px; cursor: ns-resize; width:100%; height:5px; position: absolute; z-index: 1003;";
    _oBorderTop.addEventListener("mousedown", _fnOnMouseDownBorderTop, false);

    //Setup top right corner.
    _fnOnMouseMoveCornerTopRight = function (e) {
        let iHeight = (_iHeight - e.clientY + _iVerticalPosition);
        let iWidth = (_iWidth + e.clientX - _iHorizontalPosition);

        _oControl.style.cssText += "; width: " + iWidth + "px; height: " + iHeight + "px;";

        if (iHeight > 100) {
            _oControl.style.cssText += " top: " + (e.clientY - 100) + "px;";
        }
    };
    _fnOnMouseDownCornerTopRight = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveCornerTopRight, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oCornerTopRight = document.createElement("div");
    _oCornerTopRight.className = "Corner Top Right";
    _oCornerTopRight.style.cssText = "top: 0px; right: 0px; cursor:ne-resize; width: 5px; height: 5px; position: absolute; z-index: 1005;";
    _oCornerTopRight.addEventListener("mousedown", _fnOnMouseDownCornerTopRight, false);

    //Setup left border.
    _fnOnMouseMoveBorderLeft = function (e) {
        e.preventDefault();
        let iWidth = (_iWidth - e.clientX + _iHorizontalPosition);

        _oControl.style.cssText += "; width: " + iWidth + "px;";

        if (iWidth > 100) {
            _oControl.style.cssText += " left: " + e.clientX + "px;";
        }
    };
    _fnOnMouseDownBorderLeft = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveBorderLeft, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oBorderLeft = document.createElement("div");
    _oBorderLeft.className = "Border Left";
    _oBorderLeft.style.cssText = "float: left; left: 0px; cursor:  ew-resize; height: 100%; width:5px; position: absolute; top:0px; z-index: 1004;";
    _oBorderLeft.addEventListener("mousedown", _fnOnMouseDownBorderLeft, false);

    //Setup right border.
    _fnOnMouseMoveBorderRight = function (e) {
        _oControl.style.cssText += "; width: " + (_iWidth + e.clientX - _iHorizontalPosition) + "px;";
    };
    _fnOnMouseDownBorderRight = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveBorderRight, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oBorderRight = document.createElement("div");
    _oBorderRight.className = "Border Right";
    _oBorderRight.style.cssText = "float: right; right: 0px; cursor:  ew-resize; height: 100%; width:5px; position: absolute; top:0px; z-index: 1004;";
    _oBorderRight.addEventListener("mousedown", _fnOnMouseDownBorderRight, false);

    //Setup bottom left corner.
    _fnOnMouseMoveCornerBottomLeft = function (e) {
        let iWidth = (_iWidth - e.clientX + _iHorizontalPosition);
        let iHeight = (_iHeight + e.clientY - _iVerticalPosition);

        _oControl.style.height = _iHeight + "px";
        _oControl.cssText += "; height: " + iHeight + "px; width: " + iWidth + "px;";

        if (iWidth > 100) {
            _oControl.style.cssText += " left: " + e.clientX + "px;";
        }
    };
    _fnOnMouseDownCornerBottomLeft = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveCornerBottomLeft, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oCornerBottomLeft = document.createElement("div");
    _oCornerBottomLeft.className = "Corner Bottom Left";
    _oCornerBottomLeft.style.cssText = "bottom: 0px; left: 0px; cursor:sw-resize; width: 5px; height: 5px; position: absolute; z-index: 1005;";
    _oCornerBottomLeft.addEventListener("mousedown", _fnOnMouseDownCornerBottomLeft, false);

    //Setup bottom border.
    _fnOnMouseMoveBorderBottom = function (e) {
        _oControl.style.cssText += "; height: " + (_iHeight + e.clientY - _iVerticalPosition) + "px;";
    };
    _fnOnMouseDownBorderBottom = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveBorderBottom, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oBorderBottom = document.createElement("div");
    _oBorderBottom.className = "Border Bottom";
    _oBorderBottom.style.cssText = "float: none; bottom: 0px; cursor: ns-resize; width:100%; height:5px; position: absolute; z-index: 1003;";
    _oBorderBottom.addEventListener("mousedown", _fnOnMouseDownBorderBottom, false);

    //Setup bottom right corner
    _fnOnMouseMoveCornerBottomRight = function (e) {
        _oControl.style.cssText += "; height: " + (_iHeight + e.clientY - _iVerticalPosition) + "px; width: " + (_iWidth + e.clientX - _iHorizontalPosition) + "px;";
    };
    _fnOnMouseDownCornerBottomRight = function (e) {
        _iVerticalPosition = e.clientY;
        _iHorizontalPosition = e.clientX;
        window.addEventListener("mousemove", _fnOnMouseMoveCornerBottomRight, true);
        window.addEventListener("mouseup", _fnOnMouseUp, true);
    };
    _oCornerBottomRight = document.createElement("div");
    _oCornerBottomRight.className = "Corner Bottom Right";
    _oCornerBottomRight.style.cssText = "bottom: 0px; right: 0px; cursor:se-resize; width: 5px; height: 5px; position: absolute; z-index: 1005;";
    _oCornerBottomRight.addEventListener("mousedown", _fnOnMouseDownCornerBottomRight, false);

    _oControl.appendChild(_oCornerTopLeft);
    _oControl.appendChild(_oBorderTop);
    _oControl.appendChild(_oCornerTopRight);
    _oControl.appendChild(_oBorderLeft);
    _oControl.appendChild(_oContent);
    _oControl.appendChild(_oBorderRight);
    _oControl.appendChild(_oCornerBottomLeft);
    _oControl.appendChild(_oBorderBottom);
    _oControl.appendChild(_oCornerBottomRight);

    /**
     * Removes all eventhandlers of the box.
     */
    this.Remove = function () {
        _bResizable = false;
        _fnToggleResize();
        _fnRemove();
    };
};