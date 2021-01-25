"use strict";
/**
 * Initializes a new instance of the Color class.
 * @class Represents a singleton color-handler.
 * @hideconstructor
 * @property {String} Foreground Gets or sets the foreground color of the Client.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Convert icons to svg and colorize them?
 */
vDesk.Colors = (function Color() {

    /**
     * The foreground color of the Client.
     * @type {String}
     */
    let ForegroundColor = "";

    /**
     * The foreground color style of the Client.
     * @type {HTMLStyleElement}
     */
    const Foreground = document.createElement("style");

    /**
     * The background color of the Client.
     * @type {String}
     */
    let BackgroundColor = "";

    /**
     * The background color style of the Client.
     * @type {HTMLStyleElement}
     */
    const Background = document.createElement("style");

    /**
     * The light border color of the Client.
     * @type {String}
     */
    let BorderLightColor = "";

    /**
     * The light border color style of the Client.
     * @type {HTMLStyleElement}
     */
    const BorderLight = document.createElement("style");

    /**
     * The dark border color of the Client.
     * @type {String}
     */
    let BorderDarkColor = "";

    /**
     * The dark border color style of the Client.
     * @type {HTMLStyleElement}
     */
    const BorderDark = document.createElement("style");

    /**
     * The font of the Client.
     * @type {String}
     */
    let FontType = "";

    /**
     * The font style of the Client.
     * @type {HTMLStyleElement}
     */
    const Font = document.createElement("style");

    /**
     * The light font color of the Client.
     * @type {String}
     */
    let FontLightColor = "";

    /**
     * The light font style of the Client.
     * @type {HTMLStyleElement}
     */
    const FontLight = document.createElement("style");

    /**
     * The dark font color of the Client.
     * @type {String}
     */
    let FontDarkColor = "";

    /**
     * The dark font style of the Client.
     * @type {HTMLStyleElement}
     */
    const FontDark = document.createElement("style");

    /**
     * The dark font color of the Client.
     * @type {String}
     */
    let FontDisabledColor = "";

    /**
     * The dark font style of the Client.
     * @type {HTMLStyleElement}
     */
    const FontDisabled = document.createElement("style");

    /**
     * The control selected color of the Client.
     * @type {String}
     */
    let ControlSelectedColor = "";

    /**
     * The control selected style of the Client.
     * @type {HTMLStyleElement}
     */
    const ControlSelected = document.createElement("style");

    /**
     * The control hover color of the Client.
     * @type {String}
     */
    let ControlHoverColor = "";

    /**
     * The control hover style of the Client.
     * @type {HTMLStyleElement}
     */
    const ControlHover = document.createElement("style");

    /**
     * The control press color of the Client.
     * @type {String}
     */
    let ControlPressColor = "";

    /**
     * The control press style of the Client.
     * @type {HTMLStyleElement}
     */
    const ControlPress = document.createElement("style");

    /**
     * The button selected color of the Client.
     * @type {String}
     */
    let ButtonSelectedColor = "";

    /**
     * The button selected style of the Client.
     * @type {HTMLStyleElement}
     */
    const ButtonSelected = document.createElement("style");

    /**
     * The button hover color of the Client.
     * @type {String}
     */
    let ButtonHoverColor = "";

    /**
     * The button hover style of the Client.
     * @type {HTMLStyleElement}
     */
    const ButtonHover = document.createElement("style");

    /**
     * The button press color of the Client.
     * @type {String}
     */
    let ButtonPressColor = "";

    /**
     * The button press style of the Client.
     * @type {HTMLStyleElement}
     */
    const ButtonPress = document.createElement("style");

    /**
     * The button background color of the Client.
     * @type {String}
     */
    let ButtonBackgroundColor = "";

    /**
     * The button background style of the Client.
     * @type {HTMLStyleElement}
     */
    const ButtonBackground = document.createElement("style");

    /**
     * The textbox selected color of the Client.
     * @type {String}
     */
    let TextBoxSelectedColor = "";

    /**
     * The textbox selected style of the Client.
     * @type {HTMLStyleElement}
     */
    const TextBoxSelected = document.createElement("style");

    /**
     * The textbox error color of the Client.
     * @type {String}
     */
    let TextBoxErrorColor = "";

    /**
     * The textbox error style of the Client.
     * @type {HTMLStyleElement}
     */
    const TextBoxError = document.createElement("style");

    /**
     * The textbox disabled color of the Client.
     * @type {String}
     */
    let TextBoxDisabledColor = "";

    /**
     * The textbox disabled style of the Client.
     * @type {HTMLStyleElement}
     */
    const TextBoxDisabled = document.createElement("style");

    /**
     * The root style holding the color values as CSS-Custom-Properties for controls that can't use one of the CSS-Classes.
     * @type {HTMLStyleElement}
     */
    const Root = document.createElement("style");

    /**
     * Creates and sets the root style which holds the color values as CSS-Custom-Properties for controls that can't use one of the CSS-Classes.
     */
    const CreateRootStyle = function() {
        Root.textContent = `:root{ 
    --Foreground: ${ForegroundColor}; 
    --Background: ${BackgroundColor};
    --BorderLight: ${BorderLightColor}; 
    --BorderDark: ${BorderDarkColor}; 
    --Font: ${FontType}; 
    --FontLight: ${FontLightColor}; 
    --FontDark: ${FontDarkColor}; 
    --FontDisabled: ${FontDisabledColor}; 
    --ControlSelected: ${ControlSelectedColor}; 
    --ControlHover: ${ControlHoverColor}; 
    --ControlPress: ${ControlPressColor}; 
    --ButtonSelected: ${ButtonSelectedColor}; 
    --ButtonHover: ${ButtonHoverColor}; 
    --ButtonPress: ${ButtonPressColor};
    --ButtonBackground: ${ButtonBackgroundColor};
    --TextBoxSelected: ${TextBoxSelectedColor};
    --TextBoxError: ${TextBoxErrorColor};
    --TextBoxDisabled: ${TextBoxDisabledColor};
}`;
    };

    /**
     * Loads previous set colors.
     */
    const Load = function() {
        //Set color.
        vDesk.Colors.Foreground = vDesk.Configuration.Settings?.Local?.Client?.ForegroundColor ?? vDesk.Colors.Default.Foreground;
        vDesk.Colors.Background = vDesk.Configuration.Settings?.Local?.Client?.BackgroundColor ?? vDesk.Colors.Default.Background;
        vDesk.Colors.BorderLight = vDesk.Configuration.Settings?.Local?.Client?.BorderLightColor ?? vDesk.Colors.Default.BorderLight;
        vDesk.Colors.BorderDark = vDesk.Configuration.Settings?.Local?.Client?.BorderDarkColor ?? vDesk.Colors.Default.BorderDark;
        vDesk.Colors.Font = vDesk.Configuration.Settings?.Local?.Client?.Font ?? vDesk.Colors.Default.Font;
        vDesk.Colors.FontLight = vDesk.Configuration.Settings?.Local?.Client?.FontLightColor ?? vDesk.Colors.Default.FontLight;
        vDesk.Colors.FontDark = vDesk.Configuration.Settings?.Local?.Client?.FontDarkColor ?? vDesk.Colors.Default.FontDark;
        vDesk.Colors.FontDisabled = vDesk.Configuration.Settings?.Local?.Client?.FontDisabledColor ?? vDesk.Colors.Default.FontDisabled;
        vDesk.Colors.Control.Selected = vDesk.Configuration.Settings?.Local?.Client?.ControlSelectedColor ?? vDesk.Colors.Default.Control.Selected;
        vDesk.Colors.Control.Hover = vDesk.Configuration.Settings?.Local?.Client?.ControlHoverColor ?? vDesk.Colors.Default.Control.Hover;
        vDesk.Colors.Control.Press = vDesk.Configuration.Settings?.Local?.Client?.ControlPressColor ?? vDesk.Colors.Default.Control.Press;
        vDesk.Colors.Button.Selected = vDesk.Configuration.Settings?.Local?.Client?.ButtonSelectedColor ?? vDesk.Colors.Default.Button.Selected;
        vDesk.Colors.Button.Hover = vDesk.Configuration.Settings?.Local?.Client?.ButtonHoverColor ?? vDesk.Colors.Default.Button.Hover;
        vDesk.Colors.Button.Press = vDesk.Configuration.Settings?.Local?.Client?.ButtonPressColor ?? vDesk.Colors.Default.Button.Press;
        vDesk.Colors.Button.Background = vDesk.Configuration.Settings?.Local?.Client?.ButtonBackgroundColor ?? vDesk.Colors.Default.Button.Background;
        vDesk.Colors.TextBox.Selected = vDesk.Configuration.Settings?.Local?.Client?.TextBoxSelectedColor ?? vDesk.Colors.Default.TextBox.Selected;
        vDesk.Colors.TextBox.Error = vDesk.Configuration.Settings?.Local?.Client?.TextBoxErrorColor ?? vDesk.Colors.Default.TextBox.Error;
        vDesk.Colors.TextBox.Disabled = vDesk.Configuration.Settings?.Local?.Client?.TextBoxDisabledColor ?? vDesk.Colors.Default.TextBox.Disabled;

        CreateRootStyle();
    };

    /**
     * Saves made changes.
     */
    const Save = function() {

        vDesk.Configuration.Settings.Local.Client.ForegroundColor = vDesk.Colors.Foreground;
        vDesk.Configuration.Settings.Local.Client.BackgroundColor = vDesk.Colors.Background;
        vDesk.Configuration.Settings.Local.Client.BorderLightColor = vDesk.Colors.BorderLight;
        vDesk.Configuration.Settings.Local.Client.BorderDarkColor = vDesk.Colors.BorderDark;
        vDesk.Configuration.Settings.Local.Client.Font = vDesk.Colors.Font;
        vDesk.Configuration.Settings.Local.Client.FontLightColor = vDesk.Colors.FontLight;
        vDesk.Configuration.Settings.Local.Client.FontDarkColor = vDesk.Colors.FontDark;
        vDesk.Configuration.Settings.Local.Client.FontDisabledColor = vDesk.Colors.FontDisabled;
        vDesk.Configuration.Settings.Local.Client.ControlSelectedColor = vDesk.Colors.Control.Selected;
        vDesk.Configuration.Settings.Local.Client.ControlHoverColor = vDesk.Colors.Control.Hover;
        vDesk.Configuration.Settings.Local.Client.ControlPressColor = vDesk.Colors.Control.Press;
        vDesk.Configuration.Settings.Local.Client.ButtonSelectedColor = vDesk.Colors.Button.Selected;
        vDesk.Configuration.Settings.Local.Client.ButtonHoverColor = vDesk.Colors.Button.Hover;
        vDesk.Configuration.Settings.Local.Client.ButtonPressColor = vDesk.Colors.Button.Press;
        vDesk.Configuration.Settings.Local.Client.ButtonBackgroundColor = vDesk.Colors.Button.Background;
        vDesk.Configuration.Settings.Local.Client.TextBoxSelectedColor = vDesk.Colors.TextBox.Selected;
        vDesk.Configuration.Settings.Local.Client.TextBoxErrorColor = vDesk.Colors.TextBox.Error;
        vDesk.Configuration.Settings.Local.Client.TextBoxDisabledColor = vDesk.Colors.TextBox.Disabled;
        vDesk.Configuration.Settings.Save();

        CreateRootStyle();

    };

    /**
     * Resets the colors of the Client to the default colors.
     */
    const Reset = function() {

        vDesk.Colors.Foreground = vDesk.Configuration.Settings.Local.Client.ForegroundColor = vDesk.Colors.Default.Foreground;
        vDesk.Colors.Background = vDesk.Configuration.Settings.Local.Client.BackgroundColor = vDesk.Colors.Default.Background;
        vDesk.Colors.BorderLight = vDesk.Configuration.Settings.Local.Client.BorderLightColor = vDesk.Colors.Default.BorderLight;
        vDesk.Colors.BorderDark = vDesk.Configuration.Settings.Local.Client.BorderDarkColor = vDesk.Colors.Default.BorderDark;
        vDesk.Colors.Font = vDesk.Configuration.Settings.Local.Client.Font = vDesk.Colors.Default.Font;
        vDesk.Colors.FontLight = vDesk.Configuration.Settings.Local.Client.FontLightColor = vDesk.Colors.Default.FontLight;
        vDesk.Colors.FontDark = vDesk.Configuration.Settings.Local.Client.FontDarkColor = vDesk.Colors.Default.FontDark;
        vDesk.Colors.FontDisabled = vDesk.Configuration.Settings.Local.Client.FontDisabledColor = vDesk.Colors.Default.FontDisabled;
        vDesk.Colors.Control.Selected = vDesk.Configuration.Settings.Local.Client.ControlSelectedColor = vDesk.Colors.Default.Control.Selected;
        vDesk.Colors.Control.Hover = vDesk.Configuration.Settings.Local.Client.ControlHoverColor = vDesk.Colors.Default.Control.Hover;
        vDesk.Colors.Control.Press = vDesk.Configuration.Settings.Local.Client.ControlPressColor = vDesk.Colors.Default.Control.Press;
        vDesk.Colors.Button.Selected = vDesk.Configuration.Settings.Local.Client.ButtonSelectedColor = vDesk.Colors.Default.Button.Selected;
        vDesk.Colors.Button.Hover = vDesk.Configuration.Settings.Local.Client.ButtonHoverColor = vDesk.Colors.Default.Button.Hover;
        vDesk.Colors.Button.Press = vDesk.Configuration.Settings.Local.Client.ButtonPressColor = vDesk.Colors.Default.Button.Press;
        vDesk.Colors.Button.Background = vDesk.Configuration.Settings.Local.Client.ButtonBackgroundColor = vDesk.Colors.Default.Button.Background;
        vDesk.Colors.TextBox.Selected = vDesk.Configuration.Settings.Local.Client.TextBoxSelectedColor = vDesk.Colors.Default.TextBox.Selected;
        vDesk.Colors.TextBox.Error = vDesk.Configuration.Settings.Local.Client.TextBoxErrorColor = vDesk.Colors.Default.TextBox.Error;
        vDesk.Colors.TextBox.Disabled = vDesk.Configuration.Settings.Local.Client.TextBoxDisabledColor = vDesk.Colors.Default.TextBox.Disabled;
        vDesk.Configuration.Settings.Save();

        CreateRootStyle();

    };

    document.head.appendChild(Foreground);
    document.head.appendChild(Background);
    document.head.appendChild(BorderLight);
    document.head.appendChild(BorderDark);
    document.head.appendChild(Font);
    document.head.appendChild(FontLight);
    document.head.appendChild(FontDark);
    document.head.appendChild(FontDisabled);
    document.head.appendChild(ControlSelected);
    document.head.appendChild(ControlHover);
    document.head.appendChild(ControlPress);
    document.head.appendChild(ButtonSelected);
    document.head.appendChild(ButtonHover);
    document.head.appendChild(ButtonPress);
    document.head.appendChild(ButtonBackground);
    document.head.appendChild(TextBoxSelected);
    document.head.appendChild(TextBoxError);
    document.head.appendChild(TextBoxDisabled);
    document.head.appendChild(Root);

    return {
        Load:    Load,
        Save:    Save,
        Reset:   Reset,
        get Foreground() {
            return ForegroundColor;
        },
        set Foreground(Value) {
            Ensure.Property(Value, "string", "Foreground");
            ForegroundColor = Value;
            Foreground.textContent = `.Foreground{ background-color: ${Value}; }`;
        },
        get Background() {
            return BackgroundColor;
        },
        set Background(Value) {
            Ensure.Property(Value, "string", "Background");
            BackgroundColor = Value;
            Background.textContent = `.Background{ background-color: ${Value}; }`;
        },
        get BorderLight() {
            return BorderLightColor;
        },
        set BorderLight(Value) {
            Ensure.Property(Value, "string", "BorderLight");
            BorderLightColor = Value;
            BorderLight.textContent = `.BorderLight{ border-color: ${Value} !important; color: ${Value}; }`;
        },
        get BorderDark() {
            return BorderDarkColor;
        },
        set BorderDark(Value) {
            Ensure.Property(Value, "string", "BorderDark");
            BorderDarkColor = Value;
            BorderDark.textContent = `.BorderDark{ border-color: ${Value} !important; color: ${Value}; }`;
        },
        get Font() {
            return FontType;
        },
        set Font(Value) {
            Ensure.Property(Value, "string", "Font");
            FontType = Value;
            Font.textContent = `.Font{ font-family: ${Value}; }`;
        },
        get FontLight() {
            return FontLightColor;
        },
        set FontLight(Value) {
            Ensure.Property(Value, "string", "FontLight");
            FontLightColor = Value;
            FontLight.textContent = `.Font.Light{ color: ${Value}; }`;
        },
        get FontDark() {
            return FontDarkColor;
        },
        set FontDark(Value) {
            Ensure.Property(Value, "string", "FontDark");
            FontDarkColor = Value;
            FontDark.textContent = `.Font.Dark{ color: ${Value}; }`;
        },
        get FontDisabled() {
            return FontDisabledColor;
        },
        set FontDisabled(Value) {
            Ensure.Property(Value, "string", "FontDisabled");
            FontDisabledColor = Value;
            FontDisabled.textContent = `.Font.Disabled{ color: ${Value}; }`;
        },
        Control: {
            get Selected() {
                return ControlSelectedColor;
            },
            set Selected(Value) {
                Ensure.Property(Value, "string", "Selected");
                ControlSelectedColor = Value;
                ControlSelected.textContent = `.Control.Selected{ background-color: ${Value}; }`;
            },
            get Hover() {
                return ControlHoverColor;
            },
            set Hover(Value) {
                Ensure.Property(Value, "string", "ControlHover");
                ControlHoverColor = Value;
                ControlHover.textContent = `.Control:hover:enabled{ background-color: ${Value}; }`;
            },
            get Press() {
                return ControlPressColor;
            },
            set Press(Value) {
                Ensure.Property(Value, "string", "ControlPress");
                ControlPressColor = Value;
                ControlPress.textContent = `.Control:active:enabled{ background-color: ${Value}; }`;
            }
        },
        Button:  {
            get Selected() {
                return ButtonSelectedColor;
            },
            set Selected(Value) {
                Ensure.Property(Value, "string", "Selected");
                ButtonSelectedColor = Value;
                ButtonSelected.textContent = `.Button.Selected{ background-color: ${Value}; }`;
            },
            get Hover() {
                return ButtonHoverColor;
            },
            set Hover(Value) {
                Ensure.Property(Value, "string", "Hover");
                ButtonHoverColor = Value;
                ButtonHover.textContent = `.Button:hover:enabled{ background-color: ${Value}; }`;
            },
            get Press() {
                return ButtonPressColor;
            },
            set Press(Value) {
                Ensure.Property(Value, "string", "Press");
                ButtonPressColor = Value;
                ButtonPress.textContent = `.Button:active:enabled{ background-color: ${Value}; }`;
            },
            get Background() {
                return ButtonBackgroundColor;
            },
            set Background(Value) {
                Ensure.Property(Value, "string", "Background");
                ButtonBackgroundColor = Value;
                ButtonBackground.textContent = `.Button{ background-color: ${Value}; }`;
            }
        },
        /* @todo Evaluate reusing values of fonts and borders.*/
        TextBox: {
            get Selected() {
                return TextBoxSelectedColor;
            },
            set Selected(Value) {
                Ensure.Property(Value, "string", "Selected");
                TextBoxSelectedColor = Value;
                TextBoxSelected.textContent = `.TextBox.Selected{ border-color: ${Value} !important; }`;

            },
            get Error() {
                return TextBoxErrorColor;
            },
            set Error(Value) {
                Ensure.Property(Value, "string", "Error");
                TextBoxErrorColor = Value;
                TextBoxError.textContent = `.TextBox.Error{ border-color: ${Value} !important; }`;

            },
            get Disabled() {
                return TextBoxDisabledColor;
            },
            set Disabled(Value) {
                Ensure.Property(Value, "string", "Disabled");
                TextBoxDisabledColor = Value;
                TextBoxDisabled.textContent = `.TextBox.Disabled{ border-color: ${Value} !important; }`;

            }
        },
        /**
         * Enumeration of default colors.
         * @readonly
         * @enum {String}
         */
        Default: {
            Foreground:   "rgba(42, 176, 237, 1)",
            Background:   "rgba(255, 255, 255, 1)",
            BorderLight:  "rgba(153, 153, 153, 1)",
            BorderDark:   "rgba(0, 0, 0, 1)",
            Font:         "Arial",
            FontLight:    "rgba(255, 255, 255, 1)",
            FontDark:     "rgba(0, 0, 0, 1)",
            FontDisabled: "rgba(153, 153, 153, 1)",
            Control:      {
                Selected: "rgba(255, 207, 50, 1)",
                Hover:    "rgba(42, 176, 237, 1)",
                Press:    "rgba(70, 140, 207, 1)"
            },
            Button:       {
                Selected:   "rgba(170,170,170, 1)",
                Hover:      "rgba(153, 153, 153, 1)",
                Press:      "rgba(119, 119, 119, 1)",
                Background: "rgba(219,219,219, 1)"
            },
            TextBox:      {
                Selected: "rgba(255, 207, 50, 1)",
                Error:    "rgba(255, 51, 0, 1)",
                Disabled: "rgba(153, 153, 153, 1)"
            }
        },
        Status:  "Loading Colorinformation"
    };
})();
vDesk.Load.Colors = vDesk.Colors;