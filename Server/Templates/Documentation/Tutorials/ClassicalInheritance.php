<?php use vDesk\Documentation\Code; ?>
<article>
    <header>
        <h2>Classical inheritance and interfaces in JavaScript</h2>
        <p>
            vDesk provides a simple way of extending classes and describing their pattern through interfaces.
        </p>
        <h3>Overview</h3>
        <ul class="Topics">
            <li><a href="#Inheritance">Inheritance</a></li>
            <li><a href="#ClassComposition">Class composition</a></li>
            <li><a href="#ExtendingClasses">Extending classes</a></li>
            <li><a href="#Overriding">Overriding</a></li>
            <li><a href="#PreservingAccess">Preserving access</a></li>
            <li><a href="#Interfaces">Interfaces</a></li>
        </ul>
    </header>
    <section id="Inheritance">
        <h3>Inheritance</h3>
        <p>
            The "classical" way of inheritance is achieved via extension of the global "Object" constructor's prototype.<br>
            The inheritance functionality of vDesk will modify the prototype of the extending class' constructor-function to enable "instanceof"-checks against any parent classes.<br>
            This operation is initially performed for each extension once, so any other instances of an already "extending" class, will yield automatically the prototype of it's parent.
        </p>
        <h4>Why vDesk doesn't use prototype based inheritance</h4>
        <p>
            The problem about prototype-based inheritance, is that every instance of an extending class, will reference one and the same parent prototype-instance.<br>
        </p>
    </section>
    <section id="ClassComposition">
        <h3>Class composition</h3>
        <p>
            Class based inheritance is one of vDesk's client side technological "key"-features to build up abstract inheritance trees with a focus of reusabillity.<br>
            For example, the vDesk.Archive.Element.Viewer.Window which acts as an host for displaying files of the archive, is built upon 2 independent controls.
        </p>
        <pre><code><?= Code::BlockComment("//Visualization of an inheritance-tree.") ?>

--- <?= Code::Class("vDesk") ?>.<?= Code::Variable("Controls") ?>.<?= Code::Class("DynamicBox") ?> <?= Code::BlockComment("//Acts as the base-control.") ?>
    
    |
      --- <?= Code::Class("vDesk") ?>.<?= Code::Variable("Controls") ?>.<?= Code::Class("Window") ?> <?= Code::BlockComment("//Composes it's parent to an 'window'-like host-control.") ?>
        
          |
            --- <?= Code::Class("vDesk") ?>.<?= Code::Variable("Archive") ?>.<?= Code::Variable("Element") ?>.<?= Code::Variable("Viewer") ?>.<?= Code::Class("Window") ?> <?= Code::BlockComment("//The 'final' control. Adding further functionalities to the control-composition.") ?>
</code></pre>
    </section>
    <section id="ExtendingClasses">
        <h3>Extending classes</h3>
        <p>
            As of the fact, vDesk's inheritance system relies on JavaScript-properties instead of prototypes, you'll have to extend the desired class in the child's constructor.
        </p>
        <pre><code><?= Code\Language::JS ?>
<?= Code::BlockComment("/**
 * @class Parent class
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("Parent") ?>(<?= Code::Variable("Param") ?>){
    
    <?= Code::Keyword("let") ?> <?= Code::Variable("PrivateValue") ?> = <?= Code::Variable("Param") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("ParentsValue") ?>,{
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: () => PrivateValue,
        <?= Code::Function("set") ?>: Value => PrivateValue = Value
    })<?= Code::Delimiter ?>
        
        
    <?= Code::Comment("//Constructor logic...") ?>
    
    
}

<?= Code::BlockComment("/**
 * @class Child class
 * @extends Parent
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("Child") ?>(<?= Code::Variable("First") ?>, <?= Code::Variable("Second") ?>){

    <?= Code::Comment("//Inheritance with passing through parameters.") ?>
        
    <?= Code::Keyword("this") ?>.<?= Code::Function("Extends") ?>(<?= Code::Variable("Parent") ?>, <?= Code::Variable("Second") ?>)<?= Code::Delimiter ?>
        
        
    <?= Code::Keyword("let") ?> <?= Code::Variable("PrivateValue") ?> = <?= Code::Variable("First") ?><?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("ChildsValue") ?>, {
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: () => PrivateValue,
        <?= Code::Function("set") ?>: Value => PrivateValueParam = Value
    })<?= Code::Delimiter ?>
        
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("Both") ?>, {
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: () => <?= Code::Keyword("this") ?>.ChildsValue + <?= Code::String("\" \"") ?> + <?= Code::Keyword("this") ?>.ParentsValue
    })<?= Code::Delimiter ?>
        
        
    <?= Code::Comment("//Constructor logic...") ?>
    
    
}

<?= Code::Keyword("const") ?> <?= Code::Const("Child") ?> = <?= Code::Keyword("new") ?> <?= Code::Class("Child") ?>(<?= Code::String("hello") ?>, <?= Code::String("world") ?>)<?= Code::Delimiter ?>
        
<?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Child") ?>.<?= Code::Variable("ChildsValue") ?>)<?= Code::Delimiter ?><?= Code::Comment("  //'hello'") ?>

<?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Child") ?>.<?= Code::Variable("ParentsValue") ?>)<?= Code::Delimiter ?><?= Code::Comment(" //'world'") ?>

<?= Code::Class("console") ?>.<?= Code::Function("log") ?>(<?= Code::Variable("Child") ?>.<?= Code::Variable("Both") ?>)<?= Code::Delimiter ?><?= Code::Comment("         //'hello world'") ?>
</code></pre>
    </section>
    <section id="Overriding">
        <h3>Overriding</h3>
        <p>
            To override a member of a derived class, its enough to simply redefine it within the child-class.
        </p>
        <pre><code><?= Code\Language::JS ?>
<?= Code::BlockComment("/**
 * @class Parent class
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("Parent") ?>(<?= Code::Variable("Param") ?>){
    
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("Member") ?>, {
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: ...,
        <?= Code::Function("set") ?>: ...
    })<?= Code::Delimiter ?>
    
    
}

<?= Code::BlockComment("/**
 * @class Child class
 * @extends Parent
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("Child") ?>(){

    <?= Code::Comment("//Inherit from parent.") ?>
        
    <?= Code::Keyword("this") ?>.<?= Code::Function("Extends") ?>(<?= Code::Variable("Parent") ?>)<?= Code::Delimiter ?>
        
        
    <?= Code::Comment("//Override parent's 'Member'-property.") ?>
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("Member") ?>, {
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: ...,
        <?= Code::Function("set") ?>: ...
    })<?= Code::Delimiter ?>
    
    
}
</code></pre>
    </section>
    <section id="PreservingAccess">
        <h3>Preserving access</h3>
        <p>
            All inheriting childclasses will yield an reference to the parent's instance through the non-configurable "Parent"-property.<br>
            So it may possible to override members and further preserve the parent's functionality aswell.
        </p>
        <h5>Example for overriding inherited member preserving the access to the parent's member:</h5>
        <pre><code><?= Code\Language::JS ?>
<?= Code::BlockComment("/**
 * @class Parent class
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("Parent") ?>(<?= Code::Variable("Param") ?>){
    
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("Member") ?>, {
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: ...,
        <?= Code::Function("set") ?>: ...
    })<?= Code::Delimiter ?>
    
    
}

<?= Code::BlockComment("/**
 * @class Child class
 * @extends Parent
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("Child") ?>(){

    <?= Code::Comment("//Inherit from parent.") ?>
        
    <?= Code::Keyword("this") ?>.<?= Code::Function("Extends") ?>(<?= Code::Variable("Parent") ?>)<?= Code::Delimiter ?>
        
        
    <?= Code::Comment("//Override parent's 'Member'-property.") ?>
        
    <?= Code::Class("Object") ?>.<?= Code::Function("defineProperty") ?>(<?= Code::Keyword("this") ?>, <?= Code::String("Member") ?>, {
        <?= Code::Variable("enumerable") ?>: <?= Code::Bool("true") ?>,
        <?= Code::Function("get") ?>: () => <?= Code::Keyword("this") ?>.<?= Code::Variable("Parent") ?>.<?= Code::Variable("Member") ?>,
        <?= Code::Function("set") ?>: Value => {
        
            <?= Code::Comment("//Process value first..") ?>
        
        
            <?= Code::Comment("//Populate if needed to parent.") ?>
        
            <?= Code::Keyword("this") ?>.<?= Code::Variable("Parent") ?>.<?= Code::Variable("Member") ?> = Value<?= Code::Delimiter ?>
        
        
        }
    })<?= Code::Delimiter ?>
        
        
    <?= Code::Comment("//Override method-definitions.") ?>
        
    <?= Code::Keyword("this") ?>.<?= Code::Function("Method") ?> = <?= Code::Keyword("function") ?>(){
        
        <?= Code::Keyword("this") ?>.<?= Code::Variable("Parent") ?>.<?= Code::Function("Method") ?>()<?= Code::Delimiter ?>
        
        
        <?= Code::Comment("//Further processing...") ?>
        
        
    }<?= Code::Delimiter ?>
    
    
}
</code></pre>
    </section>
    <section id="Interfaces">
        <h3>Interfaces</h3>
        <p>
            vDesk extends the prototype of the global "Function" constructor to enable "Interface"-implementations.<br><br>
            To implement an Interface, the class has to call the "Function.Implements"-method, passing the desired Interface to implement.<br>
            After implementation, any instance of the class can be checked <u>at runtime</u> with the "instanceof"-operator for type-compliance.
        </p>
        <pre><code><?= Code\Language::JS ?>
<?= Code::BlockComment("/**
 * Example interface
 * @interface
 */") ?>
        
<?= Code::Keyword("const") ?> <?= Code::Class("IExampleInterface") ?> = <?= Code::Keyword("function") ?>(){}<?= Code::Delimiter ?>
        
<?= Code::Class("IExampleInterface") ?>.<?= Code::Variable("prototype") ?> = {
    
<?= Code::BlockComment("/**
* Does stuff.
* @type {Function}
*/") ?>
        
<?= Code::Variable("DoStuff") ?>: <?= Code::Class("Interface") ?>.<?= Code::Function("MethodNotImplemented") ?>,
    
<?= Code::BlockComment("/**
* Yields the last stuff, the IExampleInterface has done.
* @type {String}
*/") ?>
        
<?= Code::Variable("DoneStuff") ?>: <?= Code::Class("Interface") ?>.<?= Code::Field("FieldNotImplemented") ?>
    
    
}<?= Code::Delimiter ?>
        
        
<?= Code::BlockComment("/**
 * @class Example class
 * @implements IExampleInterface
 */") ?>
        
<?= Code::Keyword("function") ?> <?= Code::Class("ExampleClass") ?>(){

    <?= Code::Comment("//Implementation of IExampleInterface.DoStuff()") ?>
        
        <?= Code::Keyword("this") ?>.<?= Code::Function("DoStuff") ?> = <?= Code::Keyword("function") ?>(){
        <?= Code::Comment("//Code..") ?>
        
    }<?= Code::Delimiter ?>
    
    
}

<?= Code::Comment("//Implement interface.") ?>
        
<?= Code::Class("ExampleClass") ?>.<?= Code::Function("Implements") ?>(<?= Code::Class("IExampleInterface") ?>)<?= Code::Delimiter ?>


<?= Code::Keyword("const") ?> <?= Code::Const("Example") ?> = <?= Code::Keyword("new") ?> <?= Code::Class("ExampleClass") ?>()<?= Code::Delimiter ?>

<?= Code::Comment("//Check implementation/compliance.") ?>

<?= Code::Variable("Example") ?> <?= Code::Keyword("instanceof") ?> <?= Code::Class("IExampleInterface") ?> <?= Code::Comment("//true") ?>

<?= Code::Variable("Example") ?>.<?= Code::Function("DoStuff") ?>()<?= Code::Delimiter ?> <?= Code::Comment("//works") ?>

<?= Code::Keyword("const") ?> <?= Code::Const("LastDone") ?> = <?= Code::Variable("Example") ?>.<?= Code::Variable("DoneStuff") ?><?= Code::Delimiter ?> <?= Code::Comment("//throws error") ?>
</code></pre>
    </section>
</article>