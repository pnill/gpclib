<?php
$start = intval($_GET['i']);
$order = intval($_REQUEST['o']);
$category = htmlentities($_GET['c']);
$author = intval($_GET['a']);
$searchTerm = htmlentities($_REQUEST['q']);
$pageSize = 20.0;

// determine how to order the records
$orderQuery = 'ORDER BY timestamp DESC';
switch($order) {
case '1':
	$orderQuery = 'ORDER BY upvotes DESC, downvotes';
	break;
case '2':
	$orderQuery = 'ORDER BY downloads DESC';
	break;
case '3':
	$orderQuery = 'ORDER BY script_name';
	break;
case '4':
	$orderQuery = 'ORDER BY user_name';
	break;
}

// determine page start
$startHuman = $start + 1;
$end = $start + $pageSize;
$page = ceil($start / $pageSize);

// build category query segment
$catQuery = "";
if (!empty($category)) {
	$catQuery = "INNER JOIN script_categories sc ON s.id = sc.script_id INNER JOIN categories c ON c.id = sc.category_id WHERE c.name = :category";
}

// build search query segment
$searchQuery = "";
if (!empty($searchTerm)) {
	$modifier = empty($category) ? "WHERE" : "AND";
	$searchQuery = "$modifier MATCH (s.description, s.release_notes, s.user_name, s.script) AGAINST (:searchTerm IN BOOLEAN MODE)";
}

// build the author filter query
$authorQuery = "";
if (!empty($author)) {
	$authorQuery = "WHERE user_id = :userId";
	$catQuery = $category = "";
	$searchQuery = $searchTerm = "";
}

// get totals
$state = $app->db->prepare("SELECT COUNT(*) FROM scripts s $catQuery $searchQuery $authorQuery");
if (!empty($category)) $state->bindParam(':category', $category);
if (!empty($searchTerm)) $state->bindParam(':searchTerm', $searchTerm);
if (!empty($author)) $state->bindParam(':userId', $author);
$state->execute();
$total = $state->fetchColumn();
$totalPages = ceil($total / $pageSize);

// select all the rows
$state = $app->db->prepare("SELECT s.* FROM scripts s $catQuery $searchQuery $authorQuery $orderQuery LIMIT $start,$pageSize");
if (!empty($category)) $state->bindParam(':category', $category);
if (!empty($searchTerm)) $state->bindParam(':searchTerm', $searchTerm);
if (!empty($author)) $state->bindParam(':userId', $author);
$state->execute();
?>

<table cellpadding=0 cellspacing=0 class="gpclib_theader">
	<tr>
		<td align="left" style="padding-bottom:10px;">
			<form method="post" action="/gpclib/" class="box style">
				<input type="text" name="q" value="" style="width:200px; height:20px;" />
				<input type="button" onclick="submit();" value="Search" />
			</form>
		</td>
		<td align="right" style="padding-bottom:10px;">
			<form method="post" action="/gpclib/" class="box style">
				<select name="o" onchange="submit();">
					<option value="0" <?php if ($order == 0 || $order > 4 || $order < 0) echo "selected"; ?>>Recent Updated</option>
					<option value="1" <?php if ($order == 1) echo "selected"; ?>>Rating</option>
					<option value="2" <?php if ($order == 2) echo "selected"; ?>>Downloads</option>
					<option value="3" <?php if ($order == 3) echo "selected"; ?>>Name</option>
					<option value="4" <?php if ($order == 4) echo "selected"; ?>>Author</option>
				</select>
			</form>
		</td>
	</tr>
</table>

<div align="right" style="font-size:smaller;margin-bottom:8px;">Results <?php echo $startHuman; ?> - <?php echo $pageSize; ?> of <?php echo $total; ?></div>

<?php foreach($state->fetchAll() as $row) : ?>

<div class="gpclib_lrow">
	<table cellpadding=0 cellspacing=3 class="gpclib_tw100">
		<tr>
			<td valign="top">
				<h5 style="margin:0 0 10px 2px;"><a href="/gpclib/?s=<?php echo intval($row['id']); ?>"><?php echo htmlentities($row['script_name']); ?></a></h5>
				<code>
					<a href="/gpclib/?s=<?php echo intval($row['id']); ?>">
					<img src="/gpclib/images/icon_post_target.gif" width="11" height="9" style="height:9px;;width:11px;" />
					</a>
					Ver
					<strong><?php echo htmlentities($row['version']); ?></strong>
					by
					<a href="/forums/member.php?u=<?php echo intval($row['user_id']); ?>" style="color: #0033FF;" class="username-coloured">
					<?php echo htmlentities($row['user_name']); ?>
					</a>
				</code>
				<div style="margin-top:8px;"><?php echo htmlentities($row['description']); ?></div>
			</td>
			<td width="115" valign="middle" class="gpclib_lcell" style="padding-left:15px;;width:115px;">
				<table class="gpclib_tw100">
					<tr>
						<td align="center" style="color:#CCCCCC;"><img src="/gpclib/images/rates_p.png" width="24" height="24"  
style="height:24px;;width:24px;" /><br /><?php echo intval($row['upvotes']); ?></td>
						<td align="center" style="color:#CCCCCC;"><img src="/gpclib/images/rates_m.png" width="24" height="24"  
style="height:24px;;width:24px;" /><br /><?php echo intval($row['downvotes']); ?></td>
					</tr>
				</table>
				<ul class="line line-icon" style="color:#CCCCCC;">
					<li><strong><?php echo intval($row['downloads']); ?></strong> <span style="font-size:smaller;">downloads</span></li>
				</ul>
				<form class="box style" style="margin:5px 0;" action="/gpclib/">
					<input type="hidden" name="s" value="<?php echo intval($row['id']); ?>"> 
					<button style="width:106px;" title="Download  GPC Script">View Script</button>
				</form>
			</td>
		</tr>
	</table>
</div>

<?php endforeach; ?>

<link href="/gpclib/prism.css" rel="stylesheet" />
<link href="/gpclib/gpclib.css" rel="stylesheet" />
<link href="/gpclib/gpccode.css" rel="stylesheet" />

<div id="system">
	<div class="pagination">
		<?php for ($i = 1; $i <= $totalPages; $i++) : ?>
			<?php if ($i == 1 && $page != 0) : ?>
				<a class="previous" href="/gpclib/?o=<?php echo $order; ?>&i=<?php echo $pageSize * ($page - 1);?>">&#10094;</a>
			<?php endif; ?>
			<?php if ($page == ($i - 1)) : ?>
				<strong><?php echo $i; ?></strong>
			<?php else : ?>
				<a href="/gpclib/?o=<?php echo $order; ?>&i=<?php echo $pageSize * ($i - 1);?>"><?php echo $i; ?></a>
			<?php endif; ?>
			<?php if ($i == $totalPages && $page != ($totalPages - 1)) : ?>
				<a class="next" href="/gpclib/?o=<?php echo $order; ?>&i=<?php echo $pageSize * ($page + 1);?>">&#10095;</a>
			<?php endif; ?>
		<?php endfor; ?>
	</div>
</div>
