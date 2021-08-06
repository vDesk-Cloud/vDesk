<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Locale\LocaleDictionary;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;

/**
 * Class Locale represents ...
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Locale extends Package {

    /**
     * The name of the Package.
     */
    public const Name = "Locale";

    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";

    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The name of the Package.
     */
    public const Description = "Package enabling multi-language support and providing functionality for managing localized packages";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "vDesk"   => "1.0.1",
        "Modules" => "1.0.0"
    ];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Lib => [
                "vDesk/Locale.js"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Locale.php"
            ],
            self::Lib     => [
                "vDesk/Locale"
            ]
        ]
    ];

    /**
     * The countries of the Package.
     */
    public const Countries = [
        "DE" => [
            "AD" => "Andorra",
            "AE" => "Vereinigte Arabische Emirate",
            "AF" => "Afghanistan",
            "AG" => "Antigua und Barbuda",
            "AI" => "Anguilla",
            "AL" => "Albanien",
            "AM" => "Armenien",
            "AN" => "Niederländische Antillen",
            "AO" => "Angola",
            "AQ" => "Antarktis",
            "AR" => "Argentinien",
            "AS" => "Amerikanisch-Samoa",
            "AT" => "Österreich",
            "AU" => "Australien",
            "AW" => "Aruba",
            "AX" => "Åland",
            "AZ" => "Aserbaidschan",
            "BA" => "Bosnien und Herzegowina",
            "BB" => "Barbados",
            "BD" => "Bangladesch",
            "BE" => "Belgien",
            "BF" => "Burkina Faso",
            "BG" => "Bulgarien",
            "BH" => "Bahrain",
            "BI" => "Burundi",
            "BJ" => "Benin",
            "BL" => "Saint-Barthélemy",
            "BM" => "Bermuda",
            "BN" => "Brunei Darussalam",
            "BO" => "Bolivien",
            "BQ" => "Bonaire, Sint Eustatius und Saba",
            "BR" => "Brasilien",
            "BS" => "Bahamas",
            "BT" => "Bhutan",
            "BV" => "Bouvetinsel",
            "BW" => "Botswana",
            "BY" => "Belarus (Weißrussland)",
            "BZ" => "Belize",
            "CA" => "Kanada",
            "CC" => "Kokosinseln (Keelinginseln)",
            "CD" => "Kongo",
            "CF" => "Zentralafrikanische Republik",
            "CG" => "Republik Kongo",
            "CH" => "Schweiz",
            "CI" => "Elfenbeinküste",
            "CK" => "Cookinseln",
            "CL" => "Chile",
            "CM" => "Kamerun",
            "CN" => "China, Volksrepublik",
            "CO" => "Kolumbien",
            "CR" => "Costa Rica",
            "CU" => "Kuba",
            "CV" => "Kap Verde",
            "CW" => "Curaçao",
            "CX" => "Weihnachtsinsel",
            "CY" => "Zypern",
            "CZ" => "Tschechische Republik",
            "DE" => "Deutschland",
            "DJ" => "Dschibuti",
            "DK" => "Dänemark",
            "DM" => "Dominica",
            "DO" => "Dominikanische Republik",
            "DZ" => "Algerien",
            "EC" => "Ecuador",
            "EE" => "Estland (Reval)",
            "EG" => "Ägypten",
            "EH" => "Westsahara",
            "ER" => "Eritrea",
            "ES" => "Spanien",
            "ET" => "Äthiopien",
            "FI" => "Finnland",
            "FJ" => "Fidschi",
            "FK" => "Falklandinseln (Malwinen)",
            "FM" => "Mikronesien",
            "FO" => "Färöer",
            "FR" => "Frankreich",
            "GA" => "Gabun",
            "GB" => "Großbritannien und Nordirland",
            "GD" => "Grenada",
            "GE" => "Georgien",
            "GF" => "Französisch-Guayana",
            "GG" => "Guernsey (Kanalinsel)",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GL" => "Grönland",
            "GM" => "Gambia",
            "GN" => "Guinea",
            "GP" => "Guadeloupe",
            "GQ" => "Äquatorialguinea",
            "GR" => "Griechenland",
            "GS" => "Südgeorgien und die Südl. Sandwichinseln",
            "GT" => "Guatemala",
            "GU" => "Guam",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HK" => "Hongkong",
            "HM" => "Heard- und McDonald-Inseln",
            "HN" => "Honduras",
            "HR" => "Kroatien",
            "HT" => "Haiti",
            "HU" => "Ungarn",
            "ID" => "Indonesien",
            "IE" => "Irland",
            "IL" => "Israel",
            "IM" => "Insel Man",
            "IN" => "Indien",
            "IO" => "Britisches Territorium im Indischen Ozean",
            "IQ" => "Irak",
            "IR" => "Iran",
            "IS" => "Island",
            "IT" => "Italien",
            "JE" => "Jersey (Kanalinsel)",
            "JM" => "Jamaika",
            "JO" => "Jordanien",
            "JP" => "Japan",
            "KE" => "Kenia",
            "KG" => "Kirgisistan",
            "KH" => "Kambodscha",
            "KI" => "Kiribati",
            "KM" => "Komoren",
            "KN" => "St. Kitts und Nevis",
            "KP" => "Nordkorea",
            "KR" => "Südkorea",
            "KW" => "Kuwait",
            "KY" => "Kaimaninseln",
            "KZ" => "Kasachstan",
            "LA" => "Laos",
            "LB" => "Libanon",
            "LC" => "St. Lucia",
            "LI" => "Liechtenstein",
            "LK" => "Sri Lanka",
            "LR" => "Liberia",
            "LS" => "Lesotho",
            "LT" => "Litauen",
            "LU" => "Luxemburg",
            "LV" => "Lettland",
            "LY" => "Libyen",
            "MA" => "Marokko",
            "MC" => "Monaco",
            "MD" => "Moldawien",
            "ME" => "Montenegro",
            "MF" => "Saint-Martin (franz. Teil)",
            "MG" => "Madagaskar",
            "MH" => "Marshallinseln",
            "MK" => "Mazedonien",
            "ML" => "Mali",
            "MM" => "Myanmar (Burma)",
            "MN" => "Mongolei",
            "MO" => "Macau",
            "MP" => "Nördliche Marianen",
            "MQ" => "Martinique",
            "MR" => "Mauretanien",
            "MS" => "Montserrat",
            "MT" => "Malta",
            "MU" => "Mauritius",
            "MV" => "Malediven",
            "MW" => "Malawi",
            "MX" => "Mexiko",
            "MY" => "Malaysia",
            "MZ" => "Mosambik",
            "NA" => "Namibia",
            "NC" => "Neukaledonien",
            "NE" => "Niger",
            "NF" => "Norfolkinsel",
            "NG" => "Nigeria",
            "NI" => "Nicaragua",
            "NL" => "Niederlande",
            "NO" => "Norwegen",
            "NP" => "Nepal",
            "NR" => "Nauru",
            "NU" => "Niue",
            "NZ" => "Neuseeland",
            "OM" => "Oman",
            "PA" => "Panama",
            "PE" => "Peru",
            "PF" => "Französisch-Polynesien",
            "PG" => "Papua-Neuguinea",
            "PH" => "Philippinen",
            "PK" => "Pakistan",
            "PL" => "Polen",
            "PM" => "St. Pierre und Miquelon",
            "PN" => "Pitcairninseln",
            "PR" => "Puerto Rico",
            "PS" => "Palästina",
            "PT" => "Portugal",
            "PW" => "Palau",
            "PY" => "Paraguay",
            "QA" => "Katar",
            "RE" => "Réunion",
            "RO" => "Rumänien",
            "RS" => "Serbien",
            "RU" => "Russische Föderation",
            "RW" => "Ruanda",
            "SA" => "Saudi-Arabien",
            "SB" => "Salomonen",
            "SC" => "Seychellen",
            "SD" => "Sudan",
            "SE" => "Schweden",
            "SG" => "Singapur",
            "SH" => "St. Helena",
            "SI" => "Slowenien",
            "SJ" => "Svalbard und Jan Mayen",
            "SK" => "Slowakei",
            "SL" => "Sierra Leone",
            "SM" => "San Marino",
            "SN" => "Senegal",
            "SO" => "Somalia",
            "SR" => "Suriname",
            "SS" => "Sudsudan!Südsudan",
            "ST" => "São Tomé und Príncipe",
            "SV" => "El Salvador",
            "SX" => "Sint Maarten (niederl. Teil)",
            "SY" => "Syrien",
            "SZ" => "Swasiland",
            "TC" => "Turks- und Caicosinseln",
            "TD" => "Tschad",
            "TF" => "Französische Süd- und Antarktisgebiete",
            "TG" => "Togo",
            "TH" => "Thailand",
            "TJ" => "Tadschikistan",
            "TK" => "Tokelau",
            "TL" => "Timor-Leste",
            "TM" => "Turkmenistan",
            "TN" => "Tunesien",
            "TO" => "Tonga",
            "TR" => "Türkei",
            "TT" => "Trinidad und Tobago",
            "TV" => "Tuvalu",
            "TW" => "Taiwan",
            "TZ" => "Tansania",
            "UA" => "Ukraine",
            "UG" => "Uganda",
            "UM" => "Amerikanisch-Ozeanien",
            "US" => "Vereinigte Staaten von Amerika",
            "UY" => "Uruguay",
            "UZ" => "Usbekistan",
            "VA" => "Vatikanstadt",
            "VC" => "St. Vincent und die Grenadinen",
            "VE" => "Venezuela",
            "VG" => "Britische Jungferninseln",
            "VI" => "Amerikanische Jungferninseln",
            "VN" => "Vietnam",
            "VU" => "Vanuatu",
            "WF" => "Wallis und Futuna",
            "WS" => "Samoa",
            "YE" => "Jemen",
            "YT" => "Mayotte",
            "ZA" => "Südafrika",
            "ZM" => "Sambia",
            "ZW" => "Simbabwe"
        ],
        "EN" => [
            "AD" => "Andorra",
            "AE" => "United Arab Emirates",
            "AF" => "Afghanistan",
            "AG" => "Antigua and Barbuda",
            "AI" => "Anguilla",
            "AL" => "Albania",
            "AM" => "Armenia",
            "AN" => "Netherlands Antilles",
            "AO" => "Angola",
            "AQ" => "Antarctic",
            "AR" => "Argentina",
            "AS" => "American Samoa",
            "AT" => "Austria",
            "AU" => "Australia",
            "AW" => "Aruba",
            "AX" => "Åland",
            "AZ" => "Azerbaijan",
            "BA" => "Bosnia and Herzegovina",
            "BB" => "Barbados",
            "BD" => "Bangladesh",
            "BE" => "Belgium",
            "BF" => "Burkina Faso",
            "BG" => "Bulgaria",
            "BH" => "Bahrain",
            "BI" => "Burundi",
            "BJ" => "Benin",
            "BL" => "Saint-Barthélemy",
            "BM" => "Bermuda",
            "BN" => "Brunei Darussalam",
            "BO" => "Bolivia",
            "BQ" => "Bonaire, Sint Eustatius and Saba",
            "BR" => "Brazil",
            "BS" => "Bahamas",
            "BT" => "Bhutan",
            "BV" => "Bouvet Island",
            "BW" => "Botswana",
            "BY" => "Belarus",
            "BZ" => "Belize",
            "CA" => "Canada",
            "CC" => "Cocos Islands (Keeling Islands)",
            "CD" => "Congo",
            "CF" => "Central African Republic",
            "CG" => "Republic of the Congo",
            "CH" => "Switzerland",
            "CI" => "Ivory Coast",
            "CK" => "Cook Islands",
            "CL" => "Chile",
            "CM" => "Cameroon",
            "CN" => "China",
            "CO" => "Colombia",
            "CR" => "Costa Rica",
            "CU" => "Cuba",
            "CV" => "Cape Verde",
            "CW" => "Curacao",
            "CX" => "Christmas island",
            "CY" => "Cyprus",
            "CZ" => "Czech Republic",
            "DE" => "Germany",
            "DJ" => "Djibouti",
            "DK" => "Denmark",
            "DM" => "Dominica",
            "DO" => "Dominican Republic",
            "DZ" => "Algeria",
            "EC" => "Ecuador",
            "EE" => "Estonia (Reval)",
            "EG" => "Egypt",
            "EH" => "Western Sahara",
            "ER" => "Eritrea",
            "ES" => "Spain",
            "ET" => "Ethiopia",
            "FI" => "Finland",
            "FJ" => "Fiji",
            "FK" => "Falkland Islands (Malwinen)",
            "FM" => "Micronesia",
            "FO" => "Faroe",
            "FR" => "France",
            "GA" => "Gabon",
            "GB" => "Great Britain and northern Ireland",
            "GD" => "Grenada",
            "GE" => "Georgia",
            "GF" => "French Guiana",
            "GG" => "Guernsey (Channel Island)",
            "GH" => "Ghana",
            "GI" => "Gibraltar",
            "GL" => "Greenland",
            "GM" => "Gambia",
            "GN" => "Guinea",
            "GP" => "Guadeloupe",
            "GQ" => "Equatorial Guinea",
            "GR" => "Greece",
            "GS" => "South Georgia and the South sandwich Islands",
            "GT" => "Guatemala",
            "GU" => "Guam",
            "GW" => "Guinea-Bissau",
            "GY" => "Guyana",
            "HK" => "Hongkong",
            "HM" => "Heard and McDonald Islands",
            "HN" => "Honduras",
            "HR" => "Croatia",
            "HT" => "Haiti",
            "HU" => "Hungary",
            "ID" => "Indonesia",
            "IE" => "Ireland",
            "IL" => "Israel",
            "IM" => "Isle of Man",
            "IN" => "India",
            "IO" => "British Indian Ocean Territory",
            "IQ" => "Iraq",
            "IR" => "Iran",
            "IS" => "Iceland",
            "IT" => "Italy",
            "JE" => "Jersey (Channel Island)",
            "JM" => "Jamaica",
            "JO" => "Jordan",
            "JP" => "Japan",
            "KE" => "Kenya",
            "KG" => "Kyrgyzstan",
            "KH" => "Cambodia",
            "KI" => "Kiribati",
            "KM" => "Comoros",
            "KN" => "St. Kitts and Nevis",
            "KP" => "North Korea",
            "KR" => "South Korea",
            "KW" => "Kuwait",
            "KY" => "Cayman Islands",
            "KZ" => "Kazakhstan",
            "LA" => "Laos",
            "LB" => "Lebanon",
            "LC" => "St. Lucia",
            "LI" => "Liechtenstein",
            "LK" => "Sri Lanka",
            "LR" => "Liberia",
            "LS" => "Lesotho",
            "LT" => "Lithuania",
            "LU" => "Luxembourg",
            "LV" => "Latvia",
            "LY" => "Libya",
            "MA" => "Morocco",
            "MC" => "Monaco",
            "MD" => "Moldova",
            "ME" => "Montenegro",
            "MF" => "Saint-Martin (French part)",
            "MG" => "Madagascar",
            "MH" => "Marshall Islands",
            "MK" => "Macedonia",
            "ML" => "Mali",
            "MM" => "Myanmar (Burma)",
            "MN" => "Mongolia",
            "MO" => "Macau",
            "MP" => "Northern Mariana Islands",
            "MQ" => "Martinique",
            "MR" => "Mauritania",
            "MS" => "Montserrat",
            "MT" => "Malta",
            "MU" => "Mauritius",
            "MV" => "Maldives",
            "MW" => "Malawi",
            "MX" => "Mexico",
            "MY" => "Malaysia",
            "MZ" => "Mozambique",
            "NA" => "Namibia",
            "NC" => "Caledonia",
            "NE" => "Niger",
            "NF" => "Norfolk island",
            "NG" => "Nigeria",
            "NI" => "Nicaragua",
            "NL" => "Netherlands",
            "NO" => "Norway",
            "NP" => "Nepal",
            "NR" => "Nauru",
            "NU" => "Niue",
            "NZ" => "New Zealand",
            "OM" => "Oman",
            "PA" => "Panama",
            "PE" => "Peru",
            "PF" => "French Polynesia",
            "PG" => "Papua New Guinea",
            "PH" => "Philippines",
            "PK" => "Pakistan",
            "PL" => "Poland",
            "PM" => "St. Pierre and Miquelon",
            "PN" => "Pitcairn Islands",
            "PR" => "Puerto Rico",
            "PS" => "Palestine",
            "PT" => "Portugal",
            "PW" => "Palau",
            "PY" => "Paraguay",
            "QA" => "Qatar",
            "RE" => "Reunion",
            "RO" => "Romania",
            "RS" => "Serbia",
            "RU" => "Russian Federation",
            "RW" => "Rwanda",
            "SA" => "Saudi Arabia",
            "SB" => "Solomon Islands",
            "SC" => "Seychelles",
            "SD" => "Sudan",
            "SE" => "Sweden",
            "SG" => "Singapore",
            "SH" => "St. Helena",
            "SI" => "Slovenia",
            "SJ" => "Svalbard and Jan Mayen",
            "SK" => "Slovakia",
            "SL" => "Sierra Leone",
            "SM" => "San Marino",
            "SN" => "Senegal",
            "SO" => "Somalia",
            "SR" => "Suriname",
            "SS" => "South Sudan",
            "ST" => "Sao Tome and Principe",
            "SV" => "El Salvador",
            "SX" => "Sint Maarten (Dutch part)",
            "SY" => "Syria",
            "SZ" => "Swaziland",
            "TC" => "Turks and Caicos Islands",
            "TD" => "Chad",
            "TF" => "French Southern and Antarctic Lands",
            "TG" => "Togo",
            "TH" => "Thailand",
            "TJ" => "Tajikistan",
            "TK" => "Tokelau",
            "TL" => "Timor-Leste",
            "TM" => "Turkmenistan",
            "TN" => "Tunisia",
            "TO" => "Tonga",
            "TR" => "Turkey",
            "TT" => "Trinidad and Tobago",
            "TV" => "Tuvalu",
            "TW" => "Taiwan",
            "TZ" => "Tanzania",
            "UA" => "Ukraine",
            "UG" => "Uganda",
            "UM" => "American Oceania",
            "US" => "United States of America",
            "UY" => "Uruguay",
            "UZ" => "Uzbekistan",
            "VA" => "Vatican City",
            "VC" => "St. Vincent and the Grenadines",
            "VE" => "Venezuela",
            "VG" => "British Virgin Islands",
            "VI" => "American Virgin Islands",
            "VN" => "Vietnam",
            "VU" => "Vanuatu",
            "WF" => "Wallis and Futuna",
            "WS" => "Samoa",
            "YE" => "Yemen",
            "YT" => "Mayotte",
            "ZA" => "South Africa",
            "ZM" => "Zambia",
            "ZW" => "Zimbabwe"
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {

        Expression::Create()
                  ->Database("Locale")
                  ->Execute();

        Expression::Create()
                  ->Table(
                      "Locale.Translations",
                      [
                          "Locale" => ["Type" => Type::Char, "Size" => 2, "Collation" => Collation::ASCII],
                          "Domain" => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Tag"    => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Value"  => ["Type" => Type::Text, "Collation" => Collation::UTF8]
                      ],
                      [
                          "Primary" => ["Fields" => ["Locale", "Domain" => 255, "Tag" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Locale.Countries",
                      [
                          "Code"   => ["Type" => Type::Char, "Size" => 2, "Collation" => Collation::ASCII],
                          "Locale" => ["Type" => Type::Char, "Size" => 2, "Collation" => Collation::ASCII],
                          "Name"   => ["Type" => Type::TinyText, "Collation" => Collation::UTF8]
                      ],
                      [
                          "Primary" => ["Fields" => ["Code", "Locale"]]
                      ]
                  )
                  ->Execute();

        //Install countries.
        foreach(self::Countries as $Locale => $Countries) {
            $Expression = Expression::Insert()
                                    ->Into(
                                        "Locale.Countries",
                                        ["Locale", "Code", "Name"]
                                    );
            $Values     = [];
            foreach($Countries as $Code => $Name) {
                $Values[] = [$Locale, $Code, $Name];
            }
            $Expression->Values(...$Values)
                       ->Execute();
        }

        //Install Module.
        /** @var \Modules\Locale $Locale */
        $Locale = \vDesk\Modules::Locale();
        $Locale->Commands->Add(
            new Command(
                null,
                $Locale,
                "GetCountries",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Locale->Commands->Add(
            new Command(
                null,
                $Locale,
                "GetLocales",
                true,
                false
            )
        );
        $Locale->Commands->Add(
            new Command(
                null,
                $Locale,
                "GetTranslation",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Domain", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Tag", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Locale->Commands->Add(
            new Command(
                null,
                $Locale,
                "GetDomain",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Domain", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Locale->Commands->Add(
            new Command(
                null,
                $Locale,
                "GetLocale",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Locale->Save();

        //Extract files.
        self::Deploy($Phar, $Path);

    }

    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {

        //Uninstall Module.
        /** @var \Modules\Locale $Locale */
        $Locale = \vDesk\Modules::Locale();
        $Locale->Delete();

        //Drop database.
        Expression::Drop()
                  ->Database("Locale")
                  ->Execute();

        //Delete files.
        self::Undeploy();

    }
}