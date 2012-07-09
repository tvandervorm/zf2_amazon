<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage SimpleDb
 */

namespace Zend\Service\Amazon\SimpleDb;
use Zend\Http;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage SimpleDb
 */
class Response
{
    /**
     * XML namespace used for SimpleDB responses.
     */
    protected $_xmlNamespace = 'http://sdb.amazonaws.com/doc/2009-04-15/';

    /**
     * The original HTTP response
     *
     * This contains the response body and headers.
     *
     * @var Zend\Http\Response
     */
    private $_httpResponse = null;

    /**
     * The response document object
     *
     * @var DOMDocument
     */
    private $_document = null;

    /**
     * The response XPath
     *
     * @var DOMXPath
     */
    private $_xpath = null;

    /**
     * Creates a new high-level SimpleDB response object
     *
     * @param  Zend\Http\Response $httpResponse the HTTP response.
     * @return void
     */
    public function __construct(Http\Response $httpResponse)
    {
        $this->_httpResponse = $httpResponse;
    }

    /**
     * Gets the XPath object for this response
     *
     * @return DOMXPath the XPath object for response.
     */
    public function getXPath()
    {
        if ($this->_xpath === null) {
            $document = $this->getDocument();
            if ($document === false) {
                $this->_xpath = false;
            } else {
                $this->_xpath = new \DOMXPath($document);
                $this->_xpath->registerNamespace('sdb',
                    $this->getNamespace());
            }
        }

        return $this->_xpath;
    }

    /**
     * Gets the SimpleXML document object for this response
     *
     * @return SimpleXMLElement
     */
    public function getSimpleXMLDocument()
    {
        try {
            $body = $this->_httpResponse->getBody();
        } catch (Http\Exception $e) {
            $body = false;
        }

        return simplexml_load_string($body);
    }

    /**
     * Get HTTP response object
     *
     * @return Zend\Http\Response
     */
    public function getHttpResponse()
    {
        return $this->_httpResponse;
    }

    /**
     * Gets the document object for this response
     *
     * @return DOMDocument the DOM Document for this response.
     */
    public function getDocument()
    {
        try {
            $body = $this->_httpResponse->getBody();
        } catch (Http\Exception $e) {
            $body = false;
        }

        if ($this->_document === null) {
            if ($body !== false) {
                // turn off libxml error handling
                $errors = libxml_use_internal_errors();

                $this->_document = new \DOMDocument();
                if (!$this->_document->loadXML($body)) {
                    $this->_document = false;
                }

                // reset libxml error handling
                libxml_clear_errors();
                libxml_use_internal_errors($errors);
            } else {
                $this->_document = false;
            }
        }

        return $this->_document;
    }

    /**
     * Return the current set XML Namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->_xmlNamespace;
    }

    /**
     * Set a new XML Namespace
     *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->_xmlNamespace = $namespace;
    }
}
