<?php
namespace pte;

use pte\Component\Constants;
use pte\Utility\PregOffsetCapture;

/**
 * Class Slicer
 * Slicer engine for peel and cut down the fruit.
 *
 * @author Didit Velliz
 * @mail diditvelliz@gmail.com
 * @lisence MIT
 * @package pte
 * @copyright 2017
 */
class Slicer implements ISlicer
{

    /**
     * @var Fruits
     */
    protected $Fruit;

    /**
     * @var SlicedComponent
     */
    protected $SlicedComponent;

    /**
     * Slicer constructor.
     * @param Fruits $Fruit
     */
    public function __construct(Fruits $Fruit)
    {
        $this->Fruit = $Fruit;
    }

    public function Slices(Baskets $BasketsObject)
    {
        $SliceBegin = 0;
        $SliceEnd = 0;
        $PrevSliceEnd = 0;
        $Increment = 0;
        $StartPosition = 0;

        while (preg_match(ISlicer::PATTERN, $this->Fruit->GetFruitPack(), $Result, PREG_OFFSET_CAPTURE, $SliceEnd) > 0) {

            $Capture = new PregOffsetCapture($Result);

            if ($Capture->PregCaptureValue(ISlicer::BEFORE) !== '') {
                $SliceBegin = $Capture->PregCaptureStartPosition(ISlicer::BEFORE);
            } else {
                $SliceBegin = $Capture->PregCaptureStartPosition(ISlicer::BEGIN);
            }

            if ($Capture->PregCaptureValue(ISlicer::AFTER) !== false) {
                $SliceEnd = $Capture->PregCaptureFinishPosition(ISlicer::AFTER);
            } else {
                $SliceEnd = $Capture->PregCaptureFinishPosition(ISlicer::END);
            }

            $SliceLength = ($SliceEnd - $SliceBegin);

            if ($Increment === 0) {

                $FruitSegments = (substr($this->Fruit->GetFruitPack(), $StartPosition, ($SliceBegin - $StartPosition)));

                $Component = new Constants();
                $Component->SetValue($FruitSegments);
                $Component->Length = ($SliceBegin - $StartPosition);
                $Component->EndPosition = ($StartPosition + $Component->Length);

                $BasketsObject->AddBasket($Component);

            } else {
                //echo (substr($this->Fruit->GetFruitPack(), $PrevSliceEnd, ($SliceBegin - $PrevSliceEnd)));
            }

            //the tag itself
            //echo (substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));


            $PrevSliceEnd = $SliceEnd;
            $Increment++;
        }

        //echo (substr($this->Fruit->GetFruitPack(), $SliceEnd, $this->Fruit->GetLengthOfFruit() - $SliceEnd));
    }
}