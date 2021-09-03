<?php
if (!php_sapi_name() == "cli") {
    echo "Script can only be run through CLI";
}
$thisFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR;
/* Create master json file */
file_put_contents('index.json', json_encode(dirtree($thisFolder)));
/* Create subfolder json files */
foreach (dirtree($thisFolder) as $k => $dir) {
    if (is_dir($thisFolder . $k)) {
        $jsonFile = $thisFolder . $k . DIRECTORY_SEPARATOR . 'index.json';
        $directory = dirtree($thisFolder . $k);
        file_put_contents($jsonFile, json_encode($directory));
    }
    if ($k === 'reward' || $k === 'raid') {
        foreach ($dir as $ks => $subdir) {
            if (is_dir($thisFolder . $k . DIRECTORY_SEPARATOR . $ks)) {
                $jsonFile = $thisFolder . $k . DIRECTORY_SEPARATOR . $ks . DIRECTORY_SEPARATOR . 'index.json';
                $directory = dirtree($thisFolder . $k . DIRECTORY_SEPARATOR . $ks);
                file_put_contents($jsonFile, json_encode($directory));
            }
        }
    }
    if ($k === 'gym') {
        foreach ($dir as $ks => $subdir) {
            if (is_dir($thisFolder . $k . DIRECTORY_SEPARATOR . $ks)) {
                $jsonFile = $thisFolder . $k . DIRECTORY_SEPARATOR . $ks . DIRECTORY_SEPARATOR . 'index.json';
                $directory = dirtree($thisFolder . $k . DIRECTORY_SEPARATOR . $ks);
                file_put_contents($jsonFile, json_encode($directory));
            }
            foreach ($subdir as $sd => $ssd) {
                if (is_dir($thisFolder . $k . DIRECTORY_SEPARATOR . $ks . DIRECTORY_SEPARATOR . $sd)) {
                    $jsonFile = $thisFolder . $k . DIRECTORY_SEPARATOR . $ks . DIRECTORY_SEPARATOR . $sd . DIRECTORY_SEPARATOR . 'index.json';
                    $directory = dirtree($thisFolder . $k . DIRECTORY_SEPARATOR . $ks . DIRECTORY_SEPARATOR . $sd);
                    file_put_contents($jsonFile, json_encode($directory));
                }
            }
        }
    }
}

function dirtree($dir, $ignoreEmpty=false) {
    if (!$dir instanceof DirectoryIterator) {
        $dir = new DirectoryIterator((string)$dir);
    }
    $dirs  = array();
    $files = array();
    foreach ($dir as $node) {
        if ($node->isDir() && !$node->isDot()) {
            $tree = dirtree($node->getPathname(), $ignoreEmpty);
            if (!$ignoreEmpty || count($tree)) {
                $dirs[$node->getFilename()] = $tree;
            }
        } elseif ($node->isFile()) {
            $name = $node->getFilename();
            if (!str_ends_with($name, '.json') && !str_ends_with($name, '.php')) {
                $files[] = $name;
            }
        }
    }
    asort($dirs);
    sort($files);

    return array_merge($dirs, $files);
}
