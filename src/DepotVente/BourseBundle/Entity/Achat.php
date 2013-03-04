<?php

namespace DepotVente\BourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="DepotVente\BourseBundle\Entity\AchatRepository")
 */
class Achat
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
     * @ORM\ManyToOne(targetEntity="Facture", inversedBy="Achat")
     * @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     */
    private $facture;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="Achat")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $article;


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
     * Constructor
     */
    public function __construct()
    {
        $this->facture = new \Doctrine\Common\Collections\ArrayCollection();
        $this->article = new \Doctrine\Common\Collections\ArrayCollection();
    }




    /**
     * Set facture
     *
     * @param \DepotVente\BourseBundle\Entity\Facture $facture
     * @return Achat
     */
    public function setFacture(\DepotVente\BourseBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;
    
        return $this;
    }

    /**
     * Get facture
     *
     * @return \DepotVente\BourseBundle\Entity\Facture 
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set article
     *
     * @param \DepotVente\BourseBundle\Entity\Article $article
     * @return Achat
     */
    public function setArticle(\DepotVente\BourseBundle\Entity\Article $article = null)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \DepotVente\BourseBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }
}