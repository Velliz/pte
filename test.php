<?php
$content = file_get_contents("template/master.html");
$contentLength = strlen($content);
$pos = 0;

$val = array();
$loops = array();

$freshValue = parseVal($content, $pos, $val);
$pos = 0;
$freshLoop = parseLoop($content, $pos, $loops);

foreach ($freshValue as $k => $v) {
    if (isset($freshLoop[$k])) {
        $freshValue[$k] = $freshLoop[$k];
    }
}

//var_dump($freshValue);

function parseLoop($content, &$pos, &$loops)
{
    while (preg_match('((?=<!--)([\s\S]*?)-->)', $content, $result, PREG_OFFSET_CAPTURE, $pos) > 0) {
        $spec = ($result[0]);
        $loops[$spec[0]] = array(
            'start_pos' => $spec[1],
            'end_pos' => $spec[1] + strlen($spec[0]),
            'tag_length' => strlen($spec[0]),
        );
        $pos = $spec[1] + strlen($spec[0]);
    }

    $keys = array();

    foreach ($loops as $key => $val) {
        $keys[] = $key;
    }

    $linkedKey = array();

    foreach ($keys as $k => $v) {
        if (!strpos($v, "/")) {
            $opentag = $v;
            $closetag = str_replace("<!--{!", "<!--{/", $opentag);
            $arraykey = str_replace("<!--{!", "", $opentag);
            $arraykey = str_replace("}-->", "", $arraykey);
            $linkedKey[$arraykey] = array(
                'type' => 'loop',
                'begin' => $loops[$opentag]['start_pos'],
                'end' => $loops[$closetag]['end_pos'],
                'length' => $loops[$closetag]['end_pos'] - $loops[$opentag]['start_pos'],
                'opentag' => $loops[$opentag],
                'closetag' => $loops[$closetag],
            );
        }
    }

    return $linkedKey;
}

function parseVal($content, &$pos, &$loops)
{
    while (preg_match('((?={!)([\s\S]*?}))', $content, $result, PREG_OFFSET_CAPTURE, $pos) > 0) {
        $spec = ($result[0]);
        $loops[$spec[0]] = array(
            'start_pos' => $spec[1],
            'end_pos' => $spec[1] + strlen($spec[0]),
            'tag_length' => strlen($spec[0]),
        );

        $pos = $spec[1] + strlen($spec[0]);
    }

    $keys = array();

    foreach ($loops as $key => $val) {
        $keys[] = $key;
    }

    $linkedKey = array();

    foreach ($keys as $k => $v) {
        $opentag = $v;
        $arraykey = str_replace("{!", "", $opentag);
        $arraykey = str_replace("}", "", $arraykey);
        $linkedKey[$arraykey] = array(
            'type' => 'value',
            'begin' => $loops[$opentag]['start_pos'],
            'end' => $loops[$opentag]['end_pos'],
            'length' => $loops[$opentag]['end_pos'] - $loops[$opentag]['start_pos'],
            'opentag' => $loops[$opentag],
            'closetag' => $loops[$opentag],
        );
    }
    return $linkedKey;
}

//AMBIL HTML PERCOBAAN 2 [WORKS FOR LOOPS]
$trainCounter = 0;
foreach ($freshValue as $key => $val) {
    if ($val['type'] == 'loop') {
        var_dump(substr($content, $val['opentag']['start_pos'], $val['length']));
        var_dump("--------------------------");
    } else {
        var_dump("FALSE"); // because it value
    }
}

die();

//AMBIL HTML PERCOBAAN 1
$train = array();
$trainCounter = 0;

foreach ($freshValue as $key => $val) {
    /*
    $train[] = array(
        'tags' => $key,
        'element' => substr($content, $trainCounter, $val['begin']),
        'specs' => $val,
        'train' => $trainCounter,
        'trainLength' => $val['begin'],
    );
    */

    if ($val['type'] == 'loop') {

        $train[] = array(
            'tags' => $key,
            'element' => substr($content, $trainCounter, ($val['begin'] - $trainCounter)),
            'specs' => $val,
            'train' => $trainCounter,
            'trainLength' => $val['begin'],
        );

        $trainCounter = $val['opentag']['end_pos'];
    }
    if ($val['type'] == 'value') {

        if ($trainCounter == 0) {
            $train[] = array(
                'tags' => $key,
                'element' => substr($content, $trainCounter, $val['begin']),
                'specs' => $val,
                'train' => $trainCounter,
                'trainLength' => $val['begin'],
            );
            $trainCounter = $val['end'];
        } else {
            $train[] = array(
                'tags' => $key,
                'element' => substr($content, $trainCounter, $val['begin'] - $trainCounter),
                'specs' => $val,
                'train' => $trainCounter,
                'trainLength' => $val['begin'] - $trainCounter,
            );
            $trainCounter = $val['begin'] - $trainCounter;
        }
    }
}

foreach ($train as $key => $val) {
    var_dump($val);
}




// 1 scan tag, type tag dan lokasi mulai s.d berahkir tag pada file
// 2