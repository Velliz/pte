<?php

declare(strict_types=1);

namespace pte\fruits;

use pte\exception\PteException;

/**
 * Class Fruits
 * @package pte\fruits
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
 */
class Fruits implements IFruits
{

    /**
     * @var string
     * Overall template after combined
     */
    public string $Template = "";

    /**
     * @var string
     */
    private string $Master = "{CONTENT}";

    /**
     * @var string
     */
    private string $Body = "";

    /**
     * @var array
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
    protected array $Segments = [];

    /**
     * @var bool
     */
    protected bool $UseMaster;

    /**
     * @var bool
     */
    protected bool $UseBody;

    /**
     * @var string|null
     */
    public ?string $Name = null;

    /**
     * @var string|null
     */
    public ?string $Extension = null;

    /**
     * @var string
     */
    protected string $Location = '';

    /**
     * RawTemplate constructor.
     *
     * @param bool|null $UseMaster
     * @param bool|null $UseBody
     */
    public function __construct(?bool $UseMaster = false, ?bool $UseBody = true)
    {
        $this->UseMaster = (bool)$UseMaster;
        $this->UseBody = (bool)$UseBody;
    }

    /**
     * @param string|false $MasterLocation
     * @return bool
     */
    public function SetFruitMaster(string|false $MasterLocation)
    {
        if ($MasterLocation !== false) {
            try {
                if (!file_exists($MasterLocation)) {
                    throw new PteException(PteException::NOT_FOUND);
                }

                $Master = file_get_contents($MasterLocation);
                if ($Master === false) {
                    throw new PteException(PteException::NOT_FOUND);
                }

                $this->Master = $Master;
            } catch (PteException $pex) {
                echo sprintf("<pre>%s Expected location: %s</pre>", $pex, $MasterLocation);
                $this->UseMaster = false;
            }
        } else {
            $this->UseMaster = false;
        }

        return true;
    }

    /**
     * @param string|false $bodyLocation
     * @param bool $IsBinary
     * @return bool
     */
    public function SetFruitBody(string|false $bodyLocation, bool $IsBinary)
    {
        if ($bodyLocation !== false) {
            try {
                if (!$IsBinary) {
                    if (!file_exists($bodyLocation)) {
                        throw new PteException(PteException::NOT_FOUND);
                    }

                    $Body = file_get_contents($bodyLocation);
                    if ($Body === false) {
                        throw new PteException(PteException::NOT_FOUND);
                    }
                } else {
                    $Body = $bodyLocation;
                }

                $this->Location = $bodyLocation;

                if (!$IsBinary) {
                    $this->Name = pathinfo($bodyLocation, PATHINFO_FILENAME);
                    $this->Extension = pathinfo($bodyLocation, PATHINFO_EXTENSION);
                }

                $this->Body = $Body;
            } catch (PteException $pex) {
                echo sprintf("<pre>%s Expected location: %s</pre>", $pex, $bodyLocation);
                $this->UseBody = false;
            }
        } else {
            $this->UseBody = false;
        }

        return true;
    }

    /**
     * @param string $SegmentLocation
     * @return bool
     */
    public function AddFruitSegments(string $SegmentLocation)
    {
        try {
            if (!file_exists($SegmentLocation)) {
                throw new PteException(PteException::NOT_FOUND);
            }

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
            }

            return false;
        } catch (PteException $pex) {
            return false;
        }
    }

    /**
     * @return string
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
            // str_replace() is used instead of preg_replace(), the replacement
            // is taken literally and can therefore never be interpreted as a
            // back reference.
            $this->Template = str_replace(IFruits::CONTENT_PLACEHOLDER, $this->Body, $this->Master);
        }

        foreach ($this->Segments as $Item) {
            $this->Template = str_replace('{' . $Item['Name'] . '}', $Item['Data'], $this->Template);
        }

        return $this->Template;
    }

    /**
     * @return bool
     */
    public function isUseMaster()
    {
        return $this->UseMaster;
    }

    /**
     * @return bool
     */
    public function isUseBody()
    {
        return $this->UseBody;
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->GetFruitPack();
    }

    /**
     * @return int
     */
    public function GetLengthOfFruit()
    {
        return strlen($this->Template);
    }

    /**
     * @return string
     */
    public function GetName()
    {
        return $this->Name;
    }

    /**
     * @return string
     */
    public function GetBodyFileExtension()
    {
        return $this->Extension;
    }

    /**
     * @return string
     */
    public function GetBodyFileLocation()
    {
        return $this->Location;
    }
}
