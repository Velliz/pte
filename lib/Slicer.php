<?php
namespace pte;

use pte\component\tag\Blocks;
use pte\component\tag\Value;
use pte\component\Tag;
use pte\component\View;
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

        $Increment = $SliceBegin = $SliceEnd = $PrevSliceEnd = 0;

        $Blocks = null;

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

            //pre template
            if ($Increment === 0) {
                $ViewComponent = new View($Increment, $SliceBegin, $SliceBegin, $Increment);
                $ViewComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $Increment, $SliceBegin));
                $BasketsObject->AddBasket($ViewComponent);
            } else {
                $ViewComponent = new View($PrevSliceEnd, $SliceBegin, $SliceLength, $Increment);
                $ViewComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $PrevSliceEnd, $SliceBegin - $PrevSliceEnd));
                $BasketsObject->AddBasket($ViewComponent);
            }

            //template
            $Before = $Capture->PregCaptureValue(ISlicer::BEFORE);
            $Begin = $Capture->PregCaptureValue(ISlicer::BEGIN);
            $Flag = $Capture->PregCaptureValue(ISlicer::FLAG);
            $Inverse = $Capture->PregCaptureValue(ISlicer::INVERSE);
            $Key = $Capture->PregCaptureValue(ISlicer::KEY);
            $Parameter = $Capture->PregCaptureValue(ISlicer::PARAMETER);
            $End = $Capture->PregCaptureValue(ISlicer::END);
            $After = $Capture->PregCaptureValue(ISlicer::AFTER);

            if (strlen($Before) > 0 && strlen($After) > 0) {
                $TagComponent = new Blocks($SliceBegin, $SliceEnd, $SliceLength, $Increment);
                $TagComponent->Before = $Before;
                $TagComponent->Begin = $Begin;
                $TagComponent->Flag = $Flag;
                $TagComponent->Inverse = $Inverse;
                $TagComponent->Key = $Key;
                $TagComponent->Parameter = $Parameter;
                $TagComponent->End = $End;
                $TagComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));
                $BasketsObject->AddBasket($TagComponent);
            }

            if (strlen($Before) === 0 && strlen($After) === 0) {
                $TagComponent = new Value($SliceBegin, $SliceEnd, $SliceLength, $Increment);
                $TagComponent->Before = $Before;
                $TagComponent->Begin = $Begin;
                $TagComponent->Flag = $Flag;
                $TagComponent->Inverse = $Inverse;
                $TagComponent->Key = $Key;
                $TagComponent->Parameter = $Parameter;
                $TagComponent->End = $End;
                $TagComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));
                $BasketsObject->AddBasket($TagComponent);
            }

            $PrevSliceEnd = $SliceEnd;
            $Increment++;
        }

        //post template
        $Component = new View($SliceEnd, $this->Fruit->GetLengthOfFruit(), ($this->Fruit->GetLengthOfFruit() - $SliceEnd), $Increment);
        $Component->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceEnd, $this->Fruit->GetLengthOfFruit() - $SliceEnd));
        $BasketsObject->AddBasket($Component);

        $basket = $BasketsObject->GetBasket();
        foreach ($basket as $val)
        {
            echo $val->GetComponent();
        }

    }

    const BLOCK_OPEN = 0;
    const BLOCK_CLOSED = 1;
    const VALUE = 3;

    public function BlockResolver($Data, $BlogType)
    {
        switch ($BlogType) {
            case Slicer::BLOCK_OPEN:
                $this->BlockResolver($Data, $BlogType);
                break;
            case Slicer::BLOCK_CLOSED:

                break;
            case Slicer::VALUE:

                break;
        }
    }
}