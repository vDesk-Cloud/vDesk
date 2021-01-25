"use strict";
/**
 * Fired if the Color of the ColorPicker has been updated.
 * @event vDesk.Media.Drawing.ColorPicker#update
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'update' event.
 * @property {vDesk.Media.Drawing.ColorPicker} detail.sender The current instance of the ColorPicker.
 * @property {vDesk.Media.Drawing.Color} detail.color The current selected Color of the ColorPicker.
 */
/**
 * Initializes a new instance of the ColorPicker class.
 * @class Represents a visual control for picking colors.
 * @param {vDesk.Media.Drawing.Color} Color Initializes the ColorPicker with the specified Color.
 * @param {Number} [Mode=vDesk.Media.Drawing.ColorPicker.RGBA|vDesk.Media.Drawing.ColorPicker.HSLA|vDesk.Media.Drawing.ColorPicker.Hex] Initializes the ColorPicker with the specified mode.
 * @property {HTMLDivElement} Control Gets the underlying DOM-Node.
 * @property {vDesk.Media.Drawing.Color} Color Gets or sets the Color of the ColorPicker.
 * @property {Number} Mode Gets or sets the mode ColorPicker.
 * @memberOf vDesk.Media.Drawing
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Drawing.ColorPicker = function ColorPicker(
    Color = new vDesk.Media.Drawing.Color(0, 0, 0),
    Mode  = vDesk.Media.Drawing.ColorPicker.RGBA | vDesk.Media.Drawing.ColorPicker.HSLA | vDesk.Media.Drawing.ColorPicker.Hex
) {
    Ensure.Parameter(Color, vDesk.Media.Drawing.Color, "Color");

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Color:   {
            enumerable: true,
            get:        () => Color,
            set:        Value => {
                Ensure.Property(Value, vDesk.Media.Drawing.Color, "Color");
                Color = Value;
                window.requestAnimationFrame(UpdateHue);
            }
        },
        Value:   {
            enumerable: true,
            get:        () => this.Color,
            set:        Value => this.Color = Value
        },
        Mode:    {
            get: () => Mode,
            set: Value => {
                Ensure.Property(Value, Type.Number, "Mode");
                Mode = Value;
                while(Values.hasChildNodes()) {
                    Values.removeChild(Values.lastChild);
                }
                if(Value & vDesk.Media.Drawing.ColorPicker.RGB) {
                    Values.appendChild(Red.Control);
                    Values.appendChild(Green.Control);
                    Values.appendChild(Blue.Control);
                }
                if(Value & vDesk.Media.Drawing.ColorPicker.HSL) {
                    Values.appendChild(Hue.Control);
                    Values.appendChild(Saturation.Control);
                    Values.appendChild(Lightness.Control);
                }
                if(Value & vDesk.Media.Drawing.ColorPicker.Hex) {
                    Values.appendChild(Hex.Control);
                }
                if(Value & vDesk.Media.Drawing.ColorPicker.RGBA || Value & vDesk.Media.Drawing.ColorPicker.HSLA) {
                    Values.appendChild(Transparency.Control);
                }
            }
        }
    });

    /**
     * Updates the position of the 'hue'-indicator and draws a relation-curve into the saturation and lightness canvas.
     */
    const UpdateHue = function() {
        HueIndicator.style.left = `${Color.Hue}px`;
        ColorMap.forEach((Colors, Saturation) => Colors.forEach((HSL, Lightness) => {
            SaturationLightnessCanvasContext.fillStyle = `hsl(${Color.Hue},${Saturation}%,${100 - Lightness}%)`;
            SaturationLightnessCanvasContext.fillRect(Saturation, Lightness, 1, 1);
        }));
        window.requestAnimationFrame(UpdateSaturationLightness);
    };

    /**
     * Updates the position of the saturation and lightness indicator.
     */
    const UpdateSaturationLightness = function() {
        SaturationLightnessIndicator.style.left = `${Color.Saturation * 2}px`;
        SaturationLightnessIndicator.style.top = `${(Color.Lightness * 2 - 200) * -1}px`;
        window.requestAnimationFrame(UpdateSelection);
    };

    /**
     * Updates the color of the selection canvas and the values of the EditControls of the ColorPicker according the current selected Color.
     */
    const UpdateSelection = function() {
        SelectionCanvasContext.fillStyle = Color.ToRGBAString();
        SelectionCanvasContext.fillRect(0, 0, 50, 30);
        Red.Value = Color.Red;
        Green.Value = Color.Green;
        Blue.Value = Color.Blue;
        Hue.Value = Color.Hue;
        Saturation.Value = Color.Saturation;
        Lightness.Value = Color.Lightness;
        Hex.Value = Color.ToHexString();
        Transparency.Value = Color.Transparency;
    };

    /**
     * Eventhandler that listens on the 'mousedown' event.
     * @param {MouseEvent} Event
     */
    const OnMouseDownSaturationLightnessCanvas = Event => {
        ({
            left: OffsetLeftSL,
            top:  OffsetTopSL
        } = vDesk.Visual.TreeHelper.GetOffset(SaturationLightnessCanvas));
        StartPositionSLX = Event.pageX;
        StartPositionSLY = Event.pageY;
        Color.Lightness = (Math.min(Math.max(StartPositionSLY - OffsetTopSL - 2, 0), 200) / 2 - 100) * -1;
        Color.Saturation = Math.min(Math.max(StartPositionSLX - OffsetLeftSL - 2, 0), 200) / 2;
        UpdateSaturationLightness();
        window.addEventListener("mousemove", OnMouseMoveSaturationLightnessIndicator, true);
        window.addEventListener("mouseup", OnMouseUpSaturationLightnessIndicator, true);
    };

    /**
     * Eventhandler that listens on the 'mousedown' event.
     * @param {MouseEvent} Event
     */
    const OnMouseDownSaturationLightnessIndicator = Event => {
        StartPositionSLX = Event.pageX;
        StartPositionSLY = Event.pageY;
        window.addEventListener("mousemove", OnMouseMoveSaturationLightnessIndicator, true);
        window.addEventListener("mouseup", OnMouseUpSaturationLightnessIndicator, true);
    };

    /**
     * Eventhandler that listens on the 'mousemove' event.
     * @param {MouseEvent} Event
     */
    const OnMouseMoveSaturationLightnessIndicator = Event => {
        Color.Lightness = (Math.min(Math.max(Event.pageY - OffsetTopSL - 2, 0), 200) / 2 - 100) * -1;
        Color.Saturation = Math.min(Math.max(Event.pageX - OffsetLeftSL - 2, 0), 200) / 2;
        window.requestAnimationFrame(UpdateSaturationLightness);
    };

    /**
     * Eventhandler that listens on the 'mouseup' event.
     * @fires vDesk.Media.Drawing.ColorPicker#update
     */
    const OnMouseUpSaturationLightnessIndicator = () => {
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            color:  Color
        }).Dispatch(Control);
        window.removeEventListener("mousemove", OnMouseMoveSaturationLightnessIndicator, true);
        window.removeEventListener("mouseup", OnMouseUpSaturationLightnessIndicator, true);
    };

    /**
     * Eventhandler that listens on the 'mousedown' event.
     * @param {MouseEvent} Event The event.
     */
    const OnMouseDownHueCanvas = Event => {
        OffsetLeftH = vDesk.Visual.TreeHelper.GetOffset(HueCanvas).left;
        StartPositionH = Event.pageX;
        Color.Hue = Math.min(Math.max(StartPositionH - OffsetLeftH - 2, 0), 360);
        window.requestAnimationFrame(UpdateHue);
        window.addEventListener("mousemove", OnMouseMoveHueIndicator, true);
        window.addEventListener("mouseup", OnMouseUpHueIndicator, true);
    };

    /**
     * Eventhandler that listens on the 'mousedown' event.
     * @param {MouseEvent} Event The event.
     */
    const OnMouseDownHueIndicator = Event => {
        StartPositionH = Event.pageX;
        window.addEventListener("mousemove", OnMouseMoveHueIndicator, true);
        window.addEventListener("mouseup", OnMouseUpHueIndicator, true);
    };

    /**
     * Eventhandler that listens on the 'mousemove' event.
     * @param {MouseEvent} Event The event.
     */
    const OnMouseMoveHueIndicator = Event => {
        Color.Hue = Math.min(Math.max(Event.pageX - OffsetLeftH - 2, 0), 360);
        window.requestAnimationFrame(UpdateHue);
    };

    /**
     * Eventhandler that listens on the 'mouseup' event.
     * @fires vDesk.Media.Drawing.ColorPicker#update
     */
    const OnMouseUpHueIndicator = () => {
        new vDesk.Events.BubblingEvent("update", {
            sender: this,
            color:  Color
        }).Dispatch(Control);
        window.removeEventListener("mousemove", OnMouseMoveHueIndicator, true);
        window.removeEventListener("mouseup", OnMouseUpHueIndicator, true);
    };

    /**
     * Eventhandler that listens on the 'update' event.
     * @listens vDesk.Controls.EditControl#event:update
     * @param {CustomEvent} Event
     */
    const OnUpdate = Event => {
        Event.stopPropagation();
        switch(Event.detail.sender) {
            case Red:
                if(Red.Valid) {
                    Color.Red = Red.Value;
                }
                break;
            case Green:
                if(Green.Valid) {
                    Color.Green = Green.Value;
                }
                break;
            case Blue:
                if(Blue.Valid) {
                    Color.Blue = Blue.Value;
                }
                break;
            case Hue:
                if(Hue.Valid) {
                    Color.Hue = Hue.Value;
                }
                break;
            case Saturation:
                if(Saturation.Valid) {
                    Color.Saturation = Saturation.Value;
                }
                break;
            case Lightness:
                if(Lightness.Valid) {
                    Color.Lightness = Lightness.Value;
                }
                break;
            case Hex:
                if(Hex.Valid) {
                    Color = vDesk.Media.Drawing.Color.FromHexString(Hex.Value);
                }
                break;
            case Transparency:
                if(Transparency.Valid) {
                    Color.Transparency = Transparency.Value;
                }
        }
        window.requestAnimationFrame(UpdateHue);
    };

    /**
     * The Colors of the ColorPicker.
     * @type Array<vDesk.Media.Drawing.Color>
     * @ignore
     */
    const Colors = new Array(360);

    /**
     * The ColorMap of the ColorPicker.
     * @type Array<Array<Uint16Array>>
     * @ignore
     */
    const ColorMap = new Array(101);

    /**
     * The underlying ArrayBuffer of the ColorMap of the ColorPicker.
     * @type ArrayBuffer
     * @ignore
     */
    const ColorBuffer = new ArrayBuffer(61206);

    //Initialize ColorMap.
    for(let x = 0, i = 0; x < 101; x++) {
        ColorMap[x] = new Array(101);
        for(let y = 0; y < 101; y++) {
            ColorMap[x][y] = new Uint16Array(ColorBuffer, i, 3);
            i += 6;
        }
    }

    //Create colorspace [H: 0 - 360].
    for(let i = 0; i < 360; i++) {
        Colors[i] = vDesk.Media.Drawing.Color.FromHSL(i, 100, 50);
    }

    /**
     * The left offset of the saturation and lightness indicator.
     * @type {Number}
     */
    let OffsetLeftSL = 0;

    /**
     * The top offset of the saturation and lightness indicator.
     * @type {Number}
     */
    let OffsetTopSL = 0;

    /**
     * The horizontal startposition of the mouse according the saturation and lightness indicator.
     * @type {Number}
     */
    let StartPositionSLX = 0;

    /**
     * The vertical startposition of the mouse according the saturation and lightness indicator.
     * @type {Number}
     */
    let StartPositionSLY = 0;

    /**
     * The left offset of the 'hue'-indicator.
     * @type {Number}
     */
    let OffsetLeftH = 0;

    /**
     * The startposition of the mouse according the 'hue'-indicator.
     * @type {Number}
     */
    let StartPositionH = 0;

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "ColorPicker";

    /**
     * The saturation and lightness indicator of the ColorPicker.
     * @type {HTMLDivElement}
     */
    const SaturationLightnessIndicator = document.createElement("div");
    SaturationLightnessIndicator.className = "Indicator";
    SaturationLightnessIndicator.addEventListener("mousedown", OnMouseDownSaturationLightnessIndicator, false);

    /**
     * The saturation and lightness canvas of the ColorPicker.
     * @type HTMLCanvasElement
     */
    const SaturationLightnessCanvas = document.createElement("canvas");
    SaturationLightnessCanvas.className = "Canvas";
    SaturationLightnessCanvas.width = 100;
    SaturationLightnessCanvas.height = 100;
    SaturationLightnessCanvas.addEventListener("mousedown", OnMouseDownSaturationLightnessCanvas, false);

    /**
     * The saturation and lightness RenderingContext of the ColorPicker.
     * @type CanvasRenderingContext2D
     */
    const SaturationLightnessCanvasContext = SaturationLightnessCanvas.getContext("2d");

    /**
     * The saturation and lightness picker of the ColorPicker.
     * @type {HTMLDivElement}
     */
    const SaturationLightnessPicker = document.createElement("div");
    SaturationLightnessPicker.className = "SaturationLightness";
    SaturationLightnessPicker.appendChild(SaturationLightnessIndicator);
    SaturationLightnessPicker.appendChild(SaturationLightnessCanvas);

    /**
     * The selection canvas of the ColorPicker.
     * @type HTMLCanvasElement
     */
    const SelectionCanvas = document.createElement("canvas");
    SelectionCanvas.className = "Canvas";
    SelectionCanvas.width = 50;
    SelectionCanvas.height = 30;

    /**
     * The selection RenderingContext of the ColorPicker.
     * @type CanvasRenderingContext2D
     */
    const SelectionCanvasContext = SelectionCanvas.getContext("2d");

    /**
     * The red EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Red = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Red ?? "Red",
        null,
        Type.Number,
        Color.Red,
        {
            Min: 0,
            Max: 255
        }
    );
    Red.Control.classList.add("Red");

    /**
     * The green EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Green = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Green ?? "Green",
        null,
        Type.Number,
        Color.Green,
        {
            Min: 0,
            Max: 255
        }
    );
    Green.Control.classList.add("Green");

    /**
     * The blue EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Blue = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Blue ?? "Blue",
        null,
        Type.Number,
        Color.Blue,
        {
            Min: 0,
            Max: 255
        }
    );
    Blue.Control.classList.add("Blue");

    /**
     * The hue EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Hue = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Hue ?? "Hue",
        null,
        Type.Number,
        Color.Hue,
        {
            Min: 0,
            Max: 360
        }
    );
    Hue.Control.classList.add("Hue");

    /**
     * The saturation EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Saturation = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Saturation ?? "Saturation",
        null,
        Type.Number,
        Color.Saturation,
        {
            Min: 0,
            Max: 100
        }
    );
    Saturation.Control.classList.add("Saturation");

    /**
     * The lightness EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Lightness = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Lightness ?? "Lightness",
        null,
        Type.Number,
        Color.Lightness,
        {
            Min: 0,
            Max: 100
        }
    );
    Lightness.Control.classList.add("Lightness");

    /**
     * The hex EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Hex = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Color ?? "Color",
        null,
        Type.String,
        Color.ToHexString(),
        {Pattern: vDesk.Utils.Expression.ColorHexString}
    );
    Hex.Control.classList.add("Hex");

    /**
     * The transparency EditControl of the ColorPicker.
     * @type {vDesk.Controls.EditControl}
     */
    const Transparency = new vDesk.Controls.EditControl(
        vDesk?.Locale?.vDesk?.Transparency ?? "Transparency",
        null,
        Extension.Type.Range,
        Color.Transparency,
        {
            Min:   0,
            Max:   1,
            Steps: 0.1
        }
    );
    Transparency.Control.classList.add("Transparency");

    /**
     * The value list of the ColorPicker.
     * @type {HTMLUListElement}
     */
    const Values = document.createElement("ul");
    Values.className = "List Values Font";
    if(Mode & vDesk.Media.Drawing.ColorPicker.RGB) {
        Values.appendChild(Red.Control);
        Values.appendChild(Green.Control);
        Values.appendChild(Blue.Control);
    }
    if(Mode & vDesk.Media.Drawing.ColorPicker.HSL) {
        Values.appendChild(Hue.Control);
        Values.appendChild(Saturation.Control);
        Values.appendChild(Lightness.Control);
    }
    if(Mode & vDesk.Media.Drawing.ColorPicker.Hex) {
        Values.appendChild(Hex.Control);
    }
    if(Mode & vDesk.Media.Drawing.ColorPicker.RGBA || Mode & vDesk.Media.Drawing.ColorPicker.HSLA) {
        Values.appendChild(Transparency.Control);
    }

    Values.addEventListener("update", OnUpdate, false);
    /**
     * The selection of the ColorPicker.
     * @type {HTMLDivElement}
     */
    const Selection = document.createElement("div");
    Selection.className = "Selection";
    Selection.appendChild(SelectionCanvas);
    Selection.appendChild(Values);

    /**
     * The hue indicator of the ColorPicker.
     * @type {HTMLDivElement}
     */
    const HueIndicator = document.createElement("div");
    HueIndicator.className = "Indicator";
    HueIndicator.addEventListener("mousedown", OnMouseDownHueIndicator, false);

    /**
     * The hue canvas of the ColorPicker.
     * @type HTMLCanvasElement
     */
    const HueCanvas = document.createElement("canvas");
    HueCanvas.className = "Canvas";
    HueCanvas.width = 360;
    HueCanvas.height = 20;
    HueCanvas.addEventListener("mousedown", OnMouseDownHueCanvas, false);

    /**
     * The hue RenderingContext of the ColorPicker.
     * @type CanvasRenderingContext2D
     */
    const HueCanvasContext = HueCanvas.getContext("2d");
    Colors.forEach((Color, Index) => {
        HueCanvasContext.fillStyle = Color.ToHexString();
        HueCanvasContext.fillRect(Index, 0, 1, 20);
    });

    /**
     * The hue picker of the ColorPicker.
     * @type {HTMLDivElement}
     */
    const HuePicker = document.createElement("div");
    HuePicker.className = "Hue";
    HuePicker.appendChild(HueIndicator);
    HuePicker.appendChild(HueCanvas);

    Control.appendChild(SaturationLightnessPicker);
    Control.appendChild(Selection);
    Control.appendChild(HuePicker);

    window.requestAnimationFrame(UpdateHue);

};

vDesk.Media.Drawing.ColorPicker.RGB = 0b0001;
vDesk.Media.Drawing.ColorPicker.RGBA = vDesk.Media.Drawing.ColorPicker.RGB | vDesk.Media.Drawing.ColorPicker.Alpha;
vDesk.Media.Drawing.ColorPicker.HSL = 0b0010;
vDesk.Media.Drawing.ColorPicker.HSLA = vDesk.Media.Drawing.ColorPicker.HSL | vDesk.Media.Drawing.ColorPicker.Alpha;
vDesk.Media.Drawing.ColorPicker.Hex = 0b0100;
vDesk.Media.Drawing.ColorPicker.Alpha = 0b1000;