<?php 

error_reporting(E_ALL);

if (!isset($_SERVER['argv']) && !isset($argv)) {
    echo "Please enable the register_argc_argv directive in your php.ini\n";
    exit(1);
} elseif (!isset($argv)) {
    $argv = $_SERVER['argv'];
}

if (!isset($argv[1]) || !in_array($argv[1], ['nano', 'tetra'])) {
    echo "Run with \"php opkg-parser.php [TYPE]\"\n";
    echo "    TYPE -> 'nano' or 'tetra'\n";
    exit(1);
}




$extraPackages = [
    'OnlineHashCrack',
    'PMKIDAttack',
];

echo "\nsync mk6 packages - by DSR!\n\n";

$device = $argv[1];
$srcDir = str_replace('build', 'src', getcwd());
$moduleData = json_decode(file_get_contents("https://www.wifipineapple.com/{$device}/modules"), true);

/*
echo "======== Packages (" . count($moduleData) . ") ========\n";
foreach ($moduleData as $key => $value) {
    if ($value["type"] !== 'Sys') {
        echo "    [+] {$key}\n";
        $file = file_get_contents("https://www.wifipineapple.com/{$device}/modules/{$key}");
        @unlink("{$key}.tar.gz");
        file_put_contents("{$key}.tar.gz", $file);
    }
}
*/
echo "\n\n";
echo "======== Extra Packages (" . count($moduleData) . ") ========\n";
foreach ($extraPackages as $key) {
    //tar czf OnlineHashCrack.tar.gz OnlineHashCrack
    //tar czf PMKIDAttack.tar.gz PMKIDAttack
    $fileName = getcwd() . "/{$key}.tar.gz";
    $infoData = json_decode(file_get_contents("{$srcDir}/{$key}/module.info"));
    
    $module = [
        'name' => $key,
        'title' => $infoData->title,
        'version' => $infoData->version,
        'description' => $infoData->description,
        'author' => $infoData->author,
        'size' => filesize($fileName),
        'checksum' => hash_file('sha256', $fileName),
        'num_downloads' => '0',
    ];
    if (isset($infoData->system)) {
        $module['type'] = "System";
    } elseif (isset($infoData->cliOnly)) {
        $module['type'] = "CLI";
    } else {
        $module['type'] = "GUI";
    }
    var_dump($module);

    $moduleData[ $key ] = $module;
}

@unlink("{$device}.json");
file_put_contents("{$device}.json", json_encode($moduleData));

echo "\n\n";
echo "Complete!";