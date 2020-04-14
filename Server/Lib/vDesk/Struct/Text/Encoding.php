<?php
declare(strict_types = 1);

namespace vDesk\Struct\Text;

/**
 * Enumeration of string encoding sequences.
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
final class Encoding {

	/**
	 * UTF-8 encoding.
	 */
	public const UTF8 = "UTF-8";

	/**
	 * ASCII encoding.
	 */
	public const ASCII = "ASCII";

	/**
	 * UTF-16 encoding.
	 */
	public const UTF16 = "UTF16";

	/**
	 * UTF-32 encoding.
	 */
	public const UTF32 = "UTF32";

	private function __construct() {
	}

}

