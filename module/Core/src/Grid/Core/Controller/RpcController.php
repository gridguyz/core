<?php

namespace Grid\Core\Controller;

use Exception;
use Zend\Log\Logger;
use Grid\Core\Model\RpcAbstract;
use Zork\Rpc\CallableInterface;
use Zend\Stdlib\ErrorHandler;
use Zork\Rpc\Exception as RpcException;
use Zend\Mvc\Exception\DomainException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Grid\Core\Controller\RpcController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class RpcController extends AbstractActionController
{

    /**
     * Check against infinite recusion
     *
     * @var array
     */
    private static $rawCheck = array();

    /**
     * Convert data to raw object
     *
     * @param   array|object    $object
     * @return  object
     */
    protected static function rawData( $object )
    {
        if ( is_object( $object ) )
        {
            $hash = spl_object_hash( $object );

            if ( isset( self::$rawCheck[$hash] ) )
            {
                return array( '#recursion#' => $hash );
            }

            self::$rawCheck[$hash] = true;
        }

        $object = (array) $object;

        foreach ( $object as $key => $value )
        {
            if ( is_array( $value ) || is_object( $value ) )
            {
                $object[$key] = self::rawData( $value );
            }
        }

        return (object) $object;
    }

    /**
     * Convert exception to raw object
     *
     * @param   Exception   $exception
     * @return  object|null
     */
    protected static function rawException( $exception )
    {
        if ( error_reporting() & E_WARNING )
        {
            return static::rawData( $exception );
        }

        return null;
    }

    /**
     * Log a single exception
     *
     * @param   \Exception  $exception
     * @param   int         $priority
     */
    public function logException( Exception $exception,
                                  $priority = Logger::CRIT )
    {
        /* @var $logger \Zork\Log\LoggerManager */
        $logger = $this->getServiceLocator()
                       ->get( 'Zork\Log\LoggerManager' );

        if ( $logger->hasLogger( 'exception' ) )
        {
            $logger->getLogger( 'exception' )
                   ->log( $priority,
                          '<pre>' . $exception . PHP_EOL . '</pre>' . PHP_EOL );
        }
    }

    /**
     * Call an rpc
     *
     * @return  array
     */
    public function callAction()
    {
        $formatName = $this->params()
                           ->fromRoute( 'format', 'json' );

        try
        {
            try
            {
                $format = $this->getServiceLocator()
                               ->get( 'Grid\\Core\\Model\\Rpc\\' .
                                      ucfirst( $formatName ) );
            }
            catch ( ServiceNotFoundException $ex )
            {
                throw new DomainException(
                    'The rpc-format (' . $formatName . ') not understood', 0, $ex
                );
            }

            if ( ! $format || ! $format instanceof RpcAbstract )
            {
                throw new DomainException(
                    'The rpc-format (' . $formatName . ') not understood'
                );
            }
        }
        catch ( DomainException $ex )
        {
            $this->logException( $ex, Logger::WARN );

            $this->getResponse()
                 ->setStatusCode( 500 );

            if ( error_reporting() & E_WARNING )
            {
                $this->getResponse()
                     ->setContent( (string) $ex );
            }

            return;
        }

        $request    = $this->getRequest();
        $response   = $this->getResponse();
        $error      = $format->parse( $request, $response );

        if ( $error )
        {
            try
            {
                throw new RpcException\BadMethodCallException(
                    'Parse error',
                    $error
                );
            }
            catch ( RpcException\BadMethodCallException $ex )
            {
                $this->logException( $ex, Logger::WARN );
            }

            return $format->error(
                $error,
                $format->errorCodes[ $error ],
                $error
            );
        }

        @ list( $serviceName,
                $method ) = explode( '::', $format->getMethod(), 2 );

        if ( empty( $method ) )
        {
            $method = '__invoke';
        }

        try
        {
            $service = $this->getServiceLocator()
                            ->get( $serviceName );
        }
        catch ( ServiceNotFoundException $ex )
        {
            $this->logException( $ex, Logger::WARN );

            return $format->error(
                RpcAbstract::METHOD_NOT_FOUND . ': ' . $format->getMethod(),
                $format->errorCodes[RpcAbstract::METHOD_NOT_FOUND],
                array(
                    'service'   => $serviceName,
                    'exception' => self::rawException( $ex ),
                )
            );
        }

        if ( empty( $service ) || ! $service instanceof CallableInterface )
        {
            $this->logException( $ex, Logger::WARN );

            return $format->error(
                RpcAbstract::METHOD_NOT_FOUND . ': ' . $format->getMethod(),
                $format->errorCodes[RpcAbstract::METHOD_NOT_FOUND],
                array(
                    'service'   => $serviceName,
                )
            );
        }
        else
        {
            try
            {
                ErrorHandler::start( E_ALL );
                $result = $service->call( $method, $format->getParams() );
                ErrorHandler::stop( true );
                return $format->response( $result );
            }
            catch ( RpcException\BadMethodCallException $ex )
            {
                ErrorHandler::stop( false );
                $this->logException( $ex, Logger::WARN );

                return $format->error(
                    RpcAbstract::METHOD_NOT_FOUND . ': ' . $format->getMethod(),
                    $format->errorCodes[RpcAbstract::METHOD_NOT_FOUND],
                    array(
                        'service'   => $serviceName,
                        'method'    => $method,
                        'exception' => self::rawException( $ex ),
                    )
                );
            }
            catch ( RpcException\InvalidArgumentException $ex )
            {
                ErrorHandler::stop( false );
                $this->logException( $ex, Logger::WARN );

                return $format->error(
                    $ex->getMessage(),
                    $format->errorCodes[RpcAbstract::INVALID_PARAMS],
                    self::rawException( $ex )
                );
            }
            catch ( Exception $ex )
            {
                ErrorHandler::stop( false );
                $this->logException( $ex );

                return $format->error(
                    RpcAbstract::INTERNAL . ': #' .
                        $ex->getCode() . ' - ' . $ex->getMessage(),
                    $format->errorCodes[RpcAbstract::INTERNAL],
                    self::rawException( $ex )
                );
            }
        }
    }

}
