<?php

namespace PR\VitrineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="PR\VitrineBundle\Repository\ImageRepository")
 */
class Image
{


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToMany(targetEntity="PR\VitrineBundle\Entity\Gallery" , cascade={"persist"})
    */
    private $gallery;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="full_path", type="string", length=255)
     */
    private $fullPath;

    /**
     * @var string
     *
     * @ORM\Column(name="gallery_path", type="string", length=255)
     */
    private $galleryPath;

    /**
     * @var int
     *
     * @ORM\Column(name="picture_order", type="integer", nullable=true)
     */
    private $pictureOrder;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullPath
     *
     * @param string $fullPath
     *
     * @return Image
     */
    public function setFullPath($fullPath)
    {
        $this->fullPath = $fullPath;

        return $this;
    }

    /**
     * Get fullPath
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * Set galleryPath
     *
     * @param string $galleryPath
     *
     * @return Image
     */
    public function setGalleryPath($galleryPath)
    {
        $this->galleryPath = $galleryPath;

        return $this;
    }

    /**
     * Get galleryPath
     *
     * @return string
     */
    public function getGalleryPath()
    {
        return $this->galleryPath;
    }

    /**
     * Set pictureOrder
     *
     * @param integer $pictureOrder
     *
     * @return Image
     */
    public function setPictureOrder($pictureOrder)
    {
        $this->pictureOrder = $pictureOrder;

        return $this;
    }

    /**
     * Get pictureOrder
     *
     * @return int
     */
    public function getPictureOrder()
    {
        return $this->pictureOrder;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gallery = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add gallery
     *
     * @param \PR\VitrineBundle\Entity\Gallery $gallery
     *
     * @return Image
     */
    public function addGallery(\PR\VitrineBundle\Entity\Gallery $gallery)
    {
        $this->gallery[] = $gallery;

        return $this;
    }

    /**
     * Remove gallery
     *
     * @param \PR\VitrineBundle\Entity\Gallery $gallery
     */
    public function removeGallery(\PR\VitrineBundle\Entity\Gallery $gallery)
    {
        $this->gallery->removeElement($gallery);
    }

    /**
     * Get gallery
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGallery()
    {
        return $this->gallery;
    }
}
