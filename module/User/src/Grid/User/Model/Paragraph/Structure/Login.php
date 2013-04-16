<?php

namespace Grid\User\Model\Paragraph\Structure;

use Grid\Paragraph\Model\Paragraph\Structure\AbstractLeaf;

/**
 * Content
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class Login extends AbstractLeaf
{

    /**
     * Paragraph type
     *
     * @var string
     */
    protected static $type = 'login';

    /**
     * Locale-aware properties
     *
     * @var array
     */
    protected static $localeAwareProperties = array();

    /**
     * Paragraph-render view-open
     *
     * @var string
     */
    protected static $viewOpen = 'grid/paragraph/render/login';

    /**
     * Display admin-ui link
     *
     * @var bool
     */
    protected $displayAdminUiLink;

    /**
     * Display register link
     *
     * @var bool
     */
    protected $displayRegisterLink;

    /**
     * Display password-request link
     *
     * @var bool
     */
    protected $displayPasswordRequestLink;

    /**
     * Display login-with link
     *
     * @var bool
     */
    protected $displayLoginWithLink;

    /**
     * Display admin-ui link
     *
     * @param bool $flag
     * @return \User\Model\Paragraph\Structure\Login
     */
    public function setDisplayAdminUiLink( $flag )
    {
        $this->displayAdminUiLink = (bool) $flag;
        return $this;
    }

    /**
     * Display register link
     *
     * @param bool $flag
     * @return \User\Model\Paragraph\Structure\Login
     */
    public function setDisplayRegisterLink( $flag )
    {
        $this->displayRegisterLink = (bool) $flag;
        return $this;
    }

    /**
     * Display password-request link
     *
     * @param bool $flag
     * @return \User\Model\Paragraph\Structure\Login
     */
    public function setDisplayPasswordRequestLink( $flag )
    {
        $this->displayPasswordRequestLink = (bool) $flag;
        return $this;
    }

    /**
     * Display login-with link
     *
     * @param bool $flag
     * @return \User\Model\Paragraph\Structure\Login
     */
    public function setDisplayLoginWithLink( $flag )
    {
        $this->displayLoginWithLink = (bool) $flag;
        return $this;
    }

    /**
     * Get login / logout form
     *
     * @param string $which 'login' / 'logout'
     * @return \Zend\Form\Form
     */
    public function getForm( $which )
    {
        $which      = ucfirst( $which );
        $service    = $this->getServiceLocator();
        $locale     = $service->get( 'Locale' );
        $router     = $service->get( 'Router' );
        $request    = $service->get( 'Request' );

        $form = $service->get( 'Form' )
                        ->create( 'Grid\User\\' . $which, array(
                            'returnUri' => $request->getRequestUri()
                        ) );

        $form->setAttribute( 'action', $router->assemble(
            array( 'locale' => $locale->getCurrent() ),
            array( 'name'   => 'Grid\User\\Authentication\\' . $which )
        ) );

        return $form;
    }

    /**
     * Get displayable links
     *
     * @return array
     */
    public function getDisplay()
    {
        $display = $this->getServiceLocator()
                        ->get( 'Config' )
                             [ 'modules' ]
                             [ 'Grid\User' ]
                             [ 'display' ];

        if ( null !== $this->displayAdminUiLink )
        {
            $display['adminUiLink'] = (bool) $this->displayAdminUiLink;
        }

        if ( null !== $this->displayRegisterLink )
        {
            $display['registerLink'] = (bool) $this->displayRegisterLink;
        }

        if ( null !== $this->displayPasswordRequestLink )
        {
            $display['passwordRequestLink'] = (bool) $this->displayPasswordRequestLink;
        }

        if ( null !== $this->displayLoginWithLink )
        {
            $display['loginWithLink'] = (bool) $this->displayLoginWithLink;
        }

        return $display;
    }

}
