<?php

namespace Grid\Core\Model\Rpc;

use Zend\Http\Request;
use Zend\Http\Response;
use Grid\Core\Model\RpcAbstract;

/**
 * Grid\Core\Model\Rpc\Json
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Json extends RpcAbstract
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
    protected static $format    = 'json';

    /**
     * Accept mime
     *
     * @var string
     */
    protected static $requestMime  = 'application/json';

    /**
     * Response mime
     *
     * @var string
     */
    protected static $responseMime  = 'application/json';

    /**
     * Id
     * @var int
     */
    private $id                 = null;

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

        $body = json_decode( $request->getContent() );

        if ( empty( $body ) )
        {
            return self::PARSE;
        }

        if ( empty( $body->method ) || ! isset( $body->params ) )
        {
            return self::INVALID_REQUEST;
        }

        $this->id       = $body->id;
        $this->method   = $body->method;
        $this->params   = $body->params;

        return null;
    }

    /**
     * Set response object
     *
     * @param string $key
     * @param object $object
     */
    protected function getResponse( $key, $object )
    {
        $result = array(
            'jsonrpc'   => '2.0',
            $key        => $object,
        );

        if ( null !== $this->id )
        {
            $result['id'] = $this->id;
        }

        $this->response
             ->setContent( json_encode( $result ) );

        return $this->response;
    }

    /**
     * Send error result
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     */
    public function error( $message, $code = null, $data = null )
    {
        if ( null === $code && isset( $this->errorCodes[$message] ) )
        {
            $code = $this->errorCodes[$message];
        }

        return $this->getResponse( 'error', (object) array(
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
        ) );
    }

    /**
     * Send valid result
     *
     * @param mixed $result
     */
    public function response( $result )
    {
        return $this->getResponse( 'result',
            $result instanceof \Traversable
                ? iterator_to_array( $result )
                : $result
        );
    }

}
