<?php

namespace Selenior\ApiBundle\Http;

use GuzzleHttp\Exception\BadResponseException;
use JMS\Serializer\Annotation\Exclude;

class ApiResponse
{
    /**
     * @var array Array of reason phrases and their corresponding status codes
     *
     * @Exclude()
     */
    private static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var array|mixed
     */
    protected $body;

    /**
     * @var mixed|null
     */
    protected $meta;

    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $reasonPhrase;

    /**
     * @var string
     */
    protected $effectiveUrl;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $alerts = [];

    /**
     * @var string
     */
    protected $machineId;

    /**
     * @param mixed $body
     * @param int   $statusCode
     * @param mixed $meta
     */
    function __construct($body = [], $statusCode = 200, $meta = null)
    {
        $this->setBody($body);
        $this->setStatus($statusCode);
        $this->meta = $meta;
    }

    /**
     * Set the response status
     *
     * @param int    $statusCode   Response status code to set
     * @param string $reasonPhrase Response reason phrase
     *
     * @return self
     * @throws BadResponseException when an invalid response code is received
     */
    public function setStatus($statusCode, $reasonPhrase = '')
    {
        $this->statusCode = (int) $statusCode;

        if (!$reasonPhrase && isset(self::$statusTexts[$this->statusCode])) {
            $this->reasonPhrase = self::$statusTexts[$this->statusCode];
        } else {
            $this->reasonPhrase = $reasonPhrase;
        }

        return $this;
    }

    /**
     * Checks if HTTP Status code is a Client Error (4xx)
     *
     * @return bool
     */
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Checks if HTTP Status code is Server OR Client Error (4xx or 5xx)
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->isClientError() || $this->isServerError();
    }

    /**
     * Checks if HTTP Status code is Information (1xx)
     *
     * @return bool
     */
    public function isInformational()
    {
        return $this->statusCode < 200;
    }

    /**
     * Checks if HTTP Status code is a Redirect (3xx)
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Checks if HTTP Status code is Server Error (5xx)
     *
     * @return bool
     */
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * Checks if HTTP Status code is Successful (2xx | 304)
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return ($this->statusCode >= 200 && $this->statusCode < 300) || $this->statusCode == 304;
    }


    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        if (is_object($body) && $className = $this->getClassName($body)) {
            $this->body = [$className => $body];
        } else {
            $this->body = $body;
        }

        return $this;
    }


    /**
     * @param $object
     *
     * @return null|string
     */
    private function getClassName($object)
    {
        $nameSpace = explode("\\", get_class($object));

        return array_pop($nameSpace);
    }


    /**
     * @param mixed       $row
     * @param string|null $name
     *
     * @param bool        $isArray
     *
     * @return $this
     */
    public function addToBody($row, $name = null, $isArray = true)
    {
        return $this->addTo("body", $row, $name, $isArray );

    }

    /**
     * @param mixed       $row
     * @param string|null $name
     *
     * @return $this
     */
    public function addToMeta($row, $name = null)
    {
        return $this->addTo("meta", $row, $name);
    }

    /**
     * @param string      $field
     * @param string      $row
     * @param string|null $name
     *
     * @param bool        $isArray
     *
     * @return $this
     */
    private function addTo($field, $row, $name = null, $isArray = true)
    {
        $getField = "get" . ucfirst($field);

        if ($name) {
            if ($this->$getField() && array_key_exists($name, $this->$getField())) {
                if (!is_array($this->{$field}[$name])) {
                    $this->{$field}[$name] = [$this->{$field}[$name]];
                }

                array_push($this->{$field}[$name], $row);
            } else {
                if($isArray){
                    $this->{$field}[$name] = [$row];
                }else{
                    $this->{$field}[$name] = $row;
                }
            }
        } else {
            $this->{$field}[] = $row;
        }

        return $this;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }
}
