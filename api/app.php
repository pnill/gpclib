<?php

require 'vendor/autoload.php';

\Slim\Slim::registerAutoloader();

class App extends \Slim\Slim {
	function __construct(array $userSettings = array()) {
		// call the normal parent constructor
		parent::__construct($userSettings);

		// insert the database singleton
		$this->container->singleton('db', function() {
			$conn = new PDO("mysql:host=". $this->config('db.hostname') . ";dbname=" . $this->config('db.database'), $this->config('db.username'), $this->config('db.password'));
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		});
	}
}

$app = new App(array(
	'log.writer'  => new \Slim\LogWriter(fopen('slim.log', 'a')),
	'debug'       => false,
	'db.hostname' => 'localhost',
	'db.database' => 'cronuste_gpclib',
	'db.username' => 'cronuste_test',
	'db.password' => 'xxx',
));

$app->log->setEnabled(true);

?>
