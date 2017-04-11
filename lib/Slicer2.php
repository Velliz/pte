<?php

namespace pte;

use pte\component\tag\Blocks;

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
class Slicer2 implements ISlicer
{

    const BLOCK = 1;
    const VARS = 2;
    const FINALIZE = 3;

    private $_Globals = array();

    public $_EnableCache = false;

    private $_CustomFunctions = array();

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

    private function FruitParser($source, $pattern, &$parsedStructure, &$startPos)
    {
        $tResult = array('MK' => '', 'LastPos' => 0, 'EndPos' => 0);
        while (preg_match($pattern, $source, $matched, PREG_OFFSET_CAPTURE, $startPos) > 0) {
            $currentKey = trim($matched['Key'][0]);
            $params = array();

            //init param {!ref(test/test)}
            if (isset($matched['Parameter'])) {
                $xParamString = trim($matched['Parameter'][0]);
                if ($xParamString != '') {
                    $temp = explode(',', $xParamString);
                    foreach ($temp as $value) {
                        if ($value[0] == "'")
                            $params[] = array('key' => null, 'value' => substr($value, 1, strlen($value) - 2));
                        else
                            $params[] = array('key' => $value, 'value' => null);
                    }
                }
            }

            //init inverse {!!Test}
            $isInverted = false;
            if ($matched['Flag'][0] == '/') {
                if (($matched['BO'][0] == '<!--') && ($matched['BE'][0] == '-->')) {
                    $tResult = array('MK' => $currentKey, 'LastPos' => $startPos, 'EndPos' => $matched['BO'][1]);
                    $startPos = ($matched['BO'][1] + strlen('<!--{!' . $matched['Key'][0] . '}-->'));
                    break;
                }
            } else {
                if (isset($matched['Inverse']))
                    $isInverted = ($matched['Inverse'][0] == '!');
            }

            //init loop tag
            if (($matched['BO'][0] == '<!--') && ($matched['BE'][0] == '-->')) {
                $token = array(
                    't_start' => substr($source, $startPos, ($matched['BO'][1] - $startPos)),
                    'startpos' => $matched['BO'][1],
                    'type' => Slicer2::BLOCK,
                    'id' => $matched['Key'][0],
                    'key' => $currentKey,
                    'params' => $params,
                    'inverted' => $isInverted,
                    'content' => '',
                    't_end' => ''
                );
                $startPos = ($matched['BO'][1] + strlen($matched[0][0]));

                $childs = array();
                $mRes = $this->FruitParser($source, $pattern, $childs, $startPos);
                if ($mRes['MK'] == $token['key']) {
                    $token['t_end'] = substr($source, $mRes['LastPos'], ($mRes['EndPos'] - $mRes['LastPos']));
                    $token['childs'] = $childs;
                    $parsedStructure[] = $token;
                }
            } else { //init else of loop tag
                $token = array(
                    't_start' => substr($source, $startPos, ($matched['O'][1] - $startPos)),
                    'startpos' => $matched['O'][1],
                    'type' => Slicer2::VARS,
                    'id' => $matched['Key'][0],
                    'key' => $currentKey,
                    'params' => $params,
                    'inverted' => $isInverted,
                    'content' => '',
                    't_end' => '',
                    'childs' => array()
                );
                $startPos = ($matched['O'][1] + strlen($matched[0][0]));
                $parsedStructure[] = $token;
            }
        }

        return $tResult;
    }

    private function FinalizeFruitParser($source, $pattern, &$parsedStructure, &$startPos)
    {
        $token = array(
            't_start' => substr($source, $startPos),
            'startpos' => $startPos,
            'type' => Slicer2::FINALIZE,
            'id' => '',
            'key' => '',
            'params' => array(),
            'inverted' => false,
            'content' => '',
            't_end' => '',
            'childs' => array()
        );
        $parsedStructure[] = $token;
    }

    public function FruitLexer($text)
    {
        $startPos = 0;
        $pTokens = array(
            't_start' => '',
            'startpos' => $startPos,
            'type' => Slicer2::BLOCK,
            'id' => '',
            'key' => '',
            'params' => array(),
            'inverted' => false,
            'content' => '',
            't_end' => '',
            'childs' => array()
        );

        $this->FruitParser($text, ISlicer::PATTERN, $pTokens['childs'], $startPos);
        $this->FinalizeFruitParser($text, ISlicer::PATTERN, $pTokens['childs'], $startPos);
        return $pTokens;
    }

    public function Parse($text, $variables)
    {
        //TODO: cache ke redis/apc
        $tokens = $this->FruitLexer($text);

        return $this->Compile($tokens, $variables);

    }

    private function _VariableToParameters($params, $vars)
    {
        $result = array();
        foreach ($params as $item) {
            if ($item['value'] != null)
                $result[] = $item['value'];
            else if ($item['key'] != null) {
                if (isset($vars[$item['key']]))
                    $result[] = $vars[$item['key']];
                else if (isset($this->_Globals[$item['key']]))
                    $result[] = $this->_Globals[$item['key']];
            }
        }
        return $result;
    }

    public function Compile($tokens, $vars)
    {
        $result = '';
        foreach ($tokens['childs'] as $child) {
            $result .= $child['t_start'];

            if ($child['type'] == Slicer2::BLOCK) {
                $tempResult = '';
                $CRO = $this->GetObjectOfRenderFunctions($child['key']);
                if ($CRO !== null) {
                    $_params = $this->_VariableToParameters($child['params'], $vars);
                    $ptr = $CRO->RenderBlock($child['key'], $_params, $child, $child['inverted'], $vars);
                    if (is_bool($ptr)) {
                        $doRenderBlock = (bool)$ptr;
                        if ($doRenderBlock) {
                            $tempResult .= $this->Compile($child, $vars);
                            $tempResult .= $child['t_end'];
                        }
                    } else if (is_string($ptr)) {
                        $tempResult .= $ptr;
                    } else if (is_array($ptr)) {
                        if (count($ptr) > 0) {
                            if ($this->_IsAssociativeArray($ptr)) {
                                $tempResult .= $this->Compile($child, $ptr);
                                $tempResult .= $child['t_end'];
                            } else {
                                foreach ($ptr as $ptrRow) {
                                    $tempResult .= $this->Compile($child, $ptrRow);
                                    $tempResult .= $child['t_end'];
                                }
                            }
                        }
                    }
                } else {
                    $vObject = null;
                    if (isset($vars[$child['key']]))
                        $vObject = $vars[$child['key']];
                    else if (isset($this->_Globals[$child['key']]))
                        $vObject = $this->_Globals[$child['key']];

                    if ($vObject !== null) {
                        if (is_object($vObject)) {
                            if ($vObject instanceof ISlicedComponent) { //ObjectRenderer
                                $ptr = $vObject->ChildRender($this);
                                if (is_bool($ptr)) {
                                    $doRenderBlock = (bool)$ptr;
                                    if ($doRenderBlock) {
                                        $tempResult .= $this->Compile($child, $vars);
                                        $tempResult .= $child['t_end'];
                                    }
                                } else if (is_string($ptr)) {
                                    $tempResult .= $ptr;
                                } else if (is_array($ptr)) {
                                    if (count($ptr) > 0) {
                                        if ($this->_IsAssociativeArray($ptr)) {
                                            $tempResult .= $this->Compile($child, $ptr);
                                            $tempResult .= $child['t_end'];
                                        } else {
                                            foreach ($ptr as $ptrRow) {
                                                $tempResult .= $this->Compile($child, $ptrRow);
                                                $tempResult .= $child['t_end'];
                                            }
                                        }
                                    }
                                }
                            }
                        } else if (is_bool($vObject)) {
                            if ($child['inverted']) {
                                $vObject = !$vObject;
                            }
                            if ($vObject) {
                                $tempResult .= $this->Compile($child, $vars);
                                $tempResult .= $child['t_end'];
                            }
                        } else if (is_array($vObject)) {
                            if (count($vObject) > 0) {
                                if ($this->_IsAssociativeArray($vObject)) {
                                    $tempResult .= $this->Compile($child, $vObject);
                                    $tempResult .= $child['t_end'];
                                } else {
                                    foreach ($vObject as $vRow) {
                                        $tempResult .= $this->Compile($child, $vRow);
                                        $tempResult .= $child['t_end'];
                                    }
                                }
                            }
                        }
                    }
                }
                $result .= $tempResult;
            } else if ($child['type'] == Slicer2::VARS) {
                if ($vars !== null) {
                    $CRO = $this->GetObjectOfRenderFunctions($child['key']);
                    if ($CRO != null) {
                        $_params = $this->_VariableToParameters($child['params'], $vars);
                        $result .= $CRO->RenderInline($child['key'], $_params, $child['inverted'], $vars);
                    } else {
                        $eObject = null;
                        if (is_object($vars))
                            $eObject = $vars->{$child['key']};
                        else if (isset($vars[$child['key']]))
                            $eObject = $vars[$child['key']];
                        else if (isset($this->_Globals[$child['key']]))
                            $eObject = $this->_Globals[$child['key']];
                        if (is_object($eObject)) {
                            if ($eObject instanceof ISlicedComponent) { //ObjectRenderer
                                $renderResult = $eObject->ChildRender($this);
                                if (is_string($renderResult)) {
                                    $result .= $renderResult;
                                }
                            }
                        } else {
                            $result .= (string)$eObject;
                        }
                    }
                }
            } else if ($child['type'] == Slicer2::FINALIZE) {

            }
        }

        return $result;
    }

    private function _IsAssociativeArray($array)
    {
        return (array_keys($array) !== range(0, count($array) - 1));
    }

    public function GetObjectOfRenderFunctions($key)
    {
        if (isset($this->_CustomFunctions[$key]))
            return $this->_CustomFunctions[$key];
        else
            return null;
    }

}