# ASSETMON #

An open-source automated web asset monitoring and compilation tool written in PHP.

----------

This tool recursively monitors and compiles all web assets so that web applications will have to load one single file.



## Getting started ##

### 1. Configure `assetmon.json` ###

> Note: `assetmon.json` is the default configuration file of **assetmon**.


The snippet below is the default content of `assetmon.json`:

	{
	    
	    "extensions": [
	        "js",
	        "css"
	    ],
	    
	    "directories" : [
	        "./"
	    ],
	    
	    "destination" : "./assets",
	    
	    "recursive" : true
	    
	}

**Configuration directives**

1. #### `extensions (array)` ####

	> **Default:** `[ "js", "css" ]`
	
	An array of file extensions to be included in asset monitoring.


2. #### `directories (array)` ####

	> **Default:** `[ "./" ]`
	
	An array of existing directories to be included in recursive file monitoring.
	**Symbolic paths** are allowed.


3. #### `destination (string)` ####

	> **Default:** `"./assets"`

	The path to destination folder. If path does not exist, it will automatically be created.
	**Symbolic paths** are allowed.


4. #### `recursive (boolean)` ####

	> **Default:** `true`

	Boolean value if assets (from `directories`) will be recursively monitored.

----------

### 2. Start `assetmon` ###

Copy **assetmon** to your current working directory and make sure that `assetmon.json` is properly configured and is in the same directory.

> **Note:** `assetmon` file is a GZip compressed PHP archive

Optionally, you can specify the configuration to be used through `config` argument.

**Console command syntax**
	
	$ php assetmon [config]


If you work with an existing `assetmon.json` config file, you can follow the simple syntax below:

    $ php assetmon/assetmon

The output asset files will vary based on what is specified in `extensions` and `destination`.

# Sample scenario #

**Example**

Given the sample configuration below:

	{
	    
	    "extensions": [
	        "js",
	        "css"
	    ],
	    
	    "directories" : [
	        "./resources",
			"./dev/resources"
	    ],
	    
	    "destination" : "./public/assets",
	    
	    "recursive" : true
	    
	}

In this example, it will do a *recursive monitoring* and compilation of `js` and `css` files.

The process starts by checking if directories `./resources` and `./dev/resources` exists.

Only existing directories/paths will be processed in *recursive monitoring*.

Outputs expected are `./public/assets/all.js` and `./public/assets/all.css`, respectively.

> **Note:** Whether the script found such file extensions from the given directories, it will still produce the mentioned files with respect to the specified `extensions`.