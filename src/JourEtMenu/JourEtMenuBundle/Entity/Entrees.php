<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entrees
 *
 * @ORM\Table("entrees")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\EntreesRepository")
 */
class Entrees
{
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Menus", inversedBy="entrees")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $menu;
	
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\FormulesJour", inversedBy="entrees")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $formuleJour;
	
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
     * @return Entrees
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
     * Set menu
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Menus $menu
     * @return Entrees
     */
    public function setMenu(\JourEtMenu\JourEtMenuBundle\Entity\Menus $menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\Menus 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set formuleJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $formuleJour
     * @return Entrees
     */
    public function setFormuleJour(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $formuleJour)
    {
        $this->formuleJour = $formuleJour;

        return $this;
    }

    /**
     * Get formuleJour
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\platDuJour 
     */
    public function getFormuleJour()
    {
        return $this->formuleJour;
    }
}
