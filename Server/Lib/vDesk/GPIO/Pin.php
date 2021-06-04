<?php
declare(strict_types=1);

namespace vDesk\GPIO;

use vDesk\IO\Directory;
use vDesk\IO\FileStream;
use vDesk\IO\IOException;
use vDesk\IO\Stream\Mode;

$File = new FileStream("/proc/cpuinfo", Mode::Read | Mode::Binary);
while(!$File->EndOfStream()) {
    $Line = $File->ReadLine();
    if(\str_contains($Line, "Revision")) {
        [, $Revision] = \explode(":", $Line);
        \define("Revision", \hexdec($Revision));
        if(\Revision < 4) {
            //V1
            \define("AvailablePins", [0, 1, 4, 7, 8, 9, 10, 11, 14, 15, 17, 18, 21, 22, 23, 24, 25]);
        } else if(\Revision < 16) {
            //V2
            \define("AvailablePins", [2, 3, 4, 7, 8, 9, 10, 11, 14, 15, 17, 18, 22, 23, 24, 25, 27]);
        } else {
            //B+
            \define("AvailablePins", [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27]);
        }
        break;
    }
}
$File->Close();

/**
 * Class that represents a stream based interface for GPIO pins.
 *
 * @package vDesk\GPIO
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Pin extends FileStream {

    /**
     * The currently exported Pins.
     *
     * @var int[]
     */
    public static array $Exported = [];

    /**
     * The path to the GPIO Pins.
     */
    public const Path = "/sys/class/gpio/";

    /**
     * Input direction of Pins.
     */
    public const In = 1;

    /**
     * Output direction of Pins.
     */
    public const Out = 0;

    /**
     * The available I/O directions of GPIO pins.
     */
    public const Directions = [self::Out => "out", self::In => "in"];

    /**
     * The available pins according the board revision.
     */
    public const Available = \AvailablePins;

    /**
     * The number of the Pin.
     *
     * @var int
     */
    private int $Number;

    /**
     * Pin constructor.
     *
     * @param null|string|int $Number
     * @param int             $Direction
     *
     * @throws \vDesk\IO\IOException Thrown if the specified number is not available on the current board.
     */
    public function __construct(mixed $Number = null, int $Direction = 1) {
        $this->Number = (int)$Number;
        if(!\in_array($this->Number, static::Available)) {
            throw new IOException("Pin {$this->Number} is not available on board with revision " . \Revision . "!");
        }
        if(\in_array($this->Number, static::$Exported)) {
            throw new IOException("Pin {$this->Number} has been already exported!");
        }
        if($Direction === static::In) {
            $Mode = Mode::Read | Mode::Binary;
        } else if($Direction === static::Out) {
            $Mode = Mode::Append | Mode::Binary;
        } else {
            throw new IOException("Invalid I/O direction!");
        }

        $this->Export();
        \file_put_contents(static::Path . "gpio{$this->Number}/direction", static::Directions[$Direction]);

        parent::__construct(static::Path . "gpio{$this->Number}/value", $Mode);
    }

    /**
     * Exports the Pin.
     */
    public function Export(): void {
        if(Directory::Exists(static::Path . "gpio{$this->Number}")) {
            $this->Unexport();
            \usleep(120000);
        }
        //Export pin.
        \file_put_contents(static::Path . "export", $this->Number);
        \usleep(120000);
        static::$Exported[] = $this->Number;
    }

    /**
     * Unexports the Pin.
     */
    public function Unexport(): void {
        //Unexport pin.
        \file_put_contents(static::Path . "unexport", $this->Number);
        foreach(static::$Exported as $Index => $Pin) {
            if($this->Number === $Pin) {
                unset(static::$Exported[$Index]);
                break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function Write(mixed $Data, ?int $Amount = null): int {
        return parent::Write((string)$Data, $Amount);
    }

    /**
     * @inheritDoc
     */
    public function Close(): bool {
        $this->Unexport();
        return parent::Close();
    }

}