<?php
/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING file.
 */

namespace OCA\Notes\App;

use \OC\Files\View;

use \OCP\AppFramework\App;

use \OCA\Notes\Core\API;

use \OCA\Notes\Controller\PageController;
use \OCA\Notes\Controller\NotesController;
use \OCA\Notes\Controller\NotesApiController;

use \OCA\Notes\Service\NotesService;

use \OCA\Notes\Utility\FileSystemUtility;

use \OCA\Notes\Middleware\CORSMiddleware;


class Notes extends App {


	/**
	 * Define your dependencies in here
	 */
	public function __construct(array $urlParams=array()){
		parent::__construct('notes', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('PageController', function($c){
			return new PageController($c->query('API'), $c->query('Request'),
				$c->query('NotesService'));
		});

		$container->registerService('NotesController', function($c){
			return new NotesController($c->query('API'), $c->query('Request'),
				$c->query('NotesService'));
		});

		$container->registerService('NotesApiController', function($c){
			return new NotesApiController($c->query('API'), $c->query('Request'),
				$c->query('NotesService'));
		});


		/**
		 * Services
		 */
		$container->registerService('NotesService', function($c){
			return new NotesService($c->query('FileSystem'),
				$c->query('FileSystemUtility'),
				$c->query('API'));
		});


		/**
		 * Utilities
		 */
		$container->registerService('FileSystem', function($c){
			$userName = $c->query('API')->getUserId();

			// restrict fileaccess to /user/files/Notes directory and work
			// relative to that path
			$view = new View('/' . $userName . '/files/Notes');
			if (!$view->file_exists('')) {
				$view->mkdir('');
			}

			return $view;
		});

		$container->registerService('FileSystemUtility', function($c){
			return new FileSystemUtility($c->query('FileSystem'));
		});

		$container->registerService('API', function($c){
			return new API($c->query('AppName'));
		});


		/** 
		 * Middleware
		 */
		$container->registerService('CORSMiddleware', function($c){
			return new CORSMiddleware($c->query('Request'));
		});	

		$container->registerMiddleWare('CORSMiddleware');

	}


}

