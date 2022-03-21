"use strict";
/**
 * Initializes a new instance of the Search class.
 * @class Represents a control for searching the metadata of Elements within the archive.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the Search.
 * @property {String} Icon Gets the icon of the Search.
 * @property {Array<vDesk.Controls.ToolBar.Group>} ToolBarGroups Gets the additional ToolBar Groups of the Search.
 * @memberOf vDesk.MetaInformation
 * @implements vDesk.Search.ICustomSearch
 * @author Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\MetaInformation
 */
vDesk.MetaInformation.Search = function Search() {

    Object.defineProperties(this, {
        Control:       {
            enumerable: true,
            get:        () => TabControl.Control
        },
        Title:         {
            enumerable: true,
            value:      vDesk.Locale.MetaInformation.MetaData
        },
        Icon:          {
            enumerable: true,
            value:      vDesk.Visual.Icons.MetaInformation.Search
        },
        ToolBarGroups: {
            enumerable: true,
            value:      []
        }
    });

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.MetaInformation.MaskList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelectMaskList = Event => {
        Search.Remove(DataSet.Control);
        DataSet = new vDesk.MetaInformation.DataSet(Event.detail.mask);
        Search.Add(DataSet.Control);
    };

    /**
     * Eventhandler that listens on the 'click' event.
     */
    const OnClick = () => {
        if(!DataSet.Rows.every(Row => Row.Value === null)){
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "MetaInformation",
                        Command:    "Search",
                        Parameters: {
                            ID:     DataSet.Mask.ID,
                            Values: DataSet.Rows
                                        .filter(Row => Row.Value !== null)
                                        .map(Row => ({
                                            ID:    Row.Row.ID,
                                            Value: Row.Value
                                        })),
                            All:    AllCheckBox.checked,
                            Strict: StrictCheckBox.checked
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status){
                        ResultList.Items = Response.Data.map(DataView => {
                            const Result = new vDesk.Search.Results[DataView.Type](DataView);
                            //Check if the result implements the IResult interface.
                            if(Result instanceof vDesk.Search.IResult){
                                return new vDesk.Search.ResultList.Item(Result);
                            }
                            //Otherwise use the default result.
                            return new vDesk.Search.ResultList.Item(new vDesk.Search.Result(DataView));
                        });
                        TabControl.CurrentTabItem = ResultTabItem;
                    }
                }
            );
        }
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Search.ResultList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelectResultList = Event => {
        new vDesk.Events.RoutedEvent("close").Dispatch(Preview.Control);
        Preview.Clear();
        Preview.Add(Event.detail.item.Result.Viewer);
    };

    /**
     * Eventhandler that listens on the 'open' event.
     * @listens vDesk.Search.ResultList#event:open
     * @param {CustomEvent} Event
     */
    const OnOpen = Event => Event.detail.item.Result.Open();

    /**
     * The TabControl of the Search.
     * @type {vDesk.Controls.TabControl}
     */
    const TabControl = new vDesk.Controls.TabControl();
    TabControl.Control.classList.add("MetaInformation");

    /**
     * The search options control of the Search.
     * @type {HTMLDivElement}
     */
    const SearchOptions = document.createElement("div");
    SearchOptions.className = "Options";

    /**
     * The search button of the Search.
     * @type {HTMLButtonElement}
     */
    const SearchButton = document.createElement("button");
    SearchButton.className = "Button Icon BorderDark Font Dark";
    SearchButton.textContent = vDesk.Locale.Search.Search;
    SearchButton.style.backgroundImage = `url("${vDesk.Visual.Icons.View}")`;
    SearchButton.addEventListener("click", OnClick, false);
    SearchOptions.appendChild(SearchButton);

    /**
     * The all label of the Search.
     * @type {HTMLSpanElement}
     */
    const AllLabel = document.createElement("span");
    AllLabel.className = "Label";
    AllLabel.textContent = vDesk.Locale.Search.StrictAccordance;
    AllLabel.title = vDesk.Locale.Search.StrictAccordanceTooltip;
    SearchOptions.appendChild(AllLabel);

    /**
     * The all checkbox of the Search.
     * @type {HTMLInputElement}
     */
    const AllCheckBox = document.createElement("input");
    AllCheckBox.type = "checkbox";
    AllCheckBox.className = "CheckBox All";
    AllCheckBox.title = AllLabel.title;
    SearchOptions.appendChild(AllCheckBox);

    /**
     * The strict label of the Search.
     * @type {HTMLSpanElement}
     */
    const StrictLabel = document.createElement("span");
    StrictLabel.className = "Label";
    StrictLabel.textContent = vDesk.Locale.Search.StrictComparison;
    StrictLabel.title = vDesk.Locale.Search.StrictComparisonTooltip;
    SearchOptions.appendChild(StrictLabel);

    /**
     * The strict checkbox of the Search.
     * @type {HTMLInputElement}
     */
    const StrictCheckBox = document.createElement("input");
    StrictCheckBox.type = "checkbox";
    StrictCheckBox.className = "CheckBox Strict";
    StrictCheckBox.title = StrictLabel.title;
    SearchOptions.appendChild(StrictCheckBox);

    /**
     * The MaskList of the DataSet.Search.
     * @type {vDesk.MetaInformation.MaskList}
     */
    const MaskList = vDesk.MetaInformation.MaskList.FromMasks();

    /**
     * The current DataSet of the Search.
     * @type {vDesk.MetaInformation.DataSet}
     */
    let DataSet = new vDesk.MetaInformation.DataSet(MaskList.Items?.[0]?.Mask ?? new vDesk.MetaInformation.Mask());

    /**
     * The search GroupBox of the Search.
     * @type {vDesk.Controls.GroupBox}
     */
    const Search = new vDesk.Controls.GroupBox(
        vDesk.Locale.Search.Module,
        [
            SearchOptions,
            MaskList.Control,
            DataSet.Control
        ]
    );
    Search.Control.classList.add("Search");
    Search.Control.addEventListener("select", OnSelectMaskList, false);

    /**
     * The search TabItem of the Search.
     * @type vDesk.Controls.TabControl.TabItem
     */
    const SearchTabItem = TabControl.Create(vDesk.Locale.Search.Module, Search.Control);

    /**
     * The results GroupBox of the Search.
     * @type {HTMLDivElement}
     */
    const Results = document.createElement("div");
    Results.className = "Results";

    /**
     * The ResultList of the Search.
     * @type vDesk.Search.ResultList
     */
    const ResultList = new vDesk.Search.ResultList();

    /**
     * The Elements GroupBox of the Search.
     * @type {vDesk.Controls.GroupBox}
     */
    const Elements = new vDesk.Controls.GroupBox(vDesk.Locale.Search.Results, [ResultList.Control]);
    Elements.Control.classList.add("Elements");
    Elements.Control.addEventListener("open", OnOpen, false);
    Elements.Control.addEventListener("select", OnSelectResultList, false);
    Results.appendChild(Elements.Control);

    /**
     * The preview GroupBox of the Search.
     * @type {vDesk.Controls.GroupBox}
     */
    const Preview = new vDesk.Controls.GroupBox(vDesk.Locale.Search.Preview);
    Preview.Control.classList.add("Preview");
    Results.appendChild(Preview.Control);

    /**
     * The result TabItem of the Search.
     * @type vDesk.Controls.TabControl.TabItem
     */
    const ResultTabItem = TabControl.Create(vDesk.Locale.Search.Results, Results);

};
vDesk.MetaInformation.Search.Implements(vDesk.Search.ICustomSearch);
vDesk.Search.Custom.MetaInformation = vDesk.MetaInformation.Search;