"use strict";
/**
 * Initializes a new instance of the Visual class.
 * @class Plugin for editing the 'look&feel' of the client.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the display-name of the Visual plugin.
 * @memberOf vDesk.Colors
 * @implements vDesk.Configuration.IPlugin
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 * @todo Evaluate providing a color-plugin-API instead of writing so much lines of code, so you'll just have to provide an object with configuration values in the simplest way.
 */
vDesk.Colors.Configuration = function Configuration() {

    //using
    const Color = vDesk.Media.Drawing.Color;

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale["Colors"]["Colors"]
        }
    });

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClickResetButton = () => {
        Foreground.Value = Color.FromRGBAString(vDesk.Colors.Default.Foreground);
        Background.Value = Color.FromRGBAString(vDesk.Colors.Default.Background);

        BorderLight.Value = Color.FromRGBAString(vDesk.Colors.Default.BorderLight);
        BorderDark.Value = Color.FromRGBAString(vDesk.Colors.Default.BorderDark);

        Font.Value = vDesk.Colors.Default.Font;
        FontLight.Value = Color.FromRGBAString(vDesk.Colors.Default.FontLight);
        FontDark.Value = Color.FromRGBAString(vDesk.Colors.Default.FontDark);
        FontDisabled.Value = Color.FromRGBAString(vDesk.Colors.Default.FontDisabled);

        ControlSelected.Value = Color.FromRGBAString(vDesk.Colors.Default.Control.Selected);
        ControlHover.Value = Color.FromRGBAString(vDesk.Colors.Default.Control.Hover);
        ControlPress.Value = Color.FromRGBAString(vDesk.Colors.Default.Control.Press);

        ButtonSelected.Value = Color.FromRGBAString(vDesk.Colors.Default.Button.Selected);
        ButtonHover.Value = Color.FromRGBAString(vDesk.Colors.Default.Button.Hover);
        ButtonPress.Value = Color.FromRGBAString(vDesk.Colors.Default.Button.Press);
        ButtonBackground.Value = Color.FromRGBAString(vDesk.Colors.Default.Button.Background);

        TextBoxSelected.Value = Color.FromRGBAString(vDesk.Colors.Default.TextBox.Selected);
        TextBoxError.Value = Color.FromRGBAString(vDesk.Colors.Default.TextBox.Error);
        TextBoxDisabled.Value = Color.FromRGBAString(vDesk.Colors.Default.TextBox.Disabled);

        vDesk.Colors.Reset();
    };

    /**
     * Eventhandler that listens on the 'click' event.
     * Sets the color of the client to the specified Color of the ColorPicker.
     */
    const OnClickConfirmButton = () => {
        vDesk.Colors.Foreground = Foreground.Value.ToRGBAString();
        vDesk.Colors.Background = Background.Value.ToRGBAString();

        vDesk.Colors.BorderLight = BorderLight.Value.ToRGBAString();
        vDesk.Colors.BorderDark = BorderDark.Value.ToRGBAString();

        vDesk.Colors.Font = Font.Value;
        vDesk.Colors.FontLight = FontLight.Value.ToRGBAString();
        vDesk.Colors.FontDark = FontDark.Value.ToRGBAString();
        vDesk.Colors.FontDisabled = FontDisabled.Value.ToRGBAString();

        vDesk.Colors.Control.Selected = ControlSelected.Value.ToRGBAString();
        vDesk.Colors.Control.Hover = ControlHover.Value.ToRGBAString();
        vDesk.Colors.Control.Press = ControlPress.Value.ToRGBAString();

        vDesk.Colors.Button.Selected = ButtonSelected.Value.ToRGBAString();
        vDesk.Colors.Button.Hover = ButtonHover.Value.ToRGBAString();
        vDesk.Colors.Button.Press = ButtonPress.Value.ToRGBAString();
        vDesk.Colors.Button.Background = ButtonBackground.Value.ToRGBAString();

        vDesk.Colors.TextBox.Selected = TextBoxSelected.Value.ToRGBAString();
        vDesk.Colors.TextBox.Error = TextBoxError.Value.ToRGBAString();
        vDesk.Colors.TextBox.Disabled = TextBoxDisabled.Value.ToRGBAString();

        vDesk.Colors.Save();
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "ColorsConfiguration";

    /**
     * The Colors of the Visual.
     * @type {HTMLDivElement}
     */
    const Colors = document.createElement("div");
    Colors.className = "Colors";
    Control.appendChild(Colors);

    /**
     * The foreground color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const Foreground = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Foreground"]}:`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Foreground)
    );

    /**
     * The background color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const Background = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Background"]}:`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Background)
    );

    /**
     * The light border color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const BorderLight = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Border"]} (${vDesk.Locale["Colors"]["Light"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.BorderLight)
    );

    /**
     * The dark border color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const BorderDark = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Border"]} (${vDesk.Locale["Colors"]["Dark"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.BorderDark)
    );

    /**
     * The colors GroupBox of the Visual plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const General = new vDesk.Controls.GroupBox(
        vDesk.Locale["Colors"]["Colors"],
        [
            Foreground.Control,
            Background.Control,
            BorderLight.Control,
            BorderDark.Control
        ]
    );
    Colors.appendChild(General.Control);

    /**
     * The font EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const Font = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Font"]}:`,
        null,
        Extension.Type.Enum,
        vDesk.Colors.Font,
        [
            "Arial",
            "Roboto",
            "Times New Roman",
            "Times",
            "Courier New",
            "Courier",
            "Verdana",
            "Georgia",
            "Palatino",
            "Garamond",
            "Bookman",
            "Comic Sans MS",
            "Candara",
            "Arial Black",
            "Impact"
        ]
    );

    /**
     * The light font color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const FontLight = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Color"]} (${vDesk.Locale["Colors"]["Light"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.FontLight)
    );

    /**
     * The dark font color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const FontDark = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Color"]} (${vDesk.Locale["Colors"]["Dark"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.FontDark)
    );

    /**
     * The disabled font color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const FontDisabled = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Color"]} (${vDesk.Locale["Colors"]["Disabled"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.FontDisabled)
    );

    /**
     * The font colors GroupBox of the Visual plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const Fonts = new vDesk.Controls.GroupBox(
        vDesk.Locale["Colors"]["Font"],
        [
            Font.Control,
            FontLight.Control,
            FontDark.Control,
            FontDisabled.Control
        ]
    );
    Colors.appendChild(Fonts.Control);

    /**
     * The selected control color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ControlSelected = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Control"]} (${vDesk.Locale["Colors"]["Selected"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Control.Selected)
    );

    /**
     * The hover control color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ControlHover = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Control"]} (${vDesk.Locale["Colors"]["Hover"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Control.Hover)
    );

    /**
     * The press control color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ControlPress = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Control"]} (${vDesk.Locale["Colors"]["Press"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Control.Press)
    );

    /**
     * The control colors GroupBox of the Visual plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const Controls = new vDesk.Controls.GroupBox(
        vDesk.Locale["Colors"]["Control"],
        [
            ControlSelected.Control,
            ControlHover.Control,
            ControlPress.Control
        ]
    );
    Colors.appendChild(Controls.Control);

    /**
     * The button selected color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ButtonSelected = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Button"]} (${vDesk.Locale["Colors"]["Selected"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Button.Selected)
    );

    /**
     * The button hover color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ButtonHover = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Button"]} (${vDesk.Locale["Colors"]["Hover"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Button.Hover)
    );

    /**
     * The button press color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ButtonPress = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Button"]} (${vDesk.Locale["Colors"]["Press"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Button.Press)
    );

    /**
     * The button background color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const ButtonBackground = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["Button"]} (${vDesk.Locale["Colors"]["Background"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.Button.Background)
    );

    /**
     * The button colors GroupBox of the Visual plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const Buttons = new vDesk.Controls.GroupBox(
        vDesk.Locale["Colors"]["Control"],
        [
            ButtonSelected.Control,
            ButtonHover.Control,
            ButtonPress.Control,
            ButtonBackground.Control
        ]
    );
    Colors.appendChild(Buttons.Control);

    /**
     * The TextBox selected color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const TextBoxSelected = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["TextBox"]} (${vDesk.Locale["Colors"]["Selected"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.TextBox.Selected)
    );

    /**
     * The TextBox error color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const TextBoxError = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["TextBox"]} (${vDesk.Locale["Colors"]["Error"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.TextBox.Error)
    );

    /**
     * The TextBox disabled color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const TextBoxDisabled = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["TextBox"]} (${vDesk.Locale["Colors"]["Disabled"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.TextBox.Disabled)
    );

    /**
     * The TextBox background color EditControl of the Visual plugin.
     * @type {vDesk.Controls.EditControl}
     */
    const TextBoxBackground = new vDesk.Controls.EditControl(
        `${vDesk.Locale["Colors"]["TextBox"]} (${vDesk.Locale["Colors"]["Background"]}):`,
        null,
        Extension.Type.Color,
        Color.FromRGBAString(vDesk.Colors.TextBox.Background)
    );

    /**
     * The TextBox colors GroupBox of the Visual plugin.
     * @type {vDesk.Controls.GroupBox}
     */
    const TextBoxes = new vDesk.Controls.GroupBox(
        vDesk.Locale["Colors"]["TextBox"],
        [
            TextBoxSelected.Control,
            TextBoxError.Control,
            TextBoxDisabled.Control,
            TextBoxBackground.Control
        ]
    );
    Colors.appendChild(TextBoxes.Control);

    /**
     * The reset button of the Visual-plugin.
     * @type {HTMLButtonElement}
     * @ignore
     */
    const ResetButton = document.createElement("button");
    ResetButton.className = "Button Icon BorderDark Font Dark";
    ResetButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Refresh}")`;
    ResetButton.textContent = vDesk.Locale["vDesk"]["Reset"];
    ResetButton.addEventListener("click", OnClickResetButton, false);

    /**
     * The confirm button of the Visual-plugin.
     * @type {HTMLButtonElement}
     * @ignore
     */
    const ConfirmButton = document.createElement("button");
    ConfirmButton.className = "Button Icon Icon BorderDark Font Dark";
    ConfirmButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Save || vDesk.Visual.Icons.Unknown}")`;
    ConfirmButton.textContent = vDesk.Locale["vDesk"]["Confirm"];
    ConfirmButton.addEventListener("click", OnClickConfirmButton, false);

    /**
     * The buttons container of the Visual plugin.
     * @type HTMLElement
     */
    const ConfirmReset = document.createElement("section");
    ConfirmReset.className = "ConfirmReset BorderLight";
    ConfirmReset.style.position = "absolute";
    ConfirmReset.style.bottom = "0";
    ConfirmReset.appendChild(ResetButton);
    ConfirmReset.appendChild(ConfirmButton);
    Control.appendChild(ConfirmReset);
};
vDesk.Colors.Configuration.Implements(vDesk.Configuration.IPlugin);
vDesk.Configuration.Local.Plugins.Colors = vDesk.Colors.Configuration;