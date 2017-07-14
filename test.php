<?php
$content = file_get_contents("template/master.html");

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

var_dump($freshValue);

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
                'opentag' => $loops[$opentag],
                'closetag' => $loops[$closetag],
            );
        }
    }

    return $linkedKey;
    //var_dump($linkedKey);
}

function parseVal($content, &$pos, &$loops)
{
    while (preg_match('((?={!)([\s\S]*?}))', $content, $result, PREG_OFFSET_CAPTURE, $pos) > 0) {
        $spec = ($result[0]);
        //todo: fix multiple same name
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
            'singletag' => $loops[$opentag],
        );
    }
    return $linkedKey;
    //var_dump($linkedKey);
}


// 1 scan tag, type tag dan lokasi mulai s.d berahkir tag pada file
// 2