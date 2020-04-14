"use strict";
/**
 * Initialises a new instance of the LoadingCircle class.
 * @constructor
 * @class Represents a loading animation, providing the user feedback while loading data.
 * @memberOf vDesk.Controls
 */
vDesk.Controls.LoadingCircle = function LoadingCircle() {

    /**
     * Displays the circle.
     */
    this.Show = function() {
        Circle.style.display = "block;";
    };
    /**
     * Hides the circle.
     */
    this.Hide = function() {
        Circle.style.display = "none";
    };

    const Circle = document.createElement("img");
    Circle.src = vDesk.Visual.Icons.LoadingBar;
    Circle.className = "LoadingCircle";

    document.body.appendChild(Circle);

};
