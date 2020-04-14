/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


vDesk.Utils.Encoding = {

    UTF8: {
        Encode: function () {

        },
        Decode: function () {
            var ix = 0;
            maxBytes = Math.min(maxBytes || bytes.length, bytes.length);

            if (bytes[0] == 0xEF && bytes[1] == 0xBB && bytes[2] == 0xBF) {
                ix = 3;
            }

            var arr = [];
            for (var j = 0; ix < maxBytes; j++) {
                var byte1 = bytes[ix++];
                if (byte1 == 0x00) {
                    break;
                } else if (byte1 < 0x80) {
                    arr[j] = String.fromCharCode(byte1);
                } else if (byte1 >= 0xC2 && byte1 < 0xE0) {
                    var byte2 = bytes[ix++];
                    arr[j] = String.fromCharCode(((byte1 & 0x1F) << 6) + (byte2 & 0x3F));
                } else if (byte1 >= 0xE0 && byte1 < 0xF0) {
                    var byte2 = bytes[ix++];
                    var byte3 = bytes[ix++];
                    arr[j] = String.fromCharCode(((byte1 & 0xFF) << 12) + ((byte2 & 0x3F) << 6) + (byte3 & 0x3F));
                } else if (byte1 >= 0xF0 && byte1 < 0xF5) {
                    var byte2 = bytes[ix++];
                    var byte3 = bytes[ix++];
                    var byte4 = bytes[ix++];
                    var codepoint = ((byte1 & 0x07) << 18) + ((byte2 & 0x3F) << 12) + ((byte3 & 0x3F) << 6) + (byte4 & 0x3F) - 0x10000;
                    arr[j] = String.fromCharCode(
                            (codepoint >> 10) + 0xD800,
                            (codepoint & 0x3FF) + 0xDC00
                            );
                }
            }
            var string = new String(arr.join(""));
            string.bytesReadCount = ix;
            return string;
        }
    }


};