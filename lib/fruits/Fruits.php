<?php

namespace pte\fruits;

use pte\exception\PteException;

/**
 * Class Fruits
 * Fruit is class for convert the template into fruit representations.
 *
 * @author Didit Velliz
 * @mail diditvelliz@gmail.com
 * @lisence MIT
 * @package pte
 * @copyright 2017
 */
class Fruits implements IFruits
{

    /**
     * @var string
     */
    public $Template = "";

    /**
     * @var string
     */
    private $Master = "";

    /**
     * @var string
     */
    private $Body = "";

    /**
     * @var array|bool
     *
     * Use segments for mix more than a fruit.
     *
     * Example:
     * array(
     *  'Location' => 'template/master.html',
     *  'Name' => 'master.html',
     *  'Data' => '<div>Hello World</div>'
     * )
     */
    protected $Segments = array();

    /**
     * @var bool
     */
    protected $UseMaster = true;

    /**
     * @var bool
     */
    protected $UseBody = true;

    public $Name;

    public $Extension;

    protected $Location;

    /**
     * RawTemplate constructor.
     */
    public function __construct()
    {

    }

    public function SetFruitMaster($MasterLocation)
    {
        $Master = file_get_contents($MasterLocation);
        if ($Master === false) {
            throw new PteException(PteException::NOT_FOUND);
        }
        $this->Master = $Master;
        return true;
    }


    public function SetFruitBody($bodyLocation)
    {
        $Body = file_get_contents($bodyLocation);
        if ($Body === false) {
            throw new PteException(PteException::NOT_FOUND);
        }
        $this->Location = $bodyLocation;
        $extension = explode('.', $bodyLocation);
        $this->Extension = $extension[sizeof($extension) - 1];
        $this->Body = $Body;
        return true;
    }

    public function AddFruitSegments($SegmentLocation)
    {
        $Segment = file_get_contents($SegmentLocation);
        if ($Segment === false) {
            throw new PteException(PteException::NOT_FOUND);
        }
        $Fragment = explode('/', $SegmentLocation);
        $SegmentData = array(
            'Location' => $SegmentLocation,
            'Name' => $Fragment[count($Fragment) - 1],
            'Data' => $Segment,
        );
        if (array_push($this->Segments, $SegmentData) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    public function GetFruitPack()
    {
        if (!$this->UseBody) {
            $this->Template = "";
            return $this->Template;
        }
        if (!$this->UseMaster) {
            $this->Template = $this->Body;
        } else {
            $this->Template = preg_replace(IFruits::CONTENT_IDENTIFIER, $this->Body, $this->Master);
        }
        foreach ($this->Segments as $Item) {
            $keywords = sprintf("({{%s}})", $Item['Name']);
            $this->Template = preg_replace($keywords, $Item['Data'], $this->Template);
        }
        return $this->Template;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->GetFruitPack();
    }

    /**
     * @return int
     */
    public function GetLengthOfFruit()
    {
        return strlen($this->Template);
    }

    public function GetFruitExtension()
    {
        return $this->Extension;
    }

    public function GetName()
    {
        return $this->Name;
    }

    public function GetFileLocation()
    {
        return $this->Location;
    }
}