<?php
declare(strict_types=1);

namespace vDesk\IO\HTTP\Client;

use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\IOException;
use vDesk\IO\Path;
use vDesk\IO\Socket;
use vDesk\Utils\Log;

/**
 * Class that represents a HTTP response providing functionality for reading and formatting data from a Socket.
 *
 * @package vDesk\IO\HTTP
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Response {
    
    /**
     * The status code of the Response.
     *
     * @var int
     */
    public int $Code;
    
    /**
     * The headers of the Response.
     *
     * @var array
     */
    public array $Headers = [];
    
    /**
     * @var mixed|string|\vDesk\IO\Socket
     */
    public $Message;
    
    /**
     * Initializes a new instance of the Response class.
     *
     * @param \vDesk\IO\Socket $Socket Initializes the Response with the specified connection Socket.
     * @param null|callable    $Bypass Initializes the Response with the specified callback to bypass the default message formatting after the headers have been received.
     *
     * @throws \JsonException
     * @throws \vDesk\IO\IOException Thrown if a malformed response has been received.
     */
    public function __construct(Socket $Socket, callable $Bypass = null) {
        
        //Parse response status.
        [$Protocol, $Code] = \explode(" ", $Socket->ReadLine());
        [, $Version] = explode("/", $Protocol);
        $this->Code = (int)$Code;
        
        if($this->Code === 0) {
            throw new IOException("Malformed response without status code received.");
        }
        
        //Parse response headers.
        while(($Line = \trim($Socket->ReadLine())) !== "") {
            [$Header, $Value] = \explode(":", $Line);
            $this->Headers[\trim($Header)] = \trim($Value);
        }
        
        //Transform headers.
        if(isset($this->Headers["Content-Length"])) {
            $this->Headers["Content-Length"] = (int)$this->Headers["Content-Length"];
        }
        $this->Headers["Content-Type"] ??= "text/plain";
        if(isset($this->Headers["Allow"])) {
            $this->Headers["Allow"] = \explode(",", $this->Headers["Allow"]);
        }
        if(isset($this->Headers["Date"])) {
            $this->Headers["Date"] = \DateTime::createFromFormat("D, d M Y G", $this->Headers["Date"]);
        }
        if(isset($this->Headers["Expires"])) {
            $this->Headers["Expires"] = \DateTime::createFromFormat("D, d M Y G", $this->Headers["Expires"]);
        }
        if(isset($this->Headers["Last-Modified"])) {
            $this->Headers["Last-Modified"] = \DateTime::createFromFormat("D, d M Y G", $this->Headers["Last-Modified"]);
        }
        
        //Check if the default formatting should get bypassed.
        if($Bypass !== null) {
            $this->Message = $Bypass($Socket, $this);
        } else {
            //Get response message.
            switch($this->Headers["Content-Type"]) {
                case "application/json":
                    if($this->Headers["Transfer-Encoding"] ?? null === "chunked") {
                        //Skip frame length indicator.
                        $Socket->ReadLine();
                        $Message       = $Socket->ReadAll();
                        $this->Message = \json_decode(\substr($Message, 0, -7), true, 512, \JSON_THROW_ON_ERROR);
                    } else {
                        $this->Message = \json_decode($Socket->ReadAll(), true, 512, \JSON_THROW_ON_ERROR);
                    }
                    break;
                case "text/event-stream":
                case "application/octet-stream":
                    $this->Message = $Socket;
                    break;
                default:
                    if(isset($this->Headers["Content-Disposition"])) {
                        [, $Filename] = \explode(";", $this->Headers["Content-Disposition"]);
                        [, $Filename] = \explode("=", $Filename);
                        $Path = \sys_get_temp_dir() . Path::Separator . $Filename;
                        $File = File::Create($Path);
                        
                        if($this->Headers["Transfer-Encoding"] ?? null === "chunked") {
                            //Skip frame length indicator.
                            $Socket->ReadLine();
                            while(!$Socket->EndOfStream()) {
                                $Chunk = $Socket->Read();
                                if($Socket->EndOfStream()) {
                                    $File->Write(\substr($Chunk, 0, -7));
                                }
                                $File->Write($Chunk);
                            }
                        } else {
                            while(!$Socket->EndOfStream()) {
                                $File->Write($Socket->Read());
                            }
                        }
                        
                        $this->Message           = new FileInfo($Path);
                        $this->Message->MimeType = $this->Headers["Content-Type"];
                        \register_shutdown_function(fn() => $this->Message->Delete());
                    } else {
                        while(!$Socket->EndOfStream()) {
                            $this->Message .= $Socket->Read();
                        }
                    }
            }
        }
    }
}