<script type="text/javascript">

</script>
<style type="text/css">
    div#photos_frame {
        display: none;
        height: 500px;
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
<a class="link_off" href="javascript:;" id="photos_frame_on" onClick="javascript:showFrame('photos_frame');">
    <img src="../images/icons/arrow_closed.gif" border="0" align="absmiddle" alt="zobraziť obrázky" /> zobraziť obrázky</a>
<a class="link_on" href="javascript:;" id="photos_frame_off" onClick="javascript:hideFrame('photos_frame');">
    <img src="../images/icons/arrow_opened.gif" border="0" align="absmiddle" alt="skryť obrázky" /> skryť obrázky</a>
<div id="photos_frame">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 20%;">
                <iframe src="<?= ROOTDIR ?>/popups/photo_left.php" style="width: 100%; height: 500px;" frameborder="0" name="photo_left"></iframe>
            </td>
            <td style="text-align: left; width: 80%;">
                <iframe src="<?= ROOTDIR ?>/popups/photo_right.php" style="width: 100%; height: 500px;" frameborder="0" name="photo_right"></iframe>
            </td>
        </tr>
    </table>
</div>
<div style="height: 3px;"></div>
