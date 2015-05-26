<?php

namespace Utilisateurs\UtilisateursBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use PUGX\MultiUserBundle\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="clients")
 * @UniqueEntity(fields = "username", targetClass = "Utilisateurs\UtilisateursBundle\Entity\Utilisateurs", message="fos_user.username.already_used")
 * @UniqueEntity(fields = "email", targetClass = "Utilisateurs\UtilisateursBundle\Entity\Utilisateurs", message="fos_user.email.already_used")
 */
class Clients extends Utilisateurs
{
	
	

	/**
	 * @ORM\OneToMany(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Reservations", mappedBy="client")
	 *  
	 */
	private $reservations;
	
	
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
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
     * @return Clients
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
     * @return Clients
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
    
    public function getType(){
    	 
    	return "clients";
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add reservations
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Reservations $reservations
     * @return Clients
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
}
