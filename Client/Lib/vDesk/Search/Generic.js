"use strict";
/**
 * Initializes a new instance of the Generic class.
 * @class Represents a generic control for searching data in the system.
 * @property {HTMLElement} Control Gets the underlying DOM-Node.
 * @property {String} Title Gets the title of the generic-search.
 * @property {String} Icon Gets the icon of the generic-search.
 * @property {Array<vDesk.Controls.ToolBar.Group>} ToolBarGroups Gets the additional toolbargroups of the generic-search.
 * @implements vDesk.Search.ICustomSearch
 * @memberOf vDesk.Search
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
vDesk.Search.Generic = function Generic() {

    /**
     * The previous searched value of the Generic search.
     * @type {String}
     */
    let PreviousSearchValue = "";

    /**
     * The current selected filters of the Generic search.
     * @type {Array<Object>}
     */
    const SelectedFilters = [];

    Object.defineProperties(this, {
        Control:       {
            enumerable: true,
            get:        () => Control
        },
        Title:         {
            enumerable: true,
            value:      vDesk.Locale.Search.Module
        },
        Icon:          {
            enumerable: true,
            value:      vDesk.Visual.Icons.View
        },
        ToolBarGroups: {
            enumerable: true,
            get:        () => [FilterToolBarGroup]
        }
    });

    /**
     * Eventhandler that listens on the 'input' event.
     * @todo Parse value for patterns.
     */
    const OnInput = () => {
        if(SearchFieldTextBox.value.length > 2 && PreviousSearchValue !== SearchFieldTextBox.value) {
            vDesk.Connection.Send(
                new vDesk.Modules.Command(
                    {
                        Module:     "Search",
                        Command:    "Search",
                        Parameters: {
                            //@todo Pass values as array.
                            Value:   SearchFieldTextBox.value,
                            Filters: SelectedFilters.map(Filter => {
                                return {
                                    Module: Filter.Module,
                                    Name:   Filter.Name
                                };
                            })
                        },
                        Ticket:     vDesk.Security.User.Current.Ticket
                    }
                ),
                Response => {
                    if(Response.Status) {
                        ResultList.Items = Response.Data.map(DataView => {
                            const Result = new vDesk.Search.Results[DataView.Type](DataView);
                            //Check if the result implements the IResult interface.
                            if(Result instanceof vDesk.Search.IResult) {
                                return new vDesk.Search.ResultList.Item(Result);
                            }
                            //Otherwise use the default result.
                            return new vDesk.Search.ResultList.Item(new vDesk.Search.Result(DataView));
                        });
                    }
                }
            );
        }
        PreviousSearchValue = SearchFieldTextBox.value;
    };

    /**
     * Eventhandler that listens on the 'select' event.
     * @listens vDesk.Search.ResultList#event:select
     * @param {CustomEvent} Event
     */
    const OnSelect = Event => {
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
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Generic";
    Control.addEventListener("select", OnSelect, false);
    Control.addEventListener("open", OnOpen, false);

    /**
     * The search column of the Generic search.
     * @type {HTMLDivElement}
     */
    const Search = document.createElement("div");
    Search.className = "Search";
    Control.appendChild(Search);

    /**
     * The search field GroupBox of the Generic search.
     * @type {vDesk.Controls.GroupBox}
     */
    const Values = new vDesk.Controls.GroupBox(vDesk.Locale.Search.Module);
    Values.Control.classList.add("Values");
    Search.appendChild(Values.Control);

    /**
     * The search field description span of the Generic search.
     * @type {HTMLSpanElement}
     */
    const SearchFieldDescription = document.createElement("span");
    SearchFieldDescription.className = "Description";
    SearchFieldDescription.textContent = vDesk.Locale.Search.SearchField;

    /**
     * The search field TextBox of the Generic search.
     * @type {HTMLInputElement}
     */
    const SearchFieldTextBox = document.createElement("input");
    SearchFieldTextBox.className = "TextBox BorderDark Font Dark Background";
    SearchFieldTextBox.type = "text";
    SearchFieldTextBox.addEventListener("input", OnInput, false);

    Values.Add(SearchFieldDescription);
    Values.Add(SearchFieldTextBox);

    /**
     * The search result GroupBox of the Generic search.
     * @type {vDesk.Controls.GroupBox}
     */
    const Results = new vDesk.Controls.GroupBox(vDesk.Locale.Search.Results);
    Results.Control.classList.add("SearchResult", "Results");
    Search.appendChild(Results.Control);

    /**
     * The search result ResultList of the Generic search.
     * @type {vDesk.Search.ResultList}
     */
    const ResultList = new vDesk.Search.ResultList();
    Results.Add(ResultList.Control);

    /**
     * The preview GroupBox of the Generic search.
     * @type {vDesk.Controls.GroupBox}
     */
    const Preview = new vDesk.Controls.GroupBox(vDesk.Locale.Search.Preview);
    Preview.Control.classList.add("Preview");

    Control.appendChild(Preview.Control);

    /**
     * The ToolBar Group containing ToolBar Items for selecting search filters.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const FilterToolBarGroup = new vDesk.Controls.ToolBar.Group(vDesk.Locale.Search.Filters);

    for(const Filter of Object.values(vDesk.Search.Filters)) {
        SelectedFilters.push(Filter);
        const Item = new vDesk.Controls.ToolBar.Item(
            Filter.Title,
            Filter.Icon,
            true,
            () => {
                const FoundFilter = SelectedFilters.find(SelectedFilter => SelectedFilter.Name === Filter.Name);
                //Check if the filter is selected.
                if(FoundFilter !== undefined) {
                    SelectedFilters.splice(SelectedFilters.indexOf(FoundFilter), 1);
                    Item.Selected = false;
                } else {
                    SelectedFilters.push(Filter);
                    Item.Selected = true;
                }
                SearchFieldTextBox.disabled = (SelectedFilters.length === 0);
            }
        );
        Item.Selected = true;
        FilterToolBarGroup.Add(Item);
    }

};

vDesk.Search.Generic.Implements(vDesk.Search.ICustomSearch);
