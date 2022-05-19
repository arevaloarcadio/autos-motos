<?php
declare(strict_types=1);

namespace App\XMLParser;

use App\XMLParser\Output\XMLNode;
use SimpleXMLElement;
use XMLReader;

/**
 * @package App\Service
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class XMLParser
{
    /**
     * @var XMLReader
     */
    private $reader;
    
    /**
     * XMLParser constructor.
     */
    public function __construct()
    {
        $this->reader = new XMLReader();
    }
    
    /**
     * @param string $url
     */
    public function open(string $url): void
    {
        if (str_contains($url, 'data.xml')) {
            $this->reader->open($url);
        } else {
            $this->reader->XML($url);
        }
    }
    
    /**
     * @param string $name
     *
     * @return bool
     */
    public function moveToFirstNodeNamed(string $name): bool
    {
        while ($this->reader->read() && $this->reader->name !== $name) {
            continue;
        }
        
        return $this->reader->name === $name;
    }
    
    /**
     * @param XMLNode $node
     * @param string  $childNodeName
     *
     * @return XMLNode
     */
    public function getChildNode(XMLNode $node, string $childNodeName): XMLNode
    {
        $parser = new self();
        $parser->open($node->getOuterXml());
        
        if (true === $parser->moveToFirstNodeNamed($childNodeName)) {
            return $parser->createNode();
        }
        
        $node = new XMLNode(-1, $childNodeName, null, null);
        
        $parser->close();
        unset($parser);
        
        return $node;
    }
    
    /**
     * @param string   $nodeName
     * @param callable $callback
     */
    public function loop(string $nodeName, callable $callback): void
    {
        $this->moveToFirstNodeNamed($nodeName);
        while ($this->reader->name === $nodeName) {
            $callback($this->createNode());
            $this->reader->next($nodeName);
        }
    }
    
    /**
     * @return XMLNode
     */
    private function createNode(): XMLNode
    {
        return new XMLNode(
            $this->reader->nodeType,
            $this->reader->name,
            $this->reader->readInnerXml(),
            $this->reader->readOuterXml()
        );
    }
    
    /**
     * @param string   $xml
     * @param string   $nodeName
     * @param callable $callback
     */
    public function loopXml(string $xml, string $nodeName, callable $callback): void
    {
        $parser = new self();
        $parser->open($xml);
        $parser->loop($nodeName, $callback);
        $parser->close();
        unset($parser);
    }
    
    /**
     * @param XMLNode $node
     *
     * @return SimpleXMLElement
     */
    public function getSimpleXmlElementFromNode(XMLNode $node): SimpleXMLElement
    {
        return new SimpleXMLElement($node->getOuterXml());
    }
    
    /**
     * @param XMLNode $node
     *
     * @return array
     */
    public function getArrayFromNode(XMLNode $node): array
    {
        $xmlObject = $this->getSimpleXmlElementFromNode($node);
        $json      = json_encode($xmlObject);
        
        return json_decode($json, true);
    }
    
    /**
     * Close the reader.
     */
    public function close(): void
    {
        $this->reader->close();
    }
    
    /**
     * Get the value of the reader property.
     *
     * @return XMLReader
     */
    public function getReader(): XMLReader
    {
        return $this->reader;
    }
}
