"use strict";
/**
 * Initializes a new instance of the AudioPlayer class.
 * @class Plugin for playing audiofiles.
 * @param {vDesk.Archive.Element} Element The element to display the image of.
 * @property {HTMLDivElement} Control Gets the underlying dom node.
 * @memberOf vDesk.Archive.Element.View
 */
vDesk.Archive.Element.View.AudioPlayer = function AudioPlayer(Element) {
    Ensure.Parameter(Element, vDesk.Archive.Element, "Element");

    /**
     * The ID3Tag of the AudioPlayer.
     * @type vDesk.Media.Audio.ID3.Tag
     * @ignore
     */
    let ID3Tag = null;

    /**
     * The ID of the interval of the attribute scheduler.
     * @type {Number}
     * @ignore
     */
    let SchedulerID = null;

    Object.defineProperty(this, "Control", {
        get: () => Control
    });

    /**
     * Eventhandler that listens on the 'close' event and cancels the execution of the command if the AudioPlayer is being unloaded.
     * @listens vDesk.Controls.Window#event:close
     */
    const OnClose = () => {
        Command.Cancel();
        window.clearInterval(SchedulerID);
    };

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Viewer AudioPlayer";
    Control.addEventListener("close", OnClose, true);

    /**
     * The album-art container of the AudioPlayer.
     * @type {HTMLDivElement}
     */
    const ImageContainer = document.createElement("div");
    ImageContainer.className = "Container";

    /**
     * The album-art image of the AudioPlayer.
     * @type {HTMLImageElement}
     */
    const Image = document.createElement("img");
    Image.className = "Image";

    ImageContainer.appendChild(Image);

    /**
     * The attribute container of the AudioPlayer.
     * @type {HTMLDivElement}
     */
    const Attributes = document.createElement("div");
    Attributes.className = "Attributes Font Light";

    /**
     * The image control containing the data.
     * @type HTMLAudioElement
     */
    const Player = document.createElement("audio");
    Player.className = "Player";
    Player.loop = true;
    Player.controls = true;
    Player.autoplay = true;
    Player.style.cssText = "width: 100%;";

    Control.appendChild(ImageContainer);
    Control.appendChild(Attributes);
    Control.appendChild(Player);

    const Command = new vDesk.Modules.Command(
        {
            Module:     "Archive",
            Command:    "Download",
            Parameters: {ID: Element.ID},
            Ticket:     vDesk.User.Ticket
        }
    );
    vDesk.Connection.Send(
        Command,
        Buffer => {
            if(Command.Canceled) {
                return;
            }

            ID3Tag = vDesk.Media.Audio.ID3.Parse(Buffer);

            //Check if the ID3-Tag contains any frames.
            if(ID3Tag.Frames !== undefined) {

                const Frames = ID3Tag.Frames.filter(Frame => vDesk.Media.Audio.ID3.Frames[Frame.ID] !== undefined && Frame.Data.length > 0);

                let Index = 0;
                const CallBack = function() {
                    Index = (Index === Frames.length) ? 0 : Index;
                    Attributes.textContent = `${vDesk.Media.Audio.ID3.Frames[Frames[Index].ID]}: ${Frames[Index].Data}`;
                    Index++;
                };
                CallBack();
                SchedulerID = window.setInterval(CallBack, 4000);

                //Check if the audiofile has an attached picture.
                const PictureFrame = ID3Tag.Frames.find(Frame => Frame instanceof vDesk.Media.Audio.ID3.V2.PictureFrame);
                if(PictureFrame !== undefined) {
                    Image.src = URL.createObjectURL(PictureFrame.Data);
                }
            }

            //Create an objecturl from the binarydata.
            Player.src = URL.createObjectURL(new Blob([Buffer], {type: "audio/" + (Element.Extension === "mp3") ? "mpeg3" : Element.Extension}));
            Player.onload = () => {
                URL.revokeObjectURL(Player.src);
                URL.revokeObjectURL(Image.src);
            };
        },
        true);
};
/**
 * The file extensions the plugin can handle
 * @constant
 * @type {Array<String>}
 */
vDesk.Archive.Element.View.AudioPlayer.Extensions = ["mp3", "ogg", "wav", "flac"];