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

        // Found
        return $response->setStatusCode( 302 );
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

        $uri = $this->params()
                    ->fromRoute( 'uri' );

        // prevent "Invalid Encoding Attack"
        if ( ! mb_check_encoding( $uri, 'UTF-8' ) )
        {
            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        $structure = $this->getUriModel()
                          ->findBySubdomainUri( $actual->id, $uri );

        if ( empty( $structure ) )
        {
            $this->paragraphLayout();

            $this->getResponse()
                 ->setStatusCode( 404 );

            return;
        }

        if ( ! $structure->default )
        {
            $default = $this->getUriModel()
                            ->findDefaultByContentSubdomain(
                                $structure->contentId,
                                $actual->id,
                                $structure->locale
                            );

            if ( $default->uri != $structure->uri ) // Avoid infinite redirect circles
            {
                $response = $this->getResponse();

                $response->getHeaders()
                         ->addHeaderLine( 'Location',
                                          '/' . $default->safeUri );

                // Permanent redirect: for "old" uris
                return $response->setStatusCode( 301 );
            }
        }

        if ( ! empty( $structure->locale ) )
        {
            $this->getServiceLocator()
                 ->get( 'Locale' )
                 ->setCurrent( $structure->locale );
        }

        return $this->forward()
                    ->dispatch( 'Grid\Paragraph\Controller\Render', array(
                        'controller'    => 'Grid\Paragraph\Controller\Render',
                        'action'        => 'paragraph',
                        'locale'        => (string) $this->locale(),
                        'paragraphId'   => $structure->contentId
                    ) );
    }

}
