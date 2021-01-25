"use strict";
/**
 * Fired if the Window is being closed. This is a routed event.
 * @event vDesk.Controls.Window#close
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'close' event.
 * @property {vDesk.Controls.Window} detail.sender The current instance of the Window.
 */
/**
 * Fired if the Window has been focused.
 * @event vDesk.Controls.Window#focus
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'focus' event.
 * @property {vDesk.Controls.Window} detail.sender The current instance of the Window.
 */
/**
 * Fired if the Window has been minimized.
 * @event vDesk.Controls.Window#minimize
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'minimize' event.
 * @property {vDesk.Controls.Window} detail.sender The current instance of the Window.
 */
/**
 * Fired if the Window has been maximized.
 * @event vDesk.Controls.Window#maximize
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'maximize' event.
 * @property {vDesk.Controls.Window} detail.sender The current instance of the Window.
 */
/**
 * Fired if the Window has been displayed.
 * @event vDesk.Controls.Window#show
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'show' event.
 * @property {vDesk.Controls.Window} detail.sender The current instance of the Window.
 */
/**
 * Fired if the Window has been hidden.
 * @event vDesk.Controls.Window#hide
 * @type {CustomEvent}
 * @property {Object} detail The arguments of the 'hide' event.
 * @property {vDesk.Controls.Window} detail.sender The current instance of the Window.
 */
/**
 * Initializes a new instance of the Window class.
 * @class Represents a movable and resizeable Window.
 * @param {String} [Icon=""] Initializes the Window with the specified icon.
 * @param {String} [Title=""] Initializes the Window with the specified title.
 * @param {Node|HTMLElement|DocumentFragment} [Content=null] Initializes the Window with the specified content.
 * @param {Boolean} [Modal=false] Determines whether the Window acts as a modal dialog.
 * @param {Number} [Height=600] Initializes the Window with the specified initial height.
 * @param {Number} [Width=800] Initializes the Window with the specified initial width.
 * @param {Number} [Top=0] Initializes the DynamicBox with the specified initial top offset.
 * @param {Number} [Left=0] Initializes the DynamicBox with the specified initial left offset.
 * @property {Boolean} Modal Gets or sets a value indicating whether the Window will block further mouse interactions.
 * @property {Boolean} Maximized Gets or sets a value indicating whether the Window is maximized.
 * @property {Boolean} Minimized Gets or sets a value indicating whether the Window is minimized.
 * @property {Boolean} Visible Gets a value indicating whether the Window is currently open.
 * @memberOf vDesk.Controls
 * @augments vDesk.Controls.DynamicBox
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Window = function Window(
    Icon    = "",
    Title   = "",
    Content = null,
    Modal   = false,
    Height  = 600,
    Width   = 800,
    Top     = 150,
    Left    = 250
) {
    Ensure.Parameter(Icon, Type.String, "Icon");
    Ensure.Parameter(Title, Type.String, "Title");
    Ensure.Parameter(Modal, Type.Boolean, "Modal");
    this.Extends(
        vDesk.Controls.DynamicBox,
        Content,
        null,
        Height,
        Width,
        Top,
        Left,
        {
            Top:    0,
            Left:   -(Width / 2),
            Right:  -(Width / 2),
            Bottom: -(Height - 30),
            Height: {
                Min: 200,
                Max: window.innerHeight
            },
            Width: {
                Min: 300,
                Max: window.innerWidth
            }
        }
    );

    /**
     * The overlay if he Window is modal.
     * @type HTMLElement
     */
    let ModalOverlay = null;

    /**
     * Flag indicating whether the Window is maximized.
     * @type {Boolean}
     */
    let Maximized = false;

    /**
     * Flag indicating whether the Window is minimized.
     * @type {Boolean}
     */
    let Minimized = false;

    Object.defineProperties(this, {
        //Override parents stackorder.
        StackOrder: {
            enumerable: true,
            get:        () => this.Parent.StackOrder,
            set:        Value => {
                this.Parent.StackOrder = Value;
                TitleSpan.style.zIndex = Value + 3;
                CloseButton.style.zIndex = Value + 3;
                if(Modal) {
                    ModalOverlay.style.zIndex = Value - 1;
                }
            }
        },
        Icon:       {
            enumerable: true,
            get:        () => IconImage.src,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Icon");
                IconImage.src = Value;
            }
        },
        Title:      {
            enumerable: true,
            get:        () => TitleSpan.textContent,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                TitleSpan.textContent = Value;
            }
        },
        Modal:      {
            enumerable: true,
            get:        () => Modal,
            set:        Value => {
                Ensure.Property(Value, Type.Boolean, "Modal");
                Modal = Value;
                this.Control.classList.toggle("Modal", Value);
            }
        },
        Maximized:  {
            enumerable: true,
            get:        () => Maximized
        },
        Minimized:  {
            enumerable: true,
            get:        () => Minimized
        },
        Visible:    {
            enumerable: true,
            get:        () => this.Control.parentNode === document.body
        }
    });

    /**
     * Eventhandler that listens on the 'mousedown' event and sets the stackorder temporarily to 1000.
     * @fires vDesk.Controls.Window#focus
     */
    const OnMouseDown = () => new vDesk.Events.BubblingEvent("focus", {sender: this}).Dispatch(this.Control);

    /**
     * Eventhandler that listens on the 'close' event and that closes the Window.
     * @param {CustomEvent} Event
     * @listens vDesk.Controls.Window#event:close
     */
    const OnClose = Event => {
        Event.Cancel();
        vDesk.Visual.Animation.FadeOut(this.Control, 500, () => document.body.removeChild(this.Control));
        if(Modal) {
            ModalOverlay.removeEventListener("click", Blink);
            document.body.removeChild(ModalOverlay);
        }
    };

    /**
     * Toggles the size of the Window between maximized and original size.
     */
    const ToggleSize = () => {
        if(Maximized) {
            this.Restore();
        } else {
            this.Maximize();
        }
    };

    /**
     * Provides visual feedback if the Window is modal and the user clicked outside of the Window.
     */
    const Blink = () => {
        const Color = vDesk.Colors.Foreground;
        let Count = 0;
        const ID = setInterval(() => {
            this.Control.style.backgroundColor = (Count % 2) === 0 ? "rgb(255,255,255)" : Color;
            Count++;
            if(Count === 6) {
                clearInterval(ID);
                this.Control.style.backgroundColor = Color;
            }
        }, 100);
    };

    /**
     * Closes the Window.
     * @fires vDesk.Controls.Window#close
     */
    this.Close = () => new vDesk.Events.RoutedEvent("close", {sender: this}, true, true).Dispatch(this.Content);

    /**
     * Maximizes the Window according to the size of the client.
     * @fires vDesk.Controls.Window#maximize
     */
    this.Maximize = () => {
        if(!Maximized) {
            Top = this.Top;
            Left = this.Left;
            Height = this.Height;
            Width = this.Width;

            vDesk.Visual.Animation.Resize(this.Control, Height, window.innerHeight - 50, Width, window.innerWidth - 2, 300);
            vDesk.Visual.Animation.Animate(this.Control, "top", Top, 0, 300);
            vDesk.Visual.Animation.Animate(this.Control, "left", Left, 0, 300);

            MaximizeRestoreButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Restore}")`;
            MaximizeRestoreButton.title = vDesk.Locale.vDesk.Restore;

            this.Resizable = false;
            this.Movable = false;
            new vDesk.Events.BubblingEvent("maximize", {sender: this}).Dispatch(this.Control);
        }
        Maximized = true;
        Minimized = false;
    };

    /**
     * Minimizes the Window.
     * @fires vDesk.Controls.Window#minimize
     */
    this.Minimize = () => {
        if(!Modal) {
            if(!Minimized) {
                Top = this.Top;
                Left = this.Left;
                Height = this.Height;
                Width = this.Width;

                vDesk.Visual.Animation.Resize(this.Control, Height, 0, Width, 0, 300);
                vDesk.Visual.Animation.Animate(this.Control, "top", Top, window.innerHeight, 300);
                vDesk.Visual.Animation.Animate(this.Control, "left", Left, 0, 300);
                vDesk.Visual.Animation.FadeOut(this.Control, 300);

                MaximizeRestoreButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Maximize}")`;
                MaximizeRestoreButton.title = vDesk.Locale.vDesk.Maximize;

                new vDesk.Events.BubblingEvent("minimize", {sender: this}).Dispatch(this.Control);
            }
            Maximized = false;
        }
        Minimized = true;
    };

    /**
     * Restores the original state of the window.
     */
    this.Restore = () => {
        if(Maximized) {
            vDesk.Visual.Animation.Resize(this.Control, window.innerHeight, Height, window.innerWidth, Width, 300);
            vDesk.Visual.Animation.Animate(this.Control, "top", 0, Top, 300);
            vDesk.Visual.Animation.Animate(this.Control, "left", 0, Left, 300);

            MaximizeRestoreButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Maximize}")`;
            MaximizeRestoreButton.title = vDesk.Locale.vDesk.Maximize;

            Maximized = false;
        } else if(Minimized) {
            vDesk.Visual.Animation.Animate(this.Control, "top", window.innerHeight, Top, 300);
            vDesk.Visual.Animation.Animate(this.Control, "left", 0, Left, 300);
            vDesk.Visual.Animation.Pop(this.Control, Height, Width, 300);
            Minimized = false;
        }
        this.Resizable = true;
        this.Movable = true;
    };

    /**
     * Displays the Window.
     * @fires vDesk.Controls.Window#show
     */
    this.Show = function() {
        window.document.body.appendChild(this.Control);
        vDesk.Visual.Animation.FadeIn(this.Control, 500);
        if(Modal) {
            ModalOverlay = document.createElement("div");
            ModalOverlay.style.cssText = "position: fixed; width: 100%; height: 100%; z-index: " + (this.StackOrder - 1) + " ; top: 0px; left: 0px;";
            ModalOverlay.addEventListener("click", Blink, false);
            document.body.appendChild(ModalOverlay);
        }
        //new vDesk.Events.RoutedEvent("show", {sender: this}, false, true).Dispatch(this.Content);
        new vDesk.Events.BubblingEvent("show", {sender: this}).Dispatch(this.Control);
    };

    /**
     * Hides the Window.
     */
    this.Hide = () => {
        window.document.body.removeChild(this.Control);
        if(Modal) {
            document.body.removeChild(ModalOverlay);
        }
        new vDesk.Events.RoutedEvent("hide", {sender: this}, false, true).Dispatch(this.Content);
    };

    this.Control.classList.add("Window");
    this.Control.classList.toggle("Modal", Modal);
    this.Control.addEventListener("close", OnClose, false);
    this.Control.addEventListener("mousedown", OnMouseDown, false);
    this.Control.style.opacity = "0";

    /**
     * The icon image of the Window.
     * @type {HTMLImageElement}
     */
    const IconImage = document.createElement("img");
    IconImage.className = "Icon";
    IconImage.src = Icon;

    /**
     * The title span of the Window.
     * @type {HTMLSpanElement}
     */
    const TitleSpan = document.createElement("span");
    TitleSpan.className = "Title Font Light";
    TitleSpan.textContent = Title;

    /**
     * The minimize button of the Window.
     * @type {HTMLButtonElement}
     */
    const MinimizeButton = document.createElement("button");
    MinimizeButton.className = "Button Minimize";
    MinimizeButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Minimize}")`;
    MinimizeButton.title = vDesk.Locale.vDesk.Minimize;
    MinimizeButton.addEventListener("click", this.Minimize, false);

    /**
     * The maximize/restore button of the Window.
     * @type {HTMLButtonElement}
     */
    const MaximizeRestoreButton = document.createElement("button");
    MaximizeRestoreButton.className = "Button MaximizeRestore";
    MaximizeRestoreButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Maximize}")`;
    MaximizeRestoreButton.title = vDesk.Locale.vDesk.Maximize;
    MaximizeRestoreButton.addEventListener("click", ToggleSize, false);

    /**
     * The close button of the Window.
     * @type {HTMLButtonElement}
     */
    const CloseButton = document.createElement("button");
    CloseButton.className = "Button Close";
    CloseButton.style.backgroundImage = `url("${vDesk.Visual.Icons.Close}")`;
    CloseButton.title = vDesk.Locale.vDesk.Close;
    CloseButton.addEventListener("click", this.Close, false);

    this.Header.classList.add("Foreground");
    this.Header.appendChild(IconImage);
    this.Header.appendChild(TitleSpan);
    this.Header.appendChild(CloseButton);
    this.Header.appendChild(MaximizeRestoreButton);
    this.Header.appendChild(MinimizeButton);

    this.Header.addEventListener("dblclick", ToggleSize, false);

    this.Content.classList.add("Background");
};