<?php

use vDesk\Documentation\Code;
use vDesk\Documentation\Code\Language;
use vDesk\Pages\Functions;

?>
<article>
    <header>
    <h2>Architecture</h2>
    <p>
        This document describes how vDesk works, interaction between client and server and how to register modules and call commands.
    </p>
    <h3>Overview</h3>
    <ul class="Topics">
        <li>
            <a href="#ApplicationFlow">Application-flow</a>
            <ul class="Topics">
                <li><a href="#OverviewClient">Client</a></li>
                <li><a href="#OverviewServer">Server</a></li>
    
            </ul>
        </li>
        <li>
            <a href="#Modules">Modules</a>
            <ul class="Topics">
                <li><a href="#Client">Client</a></li>
                <li><a href="#Server">Server</a></li>
            </ul>
        </li>
        <li><a href="#Commands">Commands</a>
            <ul class="Topics">
                <li><a href="#Parameters">Aliases</a>
                    <ul class="Topics">
                        <li><a href="#Validation">Validation</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a href="#Models">Models</a></li>
        <li><a href="#Controls">Controls</a></li>
    </ul>
    </header>
    <section id="ApplicationFlow">
        <h3 >Application-flow</h3>
        <p>
            From a global point of view, the application flow of vDesk can be described as a client/server "View-(Controller-Model)-system, where the client represents the "View",
            <br> whereas the server represents the "Controller"-host which manage the "Models".
        </p>
    <img alt="Conceptual description of vDesk's application flow" title="Conceptual description of vDesk's application flow" style="width: 100%"
         src="<?= Functions::Image("Documentation", "Concept.svg") ?>">
    </section>
    <section id="OverviewClient">
        <h4>Client</h4>
        <p>
            The client is a JavaScript-based application run by a modern browser.
        </p>
        <h5>Load</h5>
        <ol>
            <li>Performing a handshake with the specified server while sending a list of installed client-side packages and their versions to check for compatibility.</li>
            <li>Perform a login with the specified or stored credentials.</li>
            <li>Running startup-scripts located in the <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Load") ?></code>-namespace.</li>
            <li>Initializing installed Modules.</li>
        </ol>
        <h5>Run</h5>
        <ol>
            <li>Modules fetch required Models from the server and represents them as their according ViewModel</li>
            <li>Modules and/or ViewModels handling user-interaction</li>
            <li>Execute certain Command/s against the server</li>
            <li>Send command-payload through <a href="https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest">XMLHttpRequest</a></li>
            <li>Process response and update ViewModel/s.</li>
        </ol>
    </section>
    <section id="OverviewServer">
        <h4>Server</h4>
        <ol>
            <li>Validating the called command and its parameters.</li>
            <li>Run called Module and execute the command.</li>
            <li>Update/create Models in Modules.</li>
            <li>Format and stream response.</li>
            <li>Dispatch events.</li>
        </ol>
    </section>
    <section id="Modules">
        <h3>Modules</h3>
        <p>
            Modules are the controller-equivalent of MVC-frameworks.<br>
            The module-system of vDesk, is split into a server- and client-section.
        </p>
    </section>
    <section id="Client">
        <h4>Client</h4>
        <p>
            Client-side Modules are supposed to manage the UI and fetching or manipulating data by executing Commands against the server.
        </p>
        <p>
            To register a class as a client-side Module, the class to be located in the <code class="Inline">Modules</code>-namespace and must implement either the <code class="Inline">vDesk.Modules.<?= Code::Class("IModule") ?></code>-
            or <code class="Inline">vDesk.Modules.<?= Code::Class("IVisualModule") ?></code>-interface.<br>
            Modules are available in the global  <code class="Inline">vDesk.<?= Code::Class("Modules") ?></code>-dictionary after startup.
        </p>
    </section>
    <section id="Server">
        <h4>Server</h4>
        <p>
            Server-side Modules are supposed to process commands, validate parameters and manage Models.
        </p>
        <p>
            To register a class as a server-side Module, the class has to be located in the <code class="Inline">Modules</code>-namespace and must inherit from the <code class="Inline">\vDesk\Modules\<?= Code::Class("Module") ?></code>-class.<br>
            The Module base-class implements the <code class="Inline">\vDesk\Data\<?= Code::Class("IModel") ?></code>-interface which allows to simply instantiate the Module once and call
            its <code class="Inline"><?= Code::Function("Save") ?>()</code>-method to register it.
        </p>
        <p>
            Modules can be initialized via the global <code class="Inline">\vDesk\<?= Code::Class("Modules") ?></code>-facade by calling the desired Module as a method.
        </p>
        <p>
            Modules are stored in the <code class="Inline"><?= Code::Class("Modules") ?>.<?= Code::Const("Modules") ?></code>-table.
        </p>
    </section>
    <section id="Commands">
        <h3>Commands</h3>
        <p>
            vDesk's public API is exposed via a collection of "Commands" which are stored in the database, each referencing a target module and method to execute, as well as describing the
            parameters of the command.
        </p>
        <p>
            Commands are stored in the <code class="Inline"><?= Code::Class("Modules") ?>.<?= Code::Const("Commands") ?></code>-table.
        </p>
        <aside class="Code">
            <?= Code\Language::JS ?>
            <h5>Calling Commands in JavaScript</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(21) ?>
        <pre><code><?= Code::Variable("vDesk") ?>.<?= Code::Class("Connection") ?>.<?= Code::Function("Send") ?>(
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Modules") ?>.<?= Code::Class("Command") ?>(
        {
            <?= Code::Field("Module") ?>:  <?= Code::String("\"Archive\"") ?>,
            <?= Code::Field("Command") ?>: <?= Code::String("\"Upload\"") ?>,
            <?= Code::Field("Parameters") ?>: {
                <?= Code::Field("Parent") ?>: <?= Code::Variable("ParentElement") ?>,
                <?= Code::Field("File") ?>:   <?= Code::Variable("File") ?>,
            },
            <?= Code::Field("Ticket") ?>: <?= Code::Variable("vDesk") ?>.<?= Code::Field("User") ?>.<?= Code::Field("Ticket") ?>
        
        },
        <?= Code::Variable("Response") ?> => {
            <?= Code::If ?>(<?= Code::Variable("Response") ?>.<?= Code::Field("Status") ?>){
                <?= Code::Constant ?> <?= Code::Const("UploadedElement") ?> = <?= Code::Variable("vDesk") ?>.<?= Code::Field("Archive") ?>.<?= Code::Class("Element") ?>.<?= Code::Function("FromDataView") ?>(<?= Code::Variable("Response") ?>.<?= Code::Field("Data") ?>)<?= Code::Delimiter ?>
        
                <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Const("UploadedElement") ?>.<?= Code::Field("ID") ?>)<?= Code::Delimiter ?>
        
            } <?= Code::Else ?> {
                <?= Code::Function("alert") ?>(<?= Code::String("`What went wrong: ") ?>${<?= Code::Variable("Response") ?>.<?= Code::Field("Data") ?>}<?= Code::String(".`") ?>)<?= Code::Delimiter ?>
        
            }
        }
    )
)<?= Code::Delimiter ?>
</code></pre>
        </aside>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Calling Commands in PHP</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(13) ?>
            <pre><code><?= Code::Variable("\$ParentElement") ?> = <?= Code::New ?> \vDesk\IO\<?= Code::Class("FileInfo") ?>(<?= Code::Int("12") ?>)<?= Code::Delimiter ?>
            
<?= Code::Variable("\$File") ?> = <?= Code::New ?> \vDesk\IO\<?= Code::Class("FileInfo") ?>(<?= Code::String("\"Path\"") ?>)<?= Code::Delimiter ?>
            
            
<?= Code::Keyword("try") ?> {
    <?= Code::Variable("\$UploadedElement") ?> = \vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Archive") ?>()::<?= Code::Function("Upload") ?>(
        <?= Code::Variable("\$ParentElement") ?>,
        <?= Code::Variable("\$File") ?>
        
    )<?= Code::Delimiter ?>
    
} <?= Code::Keyword("catch") ?>(\<?= Code::Class("Throwable") ?> <?= Code::Variable("\$Exception") ?>) {
    <?= Code::Keyword("echo") ?> <?= Code::String("\"What went wrong: ") ?>{<?= Code::Variable("\$Exception") ?>-><?= Code::Function("getMessage") ?>()}<?= Code::String(".\"") ?><?= Code::Delimiter ?>
    
}

<?= Code::Keyword("echo") ?> <?= Code::Variable("\$UploadedElement") ?>-><?= Code::Field("ID") ?><?= Code::Delimiter ?>
</code></pre>
        </aside>
    </section>
    <section id="Parameters">
        <h4>Parameters</h4>
        <p>
            Commands can require any parameters if needed, describing the name, type, nullabillity and optionality.
        </p>
        <p>
            Parameters are stored in the <code class="Inline"><?= Code::Class("Modules") ?>.<?= Code::Const("Parameters") ?></code>-table.
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Example of adding Commands to Modules</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(22) ?>
            <pre><code><?= Code::Use ?> vDesk\Modules\<?= Code::Class("Command") ?><?= Code::Delimiter ?>
        
<?= Code::Use ?> vDesk\Struct\Collections\<?= Code::Class("Collection") ?><?= Code::Delimiter ?>
        
        
<?= Code::Variable("\$Archive") ?> = \vDesk\<?= Code::Class("Modules") ?>::<?= Code::Function("Archive") ?>()<?= Code::Delimiter ?>
        
        
<?= Code::Variable("\$Archive") ?>-><?= Code::Field("Commands") ?>-><?= Code::Function("Add") ?>(
    <?= Code::New ?> <?= Code::Class("Command") ?>(
        <?= Code::Null ?>,
        <?= Code::Variable("\$Archive") ?>,
        <?= Code::String("\"Upload\"") ?>,
        <?= Code::Bool("true") ?>,
        <?= Code::Bool("false") ?>,
        <?= Code::Null ?>,
        <?= Code::New ?> <?= Code::Class("Collection") ?>([
            <?= Code::New ?> <?= Code::Class("Parameter") ?>(<?= Code::Null ?>, <?= Code::Null ?>, <?= Code::String("\"Parent\"") ?>, <?= Code::Class("Type") ?>::<?= Code::Const("Int") ?>, <?= Code::Bool("false") ?>, <?= Code::Bool("false") ?>),
            <?= Code::New ?> <?= Code::Class("Parameter") ?>(<?= Code::Null ?>, <?= Code::Null ?>, <?= Code::String("\"File\"") ?>, \Extension\<?= Code::Class("Type") ?>::<?= Code::Const("File") ?>, <?= Code::Bool("false") ?>, <?= Code::Bool("false") ?>),
            <?= Code::New ?> <?= Code::Class("Parameter") ?>(<?= Code::Null ?>, <?= Code::Null ?>, <?= Code::String("\"Owner\"") ?>, \vDesk\Security\<?= Code::Class("User") ?>::<?= Code::Const("class") ?>, <?= Code::Bool("true") ?>, <?= Code::Bool("true") ?>)
        ])
    )
)<?= Code::Delimiter ?>
        
        
<?= Code::Variable("\$Archive") ?>-><?= Code::Function("Save") ?>()<?= Code::Delimiter ?>
</code></pre>
        </aside>
    </section>
    <section id="Validation">
        <h5>Validation</h5>
        <p>
            Parameters are being validated twice: first on the client, and if the validation has passed; a second time on the server.<br>
            This prevents the server from being stressed with useless malformed requests and ensures a manipulation-safe backend.
        </p>
        <p>
            The following table represents the global type-alias and their platform-specific datatypes of parameters.
        </p>
        <table>
        <tr>
            <th>Type</th>
            <th>JS</th>
            <th>PHP</th>
        </tr>
        <tr>
            <td>int</td>
            <td>Number(int)</td>
            <td>int</td>
        </tr>
        <tr>
            <td>float</td>
            <td>Number(float)</td>
            <td>float</td>
        </tr>
        <tr>
            <td>bool/boolean</td>
            <td>Boolean</td>
            <td>bool</td>
        </tr>
        <tr>
            <td>string</td>
            <td>String</td>
            <td>string</td>
        </tr>
        <tr>
            <td>array</td>
            <td>Array</td>
            <td>array</td>
        </tr>
        <tr>
            <td>object</td>
            <td>Object</td>
            <td>\stdClass</td>
        </tr>
        <tr>
            <td>iterable</td>
            <td>Array&Object</td>
            <td>iterable</td>
        </tr>
        <tr>
            <td>enum</td>
            <td>Object</td>
            <td>array</td>
        </tr>
        <tr>
            <td>color</td>
            <td>String(rgb/a, hsl/a, hex)</td>
            <td>string(rgb/a, hsl/a, hex)</td>
        </tr>
        <tr>
            <td>url</td>
            <td>String(URL-pattern)</td>
            <td>string(URL-pattern)</td>
        </tr>
        <tr>
            <td>email</td>
            <td>String(email-pattern)</td>
            <td>string(email-pattern)</td>
        </tr>
        <tr>
            <td>money</td>
            <td>String(money-pattern)</td>
            <td>string(money-pattern)</td>
        </tr>
        <tr>
            <td>file</td>
            <td>File</td>
            <td>\vDesk\IO\FileInfo</td>
        </tr>
        <tr>
            <td>date</td>
            <td>Date</td>
            <td>\DateTime</td>
        </tr>
        <tr>
            <td>time</td>
            <td>Date</td>
            <td>\DateTime</td>
        </tr>
        <tr>
            <td>datetime</td>
            <td>Date</td>
            <td>\DateTime</td>
        </tr>
        <tr>
            <td>timespan</td>
            <td>String(timespan-pattern)</td>
            <td>string(timespan-pattern)</td>
        </tr>
        <tr>
            <td>file</td>
            <td>File</td>
            <td>\vDesk\IO\FileInfo</td>
        </tr>
    </table>
    </section>
    <section id="Models">
        <h4>Models</h4>
        <p>
            Models represent database records and must implement the <code class="Inline">\vDesk\Data\<?= Code::Class("IModel") ?></code>-interface
            for being recognized as such.<br>
            They must implement the <code class="Inline"><?= Code::Class("IModel") ?>::<?= Code::Function("Fill") ?>()</code>-,
            <code class="Inline"><?= Code::Function("Save") ?>()</code>- and <code class="Inline"><?= Code::Function("Delete") ?>()</code>-methods for database communication
            as well as a <code class="Inline"><?= Code::Function("FromDataView") ?>()</code>- and <code class="Inline"><?= Code::Function("ToDataView") ?>()</code>-method
            which transforms the values of a model into a JSON-encodable representation or creates a new instance from it.
        </p>
        <p>
            If a module returns a model due to the execution of a command, the system automatically calls its
            <code class="Inline"><?= Code::Function("ToDataView") ?>()</code>-method and sends the returned value to the client.
        </p>
        <aside class="Code">
            <?= Code\Language::PHP ?>
            <h5>Example implementation of a Model</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(100) ?>
            <pre><code><?= Code::Use ?> vDesk\Data\<?= Code::Class("IModel") ?><?= Code::Delimiter ?>

<?= Code::Use ?> vDesk\DataProvider\<?= Code::Class("Expression") ?><?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("Product") ?> <?= Code::Implements ?> <?= Code::Class("IModel") ?> {

    <?= Code::Use ?> vDesk\Struct\<?= Code::Class("Properties") ?><?= Code::Delimiter ?>


    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("__construct") ?>(
        <?= Code::Private ?> ?<?= Code::Keyword("int") ?> <?= Code::Variable("\$ID") ?> = <?= Code::Null ?>,
        <?= Code::Private ?> ?<?= Code::Keyword("string") ?> <?= Code::Variable("\$Name") ?> = <?= Code::Null ?>,
        <?= Code::Private ?> ?<?= Code::Keyword("float") ?> <?= Code::Variable("\$Price") ?> = <?= Code::Null ?>,
        <?= Code::Private ?> ?<?= Code::Keyword("int") ?> <?= Code::Variable("\$Stock") ?> = <?= Code::Null ?>

    ) {
        <?= Code::Variable("\$this") ?>-><?= Code::Function("AddProperties") ?>([
            <?= Code::String("\"ID\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("int") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("int") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?> = <?= Code::Variable("\$Value") ?>

            ],
            <?= Code::String("\"Name\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("string") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("string") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?> = <?= Code::Variable("\$Value") ?>

            ],
            <?= Code::String("\"Price\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("float") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Price") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("float") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("Price") ?> = <?= Code::Variable("\$Value") ?>

            ],
            <?= Code::String("\"Stock\"") ?> => [
                \<?= Code::Const("Get") ?> => <?= Code::Keyword("fn") ?>(): ?<?= Code::Keyword("int") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Stock") ?>,
                \<?= Code::Const("Set") ?> => <?= Code::Keyword("fn") ?>(<?= Code::Keyword("int") ?> <?= Code::Variable("\$Value") ?>) => <?= Code::Variable("\$this") ?>-><?= Code::Field("Stock") ?> = <?= Code::Variable("\$Value") ?>

            ]
        ])<?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Fill") ?>(): <?= Code::Keyword("self") ?> {
        <?= Code::Variable("\$Product") ?> = <?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"*\"") ?>)
                             -><?= Code::Function("From") ?>(<?= Code::String("\"Shop.Products\"") ?>)
                             -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>])
                             -><?= Code::Function("Execute") ?>()
                             -><?= Code::Function("ToMap") ?>()<?= Code::Delimiter ?>


        <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?> = <?= Code::Variable("\$Product") ?>[<?= Code::String("\"Name\"") ?>]<?= Code::Delimiter ?>

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Price") ?> = (<?= Code::Keyword("float") ?>)<?= Code::Variable("\$Product") ?>[<?= Code::String("\"Price\"") ?>]<?= Code::Delimiter ?>

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Stock") ?> = (<?= Code::Keyword("int") ?>)<?= Code::Variable("\$Product") ?>[<?= Code::String("\"Stock\"") ?>]<?= Code::Delimiter ?>


        <?= Code::Return ?>> <?= Code::Variable("\$this") ?><?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Save") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::If ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?> === <?= Code::Null ?>) {
            <?= Code::Comment("//Create new dataset") ?>

            <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?> = <?= Code::Class("Expression") ?>::<?= Code::Function("Insert") ?>()
                                  -><?= Code::Function("Into") ?>(<?= Code::String("\"Shop.Products\"") ?>)
                                  -><?= Code::Function("Values") ?>([
                                      <?= Code::String("\"ID\"") ?> => <?= Code::Null ?>,
                                      <?= Code::String("\"Name\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?>,
                                      <?= Code::String("\"Price\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Price") ?>,
                                      <?= Code::String("\"Stock\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Stock") ?>

                                  ])
                                  -><?= Code::Function("ID") ?>()<?= Code::Delimiter ?>

        } <?= Code::Else ?> {
            <?= Code::Comment("//Update existing dataset") ?>

            <?= Code::Class("Expression") ?>::<?= Code::Function("Update") ?>(<?= Code::String("\"Shop.Products\"") ?>)
                      -><?= Code::Function("Set") ?>([
                          <?= Code::String("\"Name\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?>,
                          <?= Code::String("\"Price\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Price") ?>,
                          <?= Code::String("\"Stock\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Stock") ?>

                      ])
                      -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>])
                      -><?= Code::Function("Execute") ?>()<?= Code::Delimiter ?>

        }
    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Delete") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::If ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?> !== <?= Code::Null ?>) {
            <?= Code::Class("Expression") ?>::<?= Code::Function("Delete") ?>()
                      -><?= Code::Function("From") ?>(<?= Code::String("\"Shop.Products\"") ?>)
                      -><?= Code::Function("Where") ?>([<?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>])
                      -><?= Code::Function("Execute") ?>()<?= Code::Delimiter ?>

        }
    }


    <?= Code::Public ?> <?= Code::Static ?> <?= Code::Function ?> <?= Code::Function("FromDataView") ?>(<?= Code::Variable("\$DataView") ?>): <?= Code::Static ?> {
        <?= Code::Return ?> <?= Code::New ?> <?= Code::Static ?>(
            (<?= Code::Keyword("int") ?>)<?= Code::Variable("\$DataView") ?>[<?= Code::String("\"ID\"") ?>],
            <?= Code::Variable("\$DataView") ?>[<?= Code::String("\"Name\"") ?>],
            (<?= Code::Keyword("float") ?>)<?= Code::Variable("\$DataView") ?>[<?= Code::String("\"Price\"") ?>],
            (<?= Code::Keyword("int") ?>)<?= Code::Variable("\$DataView") ?>[<?= Code::String("\"Stock\"") ?>]
        )<?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("ToDataView") ?>(): <?= Code::Keyword("array") ?> {
        <?= Code::Return ?> [
            <?= Code::String("\"ID\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("ID") ?>,
            <?= Code::String("\"Name\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Name") ?>,
            <?= Code::String("\"Price\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Price") ?>,
            <?= Code::String("\"Stock\"") ?> => <?= Code::Variable("\$this") ?>-><?= Code::Field("Stock") ?>

        ]<?= Code::Delimiter ?>

    }
}
</code></pre>
        </aside>
    </section>
    <section id="Controls">
        <h4>Controls</h4>
        <p>
            Controls are JavaScript-classes which can be compared with "ViewModels" and follow a certain pattern.<br>
            They have to implement a "Control"-property that contains the container node of the control and besides generic layout controls usually provide a <code class="Inline"><?= Code::Function("FromDataView") ?>()</code>-factorymethod
            that creates a new instance from the data returned by a server side model's <code class="Inline"><?= Code::Function("ToDataView") ?>()</code>-method.
        </p>
        <p>
            Controls never should directly execute a command against the server on state change, they should be administrated by a dedicated "Editor"-control.<br>
            To communicate with such editors, controls may fire custom events that have to include a "sender"-property reference the instance of the emitting Control next to the event data.
        </p>
        <p>
            A list of all generic controls is available under the <a href="<?= Functions::URL("Documentation", "Category", "Client", "Topic", "Controls") ?>">Controls</a> client topic.
        </p>
        <aside class="Code">
            <?= Code\Language::JS ?>
            <h5>Example implementation of a control</h5>
            <?= Code::Copy ?>
            <?= Code::Lines(57) ?>
        <pre><code><?= Code::Variable("Shop") ?>.<?= Code::Class("Product") ?> = <?= Code::Function ?>(<?= Code::Variable("ID") ?>, <?= Code::Variable("Name") ?>, <?= Code::Variable("Price") ?>, <?= Code::Variable("Stock") ?>) {

    <?= Code::Variable("Object") ?>.<?= Code::Function("defineProperties") ?>(
        <?= Code::Variable("this") ?>,
        {
            <?= Code::Field("Control") ?>: {
                <?= Code::Field("get") ?>: () => <?= Code::Const("Control") ?>

            },
            <?= Code::Field("ID") ?>: {
                <?= Code::Field("get") ?>: () => <?= Code::Variable("ID") ?>,
                <?= Code::Field("set") ?>:  <?= Code::Variable("Value") ?> => <?= Code::Variable("ID") ?> = <?= Code::Variable("Value") ?>

            },
            <?= Code::Field("Name") ?>: {
                <?= Code::Field("get") ?>: () => <?= Code::Const("NameLabel") ?>.<?= Code::Field("textContent") ?>,
                <?= Code::Field("set") ?>: <?= Code::Variable("Value") ?> => <?= Code::Const("NameLabel") ?>.<?= Code::Field("textContent") ?> = <?= Code::Variable("Value") ?>

            },
            <?= Code::Field("Price") ?>: {
                <?= Code::Field("get") ?>: () => <?= Code::Variable("Number") ?>.<?= Code::Function("parseFloat") ?>(<?= Code::Const("PriceLabel") ?>.<?= Code::Field("textContent") ?>),
                <?= Code::Field("set") ?>: <?= Code::Variable("Value") ?> => <?= Code::Const("PriceLabel") ?>.<?= Code::Field("textContent") ?> = <?= Code::Variable("Value") ?>

            },
            <?= Code::Field("Stock") ?>: {
                <?= Code::Field("get") ?>: () => <?= Code::Variable("Number") ?>.<?= Code::Function("parseInt") ?>(<?= Code::Const("StockLabel") ?>.<?= Code::Field("textContent") ?>),
                <?= Code::Field("set") ?>: <?= Code::Variable("Value") ?> => <?= Code::Const("StockLabel") ?>.<?= Code::Field("textContent") ?> = <?= Code::Variable("Value") ?>

            }
        }
    )<?= Code::Delimiter ?>


    <?= Code::Constant ?> <?= Code::Const("Control") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"div\"") ?>)<?= Code::Delimiter ?>

    <?= Code::Const("Control") ?>.<?= Code::Field("className") ?> = <?= Code::String("\"Product\"") ?><?= Code::Delimiter ?>


    <?= Code::Constant ?> <?= Code::Const("NameLabel") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"span\"") ?>)<?= Code::Delimiter ?>

    <?= Code::Const("NameLabel") ?>.<?= Code::Field("textContent") ?> = <?= Code::Variable("Name") ?><?= Code::Delimiter ?>

    <?= Code::Const("Control") ?>.<?= Code::Function("appendChild") ?>(<?= Code::Const("NameLabel") ?>)<?= Code::Delimiter ?>


    <?= Code::Constant ?> <?= Code::Const("PriceLabel") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"span\"") ?>)<?= Code::Delimiter ?>

    <?= Code::Const("PriceLabel") ?>.<?= Code::Field("textContent") ?> = <?= Code::Variable("Price") ?><?= Code::Delimiter ?>

    <?= Code::Const("Control") ?>.<?= Code::Function("appendChild") ?>(<?= Code::Const("PriceLabel") ?>)<?= Code::Delimiter ?>


    <?= Code::Constant ?> <?= Code::Const("StockLabel") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"span\"") ?>)<?= Code::Delimiter ?>

    <?= Code::Const("StockLabel") ?>.<?= Code::Field("textContent") ?> = <?= Code::Variable("Stock") ?><?= Code::Delimiter ?>

    <?= Code::Const("Control") ?>.<?= Code::Function("appendChild") ?>(<?= Code::Const("StockLabel") ?>)<?= Code::Delimiter ?>


    <?= Code::Constant ?> <?= Code::Const("Buy") ?> = <?= Code::Variable("document") ?>.<?= Code::Function("createElement") ?>(<?= Code::String("\"button\"") ?>)<?= Code::Delimiter ?>

    <?= Code::Const("Buy") ?>.<?= Code::Field("textContent") ?> = <?= Code::String("\"Buy\"") ?><?= Code::Delimiter ?>

    <?= Code::Const("Buy") ?>.<?= Code::Function("addEventListener") ?>(<?= Code::String("\"click\"") ?>, () => <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Events") ?>.<?= Code::Class("BubblingEvent") ?>(<?= Code::String("\"buy\"") ?>, {<?= Code::Field("sender") ?>: <?= Code::Keyword("this") ?>}).<?= Code::Function("Dispatch") ?>(<?= Code::Const("Control") ?>))<?= Code::Delimiter ?>

    <?= Code::Const("Control") ?>.<?= Code::Function("appendChild") ?>(<?= Code::Const("Buy") ?>)<?= Code::Delimiter ?>


}<?= Code::Delimiter ?>


<?= Code::Variable("Shop") ?>.<?= Code::Class("Product") ?>.<?= Code::Function("FromDataView") ?>(<?= Code::Variable("DataView") ?>) {
    <?= Code::Return ?> <?= Code::New ?> <?= Code::Variable("Shop") ?>.<?= Code::Class("Product") ?>(
        <?= Code::Variable("DataView") ?>.<?= Code::Field("ID") ?>,
        <?= Code::Variable("DataView") ?>.<?= Code::Field("Name") ?>,
        <?= Code::Variable("DataView") ?>.<?= Code::Field("Price") ?>,
        <?= Code::Variable("DataView") ?>.<?= Code::Field("Stock") ?>

    )<?= Code::Delimiter ?>

}<?= Code::Delimiter ?>
</code></pre>
        </aside>
    </section>
</article>