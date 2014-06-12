<?php

namespace Grid\Image\Controller;

use Zend\Mvc\Controller\AbstractActionController;


/**
 * ThumbnailController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class MetaController extends AbstractActionController
{
    /**
     *
     * @return array
     */
    public function indexAction()
    {
        $serviceLocator = $this->getServiceLocator();
        $locale         = $this->params()->fromRoute('locale');
        $paragraphId    = $this->params()->fromRoute('paragraphId');
        $paragraph      = $serviceLocator->get('Grid\Paragraph\Model\Paragraph\Model')
                            ->setLocale($locale)
                            ->find($paragraphId);
        
        if( empty($paragraph) )
        {
            $this->getResponse()->setStatusCode( 404 );
            return;
        }
        
        $contentUriFactory = $serviceLocator
                                ->get('Grid\Core\Model\ContentUri\Factory');

        $contentUriAdapter = $contentUriFactory->factory(array( 
                                'type'      => 'paragraph.content',
                                'contentId' => $paragraph->getRootId(),
                                'locale'    => $locale,
                             )); 

        $contentUri = $contentUriAdapter->getUri();
        
        return array(
            'paragraph'  => $paragraph,
            'contentUri' => $contentUri,
        );
    }


}
