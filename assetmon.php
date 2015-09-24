<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/constants.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/registry.functions.php';



$configfile = getcwd() . "/assetmon.json";

$configfile = sizeof($argv) > 1 ? $argv[1] : $configfile;

if (!file_exists($configfile))
    die(sprintf("Config file (\"%s\") does not exist", $configfile));
else
    $configfile = realpath($configfile);


$config = json_decode(file_get_contents($configfile), true);
$default_config = array(
    'extensions' => array( 'css', 'js' ),
    'directories' => array(),
    'destination' => './',
    'recursive' => true
);

# Check datatype config values
#
foreach (array_keys($default_config) as $key)
    if ((isset($config[$key])
                && !(is_string($default_config[$key]) ? is_string($config[$key])
                        : (is_array($default_config[$key]) ? is_array($config[$key])
                        : (is_int($default_config[$key] ? is_int($config[$key]) : false)))))
            || !isset($config[$key]))
        $config[$key] = $default_config[$key];


$config_destination = resolve_path($config['destination'], true);
if ($config_destination === false)
{
    die(sprintf("{destination=%s} directive is not a valid directory", $config['destination']));
}
$config['destination'] = $config_destination;



# Filter directive {directories}
#
foreach (array_keys($config['directories']) as $key)
{
    $dir = resolve_path($config['directories'][$key]);

    if ($dir && is_dir($dir))
    {
        $config['directories'][$key] = $dir;
        continue;
    }

    unset($config['directories'][$key]);
    continue;
}


while (true) {


    $files = array();

    # Extract all files of the specified extensions
    #
    $extensions = implode('|', $config['extensions']);
    foreach ($config['directories'] as $dir)
    {
        $files = array_merge($files, list_files($dir, sprintf('/\.(%s)$/i', $extensions)));
    }

    # Remove repeated files
    #
    $files = array_unique($files, SORT_STRING);

    # Reset file registry
    #
    reset_registry();

    # Iterate through each extension
    #
    foreach ($config['extensions'] as $ext) {

        $arrayIterator = new ArrayIterator($files);
        $regexIterator = new RegexIterator($arrayIterator, sprintf('/\.%s$/i', $ext));

        foreach ($regexIterator as $file) {

            if (!file_exists($file))
                continue;

            switch (register_file($file)) {
                case REGISTRY_NEW_FILE: {
                    echo sprintf("[New] %s\n-- %s\n\n", date('H:i:s'), realpath($file));
                }
                case REGISTRY_MODIFIED_FILE: {
                    echo sprintf("[Modified] %s\n-- %s\n\n", date('H:i:s'), realpath($file));
                }
            }
        }
    }

    $outputs = summarize_registry();

    # Output these files respectively
    #
    foreach (array_keys($outputs) as $ext)
    {
        file_put_contents($config['destination'] . '/all.' . $ext, $outputs[$ext]);
    }

    sleep(1);
}