<?php

/*
 * Facilite l'obtention de chemins absolus et d'urls de fichiers classés par types au sein d'un projet
 * N'EST PAS UN LOADER ! Ne fait que renseigner path / url d'un fichier s'il existe
 */

defined('SEP') || define('SEP', DIRECTORY_SEPARATOR);

 abstract class Finder
 {
 	// Types de chemins : Url, absolu, ou les 2 dans un tableau
 	const URL 		= 0,
 		  PATH 	    = 1,
 		  BOTH      = 2;
 	// Chemin de base du projet dans lequel rechercher les cibles
 	public static $_basePath = NULL;
 	// Url de base
 	private static $_baseUrl = NULL;
 	/*
 	 * Tous les différents types de fichiers avec leurs potentions conteneurs
 	 * array('FileType' => array('Chemin1', 'Chemin2', ...)
 	 * )
 	 */
 	public static $_fileTypes = array();

 	public static function init($basePath, $baseUrl, $fileTypes = array())
 	{
 		self::$_basePath = $basePath;
 		self::$_baseUrl = $baseUrl;
 		self::$_fileTypes = $fileTypes;
 	}

 	public static function addFileType($type, $pathsToSearch)
 	{
 		if(!isset(self::$_fileTypes[$type]))
 			self::$_fileTypes[$type] = $pathsToSearch;
 		else
 			self::$_fileTypes[$type] = array_merge(self::$_fileTypes[$type], $pathsToSearch);
 	}

 	public static function setBasePath($basePath)
 	{
 		self::$_basePath = $basePath;
 	}

 	public static function setBaseUrl($baseUrl)
 	{
 		self::$_baseUrl = $baseUrl;
 	}

 	/*
 	 * Recherche un fichier
 	 * $name = nom de la fonction statique appellée = type du fichier recherché
 	 * $args[0] = nom du fichier
 	 * $args[1,2,...] = paramètres additionnels pouvant être utilisé dans le chemin du type de fichier
 	 * Retourne une instance de FileTarget permettant la capture du chemin absolu ou url si trouvé, sinon FALSE
 	 */
 	public static function __callStatic($name, $args)
 	{
 		$typePath = NULL;
 		// Type de chemin demandé : absolu / url ? 
 		if(substr($name, -3) == 'Url')
 		{
 			$typePath = self::URL;
 			$name = substr($name, 0, strlen($name) - 3);
 		}
 		else if(substr($name, -4) == 'Path')
 		{
 			$typePath = self::PATH;
 			$name = substr($name, 0, strlen($name) - 4);
 		}
 		else
 		{
 			$typePath = self::BOTH;
 		}

 		if(isset(self::$_fileTypes[$name]))
 		{
 			$file = array_shift($args);
 			foreach(self::$_fileTypes[$name] as $curPath)
 			{ // Chemin courant
 				$nbVars = substr_count($curPath, '%s');
 				if($nbVars > count($args))
 					continue;
 				// Array séparé ne contenant que les args nécessaires au chemin actuel pour conserver les originaux
 				$curArgs = array_slice($args, 0, $nbVars);
 				// Le nombre de paramètre additionnel semble correspondre avec le chemin actuel
 				// Fichier trouvé
 				if(file_exists($path = 
 				  (rtrim(self::$_basePath, SEP) . SEP . rtrim(vsprintf($curPath, $curArgs), SEP) . SEP . $file)))
 				{
 					switch($typePath)
 					{
 						case self::PATH : 
 							return $path;
 						break;
 						case self::URL : 
 							return self::getUrl($path);
 						break;
 						default:
 						case self::BOTH :
 							return array('path' => $path,
 										 'url'  => self::getUrl($path));
 						break;
 					}
 				}
 			}
 		}
 		
		return FALSE;
 	}

 	public static function getUrl($path)
 	{
 		// Bien un chemin absolu
 		if(strpos($path, self::$_basePath) === 0)
 		{
 			$path = substr($path, strlen(self::$_basePath) + 1);
 			return trim(self::$_baseUrl, '/') . '/' . $path;
 		}
 		else
 			return FALSE;
 	}

 	/*
 	 * Crée des fonctions d'accès afin de minimiser l'écriture Finder::viewPath ...
 	 * ! Attention ! Mauvaises performances, perte d'une bonne ms sur DonkeyCMS
 	 */
 	public static function generateAccessFunctions()
 	{
 		$genCode = function($fileType, $returnType)
 		{
 			return 'function '. $fileType.$returnType .'($fileName) 
 				    {
 				    	if(func_num_args() > 1)
 				    	{
 				    		$params = array_slice(func_get_args(), 1);
 				    	}
 				    	else
 				    		$params = array();

 				    	return ' . __CLASS__. '::'. $fileType.$returnType .'($fileName, $params);
 				    }';
 		};

 		foreach(self::$_fileTypes as $fileType => $v)
 		{
 			if(!function_exists($fileType.'Path'))
 				eval($genCode($fileType, 'Path'));

 			if(!function_exists($fileType.'Url'))
 				eval($genCode($fileType, 'Url'));

 			if(!function_exists($fileType))
				eval($genCode($fileType, '')); 			
 		}
 	}
 }