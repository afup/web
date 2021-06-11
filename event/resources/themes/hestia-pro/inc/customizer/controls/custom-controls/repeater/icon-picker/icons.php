<?php
/**
 * The list of icons
 *
 * @package Hestia
 */
?>
<div class="iconpicker-popover bottomLeft">
	<div class="arrow"></div>
	<div class="popover-title">
		<input type="search" class="form-control iconpicker-search" placeholder="Type to filter">
	</div>
	<div class="popover-content">
		<div class="iconpicker">
			<div class="iconpicker-items">
				<?php
				echo apply_filters(
					'hestia_repeater_icons',
					'<i data-type="iconpicker-item" title=".fa-behance" class="fab fa-behance" data-icon=""></i>
					<i data-type="iconpicker-item" title=".fa-behance-square" class="fab fa-behance-square"></i>
					<i data-type="iconpicker-item" title=".fa-facebook-f" class="fab fa-facebook-f"></i>
					<i data-type="iconpicker-item" title=".fa-facebook-square" class="fab fa-facebook-square"></i>
					<i data-type="iconpicker-item" title=".fa-google-plus-g" class="fab fa-google-plus-g"></i>
					<i data-type="iconpicker-item" title=".fa-google-plus-square" class="fab fa-google-plus-square"></i>
					<i data-type="iconpicker-item" title=".fa-linkedin-in" class="fab fa-linkedin-in"></i>
					<i data-type="iconpicker-item" title=".fa-linkedin" class="fab fa-linkedin"></i>
					<i data-type="iconpicker-item" title=".fa-twitter" class="fab fa-twitter"></i>
					<i data-type="iconpicker-item" title=".fa-twitter-square" class="fab fa-twitter-square"></i>
					<i data-type="iconpicker-item" title=".fa-vimeo-v" class="fab fa-vimeo-v"></i>
					<i data-type="iconpicker-item" title=".fa-vimeo-square" class="fab fa-vimeo-square"></i>
					<i data-type="iconpicker-item" title=".fa-youtube" class="fab fa-youtube"></i>
					<i data-type="iconpicker-item" title=".fa-youtube-square" class="fab fa-youtube-square"></i>
					<i data-type="iconpicker-item" title=".fa-ambulance" class="fas fa-ambulance"></i>
					<i data-type="iconpicker-item" title=".fa-american-sign-language-interpreting" class="fas fa-american-sign-language-interpreting"></i>
					<i data-type="iconpicker-item" title=".fa-anchor" class="fas fa-anchor"></i>
					<i data-type="iconpicker-item" title=".fa-android" class="fab fa-android"></i>
					<i data-type="iconpicker-item" title=".fa-apple" class="fab fa-apple"></i>
					<i data-type="iconpicker-item" title=".fa-archive" class="fas fa-archive"></i>
					<i data-type="iconpicker-item" title=".fa-chart-area" class="fas fa-chart-area"></i>
					<i data-type="iconpicker-item" title=".fa-asterisk" class="fas fa-asterisk"></i>
					<i data-type="iconpicker-item" title=".fa-car" class="fas fa-car"></i>
					<i data-type="iconpicker-item" title=".fa-balance-scale" class="fas fa-balance-scale"></i>
					<i data-type="iconpicker-item" title=".fa-ban" class="fas fa-ban"></i>
					<i data-type="iconpicker-item" title=".fa-university" class="fas fa-university"></i>
					<i data-type="iconpicker-item" title=".fa-bicycle" class="fas fa-bicycle"></i>
					<i data-type="iconpicker-item" title=".fa-birthday-cake" class="fas fa-birthday-cake"></i>
					<i data-type="iconpicker-item" title=".fa-btc" class="fab fa-btc"></i>
					<i data-type="iconpicker-item" title=".fa-black-tie" class="fab fa-black-tie"></i>
					<i data-type="iconpicker-item" title=".fa-bookmark" class="fas fa-bookmark"></i>
					<i data-type="iconpicker-item" title=".fa-briefcase" class="fas fa-briefcase"></i>
					<i data-type="iconpicker-item" title=".fa-bus" class="fas fa-bus"></i>
					<i data-type="iconpicker-item" title=".fa-taxi" class="fas fa-taxi"></i>
					<i data-type="iconpicker-item" title=".fa-camera" class="fas fa-camera"></i>
					<i data-type="iconpicker-item" title=".fa-check" class="fas fa-check"></i>
					<i data-type="iconpicker-item" title=".fa-child" class="fas fa-child"></i>
					<i data-type="iconpicker-item" title=".fa-code" class="fas fa-code"></i>
					<i data-type="iconpicker-item" title=".fa-coffee" class="fas fa-coffee"></i>
					<i data-type="iconpicker-item" title=".fa-cog" class="fas fa-cog"></i>
					<i data-type="iconpicker-item" title=".fa-comment-dots" class="fas fa-comment-dots"></i>
					<i data-type="iconpicker-item" title=".fa-cube" class="fas fa-cube"></i>
					<i data-type="iconpicker-item" title=".fa-dollar-sign" class="fas fa-dollar-sign"></i>
					<i data-type="iconpicker-item" title=".fa-gem" class="far fa-gem"></i>
					<i data-type="iconpicker-item" title=".fa-envelope" class="fas fa-envelope"></i>
					<i data-type="iconpicker-item" title=".fa-female" class="fas fa-female"></i>
					<i data-type="iconpicker-item" title=".fa-fire-extinguisher" class="fas fa-fire-extinguisher"></i>
					<i data-type="iconpicker-item" title=".fa-glass-martini" class="fas fa-glass-martini"></i>
					<i data-type="iconpicker-item" title=".fa-globe" class="fas fa-globe"></i>
					<i data-type="iconpicker-item" title=".fa-graduation-cap" class="fas fa-graduation-cap"></i>
					<i data-type="iconpicker-item" title=".fa-heartbeat" class="fas fa-heartbeat"></i>
					<i data-type="iconpicker-item" title=".fa-heart" class="fas fa-heart"></i>
					<i data-type="iconpicker-item" title=".fa-bed" class="fas fa-bed"></i>
					<i data-type="iconpicker-item" title=".fa-hourglass" class="fas fa-hourglass"></i>
					<i data-type="iconpicker-item" title=".fa-home" class="fas fa-home"></i>
					<i data-type="iconpicker-item" title=".fa-gavel" class="fas fa-gavel"></i>
					<i data-type="iconpicker-item" title=".fa-lock" class="fas fa-lock"></i>
					<i data-type="iconpicker-item" title=".fa-map-signs" class="fas fa-map-signs"></i>
					<i data-type="iconpicker-item" title=".fa-paint-brush" class="fas fa-paint-brush"></i>
					<i data-type="iconpicker-item" title=".fa-plane" class="fas fa-plane"></i>
					<i data-type="iconpicker-item" title=".fa-rocket" class="fas fa-rocket"></i>
					<i data-type="iconpicker-item" title=".fa-puzzle-piece" class="fas fa-puzzle-piece"></i>
					<i data-type="iconpicker-item" title=".fa-shield-alt" class="fas fa-shield-alt"></i>
					<i data-type="iconpicker-item" title=".fa-tag" class="fas fa-tag"></i>
					<i data-type="iconpicker-item" title=".fa-times" class="fas fa-times"></i>
					<i data-type="iconpicker-item" title=".fa-unlock" class="fas fa-unlock"></i>
					<i data-type="iconpicker-item" title=".fa-user" class="fas fa-user"></i>
					<i data-type="iconpicker-item" title=".fa-user-md" class="fas fa-user-md"></i>
					<i data-type="iconpicker-item" title=".fa-video" class="fas fa-video"></i>
					<i data-type="iconpicker-item" title=".fa-wordpress" class="fab fa-wordpress"></i>
					<i data-type="iconpicker-item" title=".fa-wrench" class="fas fa-wrench"></i>'
				);
				?>
			</div> <!-- /.iconpicker-items -->
		</div> <!-- /.iconpicker -->
	</div> <!-- /.popover-content -->
</div> <!-- /.iconpicker-popover -->
