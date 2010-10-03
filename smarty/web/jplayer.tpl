<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'> 
<head> 
	<title>a simple audio player</title> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	{include file='web/_bg_style.tpl'}

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
	<script type="text/javascript" src="js/jquery.jplayer.min.js"></script> 
{literal}
	<script type="text/javascript"> 
	<!--
	$(document).ready( function()
		{
{/literal}
			var apiMedia = "{$api_media}";
			var apiRegister = "{$api_url}register_media.php?{$session_urlparam}";
			var urlNext = "{$url_next}";
			var mediaId = "{$media_info->mediaid}";
{literal}
			// Local copy of jQuery selectors, for performance.
			var jpPlayTime = $("#player_play_time");
			var jpTotalTime = $("#player_total_time");
		 
			$("#jquery_jplayer").jPlayer({
				ready: function () {
					this.element.jPlayer("setFile", apiMedia ).jPlayer("play");
					//$('#player_play').css('display','none');
				},
				volume: 25,
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

			$("#player_play").click( function() {
				$('#jquery_jplayer').jPlayer("play");
				//$(this).css('display','none');
				//$('#player_pause').css('display','inline');
				$(this).blur();
				return false;
			});
		 
			$("#player_pause").click( function() {
				$('#jquery_jplayer').jPlayer("pause");
				//$('#player_play').css('display','inline');
				//$(this).css('display','none');
				$(this).blur();
				return false;
			});
		 
			$("#player_stop").click( function() {
				$('#jquery_jplayer').jPlayer("stop");	///// chrome does not work well.
				//$('#player_play').css('display','inline');
				//$('#player_pause').css('display','none');
				$(this).blur();
				return false;
			});

			$("#register_media").click( function()
				{
					var selector = document.getElementById("mylist_options");
					var mylist = selector.options[selector.selectedIndex].value;
					var url = apiRegister + "&mid=" + mediaId + "&lid=" + mylist;
					//$("#registered_out").text("registering !" + url);
					
					var http = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('MSXML2.XMLHTTP');
					http.open('GET', url);
					http.onreadystatechange = function()
						{
							if (http.readyState == 4 )
							{
								var res = eval( "(" + http.responseText + ")" );
								if( res.status == "ok" ) $("#registered_out").text("registered into " + res.title);
							}
						}
					http.send(null);
				}
			);
				
		}
	);
	-->
	</script> 
{/literal}
</head> 
<body id="bg">
{if $error}<div align="center">{$error}</div>{/if}

<div id="jquery_jplayer"></div>

<div align="center">
	<br />
	<table>
		<tr>
			<td colspan='2'>{image_link _media_info=$media_info size="wall"}<br/></td>
		</tr>
		<tr>
			<td colspan='2'>
				<span id="player_play_time"></span> /
				<span id="player_total_time"></span>
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
			</td>
			<td style="text-align: right">
				<a href="#" id="player_play">play</a>
				| <a href="#" id="player_pause">pause</a>
				| <a href="#" id="player_stop">stop</a>
			</td>
		</tr>
		<tr>
			<td colspan='2'><hr /></td>
		</tr>
		{if $playlist_array}
		<tr>
			<td colspan='2'>
				<div id="registered_out">
					Register media into a mylist<br />
					<select name="playlist_id" id="mylist_options">
					{foreach from=$playlist_array item=play}
						<option value="{$play->playlistid}">{$play->title|escape}</option>
					{/foreach}
					</select>
					<input type="submit" id="register_media" value="Register" />
				</div>
			</td>
		</tr>
		{/if}
	</table>
</div>

{include file='web/_footer.tpl'}
</body> 
</html>