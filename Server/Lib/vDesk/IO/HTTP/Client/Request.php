<?php
declare(strict_types=1);

namespace vDesk\IO\HTTP\Client;

use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\FileStream;
use vDesk\IO\Path;
use vDesk\IO\Socket;
use vDesk\Struct\Collections\Dictionary;
use vDesk\Struct\Text;
use vDesk\Struct\Type;
use vDesk\IO\HTTP\Method;
use vDesk\Utils\Log;

class Request {
    
    protected Socket $Socket;
    
    /**
     * The CGI parameters of the Request.
     *
     * @var \vDesk\Struct\Collections\Dictionary
     */
    public Dictionary $Parameters;
    
    /**
     * The multipart boundary of the Request.
     *
     * @var null|string
     */
    private ?string $Boundary;
    
    /**
     * The default HTTP headers of the Request.
     */
    public const DefaultHeaders = [
        "Connection"   => "close",
        "User-Agent"   => self::class,
        "Content-Type" => "text/plain"
    ];
    
    /**
     * Initializes an new instance of the Request class.
     *
     * @param string   $Method     Initializes the Request with the specified HTTP method.
     * @param string   $Host       Initializes the Request with the specified target host.
     * @param int      $Port       Initializes the Request with the specified port.
     * @param string   $Resource   Initializes the Request with the specified resource location.
     * @param array    $Headers    Initializes the Request with the specified set of HTTP headers,
     * @param iterable $Parameters Initializes the Request with the specified map of CGI parameters.
     * @param mixed    $Message    Initializes the Request with the specified message payload.
     *
     * @throws \JsonException
     */
    public function __construct(
        public string $Method = Method::Get,
        public string $Host = "",
        public int $Port = 80,
        public string $Resource = "/",
        public array $Headers = self::DefaultHeaders,
        iterable $Parameters = [],
        public mixed $Message = null
    ) {
        $this->Parameters = new Dictionary($Parameters);
        
        //Calculate size.
        //Welcome to the switch of death...
        switch($Method) {
            case Method::Put:
            case Method::Patch:
                $this->Headers["Content-Length"] = match (Type::Of($Message)) {
                    FileInfo::class => $Message->Size,
                    FileStream::class => File::Size($Message->File),
                    default => \strlen((string)$Message),
                };
                break;
            case Method::Post:
                $this->Headers["Content-Length"] = 0;
                switch($Headers["Content-Type"]) {
                    case "application/x-www-form-urlencoded":
                        foreach($Message as $Key => $Value) {
                            $this->Headers["Content-Length"] += \strlen($Key) + 2 + \strlen((string)$Value);
                        }
                        $this->Headers["Content-Length"]--;
                        break;
                    case "multipart/formdata":
                        $this->Boundary = "--" . \random_int(10000000, 99999999);
                        foreach($Message as $Key => $Value) {
                            $this->Headers["Content-Length"] += 49 + \strlen($Key);
                            $this->Headers["Content-Length"] += match (Type::Of($Value)) {
                                FileInfo::class => $Value->Size + 11 + \strlen($Value->Name) + \strlen($Value->Extension),
                                FileStream::class => File::Size($Value->File) + 10 + \strlen(Path::GetFileName($Value->File, true)),
                                default => \strlen((string)$Value),
                            };
                        }
                        break;
                    case "application/json":
                        switch(Type::Of($Message)) {
                            case Type::String:
                                $this->Headers["Content-Length"] += \strlen($Message);
                                break;
                            default:
                                $this->Message                   = \json_encode($Message, \JSON_THROW_ON_ERROR);
                                $this->Headers["Content-Length"] += \strlen($this->Message);
                        }
                        break;
                    default:
                        $this->Headers["Content-Length"] += \strlen((string)$Message);
                }
        }
        
        $this->Socket = new Socket("tcp://{$Host}:{$Port}", Socket::Remote);
        
    }
    
    /**
     * Sends the Request.
     *
     * @param null|callable $BypassUpstream   Callback to bypass the default message formatting after the request
     *                                        headers have been sent.
     * @param null|callable $BypassDownstream Callback to bypass the default message formatting after the response
     *                                        headers have been received.
     *
     * @return \vDesk\IO\HTTP\Client\Response A Response representing the result of the Request.
     * @throws \JsonException
     */
    public function Send(callable $BypassUpstream = null, callable $BypassDownstream = null): Response {
        if($this->Parameters->Count > 0) {
            $this->Socket->Send(
                "{$this->Method} {$this->Resource}?"
                . \implode("&", $this->Parameters->Map(static fn($Value, $Key): string => "{$Key}={$Value}")->Values)
                . " HTTP/1.1\r\n"
            );
        } else {
            $this->Socket->Send("{$this->Method} {$this->Resource} HTTP/1.1\r\n");
        }
        $this->Socket->Send("Host: {$this->Host}\r\n");
        
        //Send headers.
        foreach($this->Headers as $Header => $Value) {
            $this->Socket->Send("{$Header}: {$Value}\r\n");
        }
        
        //End header section.
        $this->Socket->Send("\r\n");
        
        //Check if the default formatting should get bypassed.
        if($BypassUpstream !== null) {
            $BypassUpstream($this->Socket, $this);
            return new Response($this->Socket, $BypassDownstream);
        }
        
        //Send message.
        //...the madness continues...
        switch($this->Method) {
            case Method::Put:
            case Method::Patch:
                switch(Type::Of($this->Message)) {
                    case FileInfo::class:
                        $Stream = $this->Message->Open();
                        while(!$Stream->EndOfStream()) {
                            $this->Socket->Send($Stream->Read());
                        }
                        break;
                    case FileStream::class:
                        while(!$this->Message->EndOfStream()) {
                            $this->Socket->Send($this->Message->Read());
                        }
                        break;
                    default:
                        $this->Socket->Send((string)$this->Message);
                }
                break;
            case Method::Post:
                switch($this->Headers["Content-Type"]) {
                    case "application/x-www-form-urlencoded":
                        $Separator = "";
                        foreach($this->Message as $Key => $Value) {
                            $this->Socket->Send("{$Separator}{$Key}={$Value}");
                            $Separator = "&";
                        }
                        break;
                    case "multipart/formdata":
                        foreach($this->Message as $Key => $Value) {
                            $this->Socket->Send("{$this->Boundary}\r\n");
                            switch(Type::Of($Value)) {
                                case FileInfo::class:
                                    /** @var \vDesk\IO\FileInfo $Value */
                                    $this->Socket->Send("Content-Disposition: form-data; name=\"{$Key}\"; filename=\"{$Value->Name}.{$Value->Extension}\"\r\n\r\n");
                                    $Stream = $Value->Open();
                                    while(!$Stream->EndOfStream()) {
                                        $this->Socket->Send($Stream->Read());
                                    }
                                    break;
                                case FileStream::class:
                                    /** @var \vDesk\IO\FileStream $Value */
                                    $this->Socket->Send("Content-Disposition: form-data; name=\"{$Key}\"; filename=\"" . Path::GetFileName($Value->File, true) . "\"\r\n\r\n");
                                    while(!$Value->EndOfStream()) {
                                        $this->Socket->Send($Value->Read());
                                    }
                                    break;
                                default:
                                    $this->Socket->Send("Content-Disposition: form-data; name=\"{$Key}\"\r\n\r\n");
                                    $this->Socket->Send((string)$Value);
                                    break;
                            }
                            $this->Socket->Send("\r\n");
                        }
                        break;
                    case "application/json":
                        switch(Type::Of($this->Message)) {
                            case Type::String:
                                $this->Socket->Send($this->Message);
                                break;
                            default:
                                $this->Socket->Send(\json_encode($this->Message, \JSON_THROW_ON_ERROR));
                        }
                        break;
                    default:
                        $this->Socket->Send((string)$this->Message);
                }
        }
        
        return new Response($this->Socket, $BypassDownstream);
        //...and now I need a therapy.
    }
    
    /**
     * Factory method that creates a new instance of the Request class representing a HTTP "GET" request that only
     * supports the transmission of headers and CGI parameters.
     *
     * @param string         $URL        The target URL of the Request.
     * @param array|iterable $Parameters The CGI parameters of the Request.
     * @param array|string[] $Headers    The HTTP headers of the Request.
     *
     * @return \vDesk\IO\HTTP\Client\Request A Request prepared to perform a "GET" request against the specified URL.
     */
    public static function Get(string $URL, iterable $Parameters = [], array $Headers = self::DefaultHeaders): Request {
        [$Host, $Port, $Resource] = static::ParseURL($URL);
        return new static(Method::Get, $Host, $Port, $Resource, $Headers, $Parameters);
    }
    
    /**
     * Factory method that creates a new instance of the Request class representing a HTTP "HEAD" request that only
     * supports the transmission of headers and CGI parameters.
     *
     * @param string         $URL        The target URL of the Request.
     * @param array|iterable $Parameters The CGI parameters of the Request.
     * @param array|string[] $Headers    The HTTP headers of the Request.
     *
     * @return \vDesk\IO\HTTP\Client\Request A Request prepared to perform a "HEAD" request against the specified URL.
     */
    public static function Head(string $URL, iterable $Parameters = [], array $Headers = self::DefaultHeaders): Request {
        [$Host, $Port, $Resource] = static::ParseURL($URL);
        return new static(Method::Head, $Host, $Port, $Resource, $Headers, $Parameters);
    }
    
    /**
     * Factory method that creates a new instance of the Request class representing a HTTP "POST" request that supports
     * the transmission of headers, CGI parameters and custom data.
     *
     * @param string         $URL
     * @param iterable       $Message
     * @param array|iterable $Parameters
     * @param array|string[] $Headers
     *
     * @return \vDesk\IO\HTTP\Client\Request A Request prepared to perform a "POST" request against the specified URL.
     */
    public static function Post(string $URL, iterable $Message, iterable $Parameters = [], array $Headers = self::DefaultHeaders): Request {
        [$Host, $Port, $Resource] = static::ParseURL($URL);
        return new static(Method::Post, $Host, $Port, $Resource, $Headers, $Parameters, $Message);
    }
    
    /**
     * Factory method that creates a new instance of the Request class representing a HTTP "PUT" request that supports
     * the transmission of headers, CGI parameters and binary data.
     *
     * @param string             $URL        The target URL of the Request.
     * @param \vDesk\IO\FileInfo $File       The file to transmit of the Request.
     * @param array|iterable     $Parameters The CGI parameters of the Request.
     * @param array|string[]     $Headers    The HTTP headers of the Request.
     *
     * @return \vDesk\IO\HTTP\Client\Request A Request prepared to perform a "PUT" request against the specified URL.
     */
    public static function Put(string $URL, FileInfo $File, iterable $Parameters = [], array $Headers = self::DefaultHeaders): Request {
        [$Host, $Port, $Resource] = static::ParseURL($URL);
        return new static(Method::Put, $Host, $Port, $Resource, $Headers, $Parameters, $File);
    }
    
    /**
     * Factory method that creates a new instance of the Request class representing a HTTP "PATCH" request that
     * supports the transmission of headers, CGI parameters and binary data.
     *
     * @param string             $URL        The target URL of the Request.
     * @param \vDesk\IO\FileInfo $File       The file to transmit of the Request.
     * @param array|iterable     $Parameters The CGI parameters of the Request.
     * @param array|string[]     $Headers    The HTTP headers of the Request.
     *
     * @return \vDesk\IO\HTTP\Client\Request A Request prepared to perform a "PATCH" request against the specified URL.
     */
    public static function Patch(string $URL, FileInfo $File, iterable $Parameters = [], array $Headers = self::DefaultHeaders): Request {
        [$Host, $Port, $Resource] = static::ParseURL($URL);
        return new static(Method::Patch, $Host, $Port, $Resource, $Headers, $Parameters, $File);
    }
    
    /**
     * Parses an URL into its parts.
     *
     * @param string $URL The URL to parse.
     *
     * @return array An array containing the parts of the specified URL.
     */
    public static function ParseURL(string $URL): array {
        $PortIndex     = Text::IndexOf($URL, ":");
        $ResourceIndex = Text::IndexOf($URL, "/");
        $Host          = null;
        $Port          = 80;
        $Resource      = "/";
        
        //Check if the URL contains a port.
        if($PortIndex > 2) {
            if($ResourceIndex > $PortIndex) {
                $Port     = (int)(string)Text::Substring($URL, $PortIndex + 1, $ResourceIndex - $PortIndex - 1);
                $Resource = (string)Text::Substring($URL, $ResourceIndex);
            } else {
                $Port = (int)(string)Text::Substring($URL, $PortIndex + 1);
            }
            $Host = (string)Text::Substring($URL, 0, $PortIndex);
        } //Check if the URL contains a (query-string)resource-locator.
        else if($ResourceIndex > 2) {
            $Host     = (string)Text::Substring($URL, 0, $ResourceIndex);
            $Resource = (string)Text::Substring($URL, $ResourceIndex);
        } else {
            $Host = $URL;
        }
        
        return [$Host, $Port, $Resource];
    }
    
}