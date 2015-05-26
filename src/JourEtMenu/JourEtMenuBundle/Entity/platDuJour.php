<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * platDuJour
 *
 * @ORM\Table("plat_du_jour")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\platDuJourRepository")
 */
class platDuJour
{
	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Medias",mappedBy="platdujour")
	 * 
	 */
	private $medias;
	
	/**
	 * @ORM\ManyToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Ingredients",mappedBy="platdujour")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $ingredients;
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="updated_at",type="datetime", nullable=true)
	 */
	private $updateAt;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Restaurateurs", inversedBy="platsDuJour")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $restaurant;
	

	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Reservations", mappedBy="platDuJour")
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
     * @var string
     *
     * @ORM\Column(name="desciption", type="string", length=255)
     */
    private $desciption;

    /**
     * @var boolean
     *
     * @ORM\Column(name="disponible", type="boolean")
     */
    private $disponible;


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
     * @return platDuJour
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }
    
    
    public function getType(){
    
    	return "pdj";
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
     * @return platDuJour
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
     * Set desciption
     *
     * @param string $desciption
     * @return platDuJour
     */
    public function setDesciption($desciption)
    {
        $this->desciption = $desciption;

        return $this;
    }

    /**
     * Get desciption
     *
     * @return string 
     */
    public function getDesciption()
    {
        return $this->desciption;
    }

    /**
     * Set disponible
     *
     * @param boolean $disponible
     * @return platDuJour
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * Get disponible
     *
     * @return boolean 
     */
    public function getDisponible()
    {
        return $this->disponible;
    }
    
    

     

    
        /**
     * Constructor
     */
    public function __construct()
    {
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ingredients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add medias
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Medias $medias
     * @return platDuJour
     */
    public function addMedia(\JourEtMenu\JourEtMenuBundle\Entity\Medias $medias)
    {$medias->setPlatdujour($this);
        $this->medias[] = $medias;

        return $this;
    }

    /**
     * Remove medias
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Medias $medias
     */
    public function removeMedia(\JourEtMenu\JourEtMenuBundle\Entity\Medias $medias)
    {
        $this->medias->removeElement($medias);
    }

    /**
     * Get medias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * Add ingredients
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Ingredients $ingredients
     * @return platDuJour
     */
    public function addIngredient(\JourEtMenu\JourEtMenuBundle\Entity\Ingredients $ingredients)
    {
        $this->ingredients[] = $ingredients;

        return $this;
    }

    /**
     * Remove ingredients
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Ingredients $ingredients
     */
    public function removeIngredient(\JourEtMenu\JourEtMenuBundle\Entity\Ingredients $ingredients)
    {
        $this->ingredients->removeElement($ingredients);
    }

    /**
     * Get ingredients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * Set reservations
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations
     * @return platDuJour
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
     * @return platDuJour
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
     * Set updateAt
     *
     * @param \DateTime $updateAt
     * @return platDuJour
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime 
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set restaurant
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant
     * @return platDuJour
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
