<?php

namespace Grid\Paragraph\Model\Paragraph;

use Zend\Authentication\AuthenticationService;
use Grid\Core\Model\SubDomain\Model as SubDomainModel;
use Grid\Paragraph\Model\Paragraph\Model as ParagraphModel;
use Grid\User\Model\User\Settings\Model as UserSettingsModel;
use Zork\Mvc\Controller\Plugin\MiddleLayout as MiddleLayout;
use Zork\Authentication\AuthenticationServiceAwareTrait;

class MiddleLayoutModel
{

    use AuthenticationServiceAwareTrait;

    /**
     * @var \Paragraph\Model\Paragraph\Model
     */
    protected $paragraphModel;

    /**
     * @var \User\Model\User\Settings\Model
     */
    protected $userSettingsModel;

    /**
     * @var \Core\Model\SubDomain\Model
     */
    protected $subDomainModel;

    /**
     * @return \Paragraph\Model\Paragraph\Model
     */
    public function getParagraphModel()
    {
        return $this->paragraphModel;
    }

    /**
     * @return \Core\Model\SubDomain\Model
     */
    public function getSubDomainModel()
    {
        return $this->subDomainModel;
    }

    /**
     * @return \User\Model\User\Settings\Model
     */
    public function getUserSettingsModel()
    {
        return $this->userSettingsModel;
    }

    /**
     * Constructor
     *
     * @param   ParagraphModel          $paragraphModel
     * @param   SubDomainModel          $subDomainModel
     * @param   UserSettingsModel       $userSettingsModel
     * @param   AuthenticationService   $authenticationService
     */
    public function __construct( ParagraphModel         $paragraphModel,
                                 SubDomainModel         $subDomainModel,
                                 UserSettingsModel      $userSettingsModel,
                                 AuthenticationService  $authenticationService )
    {
        $this->paragraphModel       = $paragraphModel;
        $this->subDomainModel       = $subDomainModel;
        $this->userSettingsModel    = $userSettingsModel;
        $this->setAuthenticationService( $authenticationService );
    }

    /**
     * @param int|null $layoutParagraphId
     * @return \Zork\Mvc\Controller\Plugin\MiddleLayout|boolean
     */
    public function findMiddleParagraphLayoutById( $layoutParagraphId = null )
    {
        if ( empty( $layoutParagraphId ) )
        {
            $layoutParagraphId = $this->subDomainModel
                                      ->findActual()
                                      ->defaultLayoutId;
        }

        $layoutRenderList = $this->paragraphModel->findRenderList( $layoutParagraphId );

        if ( ! empty( $layoutRenderList ) )
        {
            $auth = $this->getAuthenticationService();

            if ( $auth->hasIdentity() )
            {
                $adminMenuSettings = $this->userSettingsModel
                                          ->find( $auth->getIdentity()->id,
                                                  'adminMenu' );
            }
            else
            {
                $adminMenuSettings = null;
            }

            $template = new MiddleLayout( array(
                'template'  => 'grid/paragraph/render/paragraph',
                'variables' => array(
                    'paragraphRenderList'   => $layoutRenderList,
                    'adminMenuSettings'     => $adminMenuSettings,
                ),
            ) );

            return $template;
        }
        else
        {
            return false;
        }
    }

}
