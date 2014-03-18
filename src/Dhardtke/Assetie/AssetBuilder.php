<?php
namespace Dhardtke\Assetie;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Asset\FileAsset;
use Assetic\Asset\AssetCollection;

use Config;
use Exception;

use Cache;
use URL;

class AssetBuilder {
	private $group, $buildExtension;
	
	public function __construct($group, $buildExtension) {
		$this->group = $group;
		$this->buildExtension = $buildExtension;
	}
	
	public function build($collection_name) {
		$build_path_setting = Config::get("assetie::build_path");
		
		$build_directory = public_path() . DIRECTORY_SEPARATOR . $build_path_setting;
		/**
		 * the designated name of the build, i.e. base_123.js
		*/
		$build_name = $collection_name . "." . $this->buildExtension;
		$build_file = $build_directory . DIRECTORY_SEPARATOR . $build_name;
		$buildExists = file_exists($build_file);
		
		$build_url = URL::asset($build_path_setting . DIRECTORY_SEPARATOR . $build_name);
		
		$debugMode = Config::get("app.debug");
		
		if (!$buildExists || $debugMode) {
			$files = \Collection::dump($collection_name)[$this->group];
			$collection_hash = sha1(serialize($files));
			
			$hash_in_cache = Cache::get("collection_" . $this->group . "_" . $collection_name);
			
			$collectionChanged = $collection_hash != $hash_in_cache;
			
			$src_dir = app_path() . DIRECTORY_SEPARATOR . Config::get("assetie::directories." . $this->group) . DIRECTORY_SEPARATOR;
			
			$forceRebuild = false;
			if ($collectionChanged) {
				$forceRebuild = true;
			} else if($buildExists) {
				/**
				 * only recompile if no compiled build exists or when in debug mode and
				 * build's source files or collections.php has been changed
				*/
				$forceRebuild = $this->checkModification($build_file, $files, $src_dir);
			}
			
			if (!$buildExists || $forceRebuild) {
				$am = new AssetManager();
				$assets = [];
				
				foreach ($files as $file) {
					$filters = $this->getFilters($file);
					
					$assets[] = new FileAsset($src_dir . $file, $filters);
				}
				
				$collection = new AssetCollection($assets); // , $filters
				$collection->setTargetPath($build_name);
				
				$am->set('collection', $collection);
				
				$writer = new AssetWriter($build_directory);
				$writer->writeManagerAssets($am);
			}
			
			// Cache::forever("collection_" . $collection_name, $collection_hash);
			$cache_key = "collection_" . $this->group . "_" . $collection_name;
			if (Cache::has($cache_key) && $collectionChanged) {
				Cache::forget($cache_key);
			}
			
			if ($collectionChanged) {
				Cache::put($cache_key, $collection_hash, 60); // 1 hour
			}
		}
		
		return $build_url;
	}
	
	private function getFilters($file) {
		$configFilters = Config::get("assetie::filters");
		$filter_extensions = array_keys($configFilters);
		$filters = [];
		
		foreach ($filter_extensions as $extension) {
			if (strpos($file, $extension) !== false) {
				$filters = $configFilters[$extension];
				break;
			}
		}
		return $filters;
	}
	
	private function checkModification($build_file, $files, $src_dir) {
		$build_modified = filemtime($build_file);
		
		foreach ($files as $file) {
			if (filemtime($src_dir . $file) > $build_modified) {
				return true;
			}
		}
		return false;
	}
}