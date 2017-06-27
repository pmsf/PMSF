<?php

$localeData = null;

function i8ln($word)
{
    global $locale;
    if ($locale == "en")
        return $word;

    global $localeData;
    if ($localeData == null) {
        $filepath = 'static/dist/locales/' . $locale . '.min.json';
        if (file_exists($filepath)) {
            $json_contents = file_get_contents($filepath);
            $localeData = json_decode($json_contents, TRUE);
        } else {
            return $word;
        }
    }

    if (isset($localeData[$word])) {
        return $localeData[$word];
    } else {
        return $word;
    }
}