<?php


/**
 * Reset the file registry
 */
function reset_registry()
{
    if (!session_status() != PHP_SESSION_DISABLED)
        session_start();

    if (!isset($_SESSION['files_registry']))
        $_SESSION['files_registry'] = array();

    foreach (array_keys($_SESSION['files_registry']) as $key)
    {
        # Check if this is an array
        #
        if (!is_array($_SESSION['files_registry']))
        {
            # Otherwise, delete it
            #
            unset($_SESSION['files_registry'][$key]);
            continue;
        }

        if (!isset($_SESSION['files_registry'][$key]))
        {
            $_SESSION['files_registry'][$key]['good'] = false;
            continue;
        }
        $_SESSION['files_registry'][$key]['good'] = false;
    }
}


/**
 * Register a file to the registry
 *
 * @param $filename Name of the file to be added
 *
 * @return int
 */
function register_file($filename)
{
    if (!isset($_SESSION['files_registry'][$filename]))
    {
        $_SESSION['files_registry'][$filename] = array(
            'signature' => file_exists($filename) ? sha1_file($filename) : null,
            'good' => true
        );
        return REGISTRY_NEW_FILE;
    }

    # Otherwise, compare the signature with the existing
    #
    if (file_exists($filename) && strcasecmp($_SESSION['files_registry'][$filename]['signature'], sha1_file($filename)) != 0)
    {
        $_SESSION['files_registry'][$filename] = array(
            'signature' => sha1_file($filename),
            'good' => true
        );
        return REGISTRY_MODIFIED_FILE;
    }
    else {
        # Mark as a good file
        #
        $_SESSION['files_registry'][$filename]['good'] = true;
        return REGISTRY_OLD_FILE;
    }
}


/**
 * Summarize the file registry in a way like
 * [ 'ext1' => 'contents', 'ext2' => 'contents' ]
 *
 * @return array
 */
function summarize_registry()
{
    $summasummarum = array();

    $keys = array_keys($_SESSION['files_registry']);

    foreach ($keys as $filename)
    {
        $summarized = "";

        $last_dot_pos = strrpos($filename, ".");
        $ext = strtolower( substr($filename, $last_dot_pos + 1, strlen($filename) - $last_dot_pos - 1) );


        if (!$_SESSION['files_registry'][$filename]['good'])
        {
            unset($_SESSION['files_registry'][$filename]);
            echo sprintf("[Deleted] %s\n-- %s\n\n", date('H:i:s'), $filename);
            continue;
        }

        $summarized .= trim(file_get_contents($filename));

        if (!isset($summasummarum[$ext]))
            $summasummarum[$ext] = "";

        if ($ext == 'css')
        {
            $summarized = Minify_CSS::minify(file_get_contents($filename));
        }
        else if ($ext == 'js')
        {
            $summarized = JSMinPlus::minify(file_get_contents($filename));
        }
        else
        {
            $summarized = trim(file_get_contents($filename));
        }

        $summasummarum[$ext] .= $summarized;
    }

    return $summasummarum;
}


?>