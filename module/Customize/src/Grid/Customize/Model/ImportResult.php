<?php

namespace Grid\Customize\Model;

/**
 * ImportResult
 *
 * @author David Pozsar <david.pozsar@megaweb.hu>
 */
class ImportResult
{

    /**
     * @const int
     */
    const SUCCESS = 0;

    /**
     * @const int
     */
    const UNKNOWN_ERROR = 1;

    /**
     * @const int
     */
    const FILE_NOT_EXISTS = 2;

    /**
     * @const int
     */
    const FILE_NOT_ZIP = 3;

    /**
     * @const int
     */
    const STRUCTURE_XML_NOT_FOUND = 4;

    /**
     * @const int
     */
    const STRUCTURE_XML_DOCTYPE_MISMATCH = 5;

    /**
     * @const int
     */
    const STRUCTURE_XML_NOT_VALID = 6;

    /**
     * @const int
     */
    const STRUCTURE_XML_UNKNOWN_VERSION = 7;

    /**
     * @const int
     */
    const STRUCTURE_TYPE_NOT_ALLOWED = 8;

    /**
     * @var int|null
     */
    protected $code;

    /**
     * @var int|string|null
     */
    protected $data;

    /**
     * @return  int|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return  int|string|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param   int|null        $code
     * @param   int|string|null $data
     */
    public function __construct($code, $data)
    {
        $this->code = (int) $code;
        $this->data = $data;
    }

    /**
     * @return  bool
     */
    public function isSuccess()
    {
        return static::SUCCESS === $this->code;
    }

    /**
     * @return  int|null
     */
    public function getCreatedParagraphId()
    {
        return $this->isSuccess() ? (int) $this->data : null;
    }

    /**
     * @return  string|null
     */
    public function getErrorMessage()
    {
        return $this->isSuccess() ? null : (string) $this->data;
    }

}
