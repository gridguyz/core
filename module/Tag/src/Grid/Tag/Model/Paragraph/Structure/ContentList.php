<?php

namespace Grid\Tag\Model\Paragraph\Structure;

use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;
use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;

/**
 * ContentList
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ContentList extends AbstractLeaf
{

    /**
     * @const string
     */
    const MODE_SOME = 'some';

    /**
     * @const string
     */
    const MODE_ALL = 'all';

    /**
     * @const string
     */
    const DEFAULT_MODE = self::MODE_ALL;

    /**
     * @const int
     */
    const DEFAULT_ITEM_COUNT = 5;

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'contentList';

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/contentList';

    /**
     * @var \Paragraph\Model\Paragraph\Model
     */
    private $_paragraphModel;

    /**
     * @var string
     */
    protected $mode = self::DEFAULT_MODE;

    /**
     * @var 10
     */
    protected $itemCount = self::DEFAULT_ITEM_COUNT;

    /**
     * Get paragraph-model
     *
     * @return \Paragraph\Model\Paragraph\Model
     */
    public function getParagraphModel()
    {
        if ( null === $this->_paragraphModel )
        {
            $this->_paragraphModel = $this->getServiceLocator()
                                          ->get( 'Grid\Paragraph\Model\Paragraph\Model' );
        }

        return $this->_paragraphModel;
    }

    /**
     * Set tag-model
     *
     * @param   \Paragraph\Model\Paragraph\Model $paragraphModel
     * @return  \Tag\Model\Paragraph\Structure\ContentList
     */
    public function setParagraphModel( ParagraphModel $paragraphModel )
    {
        $this->_paragraphModel = $paragraphModel;
        return $this;
    }

    /**
     * Set mode
     *
     * @param   string  $mode
     * @return  \Tag\Model\Paragraph\Structure\ContentList
     */
    public function setMode( $mode )
    {
        $mode = (string) $mode;

        if ( static::MODE_ALL === $mode ||
             static::MODE_SOME === $mode )
        {
            $this->mode = $mode;
        }

        return $this;
    }

    /**
     * Set item count
     *
     * @param   int $count
     * @return  \Tag\Model\Paragraph\Structure\ContentList
     */
    public function setItemCount( $count )
    {
        $this->itemCount = max( 1, (int) $count );
        return $this;
    }

    /**
     * Is in all mode
     *
     * @return bool
     */
    public function isModeAll()
    {
        return static::MODE_ALL === $this->mode;
    }

    /**
     * Is in some mode
     *
     * @return bool
     */
    public function isModeSome()
    {
        return static::MODE_SOME === $this->mode;
    }

    /**
     * Contents in paginator
     *
     * @return  \Zend\Paginator\Paginator
     */
    public function findContentPaginator()
    {
        return $this->getParagraphModel()
                    ->getContentPaginatorByTags(
                        $this->getTags(),
                        $this->isModeAll()
                    );
    }

}
