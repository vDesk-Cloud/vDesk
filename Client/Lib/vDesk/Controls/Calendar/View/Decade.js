"use strict";
/**
 * Initializes a new instance of the Decade class.
 * @class Represents a calendar-view for displaying the years of a decade.
 * @param {Adaptor} Adaptor The adaptor to the calendar within the view is hosted.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets or sets the title of the decade.
 * @property {Date} Date Gets or sets the current (displayed) date of the decade.
 * @property {vDesk.Controls.Calendar.Cell} Selected Gets or sets the current selected cell of the decade.
 * @property {vDesk.Controls.Calendar.Cell} Now Gets or sets the cell of the Decade representing the current year.
 * @property {Array<vDesk.Controls.Calendar.Cell>} Cells Gets the cells of the decade.
 * @implements {vDesk.Controls.Calendar.IView}
 * @memberOf vDesk.Controls.Calendar.View
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.View.Decade = function Decade(Adaptor) {

    /**
     * The current date of the Decade.
     * @type {null|Date}
     */
    let Date = null;

    Object.defineProperties(this, {
        Control:      {
            enumerable: true,
            get:        () => Control
        },
        Title:        {
            enumerable: true,
            get:        () => Title,
            set:        Value => {
                Ensure.Property(Value, Type.String, "Title");
                Title = Value;
            }
        },
        Date:         {
            enumerable: true,
            get:        () => Date,
            set:        Value => {
                Ensure.Property(Value, window.Date, "Title");
                Date = Value;
            }
        },
        Selected: {
            enumerable: true,
            get:        () => Selected,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Calendar.Cell, "Selected");
                Selected.Selected = false;
                Selected = Value;
                Selected.Selected = true;
            }
        },
        Now:      {
            enumerable: true,
            get:        () => Now,
            set:        Value => {
                Ensure.Property(Value, vDesk.Controls.Calendar.Cell, "Now");
                Now.Now = false;
                Now = Value;
                Now.Now = true;
            }
        },
        Cells:        {
            enumerable: true,
            get:        () => Cells
        }
    });

    /**
     * Displays the decade-timerange of a specified year.
     * @param {Date} Year The year to show the decade of.
     * @fires vDesk.Controls.Calendar.IView#datechanged
     */
    this.Show = function(Year) {
        Ensure.Parameter(Year, window.Date, "Year");

        //Clone the specified date and set the start date 5 years in the past for iteration.
        Date = Year.clone();
        Year.setFullYear(Year.getFullYear() - (Year.getFullYear() % 10) - 1);

        //Reset selection.
        Selected.Selected = false;
        Selected = null;

        //Reset today.
        if(Now !== null) {
            Now.Now = false;
            Now = null;
        }

        //Loop trough cells and update their dates.
        Cells.forEach(Cell => {
            //Update the date of the cell.
            Cell.Date = Year.clone();
            Cell.Text = Year.getFullYear().toString();

            //Check if the cell's date matches the current year.
            if(vDesk.Controls.Calendar.Today.getFullYear() === Year.getFullYear()) {
                Now = Cell;
                Now.Now = true;
            }

            //Select the given year.
            if(Date.getFullYear() === Year.getFullYear()) {
                Selected = Cell;
                Selected.Selected = true;
            }

            //Increment year for iteration.
            Year.setFullYear(Year.getFullYear() + 1);
        });

        //Set the title of the decade.
        Title = `${Cells[1].Date.getFullYear()} - ${Cells[10].Date.getFullYear()}`;

        //Notify the change of the view.
        new vDesk.Events.BubblingEvent("datechanged", {
            sender: this,
            date:   Date.clone()
        }).Dispatch(Control);

    };

    /**
     * Displays the years of the next decade.
     */
    this.Forward = function() {
        this.Show(Cells[Cells.length - 1].Date);
    };

    /**
     * Displays the years of the previous decade.
     */
    this.Backward = function() {
        this.Show(Cells[0].Date);
    };

    /**
     * Navigates one Cell up within the Decade.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Up = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the previous decade.
        if((Index - 3) <= 1) {
            Cells[Index].Date.setFullYear(Cells[Index].Date.getFullYear() - 4);
            Title = Cells[Index].Date.getFullYear();
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Cells[Index].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the previous cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index - 4];
            Selected.Selected = true;
        }
    };

    /**
     * Navigates one Cell down within the Decade.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Down = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the next decade.
        if((Index + 3) >= Cells.length - 2) {
            Cells[Index].Date.setFullYear(Cells[Index].Date.getFullYear() + 4);
            Title = Cells[Index].Date.getFullYear();
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Cells[Index].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the next cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index + 4];
            Selected.Selected = true;
        }
    };

    /**
     * Navigates one Cell left within the Decade.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Left = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the previous decade.
        if(Index === 1) {
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Cells[0].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the previous cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index - 1];
            Selected.Selected = true;
        }
    };

    /**
     * Navigates one Cell right within the Decade.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Right = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the next decade.
        if(Index >= Cells.length - 2) {
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Cells[Cells.length - 1].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the next cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index + 1];
            Selected.Selected = true;
        }
    };

    /**
     * The Cells of the Decade.
     * @type {Array<vDesk.Controls.Calendar.Cell>}
     */
    const Cells = [];

    /**
     * The title of the Decade.
     * @type {String}
     */
    let Title = "";

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Decade";

    //Setup rows and cells.
    for(let i = 0; i <= 2; i++) {
        const Row = document.createElement("ul");
        Row.className = "Row BorderLight";
        for(let a = 0; a <= 3; a++) {
            const Cell = new vDesk.Controls.Calendar.Cell();
            Cells.push(Cell);
            Cell.Type = "year";
            Row.appendChild(Cell.Control);
        }
        Control.appendChild(Row);
    }

    /**
     * The currently selected Cell of the Decade.
     * @type {vDesk.Controls.Calendar.Cell}
     */
    let Selected = Cells[0];
    Cells[0].Selected = true;

    /**
     * The Cell of the Decade representing the current year.
     * @type {vDesk.Controls.Calendar.Cell}
     */
    let Now = Cells[0];
    Cells[0].Now = true;

    //Mark the first and last cell as outer.
    Cells[0].Outer = true;
    Cells[11].Outer = true;
};

vDesk.Controls.Calendar.View.Decade.Implements(vDesk.Controls.Calendar.IView);