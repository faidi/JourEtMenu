<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormulesJour
 *
 * @ORM\Table("formules_jour")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Entity\FormulesJourRepository")
 */
class FormulesJour
{
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Aperitif", mappedBy="formuleJour")
	 * 
	 */
	private $aperitifs;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Entrees", mappedBy="formuleJour")
	 *  
	 */
	private $entrees;
	
	/**
	 * @ORM\OneToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\platDuJour", cascade={"persist","remove"})
	 *  
	 */
	private $plat;
	
	
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Desserts", mappedBy="formuleJour")
	 *  
	 */
	private $desserts;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\digestif", mappedBy="formuleJour")
	 *  
	 */
	private $digestifs;
	
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Reservations", mappedBy="formulesJour")
	 *  
	 */
	private $reservations;
	
	
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
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

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
     * @return FormulesJour
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
     * @return FormulesJour
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
     * Add aperitifs
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Aperitif $aperitifs
     * @return FormulesJour
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
     * @return FormulesJour
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
     * Set plat
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $plat
     * @return FormulesJour
     */
    public function setPlat(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $plat)
    {
        $this->plat = $plat;

        return $this;
    }

    /**
     * Get plat
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\platDuJour 
     */
    public function getPlat()
    {
        return $this->plat;
    }

    /**
     * Add desserts
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Desserts $desserts
     * @return FormulesJour
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
     * @return FormulesJour
     */
    public function addDigestif(\JourEtMenu\JourEtMenuBundle\Entity\digestif $digestifs)
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
     * @return FormulesJour
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
     * @return FormulesJour
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
     * Constructor
     */
    public function __construct()
    {
        $this->aperitifs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->entrees = new \Doctrine\Common\Collections\ArrayCollection();
        $this->desserts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->digestifs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
