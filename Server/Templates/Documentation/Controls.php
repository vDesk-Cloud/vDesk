<?php
use vDesk\Documentation\Code;
use vDesk\Pages\Functions;
?>
<article class="Controls">
    <header>
        <h2>Controls</h2>
        <p>
            This document describes represents an enumeration of vDesk's clientside controls.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li>
                <a href="#Structuring">Structuring</a>
                <ul class="Topics">
                    <li><a href="#GroupBox">GroupBox</a></li>
                    <li><a href="#FloatingBox">FloatingBox</a></li>
                    <li><a href="#ResizableBox">ResizableBox</a></li>
                    <li><a href="#DynamicBox">DynamicBox</a></li>
                    <li><a href="#Resizer">Resizer</a></li>
                    <li><a href="#TabControl">TabControl</a></li>
                    <li><a href="#Table">Table</a></li>
                    <li><a href="#Calendar">Calendar</a></li>
                </ul>
            </li>
            <li>
                <a href="#Picker">Picker</a>
                <ul class="Topics">
                    <li><a href="#DatePicker">DatePicker</a></li>
                    <li><a href="#TimePicker">TimePicker</a></li>
                    <li><a href="#DateTimePicker">DateTimePicker</a></li>
                    <li><a href="#TimeSpanPicker">TimespanPicker</a></li>

                </ul>
            </li>
            <li>
                <a href="#Input">Input</a>
                <ul class="Topics">
                    <li><a href="#ToolBar">ToolBar</a></li>
                    <li><a href="#ContextMenu">ContextMenu</a></li>
                    <li><a href="#EditControl">EditControl</a>
                        <ul class="Topics">
                            <li>
                                <a href="#TextInput">Text input validation</a>
                                <ul class="Topics">
                                    <li><a href="#String">String</a></li>
                                    <li><a href="#Text">Text</a></li>
                                    <li><a href="#Email">Email</a></li>
                                    <li><a href="#URL">URL</a></li>
                                    <li><a href="#Suggest">Suggest</a></li>
                                    <li><a href="#Password">Password</a></li>
                                    <li><a href="#Color">Color</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#NumericInput">Numeric input validation</a>
                                <ul class="Topics">
                                    <li><a href="#Number">Number</a></li>
                                    <li><a href="#Range">Range</a></li>
                                    <li><a href="#Money">Money</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#BooleanInput">Boolean input validation</a>
                                <ul class="Topics">
                                    <li><a href="#Boolean">Boolean</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#DateInput">Date input validation</a>
                                <ul class="Topics">
                                    <li><a href="#Date">Date</a></li>
                                    <li><a href="#Time">Time</a></li>
                                    <li><a href="#DateTime">DateTime</a></li>
                                    <li><a href="#TimeSpan">TimeSpan</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#EnumInput">Enumerated input validation</a>
                                <ul class="Topics">
                                    <li><a href="#Enum">Enum</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
    <h3 id="Structuring">Structuring</h3>
<section id="GroupBox" class="ControlPreview">
    <h4>Group Box</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::Constant ?> <?= Code::Const("Text") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"p\"") ?>)<?= Code::Delimiter ?>

<?= Code::Const("Text") ?>.<?= Code::Field("textContent") ?> = <?= Code::String("\"Lorem ipsum dolor sit amet\"") ?><?= Code::Delimiter ?>
        
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("GroupBox") ?>(<?= Code::String("\"Title\"") ?>, [<?= Code::Const("Text") ?>], <?= Code::True ?>)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="GroupBoxDemo" class="Preview">
    </div>
    <script>
        const GroupBoxText = document.createElement("p");
        GroupBoxText.textContent = "Lorem ipsum dolor sit amet";
        document.getElementById("GroupBoxDemo").appendChild(new vDesk.Controls.GroupBox("Title", [GroupBoxText], true, true).Control);
    </script>
</section>
<section id="FloatingBox" class="ControlPreview">
    <h4>Floating Box</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("FloatingBox") ?>(
    <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Drag me!\"") ?>),
    <?= Code::Int("10") ?>,
    <?= Code::Int("10") ?>,
    {<?= Code::Variable("Top") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Left") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Right") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Bottom") ?>: <?= Code::Int("10") ?>}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="FloatingBoxDemo" class="Preview" style="position: relative">
    </div>
    <script>
        document.getElementById("FloatingBoxDemo").appendChild(
            new vDesk.Controls.FloatingBox(
                document.createTextNode("Drag me!"),
                10,
                10,
                {Top: 10, Left: 10, Right: 10, Bottom: 10}
            ).Control
        );
    </script>
</section>
<section id="ResizableBox" class="ControlPreview">
    <h4>Resizable Box</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("ResizableBox") ?>(
    <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Resize me!\"") ?>),
    <?= Code::Int("100") ?>,
    <?= Code::Int("100") ?>,
    {<?= Code::Variable("Height") ?>: {<?= Code::Variable("Min") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("10") ?>}, <?= Code::Variable("Width") ?>: {<?= Code::Variable("Min") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("10") ?>}}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="ResizableBoxDemo" class="Preview" style="position: relative">
    </div>
    <script>
        document.getElementById("ResizableBoxDemo").appendChild(
            new vDesk.Controls.ResizableBox(
                document.createTextNode("Resize me!"),
                100,
                100,
                {Height: {Min: 10, Max: null}, Width: {Min: 10, Max: null}}
            ).Control
        );
    </script>
</section>
<section id="DynamicBox" class="ControlPreview">
    <h4>DynamicBox</h4>
    <p>The DynamicBox represents a combination between the Floating- and ResizableBox </p>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("DynamicBox") ?>(
    <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Resize me!\"") ?>),
    <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Drag me!\"") ?>),
    <?= Code::Int("100") ?>,
    <?= Code::Int("100") ?>,
    <?= Code::Int("10") ?>,
    <?= Code::Int("10") ?>,
    {
        <?= Code::Variable("Top") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Left") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Right") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Bottom") ?>: <?= Code::Int("10") ?>,
        <?= Code::Variable("Height") ?>: {<?= Code::Variable("Min") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("10") ?>},
        <?= Code::Variable("Width") ?>:  {<?= Code::Variable("Min") ?>: <?= Code::Int("10") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("10") ?>}
    }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="DynamicBoxDemo" class="Preview" style="position: relative">
    </div>
    <script>
        document.getElementById("DynamicBoxDemo").appendChild(
            new vDesk.Controls.DynamicBox(
                document.createTextNode("Resize me!"),
                document.createTextNode("Drag me!"),
                100,
                100,
                10,
                10,
                {Top: 10, Left: 10, Right: 10, Bottom: 10, Height: {Min: 10, Max: null}, Width: {Min: 10, Max: null}}
            ).Control
        );
    </script>
</section>
<section id="Resizer" class="ControlPreview">
    <h4>Resizer</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::Constant ?> <?= Code::Const("First") ?> <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"div\"") ?>)<?= Code::Delimiter ?>

<?= Code::Const("First") ?>.<?= Code::Field("textContent") ?> = <?= Code::String("\"First\"") ?><?= Code::Delimiter ?>

<?= Code::Constant ?> <?= Code::Const("Second") ?> <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"div\"") ?>)<?= Code::Delimiter ?>

<?= Code::Const("Second") ?>.<?= Code::Field("textContent") ?> = <?= Code::String("\"Second\"") ?><?= Code::Delimiter ?>
        
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Resizer") ?>(
    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Resizer") ?>.<?= Code::Field("Direction") ?>.<?= Code::Field("Horizontal") ?>,
    <?= Code::Const("First") ?>,
    <?= Code::Const("Second") ?>
    
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="ResizerDemo" class="Preview" style="position: relative">
    </div>
    <script>
        const ResizerDemo = document.getElementById("ResizerDemo");
        const First = document.createElement("div");
        First.textContent = "First";
        First.className = "First";
        const Second = document.createElement("div");
        Second.textContent = "Second";
        Second.className = "Second";
        ResizerDemo.appendChild(First);
        ResizerDemo.appendChild(
            new vDesk.Controls.Resizer(
                vDesk.Controls.Resizer.Direction.Horizontal,
                First,
                Second
            ).Control
        );
        ResizerDemo.appendChild(Second);
    </script>
</section>
<section id="TabControl" class="ControlPreview">
    <h4>TabControl</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("TabControl") ?>(
    [
        <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("TabControl") ?>.<?= Code::Class("TabItem") ?>(
            <?= Code::String("\"First\"") ?>,
            <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Content of 1st Tab.\"") ?>)
        ),
        <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("TabControl") ?>.<?= Code::Class("TabItem") ?>(
            <?= Code::String("\"Second\"") ?>,
            <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Content of 2nd Tab.\"") ?>)
        ),
        <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("TabControl") ?>.<?= Code::Class("TabItem") ?>(
            <?= Code::String("\"Third\"") ?>,
            <?= Code::Variable("document") ?>.<?= Code::Function("createTextNode") ?>(<?= Code::String("\"Content of 3rd Tab.\"") ?>)
        )
    ]
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="TabControlDemo" class="Preview" style="position: relative">
    </div>
    <script>
        document.getElementById("TabControlDemo").appendChild(
            new vDesk.Controls.TabControl(
                [
                    new vDesk.Controls.TabControl.TabItem("First", document.createTextNode("Content of 1st Tab.")),
                    new vDesk.Controls.TabControl.TabItem("Second", document.createTextNode("Content of 2nd Tab.")),
                    new vDesk.Controls.TabControl.TabItem("Third", document.createTextNode("Content of 3rd Tab."))
                ]
            ).Control
        );
    </script>
</section>
<section id="Table" class="ControlPreview">
    <h4>Table</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Table") ?>([
    {<?= Code::Variable("Name") ?>: <?= Code::String("\"ID\"") ?>, <?= Code::Variable("Label") ?>: <?= Code::String("\"Article ID\"") ?>, <?= Code::Variable("Type") ?>: <?= Code::Variable("Type") ?>.<?= Code::Const("Number") ?>},
    {<?= Code::Variable("Name") ?>: <?= Code::String("\"Vendor\"") ?>, <?= Code::Variable("Label") ?>: <?= Code::String("\"Vendor\"") ?>, <?= Code::Variable("Type") ?>: <?= Code::Variable("Type") ?>.<?= Code::Const("String") ?>},
])<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="TableDemo" class="Preview" style="position: relative">
    </div>
    <script>
        const TablePreview = document.getElementById("TableDemo");
        const Table = new vDesk.Controls.Table(
            [
                {Name: "ID", Label: "Article ID", Type: Type.Number},
                {Name: "Vendor", Label: "Vendor", Type: Type.String},
                {Name: "GPU", Label: "Model", Type: Type.String},
                {Name: "Clock", Label: "Baseclock", Type: Type.Number},
                {Name: "Boost", Label: "Boost", Type: Type.Number},
                {Name: "TGP", Label: "TGP", Type: Type.Number},
                {Name: "VRAM", Label: "VRAM", Type: Type.Number}
            ]
        );
        Table.Rows.Add(Table.CreateRow({ID: 1, Vendor: "AMD", GPU: "RX 5700 XT", Clock: 1605, Boost: 1905, TGP: 225, VRAM: 8}));
        Table.Rows.Add(Table.CreateRow({ID: 2, Vendor: "AMD", GPU: "RX 6800 XT", Clock: 1825, Boost: 2250, TGP: 300, VRAM: 16}));
        Table.Rows.Add(Table.CreateRow({ID: 3, Vendor: "AMD", GPU: "RX 6900 XT", Clock: 1825, Boost: 2250, TGP: 300, VRAM: 16}));
        Table.Rows.Add(Table.CreateRow({ID: 4, Vendor: "Nvidia", GPU: "RTX 3070", Clock: 1605, Boost: 1500, TGP: 220, VRAM: 8}));
        Table.Rows.Add(Table.CreateRow({ID: 5, Vendor: "Nvidia", GPU: "RTX 3080", Clock: 1440, Boost: 1710, TGP: 320, VRAM: 10}));
        Table.Rows.Add(Table.CreateRow({ID: 6, Vendor: "Nvidia", GPU: "RTX 3090", Clock: 1395, Boost: 1695, TGP: 350, VRAM: 24}));
        TablePreview.appendChild(Table.Control);
    </script>
</section>
<section id="Calendar" class="ControlPreview">
    <h4>Calendar</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Calendar") ?>(
    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Calendar") ?>.<?= Code::Const("Today") ?>,
    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Calendar") ?>.<?= Code::Const("DefaultViews") ?>,
    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("Calendar") ?>.<?= Code::Field("View") ?>.<?= Code::Const("Month") ?>,
    <?= Code::True ?>
    
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="CalendarDemo" class="Preview" style="position: relative; height: 300px">
    </div>
    <script>
        vDesk.Locale = {};
        document.getElementById("CalendarDemo").appendChild(
            new vDesk.Controls.Calendar(
                vDesk.Controls.Calendar.Today,
                vDesk.Controls.Calendar.DefaultViews,
                vDesk.Controls.Calendar.View.Month
            ).Control
        );
    </script>
</section>
<h3 id="Picker">Picker</h3>
<section id="DatePicker" class="ControlPreview">
    <h4>DatePicker</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("DatePicker") ?>(<?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::True ?>)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="DatePickerDemo" class="Preview">
    </div>
    <script>
        document.getElementById("DatePickerDemo").appendChild(new vDesk.Controls.DatePicker().Control);
    </script>
</section>
<section id="TimePicker" class="ControlPreview">
    <h4>TimePicker</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("TimePicker") ?>(<?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::True ?>)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="TimePickerDemo" class="Preview">
    </div>
    <script>
        document.getElementById("TimePickerDemo").appendChild(new vDesk.Controls.TimePicker().Control);
    </script>
</section>
<section id="DateTimePicker" class="ControlPreview">
    <h4>DateTimePicker</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("DateTimePicker") ?>(<?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::True ?>)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="DateTimePickerDemo" class="Preview">
    </div>
    <script>
        document.getElementById("DateTimePickerDemo").appendChild(new vDesk.Controls.DateTimePicker().Control);
    </script>
</section>
<section id="TimeSpanPicker" class="ControlPreview">
    <h4>TimeSpanPicker</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("TimeSpanPicker") ?>(<?= Code::String("\"00:00:00\"") ?>, <?= Code::True ?>)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="TimeSpanPickerDemo" class="Preview">
    </div>
    <script>
        document.getElementById("TimeSpanPickerDemo").appendChild(new vDesk.Controls.TimeSpanPicker().Control);
    </script>
</section>
<section id="TimeSpanPicker" class="ControlPreview">
    <h4>ColorPicker</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>(
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("Color") ?>(<?= Code::Int("0") ?>, <?= Code::Int("0") ?>, <?= Code::Int("0") ?>),
    <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>.<?= Code::Const("RGBA") ?>
    
    | <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>.<?= Code::Const("HSLA") ?>
    
    | <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>.<?= Code::Const("Hex") ?>
            
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="ColorPickerDemo" class="Preview">
    </div>
    <script>
        document.getElementById("ColorPickerDemo").appendChild(new vDesk.Media.Drawing.ColorPicker().Control);
    </script>
</section>
    <h3 id="Input">Input</h3>
<section id="ToolBar" class="ControlPreview">
    <h4>ToolBar</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("ToolBar") ?>([
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Field("ToolBar") ?>.<?= Code::Class("Group") ?>(
        <?= Code::String("\"Hello world\"") ?>,
        [
            <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Field("ToolBar") ?>.<?= Code::Class("Item") ?>(
                <?= Code::String("\"Lorem\"") ?>,
                <?= Code::String("\"" . Functions::Image("vDesk", "Code.png") . "\"") ?>,
                <?= Code::True ?>,
                () => <?= Code::Function("alert") ?>(<?= Code::String("\"Lorem\"") ?>)
            ),
            ...
        ]
    ),
])<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="ToolBarDemo" class="Preview">
    </div>
    <script>
        document.getElementById("ToolBarDemo").appendChild(new vDesk.Controls.ToolBar(
            [
                new vDesk.Controls.ToolBar.Group(
                    "Hello",
                    [
                        new  vDesk.Controls.ToolBar.Item("Lorem", "<?= Functions::Image("vDesk", "Code.png") ?>", true, () => alert("Lorem")),
                        new  vDesk.Controls.ToolBar.Item("ipsum", "<?= Functions::Image("vDesk", "Package.png") ?>", true, () => alert("ipsum"))
                    ]
                ),
                new vDesk.Controls.ToolBar.Group(
                    "World",
                    [
                        new  vDesk.Controls.ToolBar.Item("dolor", "<?= Functions::Image("vDesk", "Performance.png") ?>", true, () => alert("Meddl!")),
                        new  vDesk.Controls.ToolBar.Item("sit amet", "<?= Functions::Image("vDesk", "Platform.png") ?>", true, () => alert("sit amet"))
                    ]
                )
            ]
        ).Control);
    </script>
</section>
<section id="ContextMenu" class="ControlPreview">
    <h4>ContextMenu</h4>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("ContextMenu") ?>([
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Field("ContextMenu") ?>.<?= Code::Class("Item") ?>(
        <?= Code::String("\"Hello World!\"") ?>,
        <?= Code::String("\"A\"") ?>,
        <?= Code::String("\"icon.png\"") ?>,
        () => <?= Code::True ?>
        
    ),
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Field("ContextMenu") ?>.<?= Code::Class("Group") ?>(
        <?= Code::String("\"Submenu\"") ?>,
        <?= Code::String("\"icon.png\"") ?>,
        () => <?= Code::True ?>,
        [
            <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Field("ContextMenu") ?>.<?= Code::Class("Item") ?>(
                <?= Code::String("\"Hello World!\"") ?>,
                <?= Code::String("\"B\"") ?>,
                <?= Code::String("\"icon.png\"") ?>,
                () => <?= Code::True ?>
                
            ),
            ...
        ]
    ),
])<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div style="min-height: 300px; background-color: chocolate" id="ContextMenuDemo" class="Preview">
    </div>
    <script>
        const ContextMenu = new vDesk.Controls.ContextMenu([
            new vDesk.Controls.ContextMenu.Item("Hello World!", "HelloWorld", "<?= Functions::Image("vDesk", "Code.png") ?>", () => true),
            new vDesk.Controls.ContextMenu.Group("Submenu", "<?= Functions::Image("vDesk", "Code.png") ?>", () => true, [
                new vDesk.Controls.ContextMenu.Item("ABC", "HelloWorld", "<?= Functions::Image("vDesk", "Code.png") ?>", () => true),
                new vDesk.Controls.ContextMenu.Item("XYZ", "HelloWorld", "<?= Functions::Image("vDesk", "Code.png") ?>", () => true)
            ]),
        ]);
        const ContextMenuDemo = document.getElementById("ContextMenuDemo");
        ContextMenuDemo.addEventListener(
            "contextmenu",
            Event => {
                Event.preventDefault();
                ContextMenu.Show(Event.target, Event.pageX, Event.pageY);
            }
        );
        ContextMenu.Control.addEventListener("submit", () => ContextMenu.Hide());
        ContextMenuDemo.appendChild(ContextMenu.Control);
    </script>
</section>
<section id="EditControl" class="ControlPreview">
    <h4>EditControl</h4>
    <h5>Syntax</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::Keyword("String") ?> Label = <?= Code::String("\"\"") ?>,
    ?<?= Code::Keyword("String") ?> Tooltip = <?= Code::Null ?>,
    <?= Code::Keyword("String") ?> Type = <?= Code::Variable("Type") ?>.<?= Code::Const("String") ?>,
    Value = <?= Code::Null ?>,
    ?<?= Code::Keyword("Array") ?> | <?= Code::Keyword("Object") ?> Validator = <?= Code::Null ?>,
    <?= Code::Keyword("Boolean") ?> Required = <?= Code::False ?>,
    <?= Code::Keyword("Boolean") ?> Enabled = <?= Code::True ?>
    
)<?= Code::Delimiter ?>
</code></pre>
    </div>
</section>
    <h3 id="TextInput">Text input</h3>
<section id="String" class="ControlPreview">
    <h5>String</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter text:\"") ?>,
    <?= Code::String("\"This is a labelled textbox!\"") ?>,
    <?= Code::Variable("Type") ?>.<?= Code::Const("String") ?>,
    <?= Code::String("\"Hello world!\"") ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("524288") ?>, <?= Code::Variable("Expression") ?>: <?= Code::String("\".+\"") ?> }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlStringDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlStringDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter text:",
                "This is a labelled textbox!",
                Type.String,
                "Hello world!"
            ).Control
        );
    </script>
</section>
<section id="Text" class="ControlPreview">
    <h5>Text</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter long text:\"") ?>,
    <?= Code::String("\"This is a multiline textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Text") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("524288") ?>}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlTextDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlTextDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter long text:",
                "This is a multiline textbox",
                Extension.Type.Text
            ).Control
        );
    </script>
</section>
<section id="Email" class="ControlPreview">
    <h5>Email</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter Email address:\"") ?>,
    <?= Code::String("\"This is a labelled textbox accepting only email addresses!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Email") ?>,
    <?= Code::String("\"Hello world!\"") ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("524288") ?>, <?= Code::Variable("Expression") ?>: <?= Code::String("\".+\"") ?> }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlEmailDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlEmailDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter Email address:",
                "This is a labelled textbox accepting only email addresses!",
                Extension.Type.Email,
                "hello@world.com"
            ).Control
        );
    </script>
</section>
<section id="URL" class="ControlPreview">
    <h5>URL</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter an URL:\"") ?>,
    <?= Code::String("\"This is a labelled textbox accepting only URLs!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("URL") ?>,
    <?= Code::String("\"https://vdesk.cloud\"") ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("524288") ?>, <?= Code::Variable("Expression") ?>: <?= Code::String("\".+\"") ?> }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlURLDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlURLDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter an URL:",
                "This is a labelled textbox accepting only URLs!",
                Extension.Type.URL,
                "https://vdesk.cloud"
            ).Control
        );
    </script>
</section>
<section id="Suggest" class="ControlPreview">
    <h5>Suggest</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Suggest\"") ?>,
    <?= Code::String("\"This is a labelled textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Suggest") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("524288") ?>, <?= Code::Variable("Expression") ?>: <?= Code::String("\".+\"") ?> }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlSuggestDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlSuggestDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter text or choose a value:",
                "This is an autocomplete suggestion textbox!",
                Extension.Type.Suggest,
                null,
                ["Lorem", "accusam", "ipsum", "dolor", "sit", "amet", "consetetur", "sadipscing", "elitr", "sed", "diam", "sanctus", "erat", "At vero"]
            ).Control
        );
    </script>
</section>
<section id="Password" class="ControlPreview">
    <h5>Password</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter password:\"") ?>,
    <?= Code::String("\"This is a password textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Password") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Int("524288") ?>, <?= Code::Variable("Expression") ?>: <?= Code::String("\".+\"") ?> }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlPasswordDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlPasswordDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter password:",
                "This is a password textbox!",
                Extension.Type.Password
            ).Control
        );
    </script>
</section>
<section id="Color" class="ControlPreview">
    <h5>Color</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter or choose color:\"") ?>,
    <?= Code::String("\"This is a color picker textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Color") ?>,
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("Color") ?>(<?= Code::Int("0") ?>, <?= Code::Int("0") ?>, <?= Code::Int("0") ?>),
    {
        <?= Code::Variable("Mode") ?>: <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>.<?= Code::Const("RGBA") ?>
    
              | <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>.<?= Code::Const("HSLA") ?>
              
              | <?= Code::Variable("vDesk") ?>.<?= Code::Field("Media") ?>.<?= Code::Field("Drawing") ?>.<?= Code::Class("ColorPicker") ?>.<?= Code::Const("Hex") ?>
    
    }
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlColorDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlColorDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter or choose a color:",
                "This is a color picker textbox!",
                Extension.Type.Color
            ).Control
        );
    </script>
</section>
    <h3 id="NumericInput">Numeric input</h3>
<section id="Number" class="ControlPreview">
    <h5>Number</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter number:\"") ?>,
    <?= Code::String("\"This is a numeric textbox!\"") ?>,
    <?= Code::Variable("Type") ?>.<?= Code::Const("Number") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Variable("Number") ?>.<?= Code::Const("MAX_SAFE_INTEGER") ?>, <?= Code::Variable("Steps") ?>: <?= Code::Int("1") ?>}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlNumberDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlNumberDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter number:",
                "This is a numeric textbox!",
                Type.Number
            ).Control
        );
    </script>
</section>
<section id="Range" class="ControlPreview">
    <h5>Range</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter number:\"") ?>,
    <?= Code::String("\"This is a password textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Range") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Variable("Number") ?>.<?= Code::Const("MAX_SAFE_INTEGER") ?>, <?= Code::Variable("Steps") ?>: <?= Code::Int("1") ?>}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlRangeDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlRangeDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Select number:",
                "This is a numeric textbox!",
                Extension.Type.Range
            ).Control
        );
    </script>
</section>
<section id="Money" class="ControlPreview">
    <h5>Money</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter amount:\"") ?>,
    <?= Code::String("\"This is a currency textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Money") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::Int("0") ?>, <?= Code::Variable("Max") ?>: <?= Code::Variable("Number") ?>.<?= Code::Const("MAX_SAFE_INTEGER") ?>, <?= Code::Variable("Steps") ?>: <?= Code::Int("1") ?>, <?= Code::Variable("Currency") ?>: <?= Code::String("\"€\"") ?>}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlMoneyDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlMoneyDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter amount:",
                "This is a currency textbox!",
                Extension.Type.Money
            ).Control
        );
    </script>
</section>
    <h3 id="BooleanInput">Boolean input</h3>
<section id="Boolean" class="ControlPreview">
    <h5>Boolean</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Toggle value:\"") ?>,
    <?= Code::String("\"This is a bool checkbox!\"") ?>,
    <?= Code::Variable("Type") ?>.<?= Code::Const("Bool") ?>
    
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlBooleanDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlBooleanDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Toggle value:",
                "This is a bool checkbox!",
                Type.Bool
            ).Control
        );
    </script>
</section>
    <h3 id="DateInput">Date input</h3>
<section id="Date" class="ControlPreview">
    <h5>Date</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter amount:\"") ?>,
    <?= Code::String("\"This is a currency textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Date") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::Variable("Max") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>()}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlDateDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlDateDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter or select date:",
                "This is a datepicker textbox!",
                Extension.Type.Date
            ).Control
        );
    </script>
</section>
<section id="Time" class="ControlPreview">
    <h5>Time</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter or select time:\"") ?>,
    <?= Code::String("\"This is a timepicker textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Time") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::Variable("Max") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>()}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlTimeDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlTimeDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter or select time:",
                "This is a timepicker textbox!",
                Extension.Type.Time
            ).Control
        );
    </script>
</section>
<section id="DateTime" class="ControlPreview">
    <h5>DateTime</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter or select date & time:\"") ?>,
    <?= Code::String("\"This is a datetimepicker textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("DateTime") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::Variable("Max") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>()}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlDateTimeDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlDateTimeDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter or select date & time:",
                "This is a datetimepicker textbox!",
                Extension.Type.DateTime
            ).Control
        );
    </script>
</section>
<section id="TimeSpan" class="ControlPreview">
    <h5>TimeSpan</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter or select timespan:\"") ?>,
    <?= Code::String("\"This is a timespanpicker textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("TimeSpan") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("Min") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>(), <?= Code::Variable("Max") ?>: <?= Code::New ?> <?= Code::Class("Date") ?>()}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlTimeSpanDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlTimeSpanDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Enter or select timespan:",
                "This is a timespanpicker textbox!",
                Extension.Type.TimeSpan
            ).Control
        );
    </script>
</section>
    <h3 id="EnumInput">Enumerated input</h3>
<section id="Enum" class="ControlPreview">
    <h5>Enum</h5>
    <div class="Code">
        <pre><code><?= Code\Language::JS ?>
<?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Controls") ?>.<?= Code::Class("EditControl") ?>(
    <?= Code::String("\"Enter or select timespan:\"") ?>,
    <?= Code::String("\"This is a timespanpicker textbox!\"") ?>,
    <?= Code::Variable("Extension") ?>.<?= Code::Field("Type") ?>.<?= Code::Const("Enum") ?>,
    <?= Code::Null ?>,
    {<?= Code::Variable("A") ?>: <?= Code::String("\"1\"") ?>, <?= Code::Variable("B") ?>: <?= Code::String("\"2\"") ?>, <?= Code::Variable("C") ?>: <?= Code::String("\"3\"") ?>}
)<?= Code::Delimiter ?>
</code></pre>
    </div>
    <div id="EditControlEnumDemo" class="Preview">
    </div>
    <script>
        document.getElementById("EditControlEnumDemo").appendChild(
            new vDesk.Controls.EditControl(
                "Select value:",
                "This is a labelled select!",
                Extension.Type.Enum,
                null,
                {
                    A: "1",
                    B: "2",
                    C: "3"
                }
            ).Control
        );
    </script>
</section>
<div style="clear: both"></div>
</article>