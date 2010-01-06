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
 * @package    Zend_XmlRpc
 * @subpackage Generator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Client.php 17752 2009-08-22 15:49:54Z lars $
 */

/**
 * @var Zend_XmlRpc_Generator_GeneratorAbstract
 */
require_once 'Zend/XmlRpc/Generator/GeneratorAbstract.php';

/**
 * DOMDocument based implementation of a XML/RPC generator
 */
class Zend_XmlRpc_Generator_DomDocument extends Zend_XmlRpc_Generator_GeneratorAbstract
{
    /**
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * @var DOMNode
     */
    protected $_currentElement;

    /**
     * Start XML element
     *
     * Creates a new XML element and appends it to the document tree
     *
     * @param string $name
     * @param string $value
     * @return Zend_XmlRpc_Generator_DomDocument Fluent interface
     */
    public function startElement($name, $value = null)
    {
        $newElement = $this->_dom->createElement($name);

        $this->_currentElement = $this->_currentElement->appendChild($newElement);

        if ($value !== null) {
            $this->_currentElement->appendChild($this->_dom->createTextNode($value));
        }

        return $this;
    }

    /**
     * End of an XML element
     *
     * Resets $_currentElement to the next parent node in the hierarchy
     *
     * @param string $name
     * @return Zend_XmlRpc_Generator_DomDocument Fluent interface
     */
    public function endElement($name)
    {
        if (isset($this->_currentElement->parentNode)) {
            $this->_currentElement = $this->_currentElement->parentNode;
        }

        return $this;
    }

    /**
     * Save XML as a string
     *
     * @return string
     */
    public function saveXml()
    {
        return $this->_dom->saveXml();
    }

    /**
     * Initializes internal objects
     *
     * @return void
     */
    protected function _init()
    {
        $this->_dom = new DOMDocument('1.0', $this->_encoding);
        $this->_currentElement = $this->_dom;
    }
}