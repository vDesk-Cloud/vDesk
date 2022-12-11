"use strict";
/**
 * Initializes a new instance of the Color class.
 * @class Singleton facade that provides an interface for customizing, saving and updating the colors of the Client.
 * @property {String} Foreground Gets or sets the foreground color of the Client.
 * @property {String} Background Gets or sets the background color of the Client.
 * @property {String} BorderLight Gets or sets the light border color of the Client.
 * @property {String} BorderDark Gets or sets the dark border color of the Client.
 * @property {String} Font Gets or sets the font of the Client.
 * @property {String} FontLight Gets or sets the the light font color of the Client.
 * @property {String} FontDark Gets or sets the dark font color of the Client.
 * @property {String} FontDisabled Gets or sets the disabled font color of the Client.
 * @memberOf vDesk
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Convert icons to svg and colorize them.
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
    document.head.appendChild(Foreground);

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
    document.head.appendChild(Background);

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
    document.head.appendChild(BorderLight);

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
    document.head.appendChild(BorderDark);

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
    document.head.appendChild(Font);

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
    document.head.appendChild(FontLight);

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
    document.head.appendChild(FontDark);

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
    document.head.appendChild(FontDisabled);

    /**
     * The control background color of the Client.
     * @type {String}
     */
    let ControlBackgroundColor = "";

    /**
     * The control background style of the Client.
     * @type {HTMLStyleElement}
     */
    const ControlBackground = document.createElement("style");
    document.head.appendChild(ControlBackground);

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
    document.head.appendChild(ControlSelected);

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
    document.head.appendChild(ControlHover);

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
    document.head.appendChild(ControlPress);

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
    document.head.appendChild(ButtonBackground);

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
    document.head.appendChild(ButtonSelected);

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
    document.head.appendChild(ButtonHover);

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
    document.head.appendChild(ButtonPress);

    /**
     * The textbox background color of the Client.
     * @type {String}
     */
    let TextBoxBackgroundColor = "";

    /**
     * The textbox background style of the Client.
     * @type {HTMLStyleElement}
     */
    const TextBoxBackground = document.createElement("style");
    document.head.appendChild(TextBoxBackground);

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
    document.head.appendChild(TextBoxSelected);

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
    document.head.appendChild(TextBoxError);

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
    document.head.appendChild(TextBoxDisabled);

    /**
     * The root style holding the color values as CSS-Custom-Properties for controls that can't use one of the CSS-Classes.
     * @type {HTMLStyleElement}
     */
    const Root = document.createElement("style");
    document.head.appendChild(Root);

    return {
        /**
         * Loads and applies a previous set of colors from the local configuration of the current User.
         */
        Load: function() {
            this.Foreground = vDesk.Configuration.Settings?.Local?.Client?.ForegroundColor ?? this.Presets.Light.Foreground;
            this.Background = vDesk.Configuration.Settings?.Local?.Client?.BackgroundColor ?? this.Presets.Light.Background;
            this.BorderLight = vDesk.Configuration.Settings?.Local?.Client?.BorderLightColor ?? this.Presets.Light.BorderLight;
            this.BorderDark = vDesk.Configuration.Settings?.Local?.Client?.BorderDarkColor ?? this.Presets.Light.BorderDark;
            this.Font = vDesk.Configuration.Settings?.Local?.Client?.Font ?? this.Presets.Light.Font;
            this.FontLight = vDesk.Configuration.Settings?.Local?.Client?.FontLightColor ?? this.Presets.Light.FontLight;
            this.FontDark = vDesk.Configuration.Settings?.Local?.Client?.FontDarkColor ?? this.Presets.Light.FontDark;
            this.FontDisabled = vDesk.Configuration.Settings?.Local?.Client?.FontDisabledColor ?? this.Presets.Light.FontDisabled;
            this.Control.Background = vDesk.Configuration.Settings?.Local?.Client?.ControlBackgroundColor ?? this.Presets.Light.Control.Background;
            this.Control.Selected = vDesk.Configuration.Settings?.Local?.Client?.ControlSelectedColor ?? this.Presets.Light.Control.Selected;
            this.Control.Hover = vDesk.Configuration.Settings?.Local?.Client?.ControlHoverColor ?? this.Presets.Light.Control.Hover;
            this.Control.Press = vDesk.Configuration.Settings?.Local?.Client?.ControlPressColor ?? this.Presets.Light.Control.Press;
            this.Button.Background = vDesk.Configuration.Settings?.Local?.Client?.ButtonBackgroundColor ?? this.Presets.Light.Button.Background;
            this.Button.Selected = vDesk.Configuration.Settings?.Local?.Client?.ButtonSelectedColor ?? this.Presets.Light.Button.Selected;
            this.Button.Hover = vDesk.Configuration.Settings?.Local?.Client?.ButtonHoverColor ?? this.Presets.Light.Button.Hover;
            this.Button.Press = vDesk.Configuration.Settings?.Local?.Client?.ButtonPressColor ?? this.Presets.Light.Button.Press;
            this.TextBox.Background = vDesk.Configuration.Settings?.Local?.Client?.TextBoxBackgroundColor ?? this.Presets.Light.TextBox.Background;
            this.TextBox.Selected = vDesk.Configuration.Settings?.Local?.Client?.TextBoxSelectedColor ?? this.Presets.Light.TextBox.Selected;
            this.TextBox.Error = vDesk.Configuration.Settings?.Local?.Client?.TextBoxErrorColor ?? this.Presets.Light.TextBox.Error;
            this.TextBox.Disabled = vDesk.Configuration.Settings?.Local?.Client?.TextBoxDisabledColor ?? this.Presets.Light.TextBox.Disabled;
            this.Update();
        },
        /**
         * Applies a color preset object to the Client.
         * Undefined colors of the specified preset will be replaced with the according colors of the "Light"-preset.
         * @param {Object} Preset The color preset to apply.
         */
        Apply: function(Preset) {
            this.Foreground = Preset?.Foreground ?? this.Presets.Light.Foreground;
            this.Background = Preset?.Background ?? this.Presets.Light.Background;
            this.BorderLight = Preset?.BorderLight ?? this.Presets.Light.BorderLight;
            this.BorderDark = Preset?.BorderDark ?? this.Presets.Light.BorderDark;
            this.Font = Preset?.Font ?? this.Presets.Light.Font;
            this.FontLight = Preset?.FontLight ?? this.Presets.Light.FontLight;
            this.FontDark = Preset?.FontDark ?? this.Presets.Light.FontDark;
            this.FontDisabled = Preset?.FontDisabled ?? this.Presets.Light.FontDisabled;
            this.Control.Background = Preset?.Control?.Background ?? this.Presets.Light.Control.Background;
            this.Control.Selected = Preset?.Control?.Selected ?? this.Presets.Light.Control.Selected;
            this.Control.Hover = Preset?.Control?.Hover ?? this.Presets.Light.Control.Hover;
            this.Control.Press = Preset?.Control?.Press ?? this.Presets.Light.Control.Press;
            this.Button.Background = Preset?.Button?.Background ?? this.Presets.Light.Button.Background;
            this.Button.Selected = Preset?.Button?.Selected ?? this.Presets.Light.Button.Selected;
            this.Button.Hover = Preset?.Button?.Hover ?? this.Presets.Light.Button.Hover;
            this.Button.Press = Preset?.Button?.Press ?? this.Presets.Light.Button.Press;
            this.TextBox.Background = Preset?.TextBox?.Background ?? this.Presets.Light.TextBox.Background;
            this.TextBox.Selected = Preset?.TextBox?.Selected ?? this.Presets.Light.TextBox.Selected;
            this.TextBox.Error = Preset?.TextBox?.Error ?? this.Presets.Light.TextBox.Error;
            this.TextBox.Disabled = Preset?.TextBox?.Disabled ?? this.Presets.Light.TextBox.Disabled;
            this.Update();
        },
        /**
         * Saves the colors to the current User's local configuration.
         */
        Save: function() {
            vDesk.Configuration.Settings.Local.Client.ForegroundColor = this.Foreground;
            vDesk.Configuration.Settings.Local.Client.BackgroundColor = this.Background;
            vDesk.Configuration.Settings.Local.Client.BorderLightColor = this.BorderLight;
            vDesk.Configuration.Settings.Local.Client.BorderDarkColor = this.BorderDark;
            vDesk.Configuration.Settings.Local.Client.Font = this.Font;
            vDesk.Configuration.Settings.Local.Client.FontLightColor = this.FontLight;
            vDesk.Configuration.Settings.Local.Client.FontDarkColor = this.FontDark;
            vDesk.Configuration.Settings.Local.Client.FontDisabledColor = this.FontDisabled;
            vDesk.Configuration.Settings.Local.Client.ControlBackgroundColor = this.Control.Background;
            vDesk.Configuration.Settings.Local.Client.ControlSelectedColor = this.Control.Selected;
            vDesk.Configuration.Settings.Local.Client.ControlHoverColor = this.Control.Hover;
            vDesk.Configuration.Settings.Local.Client.ControlPressColor = this.Control.Press;
            vDesk.Configuration.Settings.Local.Client.ButtonBackgroundColor = this.Button.Background;
            vDesk.Configuration.Settings.Local.Client.ButtonSelectedColor = this.Button.Selected;
            vDesk.Configuration.Settings.Local.Client.ButtonHoverColor = this.Button.Hover;
            vDesk.Configuration.Settings.Local.Client.ButtonPressColor = this.Button.Press;
            vDesk.Configuration.Settings.Local.Client.TextBoxBackgroundColor = this.TextBox.Background;
            vDesk.Configuration.Settings.Local.Client.TextBoxSelectedColor = this.TextBox.Selected;
            vDesk.Configuration.Settings.Local.Client.TextBoxErrorColor = this.TextBox.Error;
            vDesk.Configuration.Settings.Local.Client.TextBoxDisabledColor = this.TextBox.Disabled;
            vDesk.Configuration.Settings.Save();
            this.Update();
        },
        /**
         * Resets the colors of the client and current User's local configuration to the colors of the "Light"-preset.
         */
        Reset: function() {
            this.Foreground = vDesk.Configuration.Settings.Local.Client.ForegroundColor = this.Presets.Light.Foreground;
            this.Background = vDesk.Configuration.Settings.Local.Client.BackgroundColor = this.Presets.Light.Background;
            this.BorderLight = vDesk.Configuration.Settings.Local.Client.BorderLightColor = this.Presets.Light.BorderLight;
            this.BorderDark = vDesk.Configuration.Settings.Local.Client.BorderDarkColor = this.Presets.Light.BorderDark;
            this.Font = vDesk.Configuration.Settings.Local.Client.Font = this.Presets.Light.Font;
            this.FontLight = vDesk.Configuration.Settings.Local.Client.FontLightColor = this.Presets.Light.FontLight;
            this.FontDark = vDesk.Configuration.Settings.Local.Client.FontDarkColor = this.Presets.Light.FontDark;
            this.FontDisabled = vDesk.Configuration.Settings.Local.Client.FontDisabledColor = this.Presets.Light.FontDisabled;
            this.Control.Background = vDesk.Configuration.Settings.Local.Client.ControlBackgroundColor = this.Presets.Light.Control.Background;
            this.Control.Selected = vDesk.Configuration.Settings.Local.Client.ControlSelectedColor = this.Presets.Light.Control.Selected;
            this.Control.Hover = vDesk.Configuration.Settings.Local.Client.ControlHoverColor = this.Presets.Light.Control.Hover;
            this.Control.Press = vDesk.Configuration.Settings.Local.Client.ControlPressColor = this.Presets.Light.Control.Press;
            this.Button.Background = vDesk.Configuration.Settings.Local.Client.ButtonBackgroundColor = this.Presets.Light.Button.Background;
            this.Button.Selected = vDesk.Configuration.Settings.Local.Client.ButtonSelectedColor = this.Presets.Light.Button.Selected;
            this.Button.Hover = vDesk.Configuration.Settings.Local.Client.ButtonHoverColor = this.Presets.Light.Button.Hover;
            this.Button.Press = vDesk.Configuration.Settings.Local.Client.ButtonPressColor = this.Presets.Light.Button.Press;
            this.TextBox.Background = vDesk.Configuration.Settings.Local.Client.TextBoxBackgroundColor = this.Presets.Light.TextBox.Background;
            this.TextBox.Selected = vDesk.Configuration.Settings.Local.Client.TextBoxSelectedColor = this.Presets.Light.TextBox.Selected;
            this.TextBox.Error = vDesk.Configuration.Settings.Local.Client.TextBoxErrorColor = this.Presets.Light.TextBox.Error;
            this.TextBox.Disabled = vDesk.Configuration.Settings.Local.Client.TextBoxDisabledColor = this.Presets.Light.TextBox.Disabled;
            vDesk.Configuration.Settings.Save();
            this.Update();
        },
        /**
         * Updates the root color-node of CSS-variables with the current applied colors.
         */
        Update: function() {
            Root.textContent = `:root{ 
    --Foreground: ${ForegroundColor}; 
    --Background: ${BackgroundColor};
    --BorderLight: ${BorderLightColor}; 
    --BorderDark: ${BorderDarkColor}; 
    --Font: ${FontType}; 
    --FontLight: ${FontLightColor}; 
    --FontDark: ${FontDarkColor}; 
    --FontDisabled: ${FontDisabledColor}; 
    --ControlBackground: ${ControlBackgroundColor};
    --ControlSelected: ${ControlSelectedColor}; 
    --ControlHover: ${ControlHoverColor}; 
    --ControlPress: ${ControlPressColor}; 
    --ButtonBackground: ${ButtonBackgroundColor};
    --ButtonSelected: ${ButtonSelectedColor}; 
    --ButtonHover: ${ButtonHoverColor}; 
    --ButtonPress: ${ButtonPressColor};
    --TextBoxBackground: ${TextBoxBackgroundColor};
    --TextBoxSelected: ${TextBoxSelectedColor};
    --TextBoxError: ${TextBoxErrorColor};
    --TextBoxDisabled: ${TextBoxDisabledColor};
    --TextBoxBackground: ${TextBoxBackgroundColor};
}`;
        },
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
        /**
         * Enumeration of "Control(big button)"-colors.
         * @readonly
         * @property {String} Background Gets or sets the control background color of the Client.
         * @property {String} Selected Gets or sets the selected control color of the Client.
         * @property {String} Hover Gets or sets the control hover color of the Client.
         * @property {String} Press Gets or sets the control press color of the Client.
         * @memberOf Colors
         */
        Control: {
            get Background() {
                return ControlBackgroundColor;
            },
            set Background(Value) {
                Ensure.Property(Value, "string", "Background");
                ControlBackgroundColor = Value;
                ControlBackground.textContent = `.Control{ background-color: ${Value}; }`;
            },
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
        /**
         * Enumeration of "Button"-colors.
         * @readonly
         * @property {String} Background Gets or sets the button background color of the Client.
         * @property {String} Selected Gets or sets the selected button color of the Client.
         * @property {String} Hover Gets or sets the button hover color of the Client.
         * @property {String} Press Gets or sets the button press color of the Client.
         * @memberOf Colors
         */
        Button: {
            get Background() {
                return ButtonBackgroundColor;
            },
            set Background(Value) {
                Ensure.Property(Value, "string", "Background");
                ButtonBackgroundColor = Value;
                ButtonBackground.textContent = `.Button{ background-color: ${Value}; }`;
            },
            get Selected() {
                return ButtonSelectedColor;
            },
            set Selected(Value) {
                Ensure.Property(Value, "string", "Selected");
                ButtonSelectedColor = Value;
                ButtonSelected.textContent = `.Button:active{ background-color: ${Value}; }`;
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
            }
        },
        /**
         * Enumeration of "TextBox"-colors.
         * @readonly
         * @property {String} Background Gets or sets the control background color of the Client.
         * @property {String} Selected Gets or sets the selected textbox color of the Client.
         * @property {String} Error Gets or sets the error textbox color of the Client.
         * @property {String} Disabled Gets or sets the disabled textbox color of the Client.
         * @memberOf Colors
         */
        TextBox: {
            get Background() {
                return TextBoxBackgroundColor;
            },
            set Background(Value) {
                Ensure.Property(Value, "string", "Background");
                TextBoxBackgroundColor = Value;
                TextBoxBackground.textContent = `.TextBox{ background-color: ${Value}; }`;
            },
            get Selected() {
                return TextBoxSelectedColor;
            },
            set Selected(Value) {
                Ensure.Property(Value, "string", "Selected");
                TextBoxSelectedColor = Value;
                TextBoxSelected.textContent = `.TextBox:focus{ border-color: ${Value} !important; }`;

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
                TextBoxDisabled.textContent = `.TextBox:disabled{ border-color: ${Value} !important; }`;

            }
        },
        /**
         * Enumeration of default color presets.
         * @readonly
         * @enum {Object<String>}
         */
        Presets: {
            Light:     {
                Foreground:   "rgba(42, 176, 237, 1)",
                Background:   "rgba(255, 255, 255, 1)",
                BorderLight:  "rgba(153, 153, 153, 1)",
                BorderDark:   "rgba(0, 0, 0, 1)",
                Font:         "Arial",
                FontLight:    "rgba(255, 255, 255, 1)",
                FontDark:     "rgba(0, 0, 0, 1)",
                FontDisabled: "rgba(153, 153, 153, 1)",
                Control:      {
                    Background: "rgba(255, 255, 255, 1)",
                    Selected:   "rgba(255, 207, 50, 1)",
                    Hover:      "rgba(42, 176, 237, 1)",
                    Press:      "rgba(70, 140, 207, 1)"
                },
                Button:       {
                    Background: "rgba(219, 219, 219, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(255, 255, 255, 1)",
                    Selected:   "rgba(70, 140, 207, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Dark:      {
                Foreground:   "rgba(18, 143, 196, 1)",
                Background:   "rgba(30, 30, 30, 1)",
                BorderLight:  "rgba(150, 150, 150, 1)",
                BorderDark:   "rgba(190, 190, 190, 1)",
                Font:         "Arial",
                FontLight:    "rgba(170, 170, 170, 1)",
                FontDark:     "rgba(190, 190, 190, 1)",
                FontDisabled: "rgba(150, 150, 150, 1)",
                Control:      {
                    Background: "rgba(30, 30, 30, 1)",
                    Selected:   "rgba(180, 120, 20, 1)",
                    Hover:      "rgba(9, 79, 109, 1)",
                    Press:      "rgba(24, 60, 93, 1)",
                },
                Button:       {
                    Background: "rgba(71, 71, 71, 1)",
                    Selected:   "rgba(170,170,170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(50, 50, 50, 1)",
                    Selected:   "rgba(220, 150, 50, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Evening:   {
                Foreground:   "rgba(22, 156, 217, 1)",
                Background:   "rgba(245, 230, 210, 1)",
                BorderLight:  "rgba(130, 130, 130, 1)",
                BorderDark:   "rgba(0, 0, 0, 1)",
                Font:         "Arial",
                FontLight:    "rgba(220, 220, 220, 1)",
                FontDark:     "rgba(30, 30, 30, 1)",
                FontDisabled: "rgba(130, 130, 130, 1)",
                Control:      {
                    Background: "rgba(245, 230, 210, 1)",
                    Selected:   "rgba(217, 195, 130, 1)",
                    Hover:      "rgba(42, 156, 217, 1)",
                    Press:      "rgba(70, 140, 207, 1)"
                },
                Button:       {
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)",
                    Background: "rgba(231, 225, 225, 1)"
                },
                TextBox:      {
                    Background: "rgba(245, 230, 210, 1)",
                    Selected:   "rgba(255, 207, 50, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Pastel:    {
                Foreground:   "rgba(224, 187, 228, 1)",
                Background:   "rgba(255, 244, 211, 1)",
                BorderLight:  "rgba(177, 138, 188, 1)",
                BorderDark:   "rgba(101, 55, 134, 1)",
                Font:         "Arial",
                FontLight:    "rgba(255, 255, 255, 1)",
                FontDark:     "rgba(86, 4, 143, 1)",
                FontDisabled: "rgba(200, 130, 205, 1)",
                Control:      {
                    Background: "rgba(234, 205, 205, 1)",
                    Selected:   "rgba(224, 187, 228, 1)",
                    Hover:      "rgba(210, 145, 188, 1)",
                    Press:      "rgba(149, 125, 173, 1)"
                },
                Button:       {
                    Background: "rgba(234, 205, 205, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(255, 233, 211, 1)",
                    Selected:   "rgba(255, 207, 50, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Forest:    {
                Foreground:   "rgba(120, 180, 60, 1)",
                Background:   "rgba(245, 255, 235, 1)",
                BorderLight:  "rgba(160, 140, 180, 1)",
                BorderDark:   "rgba(100, 150, 0, 1)",
                Font:         "Times New Roman",
                FontLight:    "rgba(190, 250, 250, 1)",
                FontDark:     "rgba(110, 60, 0, 1)",
                FontDisabled: "rgba(200, 130, 80, 1)",
                Control:      {
                    Background: "rgba(210, 230, 170, 1)",
                    Selected:   "rgba(180, 200, 160, 1)",
                    Hover:      "rgba(230, 240, 180, 1)",
                    Press:      "rgba(150, 170, 120, 1)"
                },
                Button:       {
                    Background: "rgba(196, 216, 172, 1)",
                    Selected:   "rgba(212, 255, 124, 1)",
                    Hover:      "rgba(135, 168, 103, 1)",
                    Press:      "rgba(101, 133, 66, 1)"
                },
                TextBox:      {
                    Background: "rgba(230, 255, 210, 1)",
                    Selected:   "rgba(255, 207, 50, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Sunset:    {
                Foreground:   "rgba(230, 180, 120, 1)",
                Background:   "rgba(255, 250, 245, 1)",
                BorderLight:  "rgba(190, 180, 115, 1)",
                BorderDark:   "rgba(235, 170, 132, 1)",
                Font:         "Arial",
                FontLight:    "rgba(150, 75, 75, 1)",
                FontDark:     "rgba(120, 60, 0, 1)",
                FontDisabled: "rgba(133, 133, 133, 1)",
                Control:      {
                    Background: "rgba(232, 196, 161, 1)",
                    Selected:   "rgba(235, 170, 132, 1)",
                    Hover:      "rgba(245, 150, 131, 1)",
                    Press:      "rgba(255, 120, 130, 1)"
                },
                Button:       {
                    Background: "rgba(215, 230, 250, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(150, 180, 230, 1)",
                    Press:      "rgba(120, 140, 210, 1)"
                },
                TextBox:      {
                    Background: "rgba(227, 242, 248, 1)",
                    Selected:   "rgba(120, 200, 255, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Night:     {
                Foreground:   "rgba(91, 91, 91, 1)",
                Background:   "rgba(111, 111, 111, 1)",
                BorderLight:  "rgba(71, 71, 71, 1)",
                BorderDark:   "rgba(40, 40, 0, 1)",
                Font:         "Arial",
                FontLight:    "rgba(190, 205, 150, 1)",
                FontDark:     "rgba(40, 40, 40, 1)",
                FontDisabled: "rgba(190, 190, 170, 1)",
                Control:      {
                    Background: "rgba(100, 100, 100, 1)",
                    Selected:   "rgba(130, 140, 120, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                Button:       {
                    Background: "rgba(125, 130, 120, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(100, 100, 100, 1)",
                    Selected:   "rgba(130, 140, 120, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            Aurora:    {
                Foreground:   "rgba(46, 16, 86, 1)",
                Background:   "rgba(5, 0, 20, 1)",
                BorderLight:  "rgba(86, 68, 109, 1)",
                BorderDark:   "rgba(56, 38, 79, 1)",
                Font:         "Arial",
                FontLight:    "rgba(225, 190, 115, 1)",
                FontDark:     "rgba(190, 180, 150, 1)",
                FontDisabled: "rgba(106, 88, 129, 1)",
                Control:      {
                    Background: "rgba(56, 26, 96, 1)",
                    Selected:   "rgba(105, 70, 170, 1)",
                    Hover:      "rgba(66, 36, 106, 1)",
                    Press:      "rgba(46, 16, 86, 1)"
                },
                Button:       {
                    Background: "rgba(36, 18, 59, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(46, 28, 69, 1)",
                    Press:      "rgba(26, 8, 49, 1)"
                },
                TextBox:      {
                    Background: "rgba(36, 18, 59, 1)",
                    Selected:   "rgba(70, 140, 207, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(86, 68, 109, 1)"
                }
            },
            Sunrise:   {
                Foreground:   "rgba(255, 245, 160, 1)",
                Background:   "rgba(250, 250, 255, 1)",
                BorderLight:  "rgba(150, 190, 200, 1)",
                BorderDark:   "rgba(0, 0, 0, 1)",
                Font:         "Arial",
                FontLight:    "rgba(210, 150, 130, 1)",
                FontDark:     "rgba(0, 40, 100, 1)",
                FontDisabled: "rgba(133, 133, 133, 1)",
                Control:      {
                    Background: "rgba(235, 245, 255, 1)",
                    Selected:   "rgba(220, 230, 255, 1)",
                    Hover:      "rgba(245, 250, 255, 1)",
                    Press:      "rgba(222, 240, 255, 1)"
                },
                Button:       {
                    Background: "rgba(219, 219, 219, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(255, 255, 255, 1)",
                    Selected:   "rgba(70, 140, 207, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            RushBCyka: {
                Foreground:   "rgba(76, 89, 69, 1)",
                Background:   "rgba(63, 70, 56, 1)",
                BorderLight:  "rgba(158, 162, 95, 1)",
                BorderDark:   "rgba(2, 40, 0, 1)",
                Font:         "Candara",
                FontLight:    "rgba(178, 182, 115, 1)",
                FontDark:     "rgba(206, 216, 200, 1)",
                FontDisabled: "rgba(160, 160, 160, 1)",
                Control:      {
                    Background: "rgba(63, 70, 56, 1)",
                    Selected:   "rgba(130, 140, 120, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(76, 89, 69, 1)"
                },
                Button:       {
                    Background: "rgba(76, 89, 69, 1)",
                    Selected:   "rgba(170, 170, 170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(55, 60, 50, 1)",
                    Selected:   "rgba(178, 182, 115, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            },
            H4ck3r:    {
                Foreground:   "rgba(90, 250, 40, 0.7)",
                Background:   "rgba(38, 38, 38, 0.5)",
                BorderLight:  "rgba(80, 195, 80, 1)",
                BorderDark:   "rgba(57, 140, 5, 1)",
                Font:         "Courier New",
                FontLight:    "rgba(0, 30, 0, 1)",
                FontDark:     "rgba(116, 235, 25, 1)",
                FontDisabled: "rgba(196, 253, 104, 1)",
                Control:      {
                    Background: "rgba(90, 250, 40, 0.1)",
                    Selected:   "rgba(46, 88, 14, 1)",
                    Hover:      "rgba(85, 95, 0, 1)",
                    Press:      "rgba(75, 95, 0, 1)"
                },
                Button:       {
                    Background: "rgba(90, 250, 40, 0.3)",
                    Selected:   "rgba(170,170,170, 1)",
                    Hover:      "rgba(153, 153, 153, 1)",
                    Press:      "rgba(119, 119, 119, 1)"
                },
                TextBox:      {
                    Background: "rgba(90, 250, 40, 0.2)",
                    Selected:   "rgba(255, 207, 50, 1)",
                    Error:      "rgba(255, 51, 0, 1)",
                    Disabled:   "rgba(153, 153, 153, 1)"
                }
            }
        },
        Status:  "Loading Colors"
    };
})();
vDesk.Load.Colors = vDesk.Colors;