<?php
declare(strict_types=1);

namespace Pages;

use Pages\Reflect\Index;
use Pages\Reflect\Documentation;
use vDesk\Pages\Page;

class Reflect extends Page {

    /**
     * Expression for parsing the description part of annotations in DocBlocks.
     */
    public const Description = "/(?<=\/\*\*).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@var'-tags of DocBlocks.
     */
    public const Variable = "/(?<= \* @var ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@since'-tags of DocBlocks.
     */
    public const Since = "/(?<= \* @since ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the '@ignore'-tags of DocBlocks.
     */
    public const Ignore = "/(?<= \* )@ignore/s";

    /**
     * Expression for parsing the '@inheritDoc'-tags of DocBlocks.
     */
    public const Inheritdoc = "/(?<= \* )@inheritDoc/s";

    /**
     * Initializes a new instance of the Reflect class.
     *
     * @param null|iterable                     $Values      Initializes the Reflect Page with the specified Dictionary of values.
     * @param null|iterable                     $Templates   Initializes the Reflect Page with the specified Collection of templates.
     * @param null|iterable                     $Stylesheets Initializes the Reflect Page with the specified Collection of stylesheets.
     * @param null|iterable                     $Scripts     Initializes the Reflect Page with the specified Collection of scripts.
     * @param null|\Reflector                   $Reflector   Initializes the Reflect Page with the specified Reflector.
     * @param null|\Pages\Reflect\Documentation $Documentation
     * @param null|\Pages\Reflect\Index         $Index
     * @param null|string                       $ReferenceName
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        public ?\Reflector $Reflector = null,
        public ?Documentation $Documentation = null,
        public ?Index $Index = null,
        public ?string $ReferenceName = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        if($Reflector instanceof \ReflectionClass) {
            $Name = \str_replace(["\\", "/"], [".", "*"], $Reflector->name);
            if($Reflector->isInterface()) {
                $this->Documentation = new Reflect\InterfacePage(Reflector: $Reflector);
                $this->ReferenceName = "Interface.$Name";
            } else if($Reflector->isTrait()) {
                $this->Documentation = new Reflect\TraitPage(Reflector: $Reflector);
                $this->ReferenceName = "Trait.$Name";
            } else {
                $this->Documentation = new Reflect\ClassPage(Reflector: $Reflector);
                $this->ReferenceName = "Class.$Name";
            }
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
            return new Reflect\InterfacePage(Reflector: $Class);
        }
        if($Class->isTrait()) {
            return new Reflect\TraitPage(Reflector: $Class);
        }
        return new Reflect\ClassPage(Reflector: $Class);
    }

    public static function Link(\Reflector $Target, bool $Short = false): string {

        return "<a href=\"" . match ($Target::class) {
                \ReflectionClass::class => match ($Target->isInternal()) {
                    false => match (true) {
                                 $Target->isInterface() => "./Interface.",
                                 $Target->isTrait() => "./Trait.",
                                 default => "./Class.",
                             } . \str_replace("\\", ".", $Target->name). ".html\">" . ($Short ? $Target->getShortName() : "\\" . $Target->name),
                    default => "https://www.php.net/manual/en/class." . \strtolower($Target->getShortName()) . ".php\" target=\"_blank\">\\" . $Target->getShortName()
                },
                \ReflectionMethod::class => match (true) {
                                                $Target->getDeclaringClass()->isInterface() => "./Interface.",
                                                $Target->getDeclaringClass()->isTrait() => "./Trait.",
                                                default => "./Class.",
                                            }
                                            . \str_replace("\\", ".", $Target->getDeclaringClass()->name)
                                            . ".html#Method.{$Target->name}\">{$Target->name}",
                \ReflectionClassConstant::class => match (true) {
                                                       $Target->getDeclaringClass()->isInterface() => "./Interface.",
                                                       $Target->getDeclaringClass()->isTrait() => "./Trait.",
                                                       default => "./Class.",
                                                   } . \str_replace("\\", ".", $Target->getDeclaringClass()->name)
                                                   . ".html#Constant.{$Target->name}\">{$Target->name}",
                \ReflectionProperty::class => match (true) {
                                                  $Target->getDeclaringClass()->isInterface() => "./Interface.",
                                                  $Target->getDeclaringClass()->isTrait() => "./Trait.",
                                                  default => "./Class.",
                                              } . \str_replace("\\", ".", $Target->getDeclaringClass()->name)
                                              . ".html#Property.{$Target->name}\">{$Target->name}",
            } . "</a>";

    }

}