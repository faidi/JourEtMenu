<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menus
 *
 * @ORM\Table("menus")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\MenusRepository")
 */
class Menus
{
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Aperitif", mappedBy="menu")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $aperitifs;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Entrees", mappedBy="menu")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $entrees;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Plats", mappedBy="menu")
	 *  
	 */
	private $plats;
	
	
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Desserts", mappedBy="menu")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $desserts;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\digestif", mappedBy="menu")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $digestifs;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Reservations", mappedBy="menu")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $reservations;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Restaurateurs", inversedBy="menus")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $restaurant;
	
	
	
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

 

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;


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
     * @return Menus
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
     * Set prix
     *
     * @param float $prix
     * @return Menus
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Menus
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aperitifs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->entrees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->plats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->desserts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->digestifs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add aperitifs
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Aperitif $aperitifs
     * @return Menus
     */
    public function addAperitif(\JourEtMenu\JourEtMenuBundle\Entity\Aperitif $aperitifs)
    {
        $this->aperitifs[] = $aperitifs;

        return $this;
    }

    /**
     * Remove aperitifs
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Aperitif $aperitifs
     */
    public function removeAperitif(\JourEtMenu\JourEtMenuBundle\Entity\Aperitif $aperitifs)
    {
        $this->aperitifs->removeElement($aperitifs);
    }

    /**
     * Get aperitifs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAperitifs()
    {
        return $this->aperitifs;
    }

    /**
     * Add entrees
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Entrees $entrees
     * @return Menus
     */
    public function addEntree(\JourEtMenu\JourEtMenuBundle\Entity\Entrees $entrees)
    {
        $this->entrees[] = $entrees;

        return $this;
    }

    /**
     * Remove entrees
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Entrees $entrees
     */
    public function removeEntree(\JourEtMenu\JourEtMenuBundle\Entity\Entrees $entrees)
    {
        $this->entrees->removeElement($entrees);
    }

    /**
     * Get entrees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntrees()
    {
        return $this->entrees;
    }

    /**
     * Add plats
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $plats
     * @return Menus
     */
    public function addPlat(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $plats)
    {
        $this->plats[] = $plats;

        return $this;
    }

    /**
     * Remove plats
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $plats
     */
    public function removePlat(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $plats)
    {
        $this->plats->removeElement($plats);
    }

    /**
     * Get plats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlats()
    {
        return $this->plats;
    }

    /**
     * Add desserts
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Desserts $desserts
     * @return Menus
     */
    public function addDessert(\JourEtMenu\JourEtMenuBundle\Entity\Desserts $desserts)
    {
        $this->desserts[] = $desserts;

        return $this;
    }

    /**
     * Remove desserts
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Desserts $desserts
     */
    public function removeDessert(\JourEtMenu\JourEtMenuBundle\Entity\Desserts $desserts)
    {
        $this->desserts->removeElement($desserts);
    }

    /**
     * Get desserts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDesserts()
    {
        return $this->desserts;
    }

    /**
     * Add digestifs
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Digestifs $digestifs
     * @return Menus
     */
    public function addDigestif(\JourEtMenu\JourEtMenuBundle\Entity\digestif  $digestifs)
    {
        $this->digestifs[] = $digestifs;

        return $this;
    }

    /**
     * Remove digestifs
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Digestifs $digestifs
     */
    public function removeDigestif(\JourEtMenu\JourEtMenuBundle\Entity\digestif $digestifs)
    {
        $this->digestifs->removeElement($digestifs);
    }

    /**
     * Get digestifs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDigestifs()
    {
        return $this->digestifs;
    }

    /**
     * Set reservations
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations
     * @return Menus
     */
    public function setReservations(\JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations = null)
    {
        $this->reservations = $reservations;

        return $this;
    }

    /**
     * Get reservations
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\Reservations 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Add reservations
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations
     * @return Menus
     */
    public function addReservation(\JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations)
    {
        $this->reservations[] = $reservations;

        return $this;
    }

    /**
     * Remove reservations
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations
     */
    public function removeReservation(\JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations)
    {
        $this->reservations->removeElement($reservations);
    }

    /**
     * Set restaurant
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant
     * @return Menus
     */
    public function setRestaurant(\Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant = null)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }
}
