<?php
namespace pte;

use pte\component\ViewSegment;
use pte\utility\PregOffsetCapture;

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

        $Increment = 0;
        $SliceEnd = 0;
        $PrevSliceEnd = 0;

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
                $View = (substr($this->Fruit->GetFruitPack(), 0, $SliceBegin));
                $Component = new ViewSegment();
                $Component->SetComponent($View);

                $BasketsObject->AddBasket($Component);
            } else {
                $View = (substr($this->Fruit->GetFruitPack(), $PrevSliceEnd, ($SliceBegin - $PrevSliceEnd)));
                $Component = new ViewSegment();
                $Component->SetComponent($View);

                $BasketsObject->AddBasket($Component);
            }

            //todo: classify the tag based on structure
            $TagSegments = (substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));
            $Component = new ViewSegment();
            $Component->SetComponent($TagSegments);

            $BasketsObject->AddBasket($Component);

            $PrevSliceEnd = $SliceEnd;
            $Increment++;
        }

        $View = (substr($this->Fruit->GetFruitPack(), $SliceEnd, $this->Fruit->GetLengthOfFruit() - $SliceEnd));
        $Component = new ViewSegment();
        $Component->SetComponent($View);

        $BasketsObject->AddBasket($Component);
    }
}