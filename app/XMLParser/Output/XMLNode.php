<?php
declare(strict_types=1);

namespace App\XMLParser\Output;

/**
 * @package App\XMLParser\Output
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class XMLNode
{
    /**
     * @var int
     */
    private $type;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string|null
     */
    private $content;
    
    /**
     * @var string|null
     */
    private $outerXml;
    
    /**
     * XMLNode constructor.
     *
     * @param int         $type
     * @param string      $name
     * @param string|null $content
     * @param string|null $outerXml
     */
    public function __construct(int $type, string $name, ?string $content, ?string $outerXml)
    {
        $this->type     = $type;
        $this->name     = $name;
        $this->content  = $content;
        $this->outerXml = $outerXml;
    }
    
    /**
     * Get the value of the type property.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
    
    /**
     * Get the value of the name property.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Get the value of the content property.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }
    
    /**
     * Get the value of the outerXml property.
     *
     * @return string|null
     */
    public function getOuterXml(): ?string
    {
        return $this->outerXml;
    }
}
