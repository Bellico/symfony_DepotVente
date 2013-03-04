<?php

namespace DepotVente\BourseBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Facture
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="DepotVente\BourseBundle\Entity\FactureRepository")
 */
class Facture
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
     * @ORM\ManyToOne(targetEntity="Bourse")
     * @ORM\JoinColumn(name="bourse_id", referencedColumnName="id")
     */
    private $bourse;


    /**
     * @var date
     *
     * @ORM\Column(name="dateFacture", type="date")
     */
    private $dateFacture;


    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;


    public function __construct(){
        $this->dateFacture = new \DateTime();
        $this->bourse = new ArrayCollection();
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


    /**
     * Set dateFacture
     *
     * @param \DateTime $dateFacture
     * @return Facture
     */
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get dateFacture
     *
     * @return \DateTime
     */
    public function getDateFacture()
    {
        return $this->dateFacture;
    }


    /**
     * Set total
     *
     * @param float $total
     * @return Facture
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }



    /**
     * Set bourse
     *
     * @param \DepotVente\BourseBundle\Entity\Bourse $bourse
     * @return Facture
     */
    public function setBourse(\DepotVente\BourseBundle\Entity\Bourse $bourse = null)
    {
        $this->bourse = $bourse;
    
        return $this;
    }

    /**
     * Get bourse
     *
     * @return \DepotVente\BourseBundle\Entity\Bourse 
     */
    public function getBourse()
    {
        return $this->bourse;
    }
}