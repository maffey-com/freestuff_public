<!-- Breadcrumbs line -->
<div class="crumbs">
	<ul id="breadcrumbs" class="breadcrumb"> 
		<?php 
		$tmp_breadcrumbs = BreadcrumbHelper::getBreadcrumbs();

		$count_crumbs = count($tmp_breadcrumbs);
		if ($count_crumbs == 0) {
			?>
			<li class="active"><span>Dashboard</span></li>
			<?
		} else {
			?>
			<li><a href="<?=(APP_URL)?>dashboard">Dashboard</a></li>
			<?
			$tmp_count = 0;
			foreach ($tmp_breadcrumbs as $tmp_label => $tmp_url) {
				$tmp_count++;

				# last one
				if ($tmp_count == $count_crumbs) {
					echo '<li class="active"><span>' . $tmp_label . '</span></li>';
					
				} elseif (empty($tmp_url)) {
					echo '<li><span>' . $tmp_label . '</span></li>';
					
				} else {
					echo '<li><a href="' . $tmp_url . '">' . $tmp_label . '</a></li>';
				}
			}
		}
		?>
	</ul>
        
	<?php 
	/*?>
	<ul class="alt-buttons">
		<li><a href="#" title=""><i class="icon-signal"></i><span>Stats</span></a></li>
		<li><a href="#" title=""><i class="icon-comments"></i><span>Messages</span></a></li>
		<li class="dropdown"><a href="#" title="" data-toggle="dropdown"><i class="icon-tasks"></i><span>Tasks</span> <strong>(+16)</strong></a>
			<ul class="dropdown-menu pull-right">
				<li><a href="#" title=""><i class="icon-plus"></i>Add new task</a></li>
				<li><a href="#" title=""><i class="icon-reorder"></i>Statement</a></li>
				<li><a href="#" title=""><i class="icon-cog"></i>Settings</a></li>
			</ul>
		</li>
	</ul>
	*/
	?>
</div>
<!-- /breadcrumbs line -->