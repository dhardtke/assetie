<?php
namespace Dhardtke\Assetie;

use Exception;
use Config;

/*
 * @todo unit testing:
 *     if same collection (circular calls) are detected and also non existing files / collections
 *     if caching of collectionChanged works
 *     if checking of modificationTime works
 *
 * @todo artisan command zum purgen des caches
 *
 * wichtig: @todo caching bei collectionChanged ist komisch in Kombination mit Unterseiten
*/

class Collection {
	private $callbacks = [];
	private $collections = ["javascripts" => [], "stylesheets" => []];
	private $completedCallbacks = [];
	
	private static $valid_extensions = ["js", "css", "less", "sass"];
	
	/**
	 * this function builds a link tag from a given collection name
	 *
	 * @param string $collection_name the name of the collection to build
	 *
	 * @return string the <link> tag
	*/
	public function stylesheets($collection_name) {
		$assetBuilder = new AssetBuilder("stylesheets", "css");
		
		$build_url = $assetBuilder->build($collection_name);
	
		return '<link rel="stylesheet" type="text/css" href="' . $build_url . '" />' . "\n";
	}
	
	/**
	 * this function builds a script tag from a given collection name
	 *
	 * @param string $collection_name the name of the collection to build
	 *
	 * @return string the <script> tag
	*/
	public function javascripts($collection_name) {
		$assetBuilder = new AssetBuilder("javascripts", "js");
		
		$build_url = $assetBuilder->build($collection_name);
		
		return '<script src="' . $build_url . '" async></script>' . "\n";
	}
	
	public function addCollection($collectionName, \Closure $callback) {
		if (empty($this->callbacks[$collectionName])) {
			$this->callbacks[$collectionName] = $callback;
		} else {
			throw new Exception("Collection already exists... aborting!");
		}
	}
	
	public function add($file) {
		if (is_array($file)) {
			foreach ($file as $_file) {
				$this->add($_file);
			}
		} else {
			$filetype = pathinfo($file)["extension"];
			
			/**
			 * determine group by extension
			*/
			$group = ($filetype == "js" ? "javascripts" : "stylesheets");
			
			if (!in_array($filetype, self::$valid_extensions)) {
				throw new Exception($file . " has an unknown file type");
			}
			if (in_array($file, $this->collections[$group])) {
				throw new Exception($file . " has already been added");
			}
			$filepath = app_path() . DIRECTORY_SEPARATOR . Config::get("assetie::directories." . $group) . DIRECTORY_SEPARATOR;
			if (!file_exists($filepath . $file)) {
				throw new Exception($file . " does not exist in " . $filepath);
			}
			$this->collections[$group][] = $file;
		}
		return $this;
	}
	
	public function includeCollection($collectionName) {
		if (is_array($collectionName)) {
			foreach ($collectionName as $_collectionName) {
				$this->includeCollection($_collectionName);
			}
		} else {
			if (!in_array($collectionName, array_keys($this->callbacks))) {
				throw new Exception("The collection " . $collectionName . " does not exist. Have you specified the collections in the wrong order?");
			}
			$callback = $this->callbacks[$collectionName];
			/**
			 * build a unique hash of this callback
			*/
			$callback_hash = spl_object_hash($callback);
			
			/**
			 * if the hash is already inside $completedCallbacks
			 * the callback has been called already in a circular way
			 * i.e.
			 * collection1 --> collection2 --> collection1
			 * that would lead to an infinite loop and PHP cancelling the script
			*/
			if (in_array($callback_hash, $this->completedCallbacks)) {
				throw new Exception("You have specified incorrect relationships between the collections.");
			} else {
				$this->completedCallbacks[] = $callback_hash;
				call_user_func($callback, $this);
			}
		}
		return $this;
	}
	
	public function dump($collectionName) {
		if (!array_key_exists($collectionName, $this->callbacks)) {
			throw new Exception("Collection " . $collectionName . " does not exist");
		}
		
		$this->includeCollection($collectionName);
		$collections = $this->collections;
		
		/*
		 * purge no longer needed variables
		*/
		$this->completedCallbacks = [];
		$this->collections = ["javascripts" => [], "stylesheets" => []];
		
		return $collections;
	}
}