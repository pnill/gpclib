<?php

require 'app.php';

$app->get('/', function() use ($app) {
	$pageSize = 20.0;

	// build xml header
	$app->contentType("text/xml");
	$xml = new SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1" ?><GPP/>');

	// insert optional header fields
	if ($_GET['f'] == '3') {
		$categories = $xml->addChild('C');
		$n = $xml->addChild('N');
		$n->MSG = "no, nooo, you buy pledge.";

		// enumerate the categories
		foreach($app->db->query("SELECT * FROM categories")->fetchAll() as $cat) {
			$categories->addChild('CNM', $cat['name']);
		}
	}
	$header = $xml->addChild('H');
	$header->addChild('SMR', 'GPC Scripts');
	$total = $header->addChild('TOT', 0);
	$currentPage = $header->addChild('CPG', 0);
	$totalPage = $header->addChild('TPG', 0);
	$res = $header->addChild('RES', 'Results: 0 ~ 0 of 0');

	// determine how to order the records
	$order = 'ORDER BY timestamp DESC';
	switch($_GET['o']) {
	case '1':
		$order = 'ORDER BY upvotes DESC, downvotes';
		break;
	case '2':
		$order = 'ORDER BY downloads DESC';
		break;
	case '3':
		$order = 'ORDER BY script_name';
		break;
	case '4':
		$order = 'ORDER BY user_name';
		break;
	}

	// determine page start
	$page = intval($_GET['p']);
	$start = $page * $pageSize;
	$startHuman = $start + 1;
	$end = $start + $pageSize;
	$header->CPG = $page;

	// build category query segment
	$catQuery = "";
	$category = "";
	if (!empty($_GET['c'])) {
		$category = $_GET['c'];
		$catQuery = "INNER JOIN script_categories sc ON s.id = sc.script_id INNER JOIN categories c ON c.id = sc.category_id WHERE c.name = :category";
	}

	// build search query segment
	$searchQuery = "";
	$searchTerm = "";
	if (!empty($_GET['q'])) {
		$searchTerm = strip_tags($_GET['q']);
		$header->SMR = "GPC Scripts - Search '$searchTerm'";
		$modifier = empty($_GET['c']) ? "WHERE" : "AND";
		$searchQuery = "$modifier MATCH (s.description, s.release_notes, s.user_name, s.script) AGAINST (:searchTerm IN BOOLEAN MODE)";
	}

	if (!empty($category)) {
		$header->SMR .= " on $category";
	}

	// build the author filter query
	$authorQuery = "";
	$author = "";
	if (!empty($_GET['a'])) {
		$author = strip_tags($_GET['a']);
		$authorQuery = "WHERE user_id = :userId";
		$catQuery = $category = "";
		$searchQuery = $searchTerm = "";
	}

	// update xml header with count information
	$state = $app->db->prepare("SELECT COUNT(*) FROM scripts s $catQuery $searchQuery $authorQuery");
	if (!empty($category)) $state->bindParam(':category', $category);
	if (!empty($searchTerm)) $state->bindParam(':searchTerm', $searchTerm);
	if (!empty($author)) $state->bindParam(':userId', $author);
	$state->execute();
	$total = $state->fetchColumn();
	$header->TOT = intval($total);
	$header->TPG = intval(ceil($total / $pageSize));
	$header->RES = "Results: $startHuman ~ $end of $total";

	// select all the rows
	$state = $app->db->prepare("SELECT s.* FROM scripts s $catQuery $searchQuery $authorQuery $order LIMIT $start,$pageSize");
	if (!empty($category)) $state->bindParam(':category', $category);
	if (!empty($searchTerm)) $state->bindParam(':searchTerm', $searchTerm);
	if (!empty($author)) $state->bindParam(':userId', $author);
	$state->execute();

	// add all the rows to xml
	foreach($state->fetchAll() as $row) {
		$s = $xml->addChild('S');
		$s->SID = $row['id'];
		$s->UID = $row['user_id'];
		$s->NAM = $row['script_name'];
		$s->DES = $row['description'];
		$s->RTP = $row['upvotes'];
		$s->RTM = $row['downvotes'];
		$s->UTM = strtotime($row['timestamp']);
		$s->DOW = $row['downloads'];
		$s->NOT = $row['release_notes'];
		$s->VER = $row['version'];
		$s->UNM = $row['user_name'];
		$s->TID = $row['thread_id'];
	}

	// write xml data
	$body = gzencode($xml->asXml());
	$app->response->headers->set('Content-Length', strlen($body));
	$app->response->headers->set('Content-Encoding', 'gzip');
	echo $body;
});


$app->post('/upload.php', function() use ($app) { 

if(!empty($_POST['fname']) && !empty($_POST['gpc']))
{
	$fname = intval($_POST['fname']);
	$gpc = htmlentities($_POST['gpc']);
	
	/* Get total in DB */
	$state = $app->db->prepare("SELECT count(*) FROM scripts_pending;");
	$state->execute();
	
	$remove = $state->fetchColumn() - 10;
	
	if($remove > 0):
		$rm = $app->db->prepare("DELETE FROM scripts_pending LIMIT $remove ORDER BY timestamp ASC;");
		$rm->execute();
	endif;
	
	
	/* Insert new one into DB */
	$insertion = $app->db->prepare("INSERT INTO scripts_pending (fname,script) VALUES (:fname,:gpc);");
	$insertion->bindParam(':fname', $fname);
	$insertion->bindParam(':gpc', $gpc);
	$insertion->execute();

	
	
}else{
	$app->halt(404);
}

});


$app->get('/download.php', function() use ($app) {
	// check for the script id
	if (empty($_GET['s'])) {
		$app->halt(404);
	}

	try {
		// get the script from the database
		$state = $app->db->prepare("SELECT script, script_filename FROM scripts WHERE id = :id");
		$state->bindParam(':id', $_GET['s']);
		$state->execute();
		$script = $state->fetchObject();
		$state = $app->db->prepare("UPDATE scripts SET downloads = downloads + 1 WHERE id = :id");
		$state->bindParam(':id', $_GET['s']);
		$state->execute();
	} catch(PDOException $e) {
		$app->log->error($e->getMessage());
		$app->halt(404);
	}

	$filename = trim($script->script_filename) . '.gpc';
	if (strlen($filename) == 4) {
		$filename = 'script.gpc';
	}

	$script->script = "// GPC Online Library\n// $filename\n\n" . $script->script;

	// set the headers
	$app->response->headers->set('Content-Description', 'File Transfer');
	$app->response->headers->set('Content-Disposition',  'attachment; filename=' . $filename);
	$app->response->headers->set('Content-Type', 'application/gpc');
	//$app->response->headers->set('Expires', '0');
	$app->response->headers->set('Cache-Control', 'public');
	$app->response->headers->set('Pragma', 'no-cache');

	// send the script
	$body = gzencode($script->script);
	$app->response->headers->set('Content-Length', strlen($body));
	$app->response->headers->set('Content-Encoding', 'gzip');
	echo $body;
});

$app->error(function(\Exception $e) use ($app) {
	$app->log->error($e->getMessage());
	$app->halt(500);
});

$app->notFound(function() use ($app) {
	$app->halt(404);
});

$app->run();

?>
