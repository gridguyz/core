<?php

namespace Grid\Core\Model;

use Zend\Http\Request;
use Zend\Http\Response;

/**
 * CoreRpc_Model_RpcAbstract
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class RpcAbstract
{

    /**
     * @var string
     */
    const PARSE             = 'Parse error';

    /**
     * @var string
     */
    const INVALID_REQUEST   = 'Invalid request';

    /**
     * @var string
     */
    const METHOD_NOT_FOUND  = 'Method not found';

    /**
     * @var string
     */
    const INVALID_PARAMS    = 'Invalid params';

    /**
     * @var string
     */
    const INTERNAL          = 'Internal error';

    /**
     * Error codes
     *
     * @var array
     */
    public $errorCodes = array(
        self::PARSE             => -32700,
        self::INVALID_REQUEST   => -32600,
        self::METHOD_NOT_FOUND  => -32601,
        self::INVALID_PARAMS    => -32602,
        self::INTERNAL          => -32603,
    );

    /**
     * Called method
     *
     * @var callback
     */
    protected $method = null;

    /**
     * Called parameters
     *
     * @var array|object
     */
    protected $params = null;

    /**
     * Request
     *
     * @var \Zend\Http\Request
     */
    protected $request = null;

    /**
     * Response
     *
     * @var \Zend\Http\Response
     */
    protected $response = null;

    /**
     * Format name
     *
     * @var string
     * @abstract
     */
    protected static $format;

    /**
     * Accept mime
     *
     * @var string
     * @abstract
     */
    protected static $requestMime;

    /**
     * Response mime
     *
     * @var string
     * @abstract
     */
    protected static $responseMime;

    /**
     * Get called method
     *
     * @return callback
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get called parameters
     *
     * @return object
     */
    public function getParams()
    {
        return (array) $this->params;
    }

    /**
     * Parse request
     *
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Response $request
     * @return string|null $error
     */
    public function parse( Request $request, Response $response )
    {
        $this->request  = $request;
        $this->response = $response;

        $response->getHeaders()
                 ->addHeaderLine( 'Content-Type', static::$responseMime .
                                  '; charset=utf-8' );

        $accept = $request->getHeader( 'Accept' );

        if ( empty( $accept ) )
        {
            return self::INVALID_REQUEST;
        }

        foreach ( explode( ',', $accept->getFieldValue() ) as $acceptType )
        {
            if ( preg_match( '#^' . static::$requestMime . '(;.*)?$#',
                 trim( $acceptType ) ) )
            {
                return null;
            }
        }

        return self::INVALID_REQUEST;
    }

    /**
     * Send error result
     *
     * @param Zend_Controller_Response_Http $response
     * @param string $message
     * @param int $code
     * @param mixed $data
     */
    abstract public function error( $message, $code = null, $data = null );

    /**
     * Send valid result
     *
     * @param Zend_Controller_Response_Http $response
     * @param mixed $result
     */
    abstract public function response( $result );

}
