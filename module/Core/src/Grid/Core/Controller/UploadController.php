<?php

namespace Grid\Core\Controller;

use Zork\Stdlib\String;
use Zend\Stdlib\ArrayUtils;
use Zend\Http\Header\HeaderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * UploadController
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class UploadController extends AbstractActionController
{

    /**
     * The path to save to
     *
     * @const string
     */
    const TEMP_PATH = './public/tmp';

    /**
     * The url to save to
     *
     * @const string
     */
    const TEMP_URL  = '/tmp';

    /**
     * The uploaded file's new mod
     *
     * @const string
     */
    const UPLOAD_MOD  = 0777;

    /**
     * Get mime-validators for a file
     *
     * @param string $types
     * @return array
     */
    protected function getValidators( $types )
    {
        $validators = array();

        if ( 'image/*' == $types )
        {
            $validators[] = array(
                'name' => 'Zend\Validator\File\IsImage',
            );
        }
        else if ( ! empty( $types ) && '*' !== $types && '*/*' !== $types )
        {
            $validators[] = array(
                'name'      => 'Zend\Validator\File\MimeType',
                'options'   => array(
                    'mimeType' => $types
                ),
            );
        }

        return $validators;
    }

    /**
     * Get the upload-form
     *
     * @param string|array $types
     * @param string $pattern
     * @return \Zend\Form\Form
     */
    protected function getForm( $types, $pattern )
    {
        $types = preg_replace( '/\s+/', '', implode( ',', (array) $types ) );

        if ( '*/*' === $types )
        {
            $types = '*';
        }

        if ( empty( $types ) )
        {
            $label = 'mime.sets.*';
        }
        else if ( strstr( $types, '*' ) || strstr( $types, ',' )  )
        {
            $label = 'mime.sets.' . $types;
        }
        else
        {
            $label = 'mime.type.' . $types;
        }

        $accept = '*' == $types ? '*/*' : $types;

        return $this->getServiceLocator()
                    ->get( 'Zork\Form\Factory' )
                    ->createForm( array(
                        'attributes'    => array(
                            'action'    => '?',
                            'method'    => 'post',
                        ),
                        'elements'      => array(
                            'types'     => array(
                                'spec'  => array(
                                    'type'  => 'Zork\Form\Element\Hidden',
                                    'name'  => 'types',
                                    'attributes' => array(
                                        'value' => $accept,
                                    ),
                                ),
                            ),
                            'pattern'   => array(
                                'spec'  => array(
                                    'type'  => 'Zork\Form\Element\Hidden',
                                    'name'  => 'pattern',
                                    'attributes' => array(
                                        'value' => $pattern,
                                    ),
                                ),
                            ),
                            'file'      => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\File',
                                    'name'      => 'file',
                                    'options'   => array(
                                        'required'      => true,
                                        'label'         => $label,
                                        'accept'        => $accept,
                                        'validators'    => $this->getValidators( $types )
                                    ),
                                ),
                            ),
                            'upload'    => array(
                                'spec'  => array(
                                    'type'      => 'Zork\Form\Element\Submit',
                                    'name'      => 'upload',
                                    'attributes'    => array(
                                        'value'     => 'default.upload',
                                    ),
                                ),
                            ),
                        ),
                    ) );
    }

    /**
     * Upload index
     */
    public function indexAction()
    {
        $auth = new AuthenticationService();

        if ( ! $auth->hasIdentity() )
        {
            return array(
                'success' => false,
            );
        }

        $request = $this->getRequest();
        $types   = $request->getPost( 'types', $request->getQuery( 'types' ) );
        $pattern = $request->getPost( 'pattern', $request->getQuery( 'pattern' ) );
        $form    = $this->getForm( $types, $pattern );

        if ( $request->isPost() )
        {
            $form->setData( ArrayUtils::merge(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            ) );

            if ( $form->isValid() )
            {
                $data = $form->getData();
                $file = $data['file'];
                $ext  = pathinfo( $file['name'], PATHINFO_EXTENSION );

                if ( ! is_dir( self::TEMP_PATH ) )
                {
                    @ mkdir( self::TEMP_PATH, static::UPLOAD_MOD, true );
                }

                if ( 'php' === strtolower( $ext ) )
                {
                    $ext = 'phps';
                }

                do
                {
                    $newName = sprintf(
                        $pattern,
                        String::generateRandom( null, null, true ),
                        $ext
                    );

                    $moveTo = self::TEMP_PATH . DIRECTORY_SEPARATOR . $newName;
                }
                while ( is_file( $moveTo ) );

                if ( @ move_uploaded_file( $file['tmp_name'], $moveTo ) )
                {
                    @ chmod( $moveTo, static::UPLOAD_MOD );

                    return array(
                        'success'   => true,
                        'file'      => self::TEMP_URL . '/' . $newName,
                    );
                }
                else
                {
                    return array(
                        'success'   => false,
                        'messages'  => array(
                            'File move failed' . PHP_EOL .
                            $file['tmp_name']  . PHP_EOL .
                            $moveTo
                        ),
                    );
                }
            }
            else
            {
                return array(
                    'success'   => false,
                    'messages'  => $form->getMessages(),
                );
            }
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Upload parts
     */
    public function partsAction()
    {
        /* @var $response   \Zend\Http\Response */
        /* @var $headers    \Zend\Http\Headers */
        /* @var $request    \Zend\Http\Request */
        $response   = $this->getResponse();
        $headers    = $response->getHeaders();
        $request    = $this->getRequest();

        $headers->addHeaders( array(
            'Content-Type'  => 'application/json; charset=UTF-8',
            'Last-Modified' => gmdate( 'D, d M Y H:i:s' ) . ' GMT',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Expires'       => 'Mon, 26 Jul 1997 05:00:00 GMT',
            'Pragma'        => 'no-cache',
        ) );

        $auth = new AuthenticationService();
        $view = array(
            'jsonrpc' => '2.0',
        );

        if ( $auth->hasIdentity() )
        {
            if ( ! is_dir( self::TEMP_PATH ) )
            {
                $view['error'] = array(
                    'code'      => 100,
                    'message'   => 'Failed to open temp directory',
                );
            }
            else
            {
                $mimeType = $request->getHeaders()
                                    ->get( 'Content-Type' );

                if ( $mimeType instanceof HeaderInterface )
                {
                    $mimeType = $mimeType->getFieldValue();
                }
                else if ( isset( $_FILES['file']['tmp_name'] ) &&
                          is_uploaded_file( $_FILES['file']['tmp_name'] ) )
                {
                    $mimeType = 'multipart/form-data';
                }
                else
                {
                    $mimeType = 'application/octet-stream';
                }

                $chunk  = (int) $request->getPost( 'chunk',
                                $request->getQuery( 'chunk', 0 ) );
                $chunks = (int) $request->getPost( 'chunks',
                                $request->getQuery( 'chunks', 0 ) );

                $fileBase = self::TEMP_PATH . DIRECTORY_SEPARATOR;
                $fileName = strtr(
                    preg_replace( '#^\.+#', '',
                                  $request->getPost( 'name', 'default' ) ),
                    array(
                        '"'     => '\'',
                        '<'     => '_',
                        '>'     => '_',
                        '*'     => '_',
                        '?'     => '_',
                        ':'     => '-',
                        '|'     => '-',
                        '\\'    => '-',
                        '/'     => '-',
                    )
                );

                $filePath = $fileBase . $fileName;

                // Make sure the fileName is unique but only if chunking is disabled
                if ( $chunks < 2 && file_exists( $filePath ) )
                {
                    $ext        = strrpos( $fileName, '.' );
                    $fileName_a = substr( $fileName, 0, $ext );
                    $fileName_b = substr( $fileName, $ext );

                    $count = 1;
                    while ( file_exists( $fileBase .
                                $fileName_a . ' ' . $count . $fileName_b ) )
                    {
                        $count++;
                    }

                    $fileName = $fileName_a . ' ' . $count . $fileName_b;
                }

                // Handle non multipart uploads
                // older WebKit versions didn't support multipart in HTML5
                if ( strpos( $mimeType, 'multipart' ) !== false )
                {
                    if ( isset( $_FILES['file']['tmp_name'] ) &&
                         is_uploaded_file( $_FILES['file']['tmp_name'] ) )
                    {
                        // Open temp file
                        $out = fopen( $filePath . '.part', $chunk ? 'ab' : 'wb' );

                        if ( $out )
                        {
                            // Read binary input stream and append it to temp file
                            $in = fopen( $_FILES['file']['tmp_name'], 'rb' );

                            if ( $in )
                            {
                                while ( $buff = fread( $in, 4096 ) )
                                {
                                    fwrite( $out, $buff );
                                }
                            }
                            else
                            {
                                $view['error'] = array(
                                    'code'      => 101,
                                    'message'   => 'Failed to open input stream',
                                );
                            }

                            fclose( $in );
                            fclose( $out );

                            @ unlink( $_FILES['file']['tmp_name'] );
                        }
                        else
                        {
                            $view['error'] = array(
                                'code'      => 102,
                                'message'   => 'Failed to open output stream',
                            );
                        }
                    }
                    else
                    {
                        $view['error'] = array(
                            'code'      => 103,
                            'message'   => 'Failed to move uploaded file',
                        );
                    }
                }
                else
                {
                    // Open temp file
                    $out = fopen( $filePath . '.part', $chunk ? 'ab' : 'wb' );

                    if ( $out )
                    {
                        // Read binary input stream and append it to temp file
                        $in = fopen( 'php://input', 'rb' );

                        if ( $in )
                        {
                            while ( $buff = fread( $in, 4096 ) )
                            {
                                fwrite( $out, $buff );
                            }
                        }
                        else
                        {
                            $view['error'] = array(
                                'code'      => 101,
                                'message'   => 'Failed to open input stream',
                            );
                        }

                        fclose( $in );
                        fclose( $out );
                    }
                    else
                    {
                        $view['error'] = array(
                            'code'      => 102,
                            'message'   => 'Failed to open output stream',
                        );
                    }
                }

                // Check if file has been uploaded
                if ( empty( $view['error'] ) &&
                     ( ! $chunks || $chunk == $chunks - 1 ) )
                {
                    // Strip the temp .part suffix off
                    rename( $filePath . '.part', $filePath );
                    @ chmod( $filePath, static::UPLOAD_MOD );
                }
            }
        }
        else
        {
            $view['error'] = (object) array(
                'code'      => 98,
                'message'   => 'Not logged in',
            );
        }

        if ( empty( $view['error'] ) )
        {
            $view['result'] = null;
        }

        $view['id'] = 'id';
        $content    = @ json_encode( $view );

        $headers->addHeaderLine( 'Content-Length', mb_strlen( $content ) );
        $response->setContent( $content );
        return $response;
    }

}
