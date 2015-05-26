<?php

namespace JourEtMenu\JourEtMenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Medias
 *
 *
 * @ORM\Table("medias")
 * @ORM\Entity(repositoryClass="JourEtMenu\JourEtMenuBundle\Repository\MediasRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Medias {
	/**
	 * @ORM\ManyToOne(targetEntity="Utilisateurs\UtilisateursBundle\Entity\Restaurateurs", inversedBy="images")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $restaurant;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="JourEtMenu\JourEtMenuBundle\Entity\platDuJour", inversedBy="medias")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $platdujour;
	
	/**
	 *
	 * @var integer 
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	 
	
	/**
	 * @ORM\Column(type="string",length=255, nullable=true)
	 */
	private $path;
	
	
	
	/**
	 * Image file
	 *
	 * @var File @Assert\File(
	 *      maxSize = "5M",
	 *      mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
	 *      maxSizeMessage = "The maxmimum allowed file size is 5MB.",
	 *      mimeTypesMessage = "Only the filetypes image are allowed."
	 *      )
	 */
	public $file;
	
	/**
	 * Called before saving the entity
	 *
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 */
	public function preUpload() {
		if (null !== $this->file) {
			// do whatever you want to generate a unique name
			$filename = sha1 ( uniqid ( mt_rand (), true ) );
			$this->path = $filename . '.' . $this->file->guessExtension ();
		}
	}
	
	/**
	 * Called before entity removal
	 *
	 * @ORM\PreRemove()
	 */
	public function removeUpload() {
		if ($file = $this->getAbsolutePath ()) {
			unlink ( $file );
		}
	}
	public function getUploadRootDir() {
		return __dir__ . '/../../../../web/uploads';
	}
	
	public function getAbsolutePath() {
		return null === $this->path ? null : $this->getUploadRootDir () . '/' . $this->path;
	}
	public function getAssetPath() {
		return 'uploads/' . $this->path;
	}
	
	 
	/**
	 * Called after entity persistence
	 *
	 * @ORM\PostPersist()
	 * @ORM\PostUpdate()
	 */
	public function upload() {
		// The file property can be empty if the field is not required
		if (null === $this->file) {
			return;
		}
		
		// Use the original file name here but you should
		// sanitize it at least to avoid any security issues
		
		// move takes the target directory and then the
		// target filename to move to
		$this->file->move ( $this->getUploadRootDir (), $this->path );
		
		// Set the path property to the filename where you've saved the file
		// $this->path = $this->file->getClientOriginalName();
		
		// Clean up the file property as you won't need it anymore
		$this->file = null;
	}
	
	 
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}
	public function getPath() {
		return $this->path;
	}
	 
	
	 
	 
	
	/**
	 * Set path
	 *
	 * @param string $path        	
	 * @return Medias
	 */
	public function setPath($path) {
		$this->path = $path;
		
		return $this;
	}
	
	/**
	 * Set restaurant
	 *
	 * @param \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant        	
	 * @return Medias
	 */
	public function setRestaurant(\Utilisateurs\UtilisateursBundle\Entity\Restaurateurs $restaurant) {
		$this->restaurant = $restaurant;
		
		return $this;
	}
	
	/**
	 * Get restaurant
	 *
	 * @return \Utilisateurs\UtilisateursBundle\Entity\Restaurateurs
	 */
	public function getRestaurant() {
		return $this->restaurant;
	}

    /**
     * Set platdujour
     *
     * @param \JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour
     * @return Medias
     */
    public function setPlatdujour(\JourEtMenu\JourEtMenuBundle\Entity\platDuJour $platdujour = null)
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
}
