<?php

namespace Grid\Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * IndexController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class IndexController extends AbstractActionController
{

    /**
     * Get sub-domain model
     *
     * @return \Core\Model\SubDomain\Model
     */
    protected function getSubDomainModel()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\Core\Model\SubDomain\Model' );
    }

    /**
     * Get uri model
     *
     * @return \Core\Model\Uri\Model
     */
    protected function getUriModel()
    {
        return $this->getServiceLocator()
                    ->get( 'Grid\Core\Model\Uri\Model' );
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $actual = $this->getSubDomainModel()
                       ->findActual();

        if ( empty( $actual ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $locales = array();
        $request = $this->getRequest();
        $accept  = $request->getHeader( 'Accept-Language' );

        if ( $accept )
        {
            $locales = $this->locale()
                            ->parseHeader( $accept );
        }

        if ( empty( $locales[$actual->locale] ) )
        {
            $locales[$actual->locale] = 0.01;
        }

        // the browser gives us in the right order usually,
        // but we cannot depend on it
        arsort( $locales, SORT_NUMERIC );

        $uri = $this->getUriModel()
                    ->findDefaultByContentSubdomain(
                           $actual->defaultContentId,
                           $actual->id,
                           $locales
                       );

        if ( empty( $uri ) )
        {
            $uri = $this->url()
                        ->fromRoute( 'Grid\Paragraph\Render\Paragraph', array(
                            'locale'        => (string) $this->locale(),
                            'paragraphId'   => $actual->defaultContentId,
                        ) );
        }
        else
        {
            $uri = '/' . $uri->safeUri;
        }

        $response = $this->getResponse();

        $response->getHeaders()
                 ->addHeaderLine( 'Location', $uri );

        // Temporary redirect
        return $response->setStatusCode( 307 );
    }

    /**
     * Content-uri action
     */
    public function contentUriAction()
    {
        $actual = $this->getSubDomainModel()
                       ->findActual();

        if ( empty( $actual ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $uri = $this->getUriModel()
                    ->findBySubdomainUri(
                        $actual->id,
                        $this->params()
                             ->fromRoute( 'uri' )
                    );

        if ( empty( $uri ) )
        {
            $this->paragraphLayout();

            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $uri->default )
        {
            $default = $this->getUriModel()
                            ->findDefaultByContentSubdomain(
                                $uri->contentId,
                                $actual->id,
                                $uri->locale
                            );

            if ( $default->uri != $uri->uri ) // Avoid infinite redirect circles
            {
                $response = $this->getResponse();

                $response->getHeaders()
                         ->addHeaderLine( 'Location',
                                          '/' . $default->safeUri );

                // Permanent redirect: for "old" uris
                return $response->setStatusCode( 301 );
            }
        }

        if ( ! empty( $uri->locale ) )
        {
            $this->getServiceLocator()
                 ->get( 'Locale' )
                 ->setCurrent( $uri->locale );
        }

        return $this->forward()
                    ->dispatch( 'Grid\Paragraph\Controller\Render', array(
                        'controller'    => 'Grid\Paragraph\Controller\Render',
                        'action'        => 'paragraph',
                        'locale'        => (string) $this->locale(),
                        'paragraphId'   => $uri->contentId
                    ) );
    }

}
