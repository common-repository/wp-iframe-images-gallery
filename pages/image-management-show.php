<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_iframe_display']) && $_POST['frm_iframe_display'] == 'yes')
{
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$iframe_success = '';
	$iframe_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$sSql = $wpdb->prepare(
		"SELECT COUNT(*) AS `count` FROM ".WP_iframe_TABLE."
		WHERE `iframe_id` = %d",
		array($did)
	);
	$result = '0';
	$result = $wpdb->get_var($sSql);
	
	if ($result != '1')
	{
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'wp-iframe-images-gallery'); ?></strong></p></div><?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('iframe_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_iframe_TABLE."`
					WHERE `iframe_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$iframe_success_msg = TRUE;
			$iframe_success = __('Selected record was successfully deleted.', 'wp-iframe-images-gallery');
		}
	}
	if ($iframe_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $iframe_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php _e('iFrame Images Gallery', 'wp-iframe-images-gallery'); ?>
	<a class="add-new-h2" href="<?php echo WP_iframe_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'wp-iframe-images-gallery'); ?></a></h2>
    <div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_iframe_TABLE."` order by iframe_type, iframe_order";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<form name="frm_iframe_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Title', 'wp-iframe-images-gallery'); ?></th>
            <th scope="col"><?php _e('Image URL', 'wp-iframe-images-gallery'); ?></th>
			<th scope="col"><?php _e('Target Option', 'wp-iframe-images-gallery'); ?></th>
			<th scope="col"><?php _e('Gallery Group', 'wp-iframe-images-gallery'); ?></th>
			<th scope="col"><?php _e('Display Status', 'wp-iframe-images-gallery'); ?></th>
            <th scope="col"><?php _e('Order', 'wp-iframe-images-gallery'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Title', 'wp-iframe-images-gallery'); ?></th>
            <th scope="col"><?php _e('Image URL', 'wp-iframe-images-gallery'); ?></th>
			<th scope="col"><?php _e('Target Option', 'wp-iframe-images-gallery'); ?></th>
			<th scope="col"><?php _e('Gallery Group', 'wp-iframe-images-gallery'); ?></th>
			<th scope="col"><?php _e('Display Status', 'wp-iframe-images-gallery'); ?></th>
            <th scope="col"><?php _e('Order', 'wp-iframe-images-gallery'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			if(count($myData) > 0 )
			{
				foreach ($myData as $data)
				{
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td>
						<?php echo stripslashes($data['iframe_title']); ?>
						<div class="row-actions">
						<span class="edit"><a title="Edit" href="<?php echo WP_iframe_ADMIN_URL; ?>&amp;ac=edit&amp;did=<?php echo $data['iframe_id']; ?>"><?php _e('Edit', 'wp-iframe-images-gallery'); ?></a> | </span>
						<span class="trash"><a onClick="javascript:iframe_delete('<?php echo $data['iframe_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'wp-iframe-images-gallery'); ?></a></span> 
						</div>
						</td>
						<td>
						<a target="_blank" href="<?php echo $data['iframe_path']; ?>">
							<img src="<?php echo plugins_url(); ?>/wp-iframe-images-gallery/icon/image-icon.png" alt="img"  />
						</a>
						</td>
						<td><?php echo $data['iframe_target']; ?></td>
						<td><?php echo $data['iframe_type']; ?></td>
						<td><?php echo $data['iframe_status']; ?></td>
						<td><?php echo $data['iframe_order']; ?></td>
					</tr>
					<?php 
					$i = $i+1; 
				}
			}
			else
			{
				?><tr><td colspan="6" align="center"><?php _e('No records available', 'wp-iframe-images-gallery'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('iframe_form_show'); ?>
		<input type="hidden" name="frm_iframe_display" value="yes"/>
      </form>	
	<div class="tablenav bottom">
		<a href="<?php echo WP_iframe_ADMIN_URL; ?>&amp;ac=add"><input class="button action" type="button" value="<?php _e('Add New', 'wp-iframe-images-gallery'); ?>" /></a>
		<a target="_blank" href="<?php echo WP_iframe_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help', 'wp-iframe-images-gallery'); ?>" /></a>
		<a target="_blank" href="<?php echo WP_iframe_FAV; ?>"><input class="button button-primary" type="button" value="<?php _e('Short Code', 'wp-iframe-images-gallery'); ?>" /></a>
	</div>
	</div>
</div>