<?php

namespace Grid\Paragraph\Model\Log\Structure;

use Zend\View\Renderer\RendererInterface;
use Grid\ApplicationLog\Model\Log\Structure\ProxyAbstract;

/**
 * ContentView
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ContentView extends ProxyAbstract
{

    /**
     * Log event-type
     *
     * @var string
     */
    protected static $eventType = 'content-view';

    /**
     * Content-paragraph's ID
     *
     * @var int
     */
    protected $paragraphId;

    /**
     * Viewed in this locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Title at the time-point of view
     *
     * @var string
     */
    protected $originalTitle;

    /**
     * Get description for this log-event
     *
     * @return string
     */
    public function getDescription()
    {
        if ( empty( $this->paragraphId ) )
        {
            return '';
        }

        $model  = $this->getServiceLocator()
                       ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        $locale = $model->getLocale();
        $model->setLocale( $this->locale );
        $content = $model->find( $this->paragraphId );
        $model->setLocale( $locale );

        if ( empty( $content ) )
        {
            return $this->originalTitle;
        }
        else if ( ! empty( $content->title ) )
        {
            return $content->title;
        }
        else if ( ! empty( $content->name ) )
        {
            return $content->name;
        }
        else
        {
            return $this->originalTitle;
        }
    }

    /**
     * Render extra data for this log-event
     *
     * @return string
     */
    public function render( RendererInterface $renderer )
    {
        if ( empty( $this->paragraphId ) )
        {
            return '';
        }

        return $renderer->htmlTag(
            'a',
            $this->getDescription(),
            array(
                'title' => $this->originalTitle,
                'href'  => '/app/' . $this->locale .
                           '/paragraph/render/' . $this->paragraphId,
            )
        );
    }

}
