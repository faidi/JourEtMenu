<?php

namespace Utilisateurs\UtilisateursBundle\Entity; 

use Utilisateurs\UtilisateursBundle\Entity\Adresses;

use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="restaurateurs")
 * @UniqueEntity(fields = "username", targetClass = "Utilisateurs\UtilisateursBundle\Entity\Utilisateurs", message="fos_user.username.already_used")
 * @UniqueEntity(fields = "email", targetClass = "Utilisateurs\UtilisateursBundle\Entity\Utilisateurs", message="fos_user.email.already_used")
 */
class Restaurateurs extends Utilisateurs
{
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Reservations", mappedBy="restaurant")
     *  
     */
    private $reservations;
    
    /**
     * @ORM\ManyToMany(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Clients", mappedBy="restaurantfavoris")
     *
     */
    private $clients;
    
    /**
     * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Menus", mappedBy="restaurant")
     * 
     */
    private $menus;
    
    /**
     * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\platDuJour", mappedBy="restaurant")
     * 
     */
    private $platsDuJour;
    
    
    /**
     * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Medias", mappedBy="restaurant" )
     *  
     */
    private $images;
    
    /**
     * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Specialite", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $specialite;
    
    /**
     * @ORM\OneToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Adresses", mappedBy="restaurant")
     */
    private $adresse;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="telephone", type="integer")
     */
    private $telephone;
    
 
    /**
     * Constructor
     */
    public function __construct()
    {	parent::__construct();
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    public function getType(){
    
    	return "restaurant";
    }
    /**
     * Set nom
     *
     * @param string $nom
     * @return Restaurateurs
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
     * Set telephone
     *
     * @param integer $telephone
     * @return Restaurateurs
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return integer 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Add reservations
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations
     * @return Restaurateurs
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
     * Get reservations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * Add images
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Medias $images
     * @return Restaurateurs
     */
    public function addImage(\JourEtMenu\JourEtMenuBundle\Entity\Medias $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Medias $images
     */
    public function removeImage(\JourEtMenu\JourEtMenuBundle\Entity\Medias $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set specialite
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Specialite $specialite
     * @return Restaurateurs
     */
    public function setSpecialite(\JourEtMenu\JourEtMenuBundle\Entity\Specialite $specialite)
    {
        $this->specialite = $specialite;

        return $this;
    }

    /**
     * Get specialite
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\Specialite 
     */
    public function getSpecialite()
    {
        return $this->specialite;
    }

    /**
     * Add menus
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Menus $menus
     * @return Restaurateurs
     */
    public function addMenu(\JourEtMenu\JourEtMenuBundle\Entity\Menus $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Menus $menus
     */
    public function removeMenu(\JourEtMenu\JourEtMenuBundle\Entity\Menus $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Add platsDuJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Menus $platsDuJour
     * @return Restaurateurs
     */
    public function addPlatsDuJour(\JourEtMenu\JourEtMenuBundle\Entity\Menus $platsDuJour)
    {
        $this->platsDuJour[] = $platsDuJour;

        return $this;
    }

    /**
     * Remove platsDuJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Menus $platsDuJour
     */
    public function removePlatsDuJour(\JourEtMenu\JourEtMenuBundle\Entity\Menus $platsDuJour)
    {
        $this->platsDuJour->removeElement($platsDuJour);
    }

    /**
     * Get platsDuJour
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlatsDuJour()
    {
        return $this->platsDuJour;
    }

    /**
     * Add clients
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Clients $clients
     * @return Restaurateurs
     */
    public function addClient(\Utilisateurs\UtilisateursBundle\Entity\Clients $clients)
    {
        $this->clients[] = $clients;

        return $this;
    }

    /**
     * Remove clients
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Clients $clients
     */
    public function removeClient(\Utilisateurs\UtilisateursBundle\Entity\Clients $clients)
    {
        $this->clients->removeElement($clients);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Set adresse
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Adresses $adresse
     * @return Restaurateurs
     */
    public function setAdresse(\Utilisateurs\UtilisateursBundle\Entity\Adresses $adresse = null)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return \Utilisateurs\UtilisateursBundle\Entity\Adresses 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }
}
