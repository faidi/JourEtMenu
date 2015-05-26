<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Ingredients
 *
 * @ORM\Table("ingredients")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\IngredientsRepository")
 */
class Ingredients
{
	
	
	/**
	 * @ORM\ManyToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\platDuJour", inversedBy="ingredients")
	 * @ORM\JoinTable(name="plat_ingr")
	 */
	private $platdujour;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Ingredients
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set platdujour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour
     * @return Ingredients
     */
    public function setPlatdujour(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour)
    {
        $this->platdujour = $platdujour;

        return $this;
    }

    /**
     * Get platdujour
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\platDuJour 
     */
    public function getPlatdujour()
    {
        return $this->platdujour;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->platdujour = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add platdujour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour
     * @return Ingredients
     */
    public function addPlatdujour(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour)
    {
        $this->platdujour[] = $platdujour;

        return $this;
    }

    /**
     * Remove platdujour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour
     */
    public function removePlatdujour(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour)
    {
        $this->platdujour->removeElement($platdujour);
    }
    
    public function __toString()
    {
    	return $this->nom;
    }
}
