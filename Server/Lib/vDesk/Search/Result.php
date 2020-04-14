<?php
declare(strict_types=1);

namespace vDesk\Search;

use vDesk\Data\IDataView;
use vDesk\Struct\Properties;

/**
 * Represents a set of data returned by a search operation.
 *
 * @property string $Name Gets or sets the name of the Result.
 * @property string $Type Gets or sets the type of the Result.
 * @property array  $Data Gets or sets the data of the Result.
 * @package vDesk\Search
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Result implements IDataView {

    use Properties;

    /**
     * The name of the Result.
     *
     * @var null|string
     */
    private ?string $Name;

    /**
     * The type of the Result.
     *
     * @var null|string
     */
    private ?string $Type;

    /**
     * The data of the Result.
     *
     * @var array|null
     */
    private ?array $Data;

    /**
     * Initializes a new instance of the Result class.
     *
     * @param string $Name The name of the Result.
     * @param string $Type The type of the Result.
     * @param array  $Data The data of the Result.
     */
    public function __construct(string $Name = null, string $Type = null, array $Data = null) {
        $this->Name = $Name;
        $this->Type = $Type;
        $this->Data = $Data;
        $this->AddProperties([
            "Name" => [
                \Get => fn(): ?string => $this->Name,
                \Set => fn(string $Value) => $this->Name ??= $Value
            ],
            "Type" => [
                \Get => fn(): ?string => $this->Type,
                \Set => fn(string $Value) => $this->Type ??= $Value
            ],
            "Data" => [
                \Get => fn(): ?array => $this->Data,
                \Set => fn(array $Value) => $this->Data ??= $Value
            ]
        ]);
    }

    /**
     * Creates a Result from a specified data view.
     *
     * @param array $DataView The data to use to create a Result.
     *
     * @return \vDesk\Search\Result A Result created from the specified data view.
     */
    public static function FromDataView($DataView): Result {
        return new static(
            $DataView["Name"] ?? "",
            $DataView["Type"] ?? "",
            $DataView["Data"] ?? [],
        );
    }

    /**
     * Creates a data view of the Result.
     *
     * @return array The data view representing the current state of the Result.
     */
    public function ToDataView(): array {
        return [
            "Name" => $this->Name,
            "Type" => $this->Type,
            "Data" => $this->Data
        ];
    }
}
