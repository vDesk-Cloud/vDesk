/**
 * Initializes a new instance of the Search class.
 * @module Search
 * @class The searchmodule.
 * Provides an interface for searching files, events, etc.
 * @memberOf Modules
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
Modules.Search = function Search() {

    Object.defineProperties(this, {
        Control: {
            enumerable: true,
            get:        () => Control
        },
        Name:    {
            enumerable: true,
            value:      "Search"
        },
        Title:   {
            enumerable: true,
            value:      vDesk.Locale.Search.Module
        },
        Icon:    {
            enumerable: true,
            value:      vDesk.Visual.Icons.View
        }
    });

    /**
     * The underlying DOM-Node.
     * @type {HTMLDivElement}
     */
    const Control = document.createElement("div");
    Control.className = "Search";

    /**
     * The ToolBar Group containing ToolBarItems for displaying customsearch controls.
     * @type {vDesk.Controls.ToolBar.Group}
     */
    const SearchesToolBarGroup = new vDesk.Controls.ToolBar.Group(vDesk.Locale.Search.Module);

    /**
     * The generic search of the Search module.
     * @type {vDesk.Search.Generic}
     */
    const GenericSearch = new vDesk.Search.Generic();

    /**
     * The generic search ToolBar Item of the Search module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    const GenericSearchToolBarItem = new vDesk.Controls.ToolBar.Item(
        GenericSearch.Title,
        GenericSearch.Icon,
        true,
        () => {
            Control.removeChild(CurrentSearch.Control);
            CurrentSearch = GenericSearch;
            Control.appendChild(CurrentSearch.Control);
            CurrentSearchToolBarItem.Control.classList.remove("Selected");
            CurrentSearchToolBarItem = GenericSearchToolBarItem;
            CurrentSearchToolBarItem.Control.classList.add("Selected");
            vDesk.Header.ToolBar.Groups = [SearchesToolBarGroup, ...CurrentSearch.ToolBarGroups];
        }
    );

    /**
     * The current active search-control of the search-module.
     * @type {vDesk.Search.ICustomSearch}
     */
    let CurrentSearch = GenericSearch;

    /**
     * The ToolBar Item of the current active search-control of the search-module.
     * @type {vDesk.Controls.ToolBar.Item}
     */
    let CurrentSearchToolBarItem = GenericSearchToolBarItem;
    CurrentSearchToolBarItem.Control.classList.add("Selected");

    Control.appendChild(CurrentSearch.Control);
    SearchesToolBarGroup.Add(CurrentSearchToolBarItem);

    for(const Search of Object.values(vDesk.Search.Custom)) {
        const Instance = new Search();
        if(Instance instanceof vDesk.Search.ICustomSearch) {
            const Item = new vDesk.Controls.ToolBar.Item(
                Instance.Title,
                Instance.Icon,
                true,
                () => {
                    Control.removeChild(CurrentSearch.Control);
                    CurrentSearch = Instance;
                    Control.appendChild(CurrentSearch.Control);

                    CurrentSearchToolBarItem.Control.classList.remove("Selected");
                    CurrentSearchToolBarItem = Item;
                    CurrentSearchToolBarItem.Control.classList.add("Selected");

                    vDesk.Header.ToolBar.Groups = [SearchesToolBarGroup, ...CurrentSearch.ToolBarGroups];
                }
            );
            SearchesToolBarGroup.Add(Item);
        }
    }

    this.Load = function() {
        vDesk.Header.ToolBar.Groups = [SearchesToolBarGroup, ...CurrentSearch.ToolBarGroups];
    };

    this.Unload = function() {

    };
};

Modules.Search.Implements(vDesk.Modules.IVisualModule);