<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

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
	$iframe_errors = array();
	$iframe_success = '';
	$iframe_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_iframe_TABLE."`
		WHERE `iframe_id` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'iframe_path' => $data['iframe_path'],
		'iframe_link' => $data['iframe_link'],
		'iframe_target' => $data['iframe_target'],
		'iframe_title' => $data['iframe_title'],
		'iframe_order' => $data['iframe_order'],
		'iframe_status' => $data['iframe_status'],
		'iframe_type' => $data['iframe_type']
	);
}
// Form submitted, check the data
if (isset($_POST['iframe_form_submit']) && $_POST['iframe_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('iframe_form_edit');
	
	$form['iframe_path'] 	= isset($_POST['iframe_path']) ? sanitize_text_field($_POST['iframe_path']) : '';
	$form['iframe_path']	= esc_url_raw($form['iframe_path']);
	if ($form['iframe_path'] == '')
	{
		$iframe_errors[] = __('Please select valid image.', 'wp-iframe-images-gallery');
		$iframe_error_found = TRUE;
	}

	$form['iframe_link'] 	= isset($_POST['iframe_link']) ? sanitize_text_field($_POST['iframe_link']) : '';
	$form['iframe_link']	= esc_url_raw($form['iframe_link']);
	if ($form['iframe_link'] == '')
	{
		$iframe_errors[] = __('Please enter the target link.', 'wp-iframe-images-gallery');
		$iframe_error_found = TRUE;
	}
	
	$form['iframe_target'] = isset($_POST['iframe_target']) ? sanitize_text_field($_POST['iframe_target']) : '';
	if($form['iframe_target'] != "_blank" && $form['iframe_target'] != "_parent" && $form['iframe_target'] != "_self" && $form['iframe_target'] != "_new")
	{
		$form['iframe_target'] = "_blank";
	}
	
	$form['iframe_title'] = isset($_POST['iframe_title']) ? sanitize_text_field($_POST['iframe_title']) : '';
	
	$form['iframe_order'] = isset($_POST['iframe_order']) ? sanitize_text_field($_POST['iframe_order']) : '0';
	if(!is_numeric($form['iframe_order'])) { $form['iframe_order'] = 0; }
	
	$form['iframe_status'] = isset($_POST['iframe_status']) ? sanitize_text_field($_POST['iframe_status']) : '';
	if($form['iframe_status'] != "YES" && $form['iframe_status'] != "NO")
	{
		$form['iframe_status'] = "YES";
	}
	
	$form['iframe_type'] = isset($_POST['iframe_type']) ? sanitize_text_field($_POST['iframe_type']) : '';

	//	No errors found, we can add this Group to the table
	if ($iframe_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_iframe_TABLE."`
				SET `iframe_path` = %s,
				`iframe_link` = %s,
				`iframe_target` = %s,
				`iframe_title` = %s,
				`iframe_order` = %d,
				`iframe_status` = %s,
				`iframe_type` = %s
				WHERE iframe_id = %d
				LIMIT 1",
				array($form['iframe_path'], $form['iframe_link'], $form['iframe_target'], 
						$form['iframe_title'], $form['iframe_order'], $form['iframe_status'], $form['iframe_type'], $did)
			);
		$wpdb->query($sSql);
		$iframe_success = __('Image details was successfully updated.', 'wp-iframe-images-gallery');
	}
}

if ($iframe_error_found == TRUE && isset($iframe_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $iframe_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($iframe_error_found == FALSE && strlen($iframe_success) > 0)
{
	?>
	<div class="updated fade">
		<p><strong><?php echo $iframe_success; ?> 
		<a href="<?php echo WP_iframe_ADMIN_URL; ?>"><?php _e('Click here', 'wp-iframe-images-gallery'); ?></a> <?php _e('to view the details', 'wp-iframe-images-gallery'); ?></strong></p>
	</div>
	<?php
}
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            // Let's assign the url value to the input field
            $('#iframe_path').val(img_imageurl);
			$('#iframe_title').val(img_imagetitle);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery'); // jQuery
wp_enqueue_media(); // This will enqueue the Media Uploader script
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('iFrame Images Gallery', 'wp-iframe-images-gallery'); ?></h2>
	<form name="iframe_form" method="post" action="#" onsubmit="return iframe_submit()"  >
      <h3><?php _e('Update image details', 'wp-iframe-images-gallery'); ?></h3>
      <label for="tag-image"><?php _e('Enter image path', 'wp-iframe-images-gallery'); ?></label>
      <input name="iframe_path" type="text" id="iframe_path" value="<?php echo $form['iframe_path']; ?>" size="80" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the picture located on the internet. Image link should start with http or https.', 'wp-iframe-images-gallery'); ?></p>
      <label for="tag-link"><?php _e('Enter target link', 'wp-iframe-images-gallery'); ?></label>
      <input name="iframe_link" type="text" id="iframe_link" value="<?php echo $form['iframe_link']; ?>" size="80" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them', 'wp-iframe-images-gallery'); ?></p>
      <label for="tag-target"><?php _e('Select target option', 'wp-iframe-images-gallery'); ?></label>
      <select name="iframe_target" id="iframe_target">
        <option value='_blank' <?php if($form['iframe_target']=='_blank') { echo 'selected' ; } ?>>_blank</option>
        <option value='_parent' <?php if($form['iframe_target']=='_parent') { echo 'selected' ; } ?>>_parent</option>
        <option value='_self' <?php if($form['iframe_target']=='_self') { echo 'selected' ; } ?>>_self</option>
        <option value='_new' <?php if($form['iframe_target']=='_new') { echo 'selected' ; } ?>>_new</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'wp-iframe-images-gallery'); ?></p>
      <label for="tag-title"><?php _e('Enter image reference', 'wp-iframe-images-gallery'); ?></label>
      <input name="iframe_title" type="text" id="iframe_title" value="<?php echo esc_html(stripslashes($form['iframe_title'])); ?>" size="80" />
      <p><?php _e('Enter image reference. This is only for reference.', 'wp-iframe-images-gallery'); ?></p>
      <label for="tag-select-gallery-group"><?php _e('Select gallery type/group', 'wp-iframe-images-gallery'); ?></label>
	  <select name="iframe_type" id="iframe_type">
		<?php
		$sSql = "SELECT distinct(iframe_type) as iframe_type FROM `".WP_iframe_TABLE."` order by iframe_type";
		$myDistinctData = array();
		$arrDistinctDatas = array();
		$myDistinctData = $wpdb->get_results($sSql, ARRAY_A);
		$i = 0;
		foreach ($myDistinctData as $DistinctData)
		{
			$arrDistinctData[$i]["iframe_type"] = strtoupper($DistinctData['iframe_type']);
			$i = $i+1;
		}
		for($j=$i; $j<$i+5; $j++)
		{
			$arrDistinctData[$j]["iframe_type"] = "GROUP" . $j;
		}
		$selected = "";
		$arrDistinctDatas = array_unique($arrDistinctData, SORT_REGULAR);
		foreach ($arrDistinctDatas as $arrDistinct)
		{
			if(strtoupper($form['iframe_type']) == strtoupper($arrDistinct["iframe_type"]) ) 
			{ 
				$selected = "selected"; 
			}
			?>
			<option value='<?php echo $arrDistinct["iframe_type"]; ?>' <?php echo $selected; ?>><?php echo strtoupper($arrDistinct["iframe_type"]); ?></option>
			<?php
			$selected = "";
		}
		?>
		</select>
      <p><?php _e('This is to group the images. Select your slideshow group.', 'wp-iframe-images-gallery'); ?></p>
      <label for="tag-display-status"><?php _e('Display status', 'wp-iframe-images-gallery'); ?></label>
      <select name="iframe_status" id="iframe_status">
        <option value='YES' <?php if($form['iframe_status']=='YES') { echo 'selected' ; } ?>>Yes</option>
        <option value='NO' <?php if($form['iframe_status']=='NO') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Do you want the picture to show in your galler?', 'wp-iframe-images-gallery'); ?></p>
      <label for="tag-display-order">Display order</label>
      <input name="iframe_order" type="text" id="iframe_order" size="10" value="<?php echo $form['iframe_order']; ?>" maxlength="3" />
      <p><?php _e('What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.', 'wp-iframe-images-gallery'); ?></p>
      <input name="iframe_id" id="iframe_id" type="hidden" value="">
      <input type="hidden" name="iframe_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Update Details', 'wp-iframe-images-gallery'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="iframe_redirect()" value="<?php _e('Cancel', 'wp-iframe-images-gallery'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="iframe_help()" value="<?php _e('Help', 'wp-iframe-images-gallery'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('iframe_form_edit'); ?>
    </form>
</div>
</div>