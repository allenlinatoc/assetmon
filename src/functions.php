<?php


/**
 * List all files from a directory in an array
 *
 * @param string $directory     The source directory
 * @param string $expression    [optional] Regex for filtering result filenames
 * @param bool $recursive       [optional] Boolean value if listing will be recursive
 *
 * @return array
 */
function list_files($directory, $expression = null, $recursive = true)
{
    $files = array();

    if (!is_dir($directory))
        return $files;

    $fsentries = glob(rtrim(rtrim($directory, '/'), '\\') . '/*');

    foreach ($fsentries as $path)
    {
        if (is_dir($path) && $recursive)
            $files = array_merge($files, list_files($path, $expression, $recursive));

        # If current is file
        #
        if (is_file($path))
        {
            if ($expression != null && in_array(preg_match($expression, $path), array(false, 0)))
                continue;

            array_push($files, realpath($path));
        }
    }

    sort($files, SORT_STRING);
    return $files;
}


/**
 * Resolve the real path of a path (including symbolic paths)
 *
 * @param string $path
 * @param boolean $autocreate [optional] If non-existent directories should be created. Default is <b>FALSE</b>.
 *
 * @return boolean
 *
 * @throws \Exception   Exception thrown when {autocreate} is true and attempt to create directory failed
 */
function resolve_path($path, $autocreate = false)
{
    # Backup current directory
    #
    $current_directory = getcwd();

    # Split path into array by separators
    #
    $parts = explode('/', $path);

    for ($x=0; $x < sizeof($parts); $x++)
    {
        $part = $parts[$x];

        if (strlen(trim($part)) == 0)
            continue;

        # On CURRENT DIRECTORY
        if ($x == 0 && $part == '.')
        {
            chdir(getcwd());
            continue;
        }

        # On UP ONE LEVEL
        if ($part == '..')
        {
            chdir(realpath(sprintf('%s/..', getcwd())));
            continue;
        }

        # On specific directory
        $newdir = rtrim(getcwd(), '/') . '/' . $part;
        if (!file_exists($part) && $autocreate)
        {
            if (mkdir(substr($newdir, strrpos($newdir, '/') + 1)) === false)
            {
                throw new \Exception("Failed to create directory: " . $newdir);
            }
        }

        if (is_dir($newdir))
            chdir($newdir);
        else
            return false;
    }

    # Store result
    #
    $result = getcwd();

    # Restore backup path
    #
    chdir($current_directory);

    return $result;
}

?>