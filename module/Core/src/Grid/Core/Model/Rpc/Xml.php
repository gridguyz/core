<?php

namespace Grid\Core\Model\Rpc;

use Zend\Http\Request;
use Zend\Http\Response;
use Grid\Core\Model\RpcAbstract;

/**
 * Grid\Core\Model\Rpc\Xml
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Xml extends RpcAbstract
{

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
     * Format name
     *
     * @var string
     */
    protected static $format    = 'xml';

    /**
     * Accept mime
     *
     * @var string
     * @abstract
     */
    protected static $requestMime   = '(application|text)/xml';

    /**
     * Response mime
     *
     * @var string
     */
    protected static $responseMime  = 'application/xml';

    /**
     * Parse request
     *
     * @param \Zend\Http\Request $request
     * @param \Zend\Http\Response $request
     * @return string|null $error
     */
    public function parse( Request $request, Response $response )
    {
        $error = parent::parse( $request, $response );

        if ( $error )
        {
            return $error;
        }

        $method = null;
        $params = xmlrpc_decode_request(
            $request->getContent(),
            $method,
            'utf-8'
        );

        if ( ! $params )
        {
            return self::PARSE;
        }

        if ( empty( $method ) || empty( $params ) )
        {
            return self::INVALID_REQUEST;
        }

        $this->method   = $method;
        $this->params   = $params;

        return null;
    }

    /**
     * Set response object
     *
     * @param mixed $result
     */
    protected function getResponse( $result )
    {
        $this->response
             ->setContent( xmlrpc_encode_request(
                    null, $result,
                    array( 'encoding' => 'utf-8' )
                ) );

        return $this->response;
    }

    /**
     * Send error result
     *
     * @param string $message
     * @param int $code
     * @param mixed $data (unused)
     */
    public function error( $message, $code = null, $data = null )
    {
        if ( null === $code && isset( $this->errorCodes[$message] ) )
        {
            $code = $this->errorCodes[$message];
        }

        return $this->getResponse( array(
            'faultCode'     => $code,
            'faultString'   => $message,
            'faultData'     => $data,
        ) );
    }

    /**
     * Send valid result
     *
     * @param mixed $result
     */
    public function response( $result )
    {
        return $this->getResponse(
            $result instanceof Traversable
                ? iterator_to_array( $result )
                : $result
        );
    }

}
