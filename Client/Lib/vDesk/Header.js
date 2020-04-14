"use strict";
/**
 * Initializes a new instance of the Header class.
 * @constructor
 * @class Represents the Header of the client
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.MainMenu} MainMenu Gets the MainMenu of the Header.
 * @property {vDesk.Menu} Menu Gets the Menu of the Header.
 * @property {vDesk.Controls.ToolBar} ToolBar Gets the ToolBar of the Header.
 * @memberOf vDesk
 */
vDesk.Header = (function Header() {

    /**
     * The MainMenu of the Header.
     * @type {null|vDesk.MainMenu}
     */
    let MainMenu = null;

    /**
     * The Menu of the Header.
     * @type {null|vDesk.Menu}
     */
    let Menu = null;

    /**
     * The ToolBar of the Header.
     * @type {null|vDesk.Controls.ToolBar}
     */
    let ToolBar = null;

    /**
     * The underlying DOM-Node.
     * @type {HTMLElement}
     */
    const Control = document.createElement("header");
    Control.className = "Header Background";

    return {
        get MainMenu() {
            return MainMenu;
        },
        get Menu() {
            return Menu;
        },
        get ToolBar() {
            return ToolBar;
        },
        Load() {
            if(MainMenu === null || Menu === null || ToolBar === null) {
                MainMenu = new vDesk.MainMenu();
                Menu = new vDesk.Menu();
                ToolBar = new vDesk.Controls.ToolBar();
                Control.appendChild(MainMenu.Control);
                Control.appendChild(Menu.Control);
                Control.appendChild(ToolBar.Control);
            }
            document.body.appendChild(Control);
        },
        Clear() {
            MainMenu.Clear();
            Menu.Clear();
            ToolBar.Clear();
        },
        Unload() {
            document.body.removeChild(Control);
        }
    };
})();