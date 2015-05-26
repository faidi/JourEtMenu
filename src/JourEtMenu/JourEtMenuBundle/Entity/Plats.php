<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plats
 *
 * @ORM\Table("plats")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\PlatsRepository")
 */
class Plats
{
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Menus", inversedBy="plats")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $menu;
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
     * @return Plats
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
     * @return Plats
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
}
