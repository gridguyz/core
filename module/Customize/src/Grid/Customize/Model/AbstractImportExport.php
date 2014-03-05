<?php

namespace Grid\Customize\Model;

use Zork\Db\SiteInfo;
use Zork\Db\SiteInfoAwareTrait;
use Zork\Db\SiteInfoAwareInterface;
use Grid\Customize\Model\Sheet\Model as SheetModel;
use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;

/**
 * AbstractImportExport
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
abstract class AbstractImportExport implements SiteInfoAwareInterface
{

    use SiteInfoAwareTrait;

    /**
     * @const string
     */
    const PUBLIC_DIR        = 'public/';

    /**
     * @const string
     */
    const UPLOADS_DIR       = 'uploads/';

    /**
     * @const string
     */
    const TEMP_DIR          = 'tmp/';

    /**
     * @const string
     */
    const GPML_ROOT         = 'gpml';

    /**
     * @const string
     */
    const GPML_PUBLIC       = null;

    /**
     * @const string
     */
    const GPML_SYSTEM       = 'public/styles/schemas/paragraph/1.0.dtd';

    /**
     * @const string
     */
    const GPML_NAMESPACE    = 'http://gridguyz.com/#gpml';

    /**
     * @var SheetModel
     */
    private $sheetModel = null;

    /**
     * @var ParagraphModel
     */
    private $paragraphModel = null;

    /**
     * Get the customize-sheet model
     *
     * @return  SheetModel
     */
    protected function getSheetModel()
    {
        return $this->sheetModel;
    }

    /**
     * Set the customize-sheet model
     *
     * @param   SheetModel  $sheet
     * @return  Exporter
     */
    protected function setSheetModel( SheetModel $sheet )
    {
        $this->sheetModel = $sheet;
        return $this;
    }

    /**
     * Get the paragraph model
     *
     * @return  ParagraphModel
     */
    protected function getParagraphModel()
    {
        return $this->paragraphModel;
    }

    /**
     * Set the paragraph model
     *
     * @param   ParagraphModel  $paragraphModel
     * @return  Exporter
     */
    protected function setParagraphModel( ParagraphModel $paragraphModel )
    {
        $this->paragraphModel = $paragraphModel;
        return $this;
    }

    /**
     * Constructor
     *
     * @param   SheetModel      $sheetModel
     * @param   ParagraphModel  $paragraphModel
     * @param   SiteInfo        $siteInfo
     */
    public function __construct( SheetModel     $sheetModel,
                                 ParagraphModel $paragraphModel,
                                 SiteInfo $siteInfo )
    {
        $this->setSheetModel( $sheetModel )
             ->setParagraphModel( $paragraphModel )
             ->setSiteInfo( $siteInfo );
    }

}
