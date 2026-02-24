<!-- Sidebar -->
<div id="sidebar">

	<div class="sidebar-tabs">
        <div id="general">
        	<!-- Sidebar user -->
	        <div class="sidebar-user widget">
	            <a href="#" title="" class="user"><img style="padding-top:10px" src="../img/logo_top.png" alt="<?=(COMPANY_NAME)?>" /></a>
	        </div>
	        <!-- /sidebar user -->

			<div class="general-stats widget">
				<ul class="head">
					<li><span>Users</span></li>
					<li><span>Free</span></li>
					<li><span>Wanted</span></li>
				</ul>
				<ul class="body">
					<? while (CacheHelper::getOB("cr", "counters")) { ?>
						<li><strong><?=(User::countRegisteredUsers())?></strong></li>
						<li><strong><?=(Listing::countActiveFreeListings())?></strong></li>
						<li><strong><?=(Listing::countActiveWantedListings())?></strong></li>
						<?
					}?>
				</ul>
			</div>

		    <!-- Main navigation -->
	        <ul class="navigation widget">
	        	<?php
	        	TemplateHandler::echoMenu(); 
	        	?>
	        </ul>
	        <!-- /main navigation -->
        </div>

    </div>
</div>
<!-- /sidebar -->