<?php

namespace Utilisateurs\UtilisateursBundle\Entity;

use Utilisateurs\UtilisateursBundle\Entity\Clients;
use Utilisateurs\UtilisateursBundle\Entity\Restaurateurs;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adresses
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Adresses
{
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
     * @ORM\Column(name="adresse", type="string", length=255)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255)
     */
    private $ville;

    /**
     * @var integer
     *
     * @ORM\Column(name="cp", type="integer")
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;
    
    /**
     * @ORM\OneToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Clients", inversedBy="adresse")
     * 
     **/
    private $client;

    /**
     * @ORM\OneToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Restaurateurs", inversedBy="adresse")
     * 
     **/
    private $restaurant;
    
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
     * Set adresse
     *
     * @param string $adresse
     * @return Adresses
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    

    /**
     * Set ville
     *
     * @param string $ville
     * @return Adresses
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set cp
     *
     * @param integer $cp
     * @return Adresses
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return integer 
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Adresses
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string 
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set client
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Clients $client
     * @return Adresses
     */
    public function setClient(\Utilisateurs\UtilisateursBundle\Entity\Clients $client = null)
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
     * Set restaurant
     *
     * @param \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant
     * @return Adresses
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
