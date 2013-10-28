<?php

namespace Grid\Core\Controller;

use Zend\Debug\Debug;
use Zork\Process\Process;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * CronController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class CronController extends AbstractActionController
{

    /**
     * @const string
     */
    const PHP_SELF = './public/index.php';

    /**
     * Run cron(s) in multiple domains
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $type    = $request->getParam( 'type' );

        if ( ! $request instanceof ConsoleRequest )
        {
            throw new \RuntimeException( sprintf(
                '%s can only be used from a console.',
                __METHOD__
            ) );
        }

        $phpSelf = realpath( static::PHP_SELF );

        if ( empty( $phpSelf ) || ! is_file( $phpSelf ) )
        {
            throw new \RuntimeException( sprintf(
                '%s: php not found at "%s" (in "%s").',
                __METHOD__,
                static::PHP_SELF,
                getcwd()
            ) );
        }

        $result = 'Running ' . $type . ' cron(s) ...' . PHP_EOL . PHP_EOL;

        foreach ( $this->mimicSiteInfos() as $siteInfo )
        {
            $domain  = $siteInfo->getDomain();
            $process = new Process( array(
                'command'   => 'php',
                'arguments' => array(
                    $phpSelf,
                    'cron',
                    $domain,
                    $type,
                ),
                'environmentVariables'  => array(
                    'GRIDGUYZ_HOST'     => $domain,
                    'HTTP_HOST'         => $domain,
                ),
            ) );

            $result .= 'Calling process ...' . PHP_EOL .
                       $process->getRunCommand() . PHP_EOL;

            $output = tempnam( './data/', $domain );
            $descr  = array( Process::TYPE_FILE, $output, Process::MODE_APPEND );
            file_put_contents( $output, '' );

            $process->open( array(
                Process::STREAM_STDOUT => $descr,
                Process::STREAM_STDERR => $descr,
            ) );

            $return     = $process->close();
            $messages   = rtrim( file_get_contents( $output ), PHP_EOL );
            unlink( $output );

            if ( $messages )
            {
                $result .= $messages . PHP_EOL;
            }

            $result .= sprintf(
                'Process returned with #%d: %s' . PHP_EOL . PHP_EOL,
                $return,
                $return ? 'error!' : 'success.'
            );
        }

        $result .= 'Done.' . PHP_EOL;
        return $result;
    }

    /**
     * Run cron(s) at a specific domain
     */
    public function domainAction()
    {
        $request = $this->getRequest();
        $type    = $request->getParam( 'type' );
        $domain  = $request->getParam( 'domain' );

        if ( ! $request instanceof ConsoleRequest )
        {
            throw new \RuntimeException( sprintf(
                '%s can only be used from a console.',
                __METHOD__
            ) );
        }

        $siteInfo = $this->getServiceLocator()
                         ->get( 'SiteInfo' );

        if ( $domain != $siteInfo->getDomain() )
        {
            throw new \RuntimeException( sprintf(
                '%s: domain mismatch. Detected "%s", while enforced "%s".',
                __METHOD__,
                $siteInfo->getDomain(),
                $domain
            ) );
        }

        $locator    = $this->getServiceLocator();
        $config     = $locator->get( 'Configuration' );

        if ( empty( $config['modules']['Grid\Core']['cron'] ) )
        {
            return '';
        }

        $result     = '';
        $keys       = array( 'site' );
        $cronConfig = $config['modules']['Grid\Core']['cron'];
        $modules    = $locator->get( 'Zend\ModuleManager\ModuleManagerInterface' )
                              ->getModules();

        if ( ! in_array( 'Grid\MultisitePlatform', $modules ) ||
               in_array( 'Grid\MultisiteCentral',  $modules ) )
        {
            $keys[] = 'once';
        }

        foreach ( $keys as $key )
        {
            if ( ! empty( $cronConfig[$key][$type] ) )
            {
                foreach ( $cronConfig[$key][$type] as $service )
                {
                    if ( is_scalar( $service ) )
                    {
                        $service = array(
                            'service' => $service,
                        );
                    }

                    if ( ! empty( $service['service'] ) )
                    {
                        $return = call_user_func_array(
                            array(
                                $this->getServiceLocator()
                                     ->get( $service['service'] ),
                                empty( $service['method'] )
                                    ? '__invoke'
                                    : (string) $service['method']
                            ),
                            empty( $service['arguments'] )
                                ? array()
                                : (array) $service['arguments']
                        );

                        $result .= sprintf(
                            'Service "%s" / %s / %s ',
                            $service['service'],
                            $key,
                            $type
                        );

                        if ( ! empty( $service['method'] ) )
                        {
                            $result .= sprintf( 'called "%s" ', $service['method'] );
                        }

                        if ( ! empty( $service['arguments'] ) )
                        {
                            $result .= Debug::dump(
                                $service['arguments'],
                                'with',
                                false
                            );
                        }

                        $result .= Debug::dump( $return, 'returned', false );
                        $result .= PHP_EOL;
                    }
                }
            }
        }

        return $result;
    }

}
