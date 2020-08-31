<?php 
namespace App\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Core\Repository\PluginRepository")
 * @ORM\Table(name="core_plugin")
 */
class Plugin
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string 
     */
    private $code = '';

    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     * @var string 
     */
    private $name = '';

    /**
     * @ORM\Column(type="text", nullable=false, options={"default": ""})
     * @var string 
     */
    private $description = '';

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 1})
     * @var int
     */
    private $priority = 1;

    /**
     * @ORM\Column(type="integer", length=1, nullable=false, options={"default": App\Core\Entity\Plugin::STATUS_DISABLE})
     * @var int
     */
    private $status = self::STATUS_DISABLE;

    /**
     * Require skeleton in json format
     * @ORM\Column(type="string", length=1000,nullable=false, options={"default":"[]"})
     * @var string 
     */
    private $required = '[]';

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    private $updatedDate;

    /**
     * Get $id
     * @return int 
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Set $id
     * @param int $id
     * @return $this 
     */
    public function setId(int $id) : self 
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get $code
     * @return string 
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * Set $code
     * @param string $code
     * @return $this 
     */
    public function setCode(string $code) : self 
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get $name
     * @return string 
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set $name
     * @param string $name
     * @return $this 
     */
    public function setName(string $name) : self 
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get $description
     * @return string 
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Set $description
     * @param string $description
     * @return $this 
     */
    public function setDescription(string $description) : self 
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get $priority
     * @return int 
     */
    public function getPriority() : int
    {
        return $this->priority;
    }

    /**
     * Set $priority
     * @param int $priority
     * @return $this 
     */
    public function setPriority(int $priority) : self 
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get $status
     * @return int 
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set $status
     * @param int $status
     * @return $this 
     */
    public function setStatus(int $status) : self 
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get $createdDate
     * @return \DateTime
     */
    public function getCreatedDate() : \DateTime
    {
        return $this->createdDate;
    }

    /**
     * Set $createdDate
     * @param \DateTime $createdDate
     * @return $this 
     */
    public function setCreatedDate(\DateTime $createdDate) : self 
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * Get $updatedDate
     * @return \DateTime
     */
    public function getUpdatedDate() : \DateTime
    {
        return $this->updatedDate;
    }

    /**
     * Set $updatedDate
     * @param \DateTime $updatedDate
     * @return $this 
     */
    public function setUpdatedDate(\DateTime $updatedDate) : self 
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }

    /**
     * Check is enabled
     * @return bool
     */
    public function isEnable() : bool
    {
        return $this->getStatus() == self::STATUS_ENABLE;
    }

    /**
     * Check is disabled
     * @return bool
     */
    public function isDisable() : bool
    {
        return $this->getStatus() == self::STATUS_DISABLE;
    }

    /**
     * Get $required
     * @return string|array 
     */
    public function getRequired(string $format = 'raw')
    {
        if ($format === 'serialize') {
            return json_decode($this->required);
        }
        return $this->required;
    }

    /**
     * Set $required
     * @param string $required
     * @return $this 
     */
    public function setRequired(string $required) : self 
    {
        $this->required = $required;
        return $this;
    }    
}
