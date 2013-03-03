<?php

namespace DepotVente\BourseBundle\Entity;

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
     * @var string
     *
     * @ORM\Column(name="bourse", type="string", length=255)
     */
    private $bourse;


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
     * Set bourse
     *
     * @param string $bourse
     * @return Facture
     */
    public function setBourse($bourse)
    {
        $this->bourse = $bourse;
    
        return $this;
    }

    /**
     * Get bourse
     *
     * @return string 
     */
    public function getBourse()
    {
        return $this->bourse;
    }
}
