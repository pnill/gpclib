<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); 

ini_set('error_log','php_errors.log');

chdir("/home/cronustest/public_html/forums/");
require("./global.php");
chdir("/home/cronustest/public_html/gpclib/");
require(ABSPATH . 'gpclib/api/app.php');

global $vbulletin;

$vb_username 	= $vbulletin->userinfo['username'];
$vb_userid 		= $vbulletin->userinfo['userid'];
$vb_loggouthash = $vbulletin->userinfo['logouthash'];

if($vb_userid == 0)
	echo "not logged in";// add log-in code here.

$p = intval($_GET['pid']);

if(!empty($_GET['pid'])):

?>

<h2>Publish Script</h2>
	<form action="/gpclib/usp.php?p=215653" class="box style" method="post">
	<script language="JavaScript">
		function changetab() {
			if(document.getElementById('ns').checked) {
				document.getElementById('pns').style.display = 'block';
				document.getElementById('pus').style.display = 'none';
			} else {
				document.getElementById('pns').style.display = 'none';
				document.getElementById('pus').style.display = 'block';
			}
		}
	</script>
	<div style="margin:30px 0 20px 0;">
		<span id="sns" class="box-note" style="margin-right:20px;">
			<input id="ns" name="ptype" type="radio" value="n" class="radio" style="vertical-align:-3px;" onclick="javascript:changetab();" />
			<label for="ns">New Script</label>
		</span>
		<span id="sus" class="box-note" >
			<input id="us" name="ptype" type="radio" value="u" class="radio" style="vertical-align:-3px;" onclick="javascript:changetab();" />
			<label for="us">Update Script</label>
		</span>
	</div>
	<div id="pns">
	<table cellpadding=0 cellspacing=0 class="gpclib_tw100"><tr><td valign="top">
		<ul class="line line-icon"><li style="color:#DDDDDD;"><strong>Script Name:</strong></li></ul>
		<input name="sname" type="text" value="" maxlength="128" style="width:425px;" />
		<ul class="line line-icon"><li style="color:#DDDDDD;"><strong>Description:</strong></li></ul>
		<textarea name="sdesc" style="width:425px;height:100px;"></textarea>
		<ul class="line line-icon"><li style="color:#DDDDDD;"><strong>Version:</strong></li></ul>
		<input name="sver" type="text" value="" maxlength="64" style="width:100px;" /> <span style="font-size:smaller;color:#828282;">(Ex.: 1.00)</span>
		<ul class="line line-icon"><li><em>Version Notes:</em></li></ul>
		<textarea name="snote" style="width:425px;height:50px;"></textarea>
	</td><td valign="top" width="185" class="gpclib_lcell" style="padding-left:15px;;width:185px;">
		<ul class="line line-icon"><li><em>Categories:</em></li></ul>
		<select name="scat[]" multiple style="width:100%;height:310px;">
		
		<option>PS4</option>
		
		<option>XBox One</option>
		
		<option>PS3</option>
		
		<option>XBox 360</option>
		
		<option>Wiimote</option>
		
		<option>Shooting</option>
		
		<option>Racing</option>
		
		<option>Action</option>
		
		<option>Sport</option>
		
		<option>Fighting</option>
		
		<option>Platform</option>
		
		<option>Music</option>
		
		<option>Flying</option>
		
		<option>RPG</option>
		
		</select>
		<div style="font-size:smaller;color:#828282;padding-left:5px;">You can select more than one category.</div>
	</td></tr></table>
	</div>
	<div id="pus">
		<ul class="line line-icon"><li style="color:#DDDDDD;"><strong>Update Script:</strong></li></ul>
		<select name="script_id" style="width:460px;">
		<option value="0">- Select a script to update -</option>
		
		<option value="163">edited (1.0)</option>
		
		<option value="164">edited (2.20)</option>
		
		<option value="162">edited (1.20)</option>
		
		<option value="161">edited (1.10)</option>
		
		</select>
		<ul class="line line-icon"><li style="color:#DDDDDD;"><strong>New Version:</strong></li></ul>
		<input name="suver" type="text" value="" maxlength="64" style="width:100px;" /> <span style="font-size:smaller;color:#828282;">(Ex.: 1.00)</span>
		<ul class="line line-icon"><li><em>Version Notes:</em></li></ul>
		<textarea name="sunote" style="width:640px;height:100px;"></textarea>
	</div>
	<div style="margin:10px 0 5px 0; text-align:right;">
	<button style="width:120px;">Publish</button>
	<input name="cancel" value="Cancel" type="button" onclick="javascript:document.location='/';" />
	</div>
	</form>
	<div class="scriptcode">
		<dl class="codebox"><dt>Code: <a href="#" onclick="selectCode(this); return false;" class="postlink-local">Select all</a></dt><dd><code style="background:none;"><span class="gpc_default"><span class="gpc_comment">/* *<br />* GPC SCRIPT<br />* <br />*&nbsp; GPC is a scripting language with C-like syntax.<br />*&nbsp; To learn more access GPC Language Reference on Help menu.<br />* *********************************************************** */</span><br /><br /><span class="gpc_keyword1">main</span> {<br /><br />&nbsp; &nbsp; <span class="gpc_comment">//<br /></span>&nbsp; &nbsp; <span class="gpc_comment">// The main procedure is called before every report be sent to<br /></span>&nbsp; &nbsp; <span class="gpc_comment">// console, you can think in this procedure as a loop which only<br /></span>&nbsp; &nbsp; <span class="gpc_comment">// ends when the script is unloaded.<br /></span>&nbsp; &nbsp; <span class="gpc_comment">//<br /></span>&nbsp; &nbsp; <span class="gpc_comment">// TODO: handle/change values of buttons, analog stick and/or sensors<br /></span>&nbsp; &nbsp; <span class="gpc_comment">//<br /></span><br />}<br /></span></code></dd></dl>
	</div>
<script language="JavaScript">
	document.getElementById('ns').checked = true;
	changetab();
</script>


<?php endif; ?>