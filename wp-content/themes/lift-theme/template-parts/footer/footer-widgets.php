<?php
/**
 * Displays the footer widget area.
 *
 * @package LIFT Creations 
 * @subpackage Theme by Nguyen Pham
 * https://baonguyenyam.github.io/cv
 * @since 2021
 */

global $lift_theme;
$footer_value['footer_col'] = $lift_theme['lift-theme-footer-columns'];
$footer_value['footer_style'] = $lift_theme['lift-theme-footer-layout-style'];
$footer_value['footer_row'] = $lift_theme['lift-theme-footer-row-option'];
$footer_value['footer_gutters'] = $lift_theme['lift-theme-footer-columns-gutters'];
$footer_value['footer_column_1'] = trim($lift_theme['lift-theme-footer-columns-1']);
$footer_value['footer_column_2'] = trim($lift_theme['lift-theme-footer-columns-2']);
$footer_value['footer_column_3'] = trim($lift_theme['lift-theme-footer-columns-3']);
$footer_value['footer_column_4'] = trim($lift_theme['lift-theme-footer-columns-4']);
$footer_value['footer_column_5'] = trim($lift_theme['lift-theme-footer-columns-5']);
$footer_value['footer_column_6'] = trim($lift_theme['lift-theme-footer-columns-6']);
$footer_value['footer_column_7'] = trim($lift_theme['lift-theme-footer-columns-7']);
$footer_value['footer_column_8'] = trim($lift_theme['lift-theme-footer-columns-8']);
$footer_value['footer_column_9'] = trim($lift_theme['lift-theme-footer-columns-9']);
$footer_value['footer_column_10'] = trim($lift_theme['lift-theme-footer-columns-10']);
$footer_value['footer_column_11'] = trim($lift_theme['lift-theme-footer-columns-11']);
$footer_value['footer_column_12'] = trim($lift_theme['lift-theme-footer-columns-12']);
$global_value['backtotop'] = trim($lift_theme['lift-theme-global-function-backtotop']);
$global_value['backtotop_mobile'] = trim($lift_theme['lift-theme-global-function-backtotop-mobile']);
$global_value['backtotop_phalet'] = trim($lift_theme['lift-theme-global-function-backtotop-phalet']);
$global_value['backtotop_tablet'] = trim($lift_theme['lift-theme-global-function-backtotop-tablet']);
$global_value['backtotop_smallpc'] = trim($lift_theme['lift-theme-global-function-backtotop-smallpc']);

$build_footer_spacing = '';
if(isset($lift_theme['lift-theme-footer-row-spacing'])) {
	$build_footer_spacing_value = '';
	$footer_value['footer_row_unit'] = $lift_theme['lift-theme-footer-row-spacing']['unit'];
	if(isset($lift_theme['lift-theme-footer-row-spacing']['padding-top']) && $lift_theme['lift-theme-footer-row-spacing']['padding-top'] != '') {
		$build_footer_spacing_value .= 'padding-top:'.$lift_theme['lift-theme-footer-row-spacing']['padding-top'].';';
	}
	if(isset($lift_theme['lift-theme-footer-row-spacing']['padding-bottom']) && $lift_theme['lift-theme-footer-row-spacing']['padding-bottom'] != '') {
		$build_footer_spacing_value .= 'padding-bottom:'.$lift_theme['lift-theme-footer-row-spacing']['padding-bottom'].';';
	}
	if(isset($lift_theme['lift-theme-footer-row-spacing']['padding-left']) && $lift_theme['lift-theme-footer-row-spacing']['padding-left'] != '') {
		$build_footer_spacing_value .= 'padding-left:'.$lift_theme['lift-theme-footer-row-spacing']['padding-left'].';';
	}
	if(isset($lift_theme['lift-theme-footer-row-spacing']['padding-right']) && $lift_theme['lift-theme-footer-row-spacing']['padding-right'] != '') {
		$build_footer_spacing_value .= 'padding-right:'.$lift_theme['lift-theme-footer-row-spacing']['padding-right'].';';
	}
	if(isset($lift_theme['lift-theme-footer-row-spacing']['padding-top']) && $lift_theme['lift-theme-footer-row-spacing']['padding-top'] != '' || isset($lift_theme['lift-theme-footer-row-spacing']['padding-bottom']) && $lift_theme['lift-theme-footer-row-spacing']['padding-bottom'] != '' || isset($lift_theme['lift-theme-footer-row-spacing']['padding-left']) && $lift_theme['lift-theme-footer-row-spacing']['padding-left'] != '' || isset($lift_theme['lift-theme-footer-row-spacing']['padding-right']) && $lift_theme['lift-theme-footer-row-spacing']['padding-right'] != '') {
		$build_footer_spacing = ' style="'.$build_footer_spacing_value.'"';
	}
}
?>

<!-- LIFT FOOTER  -->
<?php if(is_active_sidebar('sidebar-1')) {?>
<footer id="footer" class="footer lift-footer">
	<div class="footer-wrapper"<?= isset($footer_value['footer_row']) && $footer_value['footer_row'] === '1' ? $build_footer_spacing : ''?>>
		<div class="container<?= isset($footer_value['footer_style']) && $footer_value['footer_style'] === '1' ? '-fluid': ''?>">
			<div class="row<?= isset($footer_value['footer_gutters']) && $footer_value['footer_gutters'] !== '-1' ? ' gx-'.$footer_value['footer_gutters'] : ''?>">
				<?php
					for ($i=1; $i <= $footer_value['footer_col'] ; $i++) { 
				?>
					<?php if(is_active_sidebar( 'sidebar-'.$i )) {?>
						<div class="col-12 <?=$footer_value['footer_column_'.$i]?>"><?php dynamic_sidebar( 'sidebar-'.$i ); ?></div>
					<?php } ?>
				<?php
					}
				?>
			</div>
		</div>
	</div>
</footer>
<?php } ?>

<!-- LIFT BACK TO TOP -->
<?php if($global_value['backtotop']) {?>
	<?php
		$displaybtt = 'position-fixed';
		if($global_value['backtotop_smallpc']) {
			$displaybtt .= ' d-lg-block';
			if($global_value['backtotop_tablet']) {
				$displaybtt .= ' d-md-block';
				if($global_value['backtotop_phalet']) {
					$displaybtt .= ' d-sm-block';
					if($global_value['backtotop_mobile']) {
						$displaybtt .= ' d-block';
					}
				}
			}
		}
		$displaybtt .= ' d-xl-block';
	?>
	<a id="backtotop" class="<?=$displaybtt;?>"><i class="fa fa-angle-up"></i></a>
<?php } ?>
