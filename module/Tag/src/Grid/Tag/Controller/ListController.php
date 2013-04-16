<?php

namespace Grid\Tag\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Tag-list controller
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ListController extends AbstractActionController
{

    /**
     * List contents by tags
     */
    public function listAction()
    {
        $params = $this->params();
        $page   = (int) $params->fromRoute( 'page' );
        $mode   = $params->fromRoute( 'mode' );
        $all    = $mode == 'all';
        $tags   = array_map(
            'rawurldecode',
            explode( '/', $params->fromRoute( 'tags' ) )
        );

        $paginator = $this->getServiceLocator()
                          ->get( 'Grid\Paragraph\Model\Paragraph\Model' )
                          ->getContentPaginatorByTags( $tags, $all );

        $this->paragraphLayout();

        return array(
            'paginator'         => $paginator,
            'page'              => $page,
            'mode'              => $mode,
            'tags'              => $tags,
        );
    }

}
