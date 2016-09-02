<?php
/**
 * xml �D�Q�ɞ� array
 *
 * @param string xml�ı�����
 * @return array �D�Q��Ĕ��M
 */
function xml2array ($xml)
{
    $xmlary = array ();
    $ReElements = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*?)<(\/\s*\1\s*)>)/s';
    $ReAttributes = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';
    preg_match_all ($ReElements, $xml, $elements);
    foreach ($elements[1] as $ie => $xx) {
        $xmlary[$ie]["name"] = $elements[1][$ie];
        if ( $attributes = trim($elements[2][$ie])) {
            preg_match_all ($ReAttributes, $attributes, $att);
            foreach ($att[1] as $ia => $xx)
            $xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];
        }
        $cdend = strpos($elements[3][$ie],"<");
        if ($cdend > 0) {
            $xmlary[$ie]["text"] = substr($elements[3][$ie],0,$cdend -1);
        }
        if (preg_match ($ReElements, $elements[3][$ie])){
            $xmlary[$ie]["elements"] = xml2array ($elements[3][$ie]);
        }
        else if (isset($elements[3][$ie])){
            $xmlary[$ie]["text"] = $elements[3][$ie];
        }
        $xmlary[$ie]["closetag"] = $elements[4][$ie];
    }
    return $xmlary;
}
/**
 * array �D�Q�ɞ� xml
 *
 * @param array ������xml���ݵĔ��M
 * @return string xml�ı�����
 */
function array2xml ($arr)
{
    $xml = "";
    if (is_array($arr)) {
        while(list($key, $val) = each($arr)) {
            if (is_array($val)) {
                $xml .= array2xml($val);
            } else {
                if ('name' == $key)		$xml .= '<'.$val.'>';
                if ('text' == $key)		$xml .= $val;
                if ('closetag' == $key) $xml .= '<'.$val.'>';
            }
        }
    }
    return $xml;
}
?>