<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reservations
 *
 * @ORM\Table("reservations")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\ReservationsRepository")
 */
class Reservations
{
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\statusReservation", cascade={"persist","remove"})
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $status;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Restaurateurs", inversedBy="reservations")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $restaurant;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Clients", inversedBy="reservations")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $client;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\platDuJour", inversedBy="reservations")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $platDuJour;
	
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\Menus", inversedBy="reservations")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $menu;
	
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\FormulesJour", inversedBy="reservations")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $formulesJour;
	
	
	
	
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbrPersonne", type="integer")
     */
    private $nbrPersonne;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="message", type="string")
     */
    private $message;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Reservations
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nbrPersonne
     *
     * @param integer $nbrPersonne
     * @return Reservations
     */
    public function setNbrPersonne($nbrPersonne)
    {
        $this->nbrPersonne = $nbrPersonne;

        return $this;
    }

    /**
     * Get nbrPersonne
     *
     * @return integer 
     */
    public function getNbrPersonne()
    {
        return $this->nbrPersonne;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        
    }

     
    
     
     
   

    /**
     * Set status
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\statusReservation $status
     * @return Reservations
     */
    public function setStatus(\JourEtMenu\JourEtMenuBundle\Entity\statusReservation $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\statusReservation 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set restaurant
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant
     * @return Reservations
     */
    public function setRestaurant(\Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant)
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

    /**
     * Set client
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Clients $client
     * @return Reservations
     */
    public function setClient(\Utilisateurs\UtilisateursBundle\Entity\Clients $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Utilisateurs\UtilisateursBundle\Entity\Clients 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set platDuJours
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platDuJours
     * @return Reservations
     */
    public function setPlatDuJours(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platDuJours)
    {
        $this->platDuJours = $platDuJours;

        return $this;
    }

    /**
     * Get platDuJours
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\platDuJour 
     */
    public function getPlatDuJours()
    {
        return $this->platDuJours;
    }

    /**
     * Set menu
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\Menus $menu
     * @return Reservations
     */
    public function setMenu(\JourEtMenu\JourEtMenuBundle\Entity\Menus $menu = null)
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
     * Add formulesJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\FormulesJour $formulesJour
     * @return Reservations
     */
    public function addFormulesJour(\JourEtMenu\JourEtMenuBundle\Entity\FormulesJour $formulesJour)
    {
        $this->formulesJour[] = $formulesJour;

        return $this;
    }

    /**
     * Remove formulesJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\FormulesJour $formulesJour
     */
    public function removeFormulesJour(\JourEtMenu\JourEtMenuBundle\Entity\FormulesJour $formulesJour)
    {
        $this->formulesJour->removeElement($formulesJour);
    }

    /**
     * Get formulesJour
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFormulesJour()
    {
        return $this->formulesJour;
    }

    /**
     * Set formulesJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\FormulesJour $formulesJour
     * @return Reservations
     */
    public function setFormulesJour(\JourEtMenu\JourEtMenuBundle\Entity\FormulesJour $formulesJour)
    {
        $this->formulesJour = $formulesJour;

        return $this;
    }

    /**
     * Set platDuJour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platDuJour
     * @return Reservations
     */
    public function setPlatDuJour(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platDuJour = null)
    {
        $this->platDuJour = $platDuJour;

        return $this;
    }

    /**
     * Get platDuJour
     *
     * @return \JourEtMenu\JourEtMenuBundle\Entity\platDuJour 
     */
    public function getPlatDuJour()
    {
        return $this->platDuJour;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Reservations
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
}
