<?php
declare(strict_types=1);

use vDesk\Documentation\Code;
use vDesk\Pages\Functions;
?>
<article class="Machines">
    <header>
        <h2>Machines</h2>
        <p>
            Machines are OS-independent processes controlled through PHP.
            <br>This document explains how to use, develop and deploy custom Machines.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li>
                <a href="#Introduction">Introduction</a>
            </li>
            <li>
                <a href="#Control">Controlling Machines</a>
            </li>
            <li>
                <a href="#Development">Writing Machines</a>
            </li>
            <li>
                <a href="#Deployment">Deploying Machines</a>
            </li>
        </ul>
    </header>
    <section id="Introduction">
        <h3>Introduction</h3>
        <p>
            Machines are OS-agnostic, database-backed classes being executed with user-context in separate, unique processes.
            <br>A Machine can be thought of an object-oriented process interface, encapsulated into a scheduler Module which continuously calls its <code class="Inline"><?= Code::Function("Run") ?>()</code>-method
            similar to a "main"-loop.
        </p>
        <p>
            Session-independent control is achieved through backing up essential data upon initialization via implementing the <code class="Inline">\vDesk\Data\<?= Code::Class("IModel") ?></code>-interface.
            <br>The scheduling Module will spawn new Machines with its current process ID and initializes them with an additional GUID.
            After startup, the scheduler Module opens a single-byte shared memory page with the PID as the identifier - this is used for communicating with the scheduler from foreign processes.
            <br>
            Scheduling Modules will continuously scan the value of the shared memory page at the beginning of the main-loop and further run, suspend/resume or stop/terminate the process according the value of the shared memory page.
        </p>
        <p style="text-align: center">
            <img style="box-shadow: #333333 3px 3px 3px" src="<?= Functions::Image("Documentation/MachinesArchive.png") ?>" alt="Location of Machines in the Archive">
        </p>
    </section>
    <section id="Control">
        <h3>Controlling Machines</h3>
        <p>
            Machines are primarily controlled over the client-side administration-plugin located under vDesk ➜ Administration ➜ Machines.
        </p>
        <p style="text-align: center">
            <img style="box-shadow: #333333 3px 3px 3px" src="<?= Functions::Image("Documentation/MachinesControl.png") ?>" alt="Location of Machines in the Archive">
        </p>
        <p>
            New Machines can be spawned via selecting the desired Machine in the dropdown-menu and clicking the "Start"-button afterwards.
            <br>The PID and GUID should appear after a short amount of time if the Machine has been successfully started; otherwise a messagebox containing the message-text of any occurred Exception will be displayed.
        </p>
        <p>
            Machines can be suspended by selecting them in the table of running Machines and clicking the "Suspend"-button.
            If an already suspended Machine has been selected, the content of the button will display "Resume" instead and clicking it will continue the execution of the Machine.
        </p>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                Depending on the time a full <code class="Inline"><?= Code::Class("Machine") ?>::<?= Code::Function("Run") ?>()</code>-cycle takes,
                it may come to a delay until the state of a Machine has been changed.
                <br>The same behaviour applies to gracefully shutdown attempts too.
            </p>
        </aside>
        <p>
            To stop a Machine, a click on the "Stop"-button after a running Machine has been selected, will gracefully attempt to stop the Machine.
            If a Machine can't be stopped, clicking on the "Terminate"-button kills the process, whether it is running or not and deletes the database representation of the Machine.
        </p>
        <p>
            The scythe-button can be used to scan and clear the system of zombie-processes.
        </p>
    </section>
    <section id="Development">
        <h3>Writing Machines</h3>
        <p>
            Machines are simple classes which must provide at least an abstract <code class="Inline"><?= Code::Function("Run") ?>()</code>-method, that acts as the payload in a main-loop.
            <br>Custom implementations must be located in the <code class="Inline">\vDesk\Machines</code>-namespace and inherit from the abstract <code class="Inline">\vDesk\Machines\<?= Code::Class("Machine") ?></code>-class.
        </p>
        <h4>Basic Machine implementation</h4>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>


<?= Code::Namespace ?> vDesk\Machines<?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("Worker") ?> <?= Code::Extends ?> <?= Code::Class("Machine") ?> {

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Run") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::Comment("//Do work") ?>

        \<?= Code::Function("sleep") ?>(<?= Code::Int("10") ?>)<?= Code::Delimiter ?>

    }

}</code></pre>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                The scheduler doesn't track the execution time of Machines, custom implementations have to care manually about freeing up CPU-cycles
                by calling <code class="Inline">\<?= Code::Function("sleep") ?>()</code> and/or <code class="Inline">\<?= Code::Function("usleep") ?>()</code>.
            </p>
        </aside>
        <p>
            <br>Machines can implement several callback-methods to react on process-state changes, this includes a <code class="Inline"><?= Code::Function("Start") ?>()</code>-method
            which gets called upon Machine start and can be used as a constructor,
            <br> a <code class="Inline"><?= Code::Function("Suspend") ?>()</code>- and <code class="Inline"><?= Code::Function("Resume") ?>()</code>-method which get called if the Machine has been paused or resumed,
            <br> and a <code class="Inline"><?= Code::Function("Stop") ?>()</code>-method which gets called when a Machine has been stopped or terminated.
        </p>
        <aside class="Note">
            <h4>Note</h4>
            <p>
                The <code class="Inline"><?= Code::Function("Stop") ?>()</code>-method is a combination of a callback and a procedure,
                calling this method will delete the database entry of the Machine and exit the current PHP process with the specified stop code.
            </p>
        </aside>
        <h4>Machine stub skeleton</h4>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::PHP ?>

<?= Code::Declare ?>(strict_types=<?= Code::Int("1") ?>)<?= Code::Delimiter ?>


<?= Code::Namespace ?> vDesk\Machines<?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("Example") ?> <?= Code::Extends ?> <?= Code::Class("Machine") ?> {

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Start") ?>(): <?= Code::Keyword("void") ?> {

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Run") ?>(): <?= Code::Keyword("void") ?> {

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Suspend") ?>(): <?= Code::Keyword("void") ?> {

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Resume") ?>(): <?= Code::Keyword("void") ?> {

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Stop") ?>(<?= Code::Keyword("int") ?> <?= Code::Variable("\$Code") ?> = <?= Code::Int("0") ?>): <?= Code::Keyword("void") ?> {
        <?= Code::Parent ?>::<?= Code::Function("Stop") ?>(<?= Code::Variable("\$Code") ?>)<?= Code::Delimiter ?>

    }

}</code></pre>

    </section>
    <section id="LanguageLevels">
        <h4>Example Machine</h4>
        <p>The following code represents an example implementation of the Machine-pattern as used in the Tasks-Package.</p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Namespace ?> vDesk\Machines<?= Code::Delimiter ?>


<?= Code::Use ?> \vDesk\Configuration\<?= Code::Class("Settings") ?><?= Code::Delimiter ?>

<?= Code::Use ?> \vDesk\DataProvider\<?= Code::Class("Expression") ?><?= Code::Delimiter ?>

<?= Code::Use ?> \vDesk\IO\<?= Code::Class("Path") ?><?= Code::Delimiter ?>

<?= Code::Use ?> \vDesk\Struct\Collections\<?= Code::Class("Collection") ?><?= Code::Delimiter ?>

<?= Code::Use ?> \vDesk\Struct\Collections\<?= Code::Class("Queue") ?><?= Code::Delimiter ?>

<?= Code::Use ?> \vDesk\Tasks\<?= Code::Class("Task") ?><?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("Tasks") ?> <?= Code::Extends ?> <?= Code::Class("Machine") ?> {

    <?= Code::Protected ?> <?= Code::Class("Collection") ?> <?= Code::Field("\$Tasks") ?><?= Code::Delimiter ?>

    <?= Code::Protected ?> <?= Code::Class("Queue") ?> <?= Code::Field("\$Running") ?><?= Code::Delimiter ?>


    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Start") ?>(): <?= Code::Keyword("void") ?> {

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Running") ?> = <?= Code::New ?> <?= Code::Class("Queue") ?>()<?= Code::Delimiter ?>

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?> = <?= Code::New ?> <?= Code::Class("Collection") ?>()<?= Code::Delimiter ?>

        <?= Code::Variable("\$TimeStamp") ?> = \<?= Code::Function("microtime") ?>(<?= Code::Bool("true") ?>)<?= Code::Delimiter ?>


        <?= Code::Comment("//Load Tasks.") ?>

        <?= Code::ForEach ?>(
            <?= Code::Class("Expression") ?>::<?= Code::Function("Select") ?>(<?= Code::String("\"File\"") ?>, <?= Code::String("\"Name\"") ?>)
                      -><?= Code::Function("From") ?>(<?= Code::String("\"Archive.Elements\"") ?>)
                      -><?= Code::Function("Where") ?>([
                    <?= Code::String("\"Parent\"") ?> => <?= Code::Class("Settings") ?>::<?= Code::Field("\$Local") ?>[<?= Code::String("\"Tasks\"") ?>][<?= Code::String("\"Directory\"") ?>],
                    <?= Code::String("\"Extension\"") ?> => <?= Code::String("\"php\"") ?>

                ])
            <?= Code::Keyword("as") ?> <?= Code::Variable("\$Task") ?>

        ) {
            <?= Code::Keyword("include") ?> <?= Code::Class("Settings") ?>::<?= Code::Field("\$Local") ?>[<?= Code::String("\"Archive\"") ?>][<?= Code::String("\"Directory\"") ?>] . <?= Code::Class("Path") ?>::<?= Code::Const("Separator") ?> . <?= Code::Variable("\$Task") ?>[<?= Code::String("\"File\"") ?>]<?= Code::Delimiter ?>

            <?= Code::Variable("\$Class") ?> = <?= Code::String("\"vDesk\\\\Tasks\\\\") ?>{<?= Code::Variable("\$Task") ?>[<?= Code::String("\"Name\"") ?>]}<?= Code::String("\"") ?><?= Code::Delimiter ?>

            <?= Code::If ?>(!\<?= Code::Function("class_exists") ?>(<?= Code::Variable("\$Class") ?>)) {
                <?= Code::Continue ?><?= Code::Delimiter ?>

            }

            <?= Code::Variable("\$Task") ?> = <?= Code::New ?> <?= Code::Variable("\$Class") ?>()<?= Code::Delimiter ?>

            <?= Code::If ?>(!<?= Code::Variable("\$Task") ?> <?= Code::InstanceOf ?> <?= Code::Class("Task") ?>) {
                <?= Code::Continue ?><?= Code::Delimiter ?>

            }
            <?= Code::Variable("\$this") ?>-><?= Code::Function("Add") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

        }
    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Run") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::Variable("\$Start") ?> = \<?= Code::Function("microtime") ?>(<?= Code::Bool("true") ?>)<?= Code::Delimiter ?>


        <?= Code::Comment("//Get pending Tasks.") ?>

        <?= Code::ForEach ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?>-><?= Code::Function("Filter") ?>(<?= Code::Keyword("fn") ?> (<?= Code::Variable("\$Task") ?>) => <?= Code::Variable("\$Task") ?>-><?= Code::Field("TimeStamp") ?> <= <?= Code::Variable("\$Start") ?>) <?= Code::Keyword("as") ?> <?= Code::Variable("\$Task") ?>) {
            <?= Code::Variable("\$this") ?>-><?= Code::Field("Running") ?>-><?= Code::Function("Enqueue") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

        }

        <?= Code::Comment("//Schedule Tasks.") ?>

        <?= Code::ForEach ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("Running") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Task") ?>) {
            <?= Code::If ?>(<?= Code::Variable("\$Task") ?>-><?= Code::Function("Schedule") ?>()) {
                <?= Code::Variable("\$this") ?>-><?= Code::Field("Running") ?>-><?= Code::Function("Enqueue") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

            }
        }

        <?= Code::Comment("//Get next estimated schedule time.") ?>

        <?= Code::Variable("\$Next") ?> = <?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?>-><?= Code::Function("Reduce") ?>(<?= Code::Keyword("fn") ?> (<?= Code::Variable("\$Previous") ?>, <?= Code::Variable("\$Current") ?>) => \<?= Code::Function("min") ?>(<?= Code::Variable("\$Current") ?>-><?= Code::Field("TimeStamp") ?>, <?= Code::Variable("\$Previous") ?>), <?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?>[<?= Code::Int("0") ?>])<?= Code::Delimiter ?>


        <?= Code::Comment("//Skip idle on overtime.") ?>

        <?= Code::Variable("\$Stop") ?> = \<?= Code::Function("microtime") ?>(<?= Code::Bool("true") ?>)<?= Code::Delimiter ?>

        <?= Code::If ?>(<?= Code::Variable("\$Next") ?> <= <?= Code::Variable("\$Stop") ?>) {
            <?= Code::Keyword("return") ?><?= Code::Delimiter ?>

        }

        <?= Code::Comment("//Calculate idle time.") ?>

        <?= Code::Variable("\$Estimated") ?> = <?= Code::Variable("\$Next") ?> - <?= Code::Variable("\$Stop") ?><?= Code::Delimiter ?>

        <?= Code::Variable("\$Seconds") ?> = (<?= Code::Keyword("int") ?>)<?= Code::Variable("\$Estimated") ?><?= Code::Delimiter ?>

        <?= Code::Variable("\$Microseconds") ?> = (<?= Code::Keyword("int") ?>)(\<?= Code::Function("round") ?>(<?= Code::Variable("\$Estimated") ?> - <?= Code::Variable("\$Seconds") ?>, <?= Code::Int("6") ?>) * <?= Code::Int("1000000") ?>)<?= Code::Delimiter ?>


        <?= Code::Comment("//Sleep until next schedule.") ?>

        \<?= Code::Function("usleep") ?>(<?= Code::Variable("\$Microseconds") ?>)<?= Code::Delimiter ?>

        \<?= Code::Function("sleep") ?>(<?= Code::Variable("\$Seconds") ?>)<?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Add") ?>(<?= Code::Class("Task") ?> <?= Code::Variable("\$Task") ?>): <?= Code::Keyword("void") ?> {
        <?= Code::Variable("\$Task") ?>-><?= Code::Function("Start") ?>(<?= Code::Variable("\$this") ?>)<?= Code::Delimiter ?>

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?>-><?= Code::Function("Add") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Schedule") ?>(<?= Code::Class("Task") ?> <?= Code::Variable("\$Task") ?>): <?= Code::Keyword("void") ?> {
        <?= Code::Variable("\$this") ?>-><?= Code::Function("Add") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Running") ?>-><?= Code::Function("Enqueue") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Remove") ?>(<?= Code::Class("Task") ?> <?= Code::Variable("\$Task") ?>): <?= Code::Keyword("void") ?> {
        <?= Code::Variable("\$Task") ?>-><?= Code::Function("Stop") ?>(<?= Code::Int("1") ?>)<?= Code::Delimiter ?>

        <?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?>-><?= Code::Function("Remove") ?>(<?= Code::Variable("\$Task") ?>)<?= Code::Delimiter ?>

    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Suspend") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::ForEach ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Task") ?>) {
            <?= Code::Variable("\$Task") ?>-><?= Code::Function("Suspend") ?>()<?= Code::Delimiter ?>

        }
    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Resume") ?>(): <?= Code::Keyword("void") ?> {
        <?= Code::ForEach ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Task") ?>) {
            <?= Code::Variable("\$Task") ?>-><?= Code::Function("Resume") ?>()<?= Code::Delimiter ?>

        }
    }

    <?= Code::Public ?> <?= Code::Function ?> <?= Code::Function("Stop") ?>(<?= Code::Keyword("int") ?> <?= Code::Variable("\$Code") ?> = <?= Code::Int("0") ?>): <?= Code::Keyword("void") ?> {
        <?= Code::ForEach ?>(<?= Code::Variable("\$this") ?>-><?= Code::Field("Tasks") ?> <?= Code::Keyword("as") ?> <?= Code::Variable("\$Task") ?>) {
            <?= Code::Variable("\$Task") ?>-><?= Code::Function("Stop") ?>(<?= Code::Variable("\$Code") ?>)<?= Code::Delimiter ?>

        }
        <?= Code::Parent ?>::<?= Code::Function("Stop") ?>(<?= Code::Variable("\$Code") ?>)<?= Code::Delimiter ?>

    }

}
</code></pre>

    </section>
    <section id="Deployment">
        <h4>Deploying Machines</h4>
        <p>
            Machines can be automatically deployed while installation if the Package providing the Machine implements the <code class="Inline">\vDesk\Machines\<?= Code::Class("IPackage") ?>()</code>-interface
            defining a public Machines constant containing an array of Machine source-files to deploy.
        </p>
        <p>
            Alternatively Machines can be manually deployed via importing the class file into the <code class="Inline">Archive/System/Machines</code>-directory.
        </p>
        <pre><code><?= Code\Language::PHP ?>
<?= Code::Namespace ?> vDesk\Packages<?= Code::Delimiter ?>


<?= Code::ClassDeclaration ?> <?= Code::Class("CustomPackage") ?> <?= Code::Extends ?> <?= Code::Class("Package") ?> <?= Code::Implements ?> \vDesk\Machines\<?= Code::Class("IPackage") ?>{

    <?= Code::Public ?> <?= Code::Constant ?> <?= Code::Const("Machines") ?> = [
        <?= Code::String("\"/vDesk/CustomPackage/Machine.php\"") ?>

    ]<?= Code::Delimiter ?>


}</code></pre>
    </section>
</article>