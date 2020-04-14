"use strict";
/**
 * Initializes a new instance of the Year class.
 * @class Represents a calendar-view for displaying the months of a year.
 * @param {Adaptor} Adaptor The adaptor to the calendar within the view is hosted.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets or sets the title of the year.
 * @property {Date} Date Gets or sets the current (displayed) date of the year.
 * @property {vDesk.Controls.Calendar.Cell} Selected Gets or sets the current selected cell of the year.
 * @property {vDesk.Controls.Calendar.Cell} Now Gets or sets the cell of the year representing the current month.
 * @property {Array<vDesk.Controls.Calendar.Cell>} Cells Gets the cells of the year.
 * @implements {vDesk.Controls.Calendar.IView}
 * @memberOf vDesk.Controls.Calendar.View
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Controls.Calendar.View.Year = function Year(Adaptor) {

    /**
     * The current date of the Year.
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
     * Creates a title according to a specified date.
     * @param {Date} Date The date to create a title from.
     * @return {String} The formatted title according to the specified date.
     */
    const CreateTitle = Date => Date.getFullYear().toString();

    /**
     * Displays the months of a specified year.
     * @param {Date} Year The year to show the months of.
     * @fires vDesk.Controls.Calendar.IView#datechanged
     */
    this.Show = function(Year) {
        Ensure.Parameter(Year, window.Date, "Year");

        //Clone the specified date.
        Date = Year.clone();
        Year.setMonth(0);

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
            Cell.Text = Adaptor.MonthAsString(Year.getMonth());

            //Check if the month and year equals the current, so mark it as "today".
            if(vDesk.Controls.Calendar.Today.getMonth() === Year.getMonth()
               && vDesk.Controls.Calendar.Today.getFullYear() === Year.getFullYear()) {
                Now = Cell;
                Now.Now = true;
            }

            //Select the month of the given year.
            if(Date.getMonth() === Year.getMonth()) {
                Selected = Cell;
                Selected.Selected = true;
            }

            //Increment month for iteration.
            Year.setMonth(Year.getMonth() + 1);
        });

        //Set the title of the year.
        Title = CreateTitle(Date);

        //Notify the change of the view.
        new vDesk.Events.BubblingEvent("datechanged", {
            sender: this,
            date:   Date.clone()
        }).Dispatch(Control);
    };

    /**
     * Displays the months of the next year.
     */
    this.Forward = function() {
        Date.setFullYear(Date.getFullYear() + 1);
        Date.setMonth(0);
        this.Show(Date);
    };

    /**
     * Displays the months of the previous year.
     */
    this.Backward = function() {
        Date.setFullYear(Date.getFullYear() - 1);
        Date.setMonth(0);
        this.Show(Date);
    };

    /**
     * Navigates one cell up within the year.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Up = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the previous year.
        if((Index - 3) <= 0) {
            Cells[Index].Date.setMonth(Cells[Index].Date.getMonth() - 4);
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
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * Navigates one cell down within the year.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Down = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the next year.
        if((Index + 3) >= Cells.length - 1) {
            Cells[Index].Date.setMonth(Cells[Index].Date.getMonth() + 4);
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
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * Navigates one cell left within the year.
     * @fires vDesk.Controls.Calendar.IView#previous
     */
    this.Left = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the previous year.
        if(Index === 0) {
            Cells[Index].Date.setMonth(Selected.Date.getMonth() - 1);
            new vDesk.Events.BubblingEvent("previous", {
                sender: this,
                date:   Cells[Index].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the previous cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index - 1];
            Selected.Selected = true;
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * Navigates one cell right within the year.
     * @fires vDesk.Controls.Calendar.IView#next
     */
    this.Right = function() {
        const Index = Cells.indexOf(Selected);
        //Check if the cell is in the next year.
        if(Index === Cells.length - 1) {
            Cells[Index].Date.setMonth(Selected.Date.getMonth() + 1);
            new vDesk.Events.BubblingEvent("next", {
                sender: this,
                date:   Cells[Index].Date.clone()
            }).Dispatch(Control);
        }
        //Else select the next cell.
        else {
            Selected.Selected = false;
            Selected = Cells[Index + 1];
            Selected.Selected = true;
            Title = CreateTitle(Selected.Date);
        }
    };

    /**
     * The Cells of the year.
     * @type {Array<vDesk.Controls.Calendar.Cell>}
     */
    const Cells = [];

    /**
     * The title of the Year.
     * @type {String}
     */
    let Title = "";

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Year";

    //Setup rows and cells.
    for(let i = 0; i <= 2; i++) {
        const Row = document.createElement("ul");
        Row.className = "Row BorderLight";
        for(let a = 0; a <= 3; a++) {
            const Cell = new vDesk.Controls.Calendar.Cell();
            Cells.push(Cell);
            Cell.Type = "month";
            Row.appendChild(Cell.Control);
        }
        Control.appendChild(Row);
    }

    /**
     * The currently selected cell of the Year.
     * @type {vDesk.Controls.Calendar.Cell}
     */
    let Selected = Cells[0];
    Cells[0].Selected = true;

    /**
     * The cell of the year representing the current month.
     * @type {vDesk.Controls.Calendar.Cell}
     */
    let Now = Cells[0];
    Cells[0].Now = true;
};

vDesk.Controls.Calendar.View.Year.Implements(vDesk.Controls.Calendar.IView);