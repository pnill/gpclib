<?php

try {
	$state = $app->db->prepare("SELECT * FROM scripts WHERE id = :id");
	$state->bindParam(":id", $scriptId);
	$state->execute();
	$script = $state->fetchObject();
} catch(PDOException $e) {
	$app->log->error($e->getMessage());
	wp_die("Database Error");
}

if (!$script) {
	wp_die("Script Not Found!");
}


?>

<script type="text/javascript" src="/gpclib/prism.js"></script>
<script type="text/javascript" src="/gpclib/gpclib.js"></script>
<link href="/gpclib/prism.css" rel="stylesheet" />
<link href="/gpclib/gpclib.css" rel="stylesheet" />
<link href="/gpclib/gpccode.css" rel="stylesheet" />

<h2><?php echo htmlentities($script->script_name); ?></h2>
<table cellpadding="0" cellspacing="0" class="gpclib_tw100">
	<tbody>
		<tr>
			<td valign="top">
				<div style="margin-bottom:10px;"><?php echo htmlentities($script->description); ?></div>
				<table class="zebra">
					<tbody>
						<tr class="odd">
							<td class="bold">Version</td>
							<td><?php echo htmlentities($script->version); ?></td>
						</tr>
						<tr>
							<td class="bold">Author</td>
							<td><a href="/forums/member.php?u=<?php echo htmlentities($script->user_id); ?>" style="color: #AA0000;" 
class="username-coloured"><?php echo htmlentities($script->user_name); ?></a></td>
						</tr>
						<tr class="odd">
							<td class="bold">Publish Date</td>
							<td><?php echo htmlentities($script->timestamp); ?></td>
						</tr>
						<tr class="even">
							<td class="bold">Downloads</td>
							<td><?php echo htmlentities($script->downloads); ?></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td width="140" valign="top" style="padding-left:10px;;width:140px;">
				<center>
					<h6 style="margin:0;">RATE</h6>
				</center>
				<hr class="dotted" style="margin:8px 0;">
				<table class="gpclib_tw100">
					<tbody>
						<tr>
							<td align="center"><a href="/gpclib/?s=<?php echo htmlentities($script->id); ?>&r=p"><img src="/gpclib/images/rate_pu.png" width="37" height="37" border="0" style="height:37px;;width:37px;"></a><br><?php echo htmlentities($script->upvotes); ?></td>
							<td align="center"><a href="/gpclib/?s=<?php echo htmlentities($script->id); ?>&r=m"><img src="/gpclib/images/rate_mu.png" width="37" height="37" border="0" style="height:37px;;width:37px;"></a><br><?php echo htmlentities($script->downvotes); ?></td>
						</tr>
					</tbody>
				</table>
				<hr class="dotted" style="margin:5px 0;">
				<form class="box style" style="margin:5px 0;" action="/gpclib/api/download.php">
					<input type="hidden" name="s" value="<?php echo htmlentities($script->id); ?>"> 
					<button style="width:136px;" title="Download <?php echo htmlentities($script->script_name); ?> GPC Script">DOWNLOAD</button>
				</form>
				<ul class="line line-icon">
					<li><a href="/forums/member.php?u=<?php echo htmlentities($script->user_id); ?>">Contact author</a></li>
					<li><a href="/gpclib/?a=<?php echo htmlentities($script->user_id); ?>">Scripts by this author</a></li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>

<?php if ($script->release_notes) : ?>

<div class="box-info">
	<strong>Release Notes:</strong>
	<?php echo htmlentities($script->release_notes); ?>
</div>

<?php endif; ?>

<div class="scriptcode">
		</dt>
	</dl>
</div>

<div id="codeheader">
	Code:<a href="#" onclick="selectCode(this); return false;" class="postlink-local">Select all</a>
</div>
<pre id="codepre" class="line-numbers">
<code id="code" class="language-clike"><?php echo htmlentities($script->script); ?></code>
</pre>
