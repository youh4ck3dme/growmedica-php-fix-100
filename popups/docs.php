<script type="text/javascript">
	function hideFrame(frameName)
	{
		var e = document.getElementById(frameName);
		if(e){
			e.style.display = 'none';
			var f = document.getElementById(frameName + '_on');
			if(f){
				f.style.display = 'block';
			}
			var g = document.getElementById(frameName + '_off');
			if(g){
				g.style.display = 'none';
			}			
		}
	}
	function showFrame(frameName)
	{
		var e = document.getElementById(frameName);
		if(e){
			e.style.display = 'block';
			var f = document.getElementById(frameName + '_off');
			if(f){
				f.style.display = 'block';
			}
			var g = document.getElementById(frameName + '_on');
			if(g){
				g.style.display = 'none';
			}			
		}
	}	
</script>
<style type="text/css">
div#docs_frame {
	display: none;
}
a.link_on {
	display: none;
}
a.link_off, a.link_on {
	text-decoration: none;
	color: #000000;
	padding: 3px 0 3px 0;
}
</style>
<a class="link_off" href="javascript:;" id="docs_frame_on" onClick="javascript:showFrame('docs_frame');"><img src="../images/icons/arrow_closed.gif" border="0" align="absmiddle" alt="zobraziť súbory" /> zobraziť súbory</a>
<a class="link_on" href="javascript:;" id="docs_frame_off" onClick="javascript:hideFrame('docs_frame');"><img src="../images/icons/arrow_opened.gif" border="0" align="absmiddle" alt="skryť súbory" /> skryť súbory</a>
<div id="docs_frame">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="width: 20%;">
		<iframe src="<?= ROOTDIR ?>/popups/docs_left.php" style="width: 100%; height: 300px;" frameborder="0" name="docs_left"></iframe>
	</td>
    <td style="text-align: left; width: 80%;">
		<iframe src="<?= ROOTDIR ?>/popups/docs_right.php" style="width: 100%; height: 300px;" frameborder="0" name="docs_right"></iframe>
	</td>
  </tr>
</table>
</div>
<div style="height: 3px;"></div>