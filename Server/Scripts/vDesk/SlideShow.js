/**
 * Class representing a slideshow switching between multiple DOM-Nodes.
 */
class SlideShow {

    /**
     * Value representing SlideShows sliding in the left direction.
     * @type {String}
     */
    static Left = "Left";

    /**
     * Value representing SlideShows sliding in the right direction.
     * @type {String}
     */
    static Right = "Right";

    /**
     * The root Node of the SlideShow.
     * @type {null|HTMLElement}
     */
    Node = null;

    /**
     * The current visible slide.
     * @type {Element}
     */
    Current = null;

    /**
     * The slides of the SlideShow.
     * @type {Array<Element>}
     */
    Slides = [];

    /**
     * The animation duration of the SlideShow.
     * @type {number}
     */
    Duration = 1;

    /**
     * The delay between slides of the SlideShow.
     * @type {number}
     */
    Delay = 3;

    /**
     * Flag indicating whether the SlideShow is paused.
     * @type {Boolean}
     */
    Paused = false;

    /**
     * The controls Node of the SlideShow.
     * @type {HTMLDivElement}
     */
    Controls = document.createElement("div");
    /**
     * The previous button of the SlideShow.
     * @type {HTMLButtonElement}
     */
    PreviousButton = document.createElement("button");

    /**
     * The play-state button of the SlideShow.
     * @type {HTMLButtonElement}
     */
    PlayState = document.createElement("button");

    /**
     * The next button of the SlideShow.
     * @type {HTMLButtonElement}
     */
    NextButton = document.createElement("button");

    /**
     * Eventhandler that listens on the "animationend" event.
     */
    OnAnimationEnd = () => {
        if(this.Current.classList.contains("Hide")) {
            this.Current.classList.toggle("Hide", false);
            this.Current.classList.toggle("HideLeft", false);
            this.Current = this.Slides?.[this.Slides.indexOf(this.Current) + 1] ?? this.Slides[0];
            this.Current.classList.toggle("Show", true);
            this.Current.style.animationDelay = "0s";
        } else {
            this.Current.style.animationDelay = `${this.Delay}s`;
            this.Current.classList.toggle("Show", false);
            this.Current.classList.toggle("ShowLeft", false);
            this.Current.classList.toggle("Hide", true);
        }
    };

    /**
     * Eventhandler that listens on the "animationend" event.
     * @param {AnimationEvent} Event
     */
    OnAnimationEndNext = Event => {
        Event.stopPropagation();
        this.Current.classList.toggle("Hide", false);
        this.Current.style.animationDelay = `${this.Delay}s`;
        if(this.Paused) {
            this.Current.classList.toggle("Paused", this.Paused);
        }
        this.Node.addEventListener("animationend", this.OnAnimationEnd);
    };

    /**
     * Eventhandler that listens on the "animationend" event.
     * @param {AnimationEvent} Event
     */
    OnAnimationEndPrevious = Event => {
        Event.stopPropagation();
        this.Current.classList.toggle("HideLeft", false);
        this.Current.style.animationDelay = `${this.Delay}s`;
        if(this.Paused) {
            this.Current.classList.toggle("Paused", this.Paused);
        }
        this.Node.addEventListener("animationend", this.OnAnimationEnd);
    };

    /**
     * The current direction of the SlideShow.
     * @type {String}
     */
    _Direction = SlideShow.Left;

    get Direction() {
        return this._Direction;
    }

    set Direction(Value) {
        this._Direction = Value;
        this.Node.classList.toggle(SlideShow.Left, false);
        this.Node.classList.toggle(SlideShow.Right, false);
        this.Node.classList.toggle(Value, true);
    }

    /**
     * Initializes a new instance of the SlideShow class.
     * @param {HTMLElement} Node Initializes the SlideShow with the specified root Node.
     * @param {String} [Direction=SlideShow.Left] Initializes the SlideShow with the specified direction. This parameter has no effect, if the specified root Node already contains a class named "Left" or "Right".
     * @param {Number} [Duration = 1] Initializes the SlideShow with the specified animation duration.
     * @param {Number} [Delay] Initializes the SlideShow with the specified delay between slide animations.
     * @param {Boolean} [Controls=true] Flag indicating whether to display the controls of the SlideShow.
     */
    constructor(Node, Direction = SlideShow.Right, Duration = 1, Delay = 3, Controls = true) {
        this.Node = Node;
        this.PreviousButton.className = "Previous";
        this.PreviousButton.textContent = "<";
        this.PreviousButton.addEventListener("click", () => this.Previous());
        Node.appendChild(this.PreviousButton);

        this.PlayState.className = "PlayState";
        this.PlayState.textContent = "⏸";
        this.PlayState.addEventListener("click", () => this.Toggle());
        Node.appendChild(this.PlayState);

        this.NextButton.className = "Next";
        this.NextButton.textContent = ">";
        this.NextButton.addEventListener("click", () => this.Next());
        Node.appendChild(this.NextButton);

        if(!Node.classList.contains("Left") && !Node.classList.contains("Right")) {
            this.Direction = Direction;
        }

        this.Duration = Duration;
        this.Delay = Delay;

        this.Slides = Array.from(Node.getElementsByClassName("Slide"));
        this.Current = this.Slides[0];
        this.Current.classList.toggle("Show", true);
        this.Node.addEventListener("animationend", this.OnAnimationEnd);
    }

    Next() {
        this.Node.removeEventListener("animationend", this.OnAnimationEnd);
        this.Current.removeEventListener(
            "animationend",
            this.OnAnimationEndPrevious,
            {capture: true, once: true}
        );
        this.Current.classList.toggle("Hide", false);
        this.Current.classList.toggle("Show", false);
        this.Current.classList.toggle("ShowLeft", false);
        this.Current = this.Slides?.[this.Slides.indexOf(this.Current) + 1] ?? this.Slides[0];
        this.Current.addEventListener(
            "animationend",
            this.OnAnimationEndPrevious,
            {capture: true, once: true}
        );
        this.Current.style.animationDelay = "0s";
        this.Current.classList.toggle("ShowLeft", true);
        if(this.Paused) {
            this.Current.classList.toggle("Paused", !this.Paused);
        }
    }

    Previous() {
        this.Node.removeEventListener("animationend", this.OnAnimationEnd);
        this.Current.removeEventListener(
            "animationend",
            this.OnAnimationEndNext,
            {capture: true, once: true}
        );
        this.Current.classList.toggle("Hide", false);
        this.Current.classList.toggle("Show", false);
        this.Current.classList.toggle("ShowLeft", false);
        this.Current = this.Slides?.[this.Slides.indexOf(this.Current) - 1] ?? this.Slides[this.Slides.length - 1];
        this.Current.addEventListener(
            "animationend",
            this.OnAnimationEndNext,
            {capture: true, once: true}
        );
        this.Current.style.animationDelay = "0s";
        this.Current.classList.toggle("Show", true);
        if(this.Paused) {
            this.Current.classList.toggle("Paused", !this.Paused);
        }
    }

    Play() {
        this.Toggle(false);
    }

    Pause() {
        this.Toggle(true);
    }

    /**
     * Toggles the play-state of the SlideShow.
     * @param {Boolean} [Force] Optional flag indicating whether to pause or play the SlideShow.
     */
    Toggle(Force) {
        if(Force === undefined) {
            this.PlayState.textContent = this.PlayState.textContent === "⏸" ? "►" : "⏸";
            this.Slides.forEach(Slide => Slide.classList.toggle("Paused"));
            this.Paused = !this.Paused;
        } else {
            this.Paused = Force;
            this.PlayState.textContent = Force ? "►" : "⏸";
            this.Slides.forEach(Slide => Slide.classList.toggle("Paused", Force));
        }
    }

}