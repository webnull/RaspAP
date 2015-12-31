<?php
namespace RaspAP\Classes;

/**
 * RaspAP
 * --
 * Extended support for INI files
 *
 * @package RaspAP\Classes
 */
class IniSupport
{
    /**
     * Write to ini file
     *
     * @param array $assoc_arr
     * @param string $path
     * @param bool $has_sections
     *
     * @url https://stackoverflow.com/questions/1268378/create-ini-file-write-values-in-php
     * @return bool|int
     */
    public static function write_ini_file($assoc_arr, $path, $has_sections = false, $quote = '"')
    {
        $content = "";
        if ($has_sections)
        {
            foreach ($assoc_arr as $key => $elem)
            {
                $content .= "[" . $key . "]\n";
                foreach ($elem as $key2 => $elem2)
                {
                    if (is_array($elem2))
                    {
                        for ($i = 0; $i < count($elem2); $i++)
                        {
                            $content .= $key2 . "[] = " . $quote . $elem2[$i] . $quote . "\n";
                        }
                    }
                    else if ($elem2 === "")
                    {
                        $content .= $key2 . "=\n";
                    }
                    else
                    {
                        $content .= $key2 . "=" . $quote . $elem2 . $quote . "\n";
                    }
                }
            }
        } else
        {
            foreach ($assoc_arr as $key => $elem)
            {
                if (is_array($elem))
                {
                    for ($i = 0; $i < count($elem); $i++)
                    {
                        $content .= $key . "[]=" . $quote . $elem[$i] . $quote ."\n";
                    }
                } else if ($elem === "") $content .= $key . "=\n";
                else $content .= $key . "=" . $quote . $elem . $quote . "\n";
            }
        }

        if (!$handle = fopen($path, 'w'))
        {
            return false;
        }

        $success = fwrite($handle, $content);
        fclose($handle);

        return $success;
    }
}