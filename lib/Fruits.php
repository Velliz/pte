<?php

namespace pte;

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
     * @var string|bool
     */
    public $Template = null;

    /**
     * @var string|bool
     */
    protected $Master = false;

    /**
     * @var string|bool
     */
    protected $Body = false;

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

    public function SetFruitSegments($SegmentLocation)
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
        if (!$this->Master) {
            if (!$this->Body) {
                $this->Template = "";
            } else {
                foreach ($this->Segments as $Item) {
                    $this->Body = preg_replace("({{" . $Item['Name'] . "}})", $Item['Data'], $this->Body);
                }
                $this->Template = $this->Body;
            }
        } else {
            foreach ($this->Segments as $Item) {
                $this->Master = preg_replace("({{" . $Item['Name'] . "}})", $Item['Data'], $this->Master);
            }
            $this->Template = preg_replace(IFruits::CONTENT_IDENTIFIER, $this->Body, $this->Master);
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