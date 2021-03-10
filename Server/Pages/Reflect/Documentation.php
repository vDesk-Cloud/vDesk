<?php
declare(strict_types=1);

namespace Pages\Reflect;

use Pages\Reflect;

/**
 * Abstract base class for documentation pages.
 *
 * @package Pages\JAG
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Documentation extends Reflect {

    /**
     * Expression for parsing the description of DocBlocks.
     */
    public const Description = "/(?<=\/\*\*).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@author'-tags of DocBlocks.
     */
    public const Author = "/(?<=@author ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@version'-tags of DocBlocks.
     */
    public const Version = "/(?<=@version ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@method'-tags of DocBlocks.
     */
    public const Method = "/(?<=@method ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@property'-tags of DocBlocks.
     */
    public const Property = "/(?<=@property ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@property-read'-tags of DocBlocks.
     */
    public const PropertyRead = "/(?<=@property-read ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@property-write'-tags of DocBlocks.
     */
    public const PropertyWrite = "/(?<=@property-write ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@property'-tags of DocBlocks.
     */
    public const PropertySignature = "/(?'Type'^\S*) (?'Name'\S*) (?'Description'.*)/";

    /**
     * Initializes a new instance of the Documentation Page class.
     *
     * @param null|iterable                     $Values            Initializes the Documentation Page with the specified Dictionary of values.
     * @param null|iterable                     $Templates         Initializes the Documentation Page with the specified Collection of templates.
     * @param null|iterable                     $Stylesheets       Initializes the Documentation Page with the specified Collection of stylesheets.
     * @param null|iterable                     $Scripts           Initializes the Documentation Page with the specified Collection of scripts.
     * @param null|\Reflector                   $Reflector         Initializes the Documentation Page with the specified ReflectionClass.
     * @param string                            $Name              Initializes the Documentation Page with the specified name.
     * @param string                            $Description       Initializes the Documentation Page with the specified name.
     * @param string[]                          $Authors           Initializes the Documentation Page with the specified set of authors.
     * @param null|string                       $Version           Initializes the Documentation Page with the specified name.
     * @param \Pages\Reflect\Constant[]         $Constants         Initializes the Documentation Page with the specified set of scripts.
     * @param \Pages\Reflect\Property[]         $Properties        Initializes the Documentation Page with the specified set of Properties.
     * @param \Pages\Reflect\Property\Virtual[] $VirtualProperties Initializes the Documentation Page with the specified set of VirtualProperties.
     * @param \Pages\Reflect\Method[]           $Methods           Initializes the Documentation Page with the specified set of Methods.
     * @param \Pages\Reflect\Method\Virtual[]   $VirtualMethods    Initializes the Documentation Page with the specified set of VirtualMethods.
     * @param null|\ReflectionClass             $Parent            Initializes the Documentation Page with the specified name.
     * @param bool                              $Internal          Initializes the Documentation Page with the specified flag indicating the Reflector is internal.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        public ?\Reflector $Reflector = null,
        public string $Name = "",
        public string $Description = "",
        public array $Authors = [],
        public ?string $Version = null,
        public array $Constants = [],
        public array $Properties = [],
        public array $VirtualProperties = [],
        public array $Methods = [],
        public array $VirtualMethods = [],
        public ?\ReflectionClass $Parent = null,
        public bool $Internal = false
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        $this->Reflector = $Reflector;

        $this->Name = $Reflector->name;

        //Parse description.
        $Matches = [];
        if((bool)\preg_match(static::Description, (string)$Reflector->getDocComment(), $Matches)) {
            $this->Description = \trim($Matches[0], "* \n\r");
        }

        //Parse authors.
        if((bool)\preg_match_all(static::Author, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Author) {
                if(\str_contains($Author, "<")) {
                    [$Name, $Mail] = \explode("<", \rtrim($Author, " >\r\n\t"));
                    $this->Authors[\trim($Name)] = $Mail;
                }
            }
        }

        //Parse version.
        if((bool)\preg_match(static::Version, (string)$Reflector->getDocComment(), $Matches)) {
            $this->Version = \trim(\str_replace(" * ", "", $Matches[0]));
        }

        //Get constants.
        foreach($Reflector->getReflectionConstants() as $Constant) {
            $this->Constants[] = new Constant(Reflector: $Constant);
        }

        //Parse virtual properties.
        if((bool)\preg_match_all(static::Property, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Property) {
                $this->VirtualProperties[] = new Property\Virtual(Signature: $Property);
            }
        }

        //Parse read-only properties.
        if((bool)\preg_match_all(static::PropertyRead, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Property) {
                $this->VirtualProperties[] = new Property\Virtual(Signature: $Property, Readonly: true);
            }
        }

        //Parse write-only properties.
        if((bool)\preg_match_all(static::PropertyWrite, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Property) {
                $this->VirtualProperties[] = new Property\Virtual(Signature: $Property, Writeonly: true);
            }
        }

        //Get properties.
        foreach($Reflector->getProperties() as $Property) {
            $this->Properties[] = new Property(Reflector: $Property);
        }

        //Parse virtual methods.
        if((bool)\preg_match_all(static::Method, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Method) {
                $this->VirtualMethods[] = new Method\Virtual(Signature: $Method);
            }
        }

        //Get methods.
        foreach($Reflector->getMethods() as $Method) {
            $this->Methods[] = new Method(Reflector: $Method);
        }

    }


    /**
     * Factory method that creates a new documentation from a specified ReflectionClass.
     *
     * @param \ReflectionClass $Class The ReflectionClass to create a documentation of.
     *
     * @return static A subtype of the Documentation representing the specified ReflectionClass.
     */
    public static function Create(\ReflectionClass $Class): static {
        if($Class->isInterface()) {
            return new JAG\Interface\Documentation(Reflector: $Class);
        }
        if($Class->isTrait()) {
            return new JAG\Trait\Documentation(Reflector: $Class);
        }
        return new JAG\Class\Documentation(Reflector: $Class);
    }

}