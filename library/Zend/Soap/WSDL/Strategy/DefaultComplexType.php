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
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Soap\WSDL\Strategy;

use Zend\Soap;

use Zend\Soap\WSDLException;

/**
 * Zend_Soap_WSDL_Strategy_DefaultComplexType
 *
 * @uses       ReflectionClass
 * @uses       \Zend\Soap\WSDLException
 * @uses       \Zend\Soap\WSDL\Strategy\AbstractStrategy
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DefaultComplexType extends AbstractStrategy
{
    /**
     * Add a complex type by recursivly using all the class properties fetched via Reflection.
     *
     * @param  string $type Name of the class to be specified
     * @return string XSD Type for the given PHP type
     */
    public function addComplexType($type)
    {
        if(!class_exists($type)) {
            throw new WSDLException(sprintf(
                'Cannot add a complex type %s that is not an object or where '
              . 'class could not be found in \'DefaultComplexType\' strategy.', $type
            ));
        }

        $dom = $this->getContext()->toDomDocument();
        $class = new \ReflectionClass($type);

        $translatedType = Soap\WSDL::translateType($type);
        $complexType = $dom->createElement('xsd:complexType');
        $complexType->setAttribute('name', $translatedType);

        $all = $dom->createElement('xsd:all');

        foreach ($class->getProperties() as $property) {
            if ($property->isPublic() && preg_match_all('/@var\s+([^\s]+)/m', $property->getDocComment(), $matches)) {

                /**
                 * @todo check if 'xsd:element' must be used here (it may not be compatible with using 'complexType'
                 * node for describing other classes used as attribute types for current class
                 */
                $element = $dom->createElement('xsd:element');
                $element->setAttribute('name', $property->getName());
                $element->setAttribute('type', $this->getContext()->getType(trim($matches[1][0])));
                $all->appendChild($element);
            }
        }

        $complexType->appendChild($all);
        $this->getContext()->getSchema()->appendChild($complexType);
        $this->getContext()->addType($type, 'tns:' . $translatedType);

        return 'tns:' . $translatedType;
    }
}
