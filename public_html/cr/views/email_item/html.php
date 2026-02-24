<?php 
function returnItemString($items) {
	$rtn_value = '';
	$rtn_value .= "<table width='100%' style='font-family:\"Trebuchet MS\", \"Lucida Grande\", \"Helvetica Neue\", Helvetica, Arial, Verdana'>";
	
	$i = 1;
	
	foreach ($items as $item) {
		$listing_id = $item['listing_id'];

		$title = $item['title'];
		$description = $item['description'];

		$temp_img = new FileHelper('listing_images', $listing_id);
		$image = $temp_img->getImagePathFromTag("most_recent_upload",100,100);

		$listing_url = SITE_URL . seoFriendlyURLs($listing_id, "listing", '', $title);
		
		if ($i == 1) {
			$rtn_value .= '<tr>';
		}
		
		$rtn_value .= '<td style="border:1px solid #E3E3E3; padding: 5px; width: 33%; vertical-align: top;">';
		$rtn_value .= '<a style="color:#205EA8; font-size:14px; margin: 5px 0" target="_blank" href="' . $listing_url . '">' . $title . '</a>';
//		$rtn_value .= '<p style="font-weight:bold; margin:4px 0;font-size:12px;">Region: ' . $region . '</p>';
		$rtn_value .= '<a target="_blank" href="' . $listing_url . '"><img src="' . $image . '" style="border:1px solid #666; padding: 2px;" alt="' . COMPANY_NAME . ' - ' . $title . '" /></a>';
		$rtn_value .= '<p style="font-style:italic; margin: 2px 0;font-size:11px;">' . nl2br($description) . '</p>';
		$rtn_value .= '</td>';
		
		if ($i == 3) {
			$rtn_value .= '</tr>';
			$i = 1;
		} else {
			$i++;
		}
	}
	 
	if ($i != 1) {
		while ($i <= 3) {
			$rtn_value .= "<td style='border:0'>&nbsp;</td>";
			$i++;
		}
		$rtn_value .= '</tr>';
	}
	$rtn_value .= "</table>";
	
	return $rtn_value;
}


ob_start();	

echo '<p>Some recently listed items on Freestuff</p>';
echo returnItemString($items);

$body = ob_get_contents();
ob_end_flush()
?>
<br />
<table width='100%'>
	<tr>
		<td>
			<h3>Body</h3>
			<textarea cols='200' rows='20'><?=$body?></textarea>
		</td>
	</tr>
</table>
