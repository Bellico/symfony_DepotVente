<?php

namespace DepotVente\BourseBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="DepotVente\BourseBundle\Entity\ArticleRepository")
 */
class Article
{

    CONST POURCENTAGE_BENEFICE = 10 ;

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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

     /**
     * @var string
     *
     * @ORM\Column(name="nro", type="string", length=25)
     */
    private $nro;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text" , nullable=true))
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var boolean
     *
     * @ORM\Column(name="validate", type="boolean")
     */
    private $validate;


    /**
     * @var date
     *
     * @ORM\Column(name="dateDepot", type="date")
     */
    private $dateDepot;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="articles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * @ORM\ManyToOne(targetEntity="Bourse", inversedBy="articles")
     * @ORM\JoinColumn(name="bourse_id", referencedColumnName="id")
     */
    private $bourse;


    public function __construct(){
        $this->nro = $this->generateKey();
        $this->validate = true;
        $this->dateDepot = new \DateTime();
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
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set description
     *
     * @param string $description
     * @return User
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
     * Set price
     *
     * @param float $price
     * @return Price
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

     /**
     * Set user
     *
     * @param User $user
     * @return User
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return user
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set validate
     *
     * @param boolean $validate
     * @return Article
     */
    public function setValidate($validate)
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Get validate
     *
     * @return boolean
     */
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * Set bourse
     *
     * @param \DepotVente\BourseBundle\Entity\Bourse $bourse
     * @return Article
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

    /**
     * Set dateDepot
     *
     * @param \DateTime $dateDepot
     * @return Article
     */
    public function setDateDepot($dateDepot)
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    /**
     * Get dateDepot
     *
     * @return \DateTime
     */
    public function getDateDepot()
    {
        return $this->dateDepot;
    }

    public function generateKey(){
        $str = "";
        $nbr = 6 ;
        $chaine = "abcdefghijklmnpqrstuvwxy0123456789";
        srand((double)microtime()*1000);
        for($i=0; $i<$nbr; $i++) {
            $str .= $chaine[rand()%strlen($chaine)];
        }
        return $str;
    }

    /**
     * Set nro
     *
     * @param string $nro
     * @return Article
     */
    public function setNro($nro)
    {
        $this->nro = $nro;

        return $this;
    }

    /**
     * Get nro
     *
     * @return string
     */
    public function getNro()
    {
        return $this->nro;
    }

    public function getTotalPrice(){
        return round($this->price * ( 1 + self::POURCENTAGE_BENEFICE / 100 ),2);
    }
}