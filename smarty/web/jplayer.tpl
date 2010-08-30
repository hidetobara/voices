<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'> 
<head> 
<!-- Website Design By: www.happyworm.com --> 
<title>Demo (jPlayer 1.2.0) : jPlayer as a stylish audio player</title> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
<script type="text/javascript" src="js/jquery.jplayer.min.js"></script> 
{literal}
	<script type="text/javascript"> 
	<!--
	$(document).ready( function()
		{
{/literal}
			var apiNow = "{$api_now}&{$session_urlparam}";
			var urlNext = "{$url_next}";
{literal}
			// Local copy of jQuery selectors, for performance.
			var jpPlayTime = $("#jplayer_play_time");
			var jpTotalTime = $("#jplayer_total_time");
		 
			$("#jquery_jplayer").jPlayer({
				ready: function () {
					this.element.jPlayer("setFile", apiNow ).jPlayer("play");
				},
				volume: 30,
				oggSupport: false,
				preload: 'none'
			})
			.jPlayer("onProgressChange", function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) {
				jpPlayTime.text($.jPlayer.convertTime(playedTime));
				jpTotalTime.text($.jPlayer.convertTime(totalTime)); 
			})
			.jPlayer("onSoundComplete", function() {
				//this.element.jPlayer("setFile", urlVoiceNext ).jPlayer("play");
				if(urlNext != "") document.location = urlNext;
			});
		}
	);

	function mylistRegister()
	{
		var options = document.getElementById("mylist_options");
		var val = options.options[options.selectedIndex].value;
		$("#mylist_out").text("registered! " + val);
		
		var http = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('MSXML2.XMLHTTP');
		http.open('GET', '/public_api/image.php');
		http.send(null);
	}
	-->
</script> 
{/literal}
</head> 
<body> 
<div id="jquery_jplayer"></div>
<div align="center">
	<table>
		<tr>
			<td colspan='2'>{image_link _media_info=$media_info size="wall"}<br/></td>
		</tr>
		<tr>
			<td colspan='2'>
				<span id="jplayer_play_time" class="jp-play-time"></span> /
				<span id="jplayer_total_time" class="jp-total-time"></span>
			</td>
		</tr>
		<tr>
			<td colspan='2'>{$media_info->title|escape}</td>
		</tr>
		<tr>
			<td colspan='2'>{$media_info->artist|escape}</td>
		</tr>
		<tr>
			<td style="text-align: left">
				<a href="#" id="jplayer_play" class="jp-play">play</a>
				<a href="#" id="jplayer_pause" class="jp-pause">pause</a>
			</td>
			<td style="text-align: right">
				<a href="#" id="jplayer_stop" class="jp-stop">stop</a>
			</td>
		</tr>
		<tr>
			<td colspan='2'><hr /></td>
		</tr>
		<tr>
			<td colspan='2' style="text-align: right">
				<a href="media_edit.php?mid={$media_info->mediaid}">&gt;&gt;&gt;Edit</a>
			</td>
		</tr>
	</table>
</div>

</body> 
</html>