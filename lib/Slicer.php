<?php

namespace pte;

use pte\component\tag\Blocks;
use pte\component\tag\Functions;
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
     * @var Blocks
     */
    protected $PointerTagComponent = null;

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

        //init slice position
        $DrillDown = $Increment = $SliceBegin = $SliceEnd = $PrevSliceEnd = 0;


        //slice the template begin with regex pattern
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

            $Before = ($Capture->PregCaptureValue(ISlicer::BEFORE) === "") ? false : $Capture->PregCaptureValue(ISlicer::BEFORE);
            $Begin = ($Capture->PregCaptureValue(ISlicer::BEGIN) === "") ? false : $Capture->PregCaptureValue(ISlicer::BEGIN);
            $Flag = ($Capture->PregCaptureValue(ISlicer::FLAG) === "") ? false : $Capture->PregCaptureValue(ISlicer::FLAG);
            $Inverse = ($Capture->PregCaptureValue(ISlicer::INVERSE) === "") ? false : $Capture->PregCaptureValue(ISlicer::INVERSE);
            $Key = ($Capture->PregCaptureValue(ISlicer::KEY) === "") ? false : $Capture->PregCaptureValue(ISlicer::KEY);
            $Parameter = ($Capture->PregCaptureValue(ISlicer::PARAMETER) === "") ? false : $Capture->PregCaptureValue(ISlicer::PARAMETER);
            $End = ($Capture->PregCaptureValue(ISlicer::END) === "") ? false : $Capture->PregCaptureValue(ISlicer::END);
            $After = ($Capture->PregCaptureValue(ISlicer::AFTER) === "") ? false : $Capture->PregCaptureValue(ISlicer::AFTER);

            if ($Increment === 0) {
                $ViewComponent = new View(0, $SliceBegin, $SliceBegin, -1);
                $ViewComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $Increment, $SliceBegin));

                if ($this->PointerTagComponent !== null) {
                    $this->PointerTagComponent->AppendChild($ViewComponent);
                } else {
                    $BasketsObject->AddBasket($ViewComponent);
                }

            } else {
                $ViewComponent = new View($SliceBegin, $SliceEnd, $SliceLength, $Increment);
                $ViewComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $PrevSliceEnd, $SliceBegin - $PrevSliceEnd));

                if ($this->PointerTagComponent !== null) {
                    $this->PointerTagComponent->AppendChild($ViewComponent);
                } else {
                    $BasketsObject->AddBasket($ViewComponent);
                }
            }

//            if ($Flag === "/") {
//                $this->PointerTagComponent = null;
//            }

            if ($Before !== false && $After !== false) {
                $TagComponent = new Blocks($SliceBegin, $SliceEnd, $SliceLength, $Increment);
                $TagComponent->Before = $Before;
                $TagComponent->Begin = $Begin;
                $TagComponent->Flag = $Flag;
                $TagComponent->Inverse = $Inverse;
                $TagComponent->Key = $Key;
                $TagComponent->Parameter = $Parameter;
                $TagComponent->End = $End;
                $TagComponent->After = $After;
                $TagComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));
                if ($this->PointerTagComponent !== null) {
                    if ($Flag == '!') {
                        $this->PointerTagComponent->AppendChild($TagComponent);
                        $this->PointerTagComponent = $this->PointerTagComponent->Child[sizeof($this->PointerTagComponent->Child) - 1];
                    }
                    if ($Flag == '/') {
                        $this->PointerTagComponent = $BasketsObject->GetBasket()[sizeof($BasketsObject->GetBasket()) - 1]->Child;
                        //todo: roll to last pointer
                        $this->PointerTagComponent->AppendChild($TagComponent);
                    }
                } else {
                    $this->PointerTagComponent = $TagComponent;
                    $BasketsObject->AddBasket($this->PointerTagComponent);
                }
            }
            if ($Parameter != false) {
                $TagComponent = new Functions($SliceBegin, $SliceEnd, $SliceLength, $Increment);
                $TagComponent->Before = $Before;
                $TagComponent->Begin = $Begin;
                $TagComponent->Flag = $Flag;
                $TagComponent->Inverse = $Inverse;
                $TagComponent->Key = $Key;
                $TagComponent->Parameter = $Parameter;
                $TagComponent->End = $End;
                $TagComponent->After = $After;
                $TagComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));
                if ($this->PointerTagComponent !== null) {
                    $this->PointerTagComponent->AppendChild($TagComponent);
                } else {
                    $BasketsObject->AddBasket($TagComponent);
                }

            }

            if ($Key != false && $Before == false && $After == false && $Parameter == false) {
                $TagComponent = new Value($SliceBegin, $SliceEnd, $SliceLength, $Increment);
                $TagComponent->Before = $Before;
                $TagComponent->Begin = $Begin;
                $TagComponent->Flag = $Flag;
                $TagComponent->Inverse = $Inverse;
                $TagComponent->Key = $Key;
                $TagComponent->Parameter = $Parameter;
                $TagComponent->End = $End;
                $TagComponent->After = $After;
                $TagComponent->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceBegin, $SliceLength));
                if ($this->PointerTagComponent !== null) {
                    $this->PointerTagComponent->AppendChild($TagComponent);
                } else {
                    $BasketsObject->AddBasket($TagComponent);
                }
            }

            $PrevSliceEnd = $SliceEnd;
            $Increment++;
        }

        //post template
        $Component = new View($SliceEnd, $this->Fruit->GetLengthOfFruit(), ($this->Fruit->GetLengthOfFruit() - $SliceEnd), $Increment);
        $Component->SetComponent(substr($this->Fruit->GetFruitPack(), $SliceEnd, $this->Fruit->GetLengthOfFruit() - $SliceEnd));
        $BasketsObject->AddBasket($Component);

        //TODO: convert to Abstract Syntax Three and Recursive
    }

    public function AppendTemplates(SlicedComponent $elements)
    {
        $branch = array();

        if ($elements instanceof Blocks) {
            $children = $this->AppendTemplates($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }


        return $branch;
    }

    public function FruitToComponent()
    {

    }

}