<?php

namespace SuiteCRM\Database;

class Utils {
    //put your code here

    /**
     * Replaces specific characters with their HTML entity values
     * @param string $string String to check/replace
     * @param bool $encode Default true
     * @return string
     *
     */
    public static function to_html($string, $encode = true) {
        global $toHTML;

        if (empty($string)) {
            return $string;
        }

        if ($encode && is_string($string)) {
            /*
             * cn: bug 13376 - handle ampersands separately
             * credit: ashimamura via bug portal
             */
            //$string = str_replace("&", "&amp;", $string);
            if (is_array($toHTML)) { // cn: causing errors in i18n test suite ($toHTML is non-array)
                $string = str_ireplace($GLOBALS['toHTML_keys'], $GLOBALS['toHTML_values'], $string);
            }
        }
        return $string;
    }

    public static function arrayToParams($array) {
        
    }

    public static function arrayToSqlPart(array $array) {
        $queryPart = '';
        $length = \count($array);
        $index = 0;
        foreach ($array as $value) {
            if ($index < $length - 1) {
                $queryPart .= ':' . $value . ',';
            } else {
                $queryPart .= ':' . $value;
            }
            $index++;
        }
        return $queryPart;
    }

    public static function startsWith($haystack, $needle) {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }

    public static function endsWith($haystack, $needle) {
        return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
    }

}
