<?php

use vDesk\Documentation\Code;
use vDesk\Documentation\Code\Language;
use vDesk\Pages\Functions;

?>
<article>
    <header>
    <h2>Modules & Commands</h2>
    <p>
        This tutorial describes the registration and execution of Commands against modules of the server.
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
    </ul>
    </header>
    <section id="ApplicationFlow">
        <h3 >Application-flow</h3>
        <p>
            From a global point of view, the application flow of vDesk can be described as a client/server "View-(Controller-Model)-system, where the client represents the "View",
            <br> whereas the server represents the "Controller"-host which manage the "Models".
        </p>
        <p>
            The application-flow exists usually of
        </p>
    </section>
    <section id="Overview">
    <h4>Overview</h4>
    <img alt="Conceptual description of vDesk's application flow" title="Conceptual description of vDesk's application flow" style="width: 100%"
         src="<?= Functions::Image("Documentation", "Concept.svg") ?>">
    </section>
    <section id="OverviewClient">
        <h4>Client</h4>
        <h5>Load</h5>
        <ol>
            <li>Performing a handshake with the specified server while sending a list of installed client-side packages and their versions to check for compatibility.</li>
            <li>Perform a login with the specified or stored credentials.</li>
            <li>Running startup-scripts located in <code class="Inline"><?= Code::Variable("vDesk") ?>.<?= Code::Field("Load") ?></code>-namespace.</li>
            <li>Initializing installed Modules.</li>
        </ol>
        <h5>Run</h5>
        <ol>
            <li>Modules fetch required Models from the server and represents them as their according ViewModel</li>
            <li>Modules and/or ViewModels handling user-interaction</li>
            <li>Execute certain Command/s against the server</li>
            <li>Send command-payload through XMLHttpRequest</li>
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
    <h3 id="Modules">Modules</h3>
    <p>
        Modules are the controller-equivalent of MVC-frameworks.<br>
        The module-system of vDesk, is split into a server- and client-section.
    </p>
    <h4 id="Client">Client</h4>
    <p>
        Client-side Modules are supposed to manage the UI and fetching or manipulating data by executing Commands against the server.
    </p>
    <p>
        To register a class as a client-side Module, the class to be located in the <code class="Inline">Modules</code>-namespace and must implement either the <code class="Inline">vDesk.Modules.<?= Code::Class("IModule") ?></code>-
        or <code class="Inline">vDesk.Modules.<?= Code::Class("IVisualModule") ?></code>-interface.
    </p>
    <h4 id="Server">Server</h4>
    <p>
        Server-side Modules are supposed to process commands, validate parameters and manage Models.
    </p>
    <p>
        To register a class as a server-side Module, the class has to be located in the <code class="Inline">Modules</code>-namespace and must inherit from the <code class="Inline">\vDesk\Modules\<?= Code::Class("Module") ?></code>-class.<br>
        The Module base-class implements the <code class="Inline">\vDesk\Data\<?= Code::Class("IModel") ?></code>-interface which allows to simply instantiate the Module once and call
        its "Save"-method to register it.
    </p>
    <p>
        Modules are stored in the <code class="Inline"><?= Code::Class("Modules") ?>.<?= Code::Const("Modules") ?></code>-table.
    </p>
    <hr>
    <h3 id="Commands">Commands</h3>
    <p>
        vDesk's public API is exposed via a collection of "Commands" which are stored in the database, each referencing a target module and method to execute, as well as describing the
        parameters of the command.
    </p>
    <p>
        Commands are stored in the <code class="Inline"><?= Code::Class("Modules") ?>.<?= Code::Const("Commands") ?></code>-table.
    </p>
    <div style="display: flex; justify-content: space-around;">
<pre style="margin: 10px"><code><?= Language::JS ?>
<?= Code::Variable("vDesk") ?>.<?= Code::Class("Connection") ?>.<?= Code::Function("Send") ?>(
    <?= Code::New ?> <?= Code::Variable("vDesk") ?>.<?= Code::Field("Modules") ?>.<?= Code::Class("Command") ?>(
        {
            <?= Code::Variable("Module") ?>:  <?= Code::String("\"Archive\"") ?>,
            <?= Code::Variable("Command") ?>: <?= Code::String("\"Upload\"") ?>,
            <?= Code::Variable("Parameters") ?>: {
                <?= Code::Variable("Parent") ?>: ParentElement,
                <?= Code::Variable("File") ?>:   File,
            },
            <?= Code::Variable("Ticket") ?>: <?= Code::Variable("vDesk") ?>.<?= Code::Field("User") ?>.<?= Code::Field("Ticket") ?>
        
        },
        Response => {
            <?= Code::If ?>(Response.<?= Code::Field("Status") ?>){
                <?= Code::Constant ?> <?= Code::Const("UploadedElement") ?> = <?= Code::Variable("vDesk") ?>.<?= Code::Field("Archive") ?>.<?= Code::Class("Element") ?>.<?= Code::Function("FromDataView") ?>(Response.<?= Code::Field("Data") ?>)<?= Code::Delimiter ?>
        
                <?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Const("UploadedElement") ?>.<?= Code::Field("ID") ?>)<?= Code::Delimiter ?>
        
            } <?= Code::Else ?> {
                <?= Code::Function("alert") ?>(<?= Code::String("`What went wrong: ") ?>${Response.<?= Code::Field("Data") ?>}<?= Code::String(".`") ?>)<?= Code::Delimiter ?>
        
            }
        }
    )
)<?= Code::Delimiter ?>
</code></pre>
    <pre style="margin: 10px"><code><?= Code\Language::PHP ?>
<?= Code::Variable("\$ParentElement") ?> = <?= Code::New ?> \vDesk\IO\<?= Code::Class("FileInfo") ?>(<?= Code::Int("12") ?>)<?= Code::Delimiter ?>
            
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
    </div>
    <h4 id="Parameters">Parameters</h4>
    <p>
        Commands can require any parameters if needed, describing the name, type, nullabillity and optionality.
    </p>
    <p>
        Parameters are stored in the <code class="Inline"><?= Code::Class("Modules") ?>.<?= Code::Const("Parameters") ?></code>-table.
    </p>
<pre><code><?= Language::PHP ?>
<?= Code::Use ?> vDesk\Modules\<?= Code::Class("Command") ?><?= Code::Delimiter ?>
        
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
    <h5 id="Validation">Validation</h5>
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
</article>