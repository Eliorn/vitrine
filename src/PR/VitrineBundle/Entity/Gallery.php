<?php

namespace PR\VitrineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gallery
 *
 * @ORM\Table(name="gallery")
 * @ORM\Entity(repositoryClass="PR\VitrineBundle\Repository\GalleryRepository")
 */
class Gallery
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
     * @var string
     *
     * @ORM\Column(name="Title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="Caption", type="string", length=255, nullable=true)
     */
    private $caption;

    /**
     * @var string
     *
     * @ORM\Column(name="Directory", type="string", length=255)
     */
    private $directory;

    /**
     * @var integer
     * 
     * @ORM\Column(name="GalleryOrder", type="integer")
     */
    private $galleryorder;

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
     * Set title
     *
     * @param string $title
     *
     * @return Gallery
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Gallery
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
     * Set caption
     *
     * @param string $caption
     *
     * @return Gallery
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set directory
     *
     * @param string $directory
     *
     * @return Gallery
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }


    /**
     * Get GalleryOrder
     *
     * @return integer
     */
    public function getGalleryOrder()
    {
        return $this->galleryorder;
    }

     /**
     * Set GalleryOrder
     *
     * @return Gallery
     */
    public function setGalleryOrder($galleryorder)
    {
        $this->galleryorder = $galleryorder;
        return $this;
    }
}

