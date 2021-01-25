<?php
declare(strict_types=1);

namespace vDesk\Archive;

use vDesk\IO\Image;
use vDesk\IO\Path;

/**
 * Utility class for creating thumbnails from image files.
 *
 * @package vDesk\Archive
 */
abstract class Thumbnail {
    /**
     * Creates a thumbnail from the specified file.
     *
     * @param string $File The file of the image to create a thumbnail of.
     *
     * @return null|string A base64 encoded representation of the specified file; otherwise, null.
     */
    public static function Create(string $File): ?string {
        if(!\in_array(\strtolower(Path::GetExtension($File)), Image::Types)) {
            return null;
        }
        $Image        = new Image($File);
        $Image->Width = 40;
        return $Image->ToBase64String();
    }
}