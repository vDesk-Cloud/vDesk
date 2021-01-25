<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Struct\Properties;

/**
 * Represents an image providing functionality for editing.
 *
 * @property string $Type   Gets or sets the image type of the Image.
 * @property int    $Width  Gets or sets the width of the Image.
 * @property int    $Height Gets or sets the height of the Image.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Image {
    
    use Properties;
    
    /**
     * JPEG Image type.
     */
    public const JPG = "jpg";
    
    /**
     * JPEG Image type.
     */
    public const JPEG = "jpeg";
    
    /**
     * Portable network graphics Image type.
     */
    public const PNG = "png";
    
    /**
     * Gif Image type.
     */
    public const GIF = "gif";
    
    /**
     * Bitmap Image type.
     */
    public const BMP = "bmp";
    
    /**
     * The supported types of the Image.
     */
    public const Types
        = [
            self::JPG,
            self::JPEG,
            self::PNG,
            self::GIF,
            self::BMP
        ];
    
    /**
     * The path to the underlying file of the Image.
     *
     * @var string
     */
    private string $Path;
    
    /**
     * The pointer to the underlying image of the Image.
     *
     * @var resource|null
     */
    private $Image;
    
    /**
     * The type of the Image.
     *
     * @var string
     */
    private string $Type;
    
    /**
     * The width of the Image.
     *
     * @var int
     */
    private int $Width;
    
    /**
     * The height of the Image.
     *
     * @var int
     */
    private int $Height;
    
    /**
     * Initializes a new instance of the Image class.
     *
     * @param string $Path Initializes the Image with the specified path to an existing image file.
     */
    public function __construct(string $Path) {
        $this->Type  = Path::GetExtension($Path);
        $this->Image = match (\strtolower($this->Type)) {
            static::PNG => \imagecreatefrompng($Path),
            static::JPG, static::JPEG => \imagecreatefromjpeg($Path),
            static::GIF => \imagecreatefromgif($Path),
            static::BMP => \imagecreatefrombmp($Path),
            default => throw new \InvalidArgumentException("'$this->Type' is not a supported image type!")
        };
        $this->AddProperties([
            "Type"   => [
                \Get => fn(): string => $this->Type,
                \Set => function(string $Value): void {
                    if(!\in_array($Value, static::Types)) {
                        throw new \InvalidArgumentException("'$Value' is not a supported image type!");
                    }
                    $this->Type = $Value;
                }
            ],
            "Width"  => [
                \Get => fn(): ?int => \imagesx($this->Image),
                \Set => fn(int $Value) => $this->Image = \imagescale($this->Image, $Value)
            ],
            "Height" => [
                \Get => fn(): ?int => \imagesy($this->Image),
                \Set => fn(int $Value) => $this->Image = \imagescale($this->Image, \imagesx($this->Image), $Value)
            ]
        ]);
    }
    
    /**
     * Saves the Image to a specified file.
     *
     * @param string|null $Path The path to save the Image to.
     */
    public function Save(string $Path = null): void {
        match (\strtolower($this->Type)) {
            static::PNG => \imagepng($this->Image, Path::ChangeExtension($Path ?? $this->Path, static::PNG)),
            static::JPG, static::JPEG => \imagejpeg($this->Image, Path::ChangeExtension($Path ?? $this->Path, static::JPG)),
            static::GIF => \imagegif($this->Image, Path::ChangeExtension($Path ?? $this->Path, static::GIF)),
            static::BMP => \imagebmp($this->Image, Path::ChangeExtension($Path ?? $this->Path, static::BMP))
        };
    }
    
    /**
     * Creates a base64 encoded string representation of the Image.
     *
     * @return string A base64 encoded string representation of the Image.
     */
    public function ToBase64String(): string {
        \ob_start();
        match (\strtolower($this->Type)) {
            static::PNG => \imagepng($this->Image),
            static::JPG, static::JPEG => \imagejpeg($this->Image),
            static::GIF => \imagegif($this->Image),
            static::BMP => \imagebmp($this->Image)
        };
        return "data:image;base64," . \base64_encode(\ob_get_clean());
    }
    
    /**
     *
     */
    public function __destruct() {
        \imagedestroy($this->Image);
    }
    
}
