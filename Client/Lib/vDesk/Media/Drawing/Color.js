"use strict";
/**
 * Initializes a new instance of the Color class.
 * @class Represents a container for a color within the RGB-/HSL-colorspace.
 * @see https://en.wikipedia.org/wiki/RGB_color_model
 * @see https://en.wikipedia.org/wiki/HSL_and_HSV
 * @param {Number} [Red=0] Initializes the Color with the specified value for red.
 * @param {Number} [Green={vDesk.Media.Drawing.Color.MinValue}] Initializes the Color with the specified value for green.
 * @param {Number} [Blue=0] Initializes the Color with the specified value for blue.
 * @param Transparency
 * @property {Number} Red Gets or sets the 'Red' amount of the Color.
 * @property {Number} Green Gets or sets the 'Green' amount of the Color.
 * @property {Number} Blue Gets or sets the 'Blue' amount of the Color.
 * @property {Uint8ClampedArray} RGB Gets or sets the RGB-values of the Color.
 * Note: this is a convenience-property for avoiding multiple recalculations of the HSL-values.
 * @property {Number} Hue Gets or sets the Hue of the Color.
 * @property {Number} Saturation Gets or sets the Saturation of the Color.
 * @property {Number} Lightness Gets or sets the Lightness of the Color.
 * @property {Uint16Array} HSL Gets or sets the HSL-values of the Color.
 * @property {Number} Transparency Gets or sets the transparency of the Color.
 * Note: this is a convenience-property for avoiding multiple recalculations of the RGB-values.
 * @memberOf vDesk.Media.Drawing
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Media.Drawing.Color = function Color(Red = 0, Green = 0, Blue = 0, Transparency = 1) {

    /**
     * The rgb values of the Color.
     * @type {Uint8ClampedArray}
     */
    const RGBValues = Uint8ClampedArray.of(Red, Green, Blue);

    /**
     * The hsl values of the Color.
     * @type {Uint16Array}
     */
    const HSLValues = Uint16Array.of(...vDesk.Media.Drawing.Color.ConvertRGBToHSL(Red, Green, Blue));

    Object.defineProperties(this, {
        Red:          {
            get:        () => RGBValues[0],
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Red");
                RGBValues[0] = value;
                [HSLValues[0], HSLValues[1], HSLValues[2]] = vDesk.Media.Drawing.Color.ConvertRGBToHSL(...RGBValues);
            },
            enumerable: true
        },
        Green:        {
            get:        () => RGBValues[1],
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Green");
                RGBValues[1] = value;
                [HSLValues[0], HSLValues[1], HSLValues[2]] = vDesk.Media.Drawing.Color.ConvertRGBToHSL(...RGBValues);
            },
            enumerable: true
        },
        Blue:         {
            get:        () => RGBValues[2],
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Blue");
                RGBValues[2] = Value;
                [HSLValues[0], HSLValues[1], HSLValues[2]] = vDesk.Media.Drawing.Color.ConvertRGBToHSL(...RGBValues);
            },
            enumerable: true
        },
        RGB:          {
            get:        () => RGBValues,
            set:        Value => {
                Ensure.Property(Value, Uint8ClampedArray, "RGB");
                [RGBValues[0], RGBValues[1], RGBValues[2]] = Value;
                [HSLValues[0], HSLValues[1], HSLValues[2]] = vDesk.Media.Drawing.Color.ConvertRGBToHSL(...RGBValues);
            },
            enumerable: true
        },
        Hue:          {
            get:        () => HSLValues[0],
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Hue");
                HSLValues[0] = Value;
                [RGBValues[0], RGBValues[1], RGBValues[2]] = vDesk.Media.Drawing.Color.ConvertHSLToRGB(...HSLValues);
            },
            enumerable: true
        },
        Saturation:   {
            get:        () => HSLValues[1],
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Saturation");
                HSLValues[1] = Value;
                [RGBValues[0], RGBValues[1], RGBValues[2]] = vDesk.Media.Drawing.Color.ConvertHSLToRGB(...HSLValues);
            },
            enumerable: true
        },
        Lightness:    {
            get:        () => HSLValues[2],
            set:        Value => {
                Ensure.Property(Value, Type.Number, "Lightness");
                HSLValues[2] = Value;
                [RGBValues[0], RGBValues[1], RGBValues[2]] = vDesk.Media.Drawing.Color.ConvertHSLToRGB(...HSLValues);
            },
            enumerable: true
        },
        HSL:          {
            get:        () => HSLValues,
            set:        Value => {
                Ensure.Property(Value, Uint16Array, "HSL");
                [HSLValues[0], HSLValues[1], HSLValues[2]] = Value;
                [RGBValues[0], RGBValues[1], RGBValues[2]] = vDesk.Media.Drawing.Color.ConvertHSLToRGB(...HSLValues);
            },
            enumerable: true
        },
        Transparency: {
            get: () => Transparency,
            set: Value => {
                Ensure.Parameter(Value, Type.Number, "Transparency");
                Transparency = Value;
            }
        }
    });

};

Object.defineProperties(vDesk.Media.Drawing.Color, {
    /**
     * Describes the maximum numeric value of a color within a 8-bit colorchannel.
     * @constant
     * @type {Number}
     * @name vDesk.Media.Drawing.Color.MaxValue
     */
    MaxValue:        {
        value: 255
    },
    /**
     * Describes the minimum numeric value of a color within a 8-bit colorchannel.
     * @constant
     * @type {Number}
     * @name vDesk.Media.Drawing.Color.MinValue
     */
    MinValue:        {
        value: 0
    },
    /**
     * Describes the maximum numeric value of the color-angle(Hue) within the HSL-colorspace.
     * @constant
     * @type {Number}
     * @name vDesk.Media.Drawing.Color.MaxHue
     */
    MaxHue:          {
        value: 360
    },
    /**
     * Describes the maximum numeric value of the saturation within the HSL-colorspace.
     * @constant
     * @type {Number}
     * @name vDesk.Media.Drawing.Color.MaxSaturation
     */
    MaxSaturation:   {
        value: 100
    },
    /**
     * Describes the maximum numeric value of the lightness within the HSL-colorspace.
     * @constant
     * @type {Number}
     * @name vDesk.Media.Drawing.Color.MaxLightness
     */
    MaxLightness:    {
        value: 100
    },
    /**
     * Converts a RGB value to HSL.
     */
    ConvertRGBToHSL: {
        /**
         * Converts a RGB value to HSL.
         * @function
         * @name vDesk.Media.Drawing.Color.ConvertRGBToHSL
         * @param {Number} Red The value of the red-colorchannel.
         * @param {Number} Green The value of the green-colorchannel.
         * @param {Number} Blue The value of the blue-colorchannel.
         * @return {Uint16Array} The HSL representation of the RGB-values. [Hue, Saturation, Lightness].
         */
        value: function(Red, Green, Blue) {

            Red /= this.MaxValue;
            Green /= this.MaxValue;
            Blue /= this.MaxValue;
            const MaxValue = Math.max(Red, Green, Blue);
            const MinValue = Math.min(Red, Green, Blue);
            let Hue, Saturation, Lightness = (MaxValue + MinValue) / 2;
            const Delta = MaxValue - MinValue;

            if(Delta === 0) {
                Hue = 0;
                Saturation = 0;
            } else {
                Saturation = (Lightness > 0.5) ? Delta / (2 - MaxValue - MinValue) : Delta / (MaxValue + MinValue);
                switch(MaxValue) {
                    case Red:
                        Hue = (Green - Blue) / Delta + (Green < Blue ? 6 : 0);
                        break;
                    case Green:
                        Hue = (Blue - Red) / Delta + 2;
                        break;
                    case Blue:
                        Hue = (Red - Green) / Delta + 4;
                        break;
                }
                Hue /= 6;
            }
            return Uint16Array.of(Hue * this.MaxHue, Saturation * this.MaxSaturation, Lightness * this.MaxLightness);
        }
    },
    /**
     * Converts a HSL value to RGB.
     */
    ConvertHSLToRGB: {
        /**
         * Converts a HSL value to RGB.
         * @function
         * @name vDesk.Media.Drawing.Color.ConvertHSLToRGB
         * @param {type} Hue The value of the color-angle.
         * @param {type} Saturation The value of the saturation.
         * @param {type} Lightness The value of the lightness.
         * @return {Uint8ClampedArray|Array<Number>} The RGB representation of the HSL-values. [Red, Green, Blue].
         */
        value: function(Hue, Saturation, Lightness) {
            Hue /= this.MaxHue;
            Saturation /= this.MaxSaturation;
            Lightness /= this.MaxLightness;

            //Check if the color is a grayscale.
            if(Saturation === this.MinValue) {
                const Value = Lightness * this.MaxValue;
                return [Value, Value, Value];
            }
            const Temp2 = Lightness < 0.5
                        ? Lightness * (Saturation + 1)
                        : Lightness + Saturation - Lightness * Saturation;
            const Temp1 = 2 * Lightness - Temp2;
            return Uint8ClampedArray.of(
                Math.round(this.ConvertHueToRGB(Temp1, Temp2, Hue + 0.3333333333333333) * this.MaxValue),
                Math.round(this.ConvertHueToRGB(Temp1, Temp2, Hue) * this.MaxValue),
                Math.round(this.ConvertHueToRGB(Temp1, Temp2, Hue - 0.3333333333333333) * this.MaxValue)
            );
        }
    },
    ConvertHueToRGB: {
        value: function(Temp1, Temp2, Hue) {
            if(Hue < 0) {
                Hue += 1;
            } else if(Hue > 1) {
                Hue -= 1;
            }
            if(Hue < 0.16666666666666666) {
                return Temp1 + (Temp2 - Temp1) * 6 * Hue;
            }
            if(Hue < 0.5) {
                return Temp2;
            }
            if(Hue < 0.6666666666666666) {
                return Temp1 + (Temp2 - Temp1) * (0.6666666666666666 - Hue) * 6;
            }
            return Temp1;
        }
    },
    /**
     * Creates a new color from a set of specified HSL-Values.
     */
    FromHSL:         {
        /**
         * Creates a new color from a set of specified HSL-Values.
         * @function
         * @name vDesk.Media.Drawing.Color.FromHSL
         * @param {Number} Hue The hue of the color.
         * @param {Number} Saturation The saturation of the color.
         * @param {Number} Lightness The lightness of the color.
         * @return {vDesk.Media.Drawing.Color} The color yielding the specified values.
         */
        value: function(Hue, Saturation, Lightness) {
            return new vDesk.Media.Drawing.Color(...this.ConvertHSLToRGB(Hue, Saturation, Lightness));
        }
    },
    /**
     * Creates a new color from a specified RGB-String.
     */
    FromRGBString:   {
        /**
         * Creates a new color from a specified RGB-String.
         * @function
         * @name vDesk.Media.Drawing.Color.FromRGBString
         * @param {String} String The string that contains the RGB-Values.
         * @return {vDesk.Media.Drawing.Color} The color yielding the values according the specified RGB-String.
         */
        value: function(String) {
            const Result = /^rgb\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\)$/i.exec(String);
            return new vDesk.Media.Drawing.Color(...Result !== null ? Result.slice(1).map(Number) : [0, 0, 0]);
        }
    },

    /**
     * Creates a new color from a specified RGBA-String.
     */
    FromRGBAString:   {
        /**
         * Creates a new color from a specified RGBA-String.
         * @function
         * @name vDesk.Media.Drawing.Color.FromRGBAString
         * @param {String} String The string that contains the RGBA-Values.
         * @return {vDesk.Media.Drawing.Color} The color yielding the values according the specified RGBA-String.
         */
        value: function(String) {
            const Result = /^rgba\((\d{1,3})\,\s?(\d{1,3})\,\s?(\d{1,3})\,\s?(1|0?\.\d+)\)$/i.exec(String);
            return new vDesk.Media.Drawing.Color(...Result !== null ? Result.slice(1).map(Number) : [0, 0, 0, 1]);
        }
    },
    /**
     * Creates a new color from a specified Hex-String.
     */
    FromHexString:   {
        /**
         * Creates a new color from a specified Hex-String.
         * @function
         * @name vDesk.Media.Drawing.Color.FromHexString
         * @param {String} String The string that contains the hexadecimal value of the color.
         * @return {vDesk.Media.Drawing.Color} The color yielding the values according the specified Hex-String.
         */
        value: function(String) {
            const Result = vDesk.Utils.Expression.ColorHexString.exec(String);
            return new vDesk.Media.Drawing.Color(...Result !== null ? Result.slice(1).map(N => Number.parseInt(N, 16)) : [0, 0, 0]);
        }
    }
});

/**
 * Creates a string containing the RGB values of the Color.
 * @return {String} The RGB-String representation of the Color.
 */
vDesk.Media.Drawing.Color.prototype.ToRGBString = function() {
    return `rgb(${this.Red}, ${this.Green}, ${this.Blue})`;
};

/**
 * Creates a string containing the RGBA values of the Color.
 * @return {String} The RGB-String representation of the Color.
 */
vDesk.Media.Drawing.Color.prototype.ToRGBAString = function() {
    return `rgba(${this.Red}, ${this.Green}, ${this.Blue}, ${this.Transparency})`;
};

/**
 * Creates a string containing the HSL values of the Color.
 * @return {String} The HSL-String representation of the Color.
 */
vDesk.Media.Drawing.Color.prototype.ToHSLString = function() {
    return `hsl(${this.Hue}, ${this.Saturation}, ${this.Lightness})`;
};

/**
 * Creates a string containing the HSLA values of the Color.
 * @return {String} The HSLA-String representation of the Color.
 */
vDesk.Media.Drawing.Color.prototype.ToHSLAString = function() {
    return `hsla(${this.Hue}, ${this.Saturation}, ${this.Lightness}, ${this.Transparency})`;
};

/**
 * Creates a string containing the hexadecimal value of the Color.
 * @return {String} The hexadecimal-String representation of the Color.
 */
vDesk.Media.Drawing.Color.prototype.ToHexString = function() {
    return `#${((1 << 24) + (this.Red << 16) + (this.Green << 8) + this.Blue).toString(16).slice(1)}`;
};

/**
 * Calculates the euclidean distance among another Color.
 * @see https://en.wikipedia.org/wiki/Color_difference#Euclidean
 * @param {vDesk.Media.Drawing.Color} Color The Color to calcualte the distance to.
 * @return Number The euclidean distance among the specified Color.
 */
vDesk.Media.Drawing.Color.prototype.Distance = function(Color) {
    return Color !== this
           ? Math.sqrt((Color.Red - this.Red) ** 2 + (Color.Green - this.Green) ** 2 + (Color.Blue - this.Blue) ** 2)
           : 0;
};