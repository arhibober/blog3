<?php
/**
* Displays the content on the plugin settings page
*/

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Fcbkbttn_Settings_Tabs' ) ) {
	class Fcbkbttn_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		* Constructor.
		*
		* @access public
		*
		* @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		*
		* @param string $plugin_basename
		*/
		public function __construct( $plugin_basename ) {
			global $fcbkbttn_options, $fcbkbttn_plugin_info;

			$tabs = array(
				'settings'		=> array( 'label' => __( 'Settings', 'facebook-button-plugin' ) ),
				'display'		=> array( 'label' => __( 'Display', 'facebook-button-plugin' ), 'is_pro' => 1 ),
				'misc'			=> array( 'label' => __( 'Misc', 'facebook-button-plugin' ) ),
				'custom_code'	=> array( 'label' => __( 'Custom Code', 'facebook-button-plugin' ) ),
				'license'		=> array( 'label' => __( 'License Key', 'facebook-button-plugin' ) ),
			);

			parent::__construct( array(
				'plugin_basename'	=> $plugin_basename,
				'plugins_info'		=> $fcbkbttn_plugin_info,
				'prefix'			=> 'fcbkbttn',
				'default_options'	=> fcbkbttn_get_options_default(),
				'options'			=> $fcbkbttn_options,
				'is_network_options'=> is_network_admin(),
				'tabs'				=> $tabs,
				'doc_link'			=> 'https://docs.google.com/document/d/1gy5uDVoebmYRUvlKRwBmc97jdJFz7GvUCtXy3L7r_Yg/',
				'wp_slug'			=> 'facebook-button-plugin',
				'pro_page'			=> 'admin.php?page=facebook-button-pro.php',
				'bws_license_plugin'=> 'facebook-button-pro/facebook-button-pro.php',
				'link_key'			=> '427287ceae749cbd015b4bba6041c4b8',
				'link_pn'			=> '78'
			) );

			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'additional_misc_options' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			add_action( get_parent_class( $this ) . '_display_second_postbox', array( $this, 'display_second_postbox' ) );
		}

		/**
		* Save plugin options to the database
		* @access public
		* @param  void
		* @return array    The action results
		*/
		public function save_options() {

			/* Takes all the changed settings on the plugin's admin page and saves them in array 'fcbkbttn_options'. */
			if ( ! empty( $_REQUEST['fcbkbttn_id'] ) ) {
				$this->options['id']	= stripslashes( esc_html( $_REQUEST['fcbkbttn_id'] ) );
			} else {
				$this->options['id']	= 1443946719181573;
			}

			$this->options['link']				= stripslashes( esc_html( $_REQUEST['fcbkbttn_link'] ) );
			$this->options['link']				= str_replace( 'https://www.facebook.com/profile.php?id=', '', $this->options['link'] );
			$this->options['link']				= str_replace( 'https://www.facebook.com/', '', $this->options['link'] );

			$this->options['size']				= isset( $_REQUEST['fcbkbttn_size'] ) ? $_REQUEST['fcbkbttn_size'] : $this->options['size'];
			$this->options['where']				= isset( $_REQUEST['fcbkbttn_where'] ) ? $_REQUEST['fcbkbttn_where'] : array();
			$this->options['location']			= isset( $_REQUEST['fcbkbttn_location'] ) ? $_REQUEST['fcbkbttn_location'] : $this->options['location'];
			$this->options['display_option']	= isset( $_REQUEST['fcbkbttn_display_option'] ) ? $_REQUEST['fcbkbttn_display_option'] : $this->options['display_option'];

			if ( 'standard' == $this->options['display_option'] ) {
				$img_name =
					'large' == $this->options['size'] ?
					'large-facebook-ico' :
					'standard-facebook-ico';

				$this->options['fb_img_link']		= plugins_url( 'images/' . $img_name . '.png', dirname( __FILE__ ) );
			}

			$this->options['my_page']				= isset( $_REQUEST['fcbkbttn_my_page'] ) ? 1 : 0;
			$this->options['like']					= isset( $_REQUEST['fcbkbttn_like'] ) ? 1 : 0;
			$this->options['share']					= isset( $_REQUEST['fcbkbttn_share'] ) ? 1 : 0;
			$this->options['layout_like_option']	= isset( $_REQUEST['fcbkbttn_like_layout'] ) ? $_REQUEST['fcbkbttn_like_layout'] : $this->options['layout_like_option'];
			$this->options['layout_share_option']	= isset( $_REQUEST['fcbkbttn_share_layout'] ) ? $_REQUEST['fcbkbttn_share_layout'] : $this->options['layout_like_option'];
			$this->options['faces']					= isset( $_REQUEST['fcbkbttn_faces'] ) ? 1 : 0;
			$this->options['like_action']			= isset( $_REQUEST['fcbkbttn_like_action'] ) ? $_REQUEST['fcbkbttn_like_action'] : $this->options['like_action'];
			$this->options['color_scheme']			= isset( $_REQUEST['fcbkbttn_color_scheme'] ) ? $_REQUEST['fcbkbttn_color_scheme'] : $this->options['color_scheme'];
			$this->options['width']					= intval( $_REQUEST['fcbkbttn_width'] );
			$this->options['locale']				= isset( $_REQUEST['fcbkbttn_locale'] ) ? $_REQUEST['fcbkbttn_locale'] : $this->options['locale'];
			$this->options['html5']					= isset( $_REQUEST['fcbkbttn_html5'] ) ? $_REQUEST['fcbkbttn_html5'] : $this->options['html5'];
			if ( isset( $_FILES['fcbkbttn_uploadfile']['tmp_name'] ) && $_FILES['fcbkbttn_uploadfile']['tmp_name'] != "" ) {
				$this->options['count_icon']		= $this->options['count_icon'] + 1;
				$file_ext = wp_check_filetype( $_FILES['fcbkbttn_uploadfile']['name'] );
				$this->options['extention']			= $file_ext['ext'];
			}

			if ( 2 < $this->options['count_icon'] ) {
				$this->options['count_icon'] = 1;
			}

			$this->options['use_multilanguage_locale'] = isset( $_REQUEST['fcbkbttn_use_multilanguage_locale'] ) ? 1 : 0;
			$this->options['display_for_excerpt'] = isset( $_REQUEST['fcbkbttn_display_for_excerpt'] ) ? 1 : 0;

			$this->options = apply_filters( 'fcbkbttn_before_save_options', $this->options );
			update_option( 'fcbkbttn_options', $this->options );
			$message = __( "Settings saved", 'facebook-button-plugin' );

			if ( ! empty( $_FILES['fcbkbttn_uploadfile']['tmp_name'] ) ) {
				if ( ! $this->upload_dir )
					$this->upload_dir = wp_upload_dir();

				if ( ! $this->upload_dir["error"] ) {
					$fcbkbttn_cstm_mg_folder = $this->upload_dir['basedir'] . '/facebook-image';
					if ( ! is_dir( $fcbkbttn_cstm_mg_folder ) ) {
						wp_mkdir_p( $fcbkbttn_cstm_mg_folder, 0755 );
					}
				}
				$max_image_width	= 100;
				$max_image_height	= 40;
				$max_image_size		= 32 * 1024;
				$valid_types		= array( 'jpg', 'jpeg', 'png' );
				/* Construction to rename downloading file */
				$new_name			= 'facebook-ico' . $this->options['count_icon'];
				$new_ext			= wp_check_filetype( $_FILES['fcbkbttn_uploadfile']['name'] );
				$namefile			= $new_name . '.' . $new_ext['ext'];
				$uploadfile			= $fcbkbttn_cstm_mg_folder . '/' . $namefile;

				/* Checks is file download initiated by user */
				if ( isset( $_FILES['fcbkbttn_uploadfile'] ) && 'custom' == $_REQUEST['fcbkbttn_display_option'] ) {
					/* Checking is allowed download file given parameters */
					if ( is_uploaded_file( $_FILES['fcbkbttn_uploadfile']['tmp_name'] ) ) {
						$filename	= $_FILES['fcbkbttn_uploadfile']['tmp_name'];
						$ext		= substr( $_FILES['fcbkbttn_uploadfile']['name'], 1 + strrpos( $_FILES['fcbkbttn_uploadfile']['name'], '.' ) );
						if ( filesize( $filename ) > $max_image_size ) {
							$error	= __( "Error: File size must not exceed 32KB", 'facebook-button-plugin' );
						}
						elseif ( ! in_array( strtolower( $ext ), $valid_types ) ) {
							$error	= __( "Error: Invalid file type", 'facebook-button-plugin' );
						} else {
							$size	= GetImageSize( $filename );
							if ( $size && $size[0] <= $max_image_width && $size[1] <= $max_image_height ) {
								/* If file satisfies requirements, we will move them from temp to your plugin folder and rename to 'facebook_ico.jpg' */
								if ( move_uploaded_file( $_FILES['fcbkbttn_uploadfile']['tmp_name'], $uploadfile ) ) {
									$message .= '. ' . __( "Upload successful.", 'facebook-button-plugin' );

									if ( 'custom' == $this->options['display_option'] ) {
										$this->options['fb_img_link'] = $this->upload_dir['baseurl'] . '/facebook-image/facebook-ico' . $this->options['count_icon'] . '.' . $this->options['extention'];

										update_option( 'fcbkbttn_options', $this->options );
									}
								} else {
									$error = __( "Error: Failed to move file.", 'facebook-button-plugin' );
								}
							} else {
								$error = __( "Error: Check image width or height.", 'facebook-button-plugin' );
							}
						}
					} else {
						$error = __( "Uploading Error: Check image properties", 'facebook-button-plugin' );
					}
				}
			}

			return compact( 'message', 'notice', 'error' );
		}

		/**
		*
		*/
		public function tab_settings() {
			global $fcbkbttn_lang_codes, $wp_version;
			if ( ! $this->upload_dir ) {
				$this->upload_dir = wp_upload_dir();
			}

			if ( ! $this->all_plugins ) {
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				$this->all_plugins = get_plugins();
			} ?>
			<h3 class="bws_tab_label"><?php _e( 'Facebook Button Settings', 'facebook-button-plugin' ); ?></h3>
			<?php $this->help_phrase(); 
			$output_key = ( 1443946719181573 != $this->options['id'] ) ? $this->options['id'] : ''; ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'General', 'facebook-button-plugin' ); ?></div>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Change App ID', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name='fcbkbttn_id' type='text' maxlength='250' value='<?php echo $output_key; ?>' />
						<br />
						<span class="bws_info"><?php _e( 'You can use standard App ID or', 'facebook-button-plugin' ); ?>&nbsp;<a href="https://developers.facebook.com/quickstarts/?platform=web" target="_blank"><?php _e( 'create a new one', 'facebook-button-plugin' ); ?></a><br /><?php _e( ' Leave blank to use standard App ID.', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Buttons', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name='fcbkbttn_my_page' type='checkbox' value='1' <?php checked( $this->options['my_page'] ); ?> /> <?php _e( 'Profile URL', 'facebook-button-plugin' ); ?></label><br />
							<label><input name='fcbkbttn_like' type='checkbox' value='1' <?php checked( $this->options['like'] ); ?> /> <?php _e( "Like", 'facebook-button-plugin' ); ?></label><br />
							<label><input name='fcbkbttn_share' type='checkbox' value='1' <?php checked( $this->options['share'] ); ?> /> <?php _e( "Share", 'facebook-button-plugin' ); ?></label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( "Buttons Size", 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="fcbkbttn_size" type="radio" value="small" <?php checked( 'small', $this->options['size'] ); ?> /> <?php _e( 'Small', 'facebook-button-plugin' ); ?></label><br />
							<label><input name="fcbkbttn_size" type="radio" value="large" <?php checked( 'large', $this->options['size'] ); ?> /> <?php _e( 'Large', 'facebook-button-plugin' ); ?></label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Position', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="checkbox" name="fcbkbttn_where[]" value="before" <?php checked( in_array( 'before', $this->options['where'] ) ); ?> />
								<?php _e( 'Before content', 'facebook-button-plugin' ); ?></option>
							</label>
							<br />
							<label>
								<input type="checkbox" name="fcbkbttn_where[]" value="after" <?php checked( in_array( 'after', $this->options['where'] ) ); ?> />
								<?php _e( 'After content', 'facebook-button-plugin' ); ?></option>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Buttons Align', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name="fcbkbttn_location" type="radio" value="right" <?php checked( 'right', $this->options['location'] ); ?> /> <?php _e( 'Right', 'facebook-button-plugin' ); ?></label><br />
							<label><input name="fcbkbttn_location" type="radio" value="middle" <?php checked( 'middle', $this->options['location'] ); ?> /> <?php _e( 'Center', 'facebook-button-plugin' ); ?></label><br />
							<label><input name="fcbkbttn_location" type="radio" value="left" <?php checked( 'left', $this->options['location'] ); ?> /> <?php _e( 'Left', 'facebook-button-plugin' ); ?></label><br />
						</fieldset>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Language', 'facebook-button-plugin' ); ?></th>
					<td>
						<select name="fcbkbttn_locale">
							<?php foreach ( $fcbkbttn_lang_codes as $key => $val ) {
								echo '<option value="' . $key . '"';
								if ( $key == $this->options['locale'] )
									echo ' selected="selected"';
								echo '>' . esc_html ( $val ) . '</option>';
							} ?>
						</select>
						<br />
						<span class="bws_info"><?php _e( 'Select the default language for Facebook button(-s).', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<tr>
					<th>Multilanguage</th>
					<td>
						<?php if ( array_key_exists( 'multilanguage/multilanguage.php', $this->all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $this->all_plugins ) ) {
							if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) { ?>
								<input type="checkbox" name="fcbkbttn_use_multilanguage_locale" value="1" <?php checked( $this->options["use_multilanguage_locale"] ); ?> />
								<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'facebook-button-plugin' ); ?></span>
							<?php } else { ?>
								<input disabled="disabled" type="checkbox" name="fcbkbttn_use_multilanguage_locale" value="1" />
								<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'facebook-button-plugin' ); ?> <a href="<?php echo bloginfo( "url" ); ?>/wp-admin/plugins.php" target="_blank"><?php _e( 'Activate', 'facebook-button-plugin' ); ?></a></span>
							<?php }
						} else { ?>
							<input disabled="disabled" type="checkbox" name="fcbkbttn_use_multilanguage_locale" value="1" />
							<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'facebook-button-plugin' ); ?> <a href="https://bestwebsoft.com/products/wordpress/plugins/multilanguage/?k=196fb3bb74b6e8b1e08f92cddfd54313&pn=78&v=<?php echo $this->plugins_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank"><?php _e( 'Install Now', 'facebook-button-plugin' ); ?></a></span>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th><?php _e( 'Excerpt', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name='fcbkbttn_display_for_excerpt' type='checkbox' value='1' <?php checked( $this->options['display_for_excerpt'] ); ?> /> <span class="bws_info"><?php _e( 'Enable to display buttons in excerpt.', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<?php do_action( 'fcbkbttn_settings_page_action', $this->options ); ?>
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'facebook-button-plugin' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr>
								<th><?php _e( 'Meta Image', 'facebook-button-plugin' ); ?></th>
								<td>
									<fieldset>
										<label>
											<input disabled="disabled" type="radio" name="fcbkbttn_meta_image" value="featured_image" checked="checked" />
											<?php _e( 'Featured Image', 'facebook-button-plugin' ); ?>
										</label><br />
										<label>
											<input disabled="disabled" type="radio" name="fcbkbttn_meta_image" value="custom_image" />
											<?php _e( 'Custom Image', 'facebook-button-plugin' ); ?> <span class="bws_info">(<?php _e( 'This image will be used for all posts', 'facebook-button-plugin' ); ?>)</span>
										</label><br />
										<input disabled="disabled" name="fcbkbttn_meta_uploadfile" type="file" />
									</fieldset>
								</td>
							</tr>
							<tr>
								<th><?php _e( 'Meta Description', 'facebook-button-plugin' ); ?></th>
								<td>
									<textarea disabled="disabled" name="fcbkbttn_meta_description_custom"></textarea>
									<br />
									<span class="bws_info"><?php _e( 'This description will be used for all pages and posts. Leave blank to use page description.', 'facebook-button-plugin' ); ?></span>
								</td>
							</tr>
						</table>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
			<div class="bws_tab_sub_label fcbkbttn_my_page_enabled"><?php _e( 'Profile URL Button', 'facebook-button-plugin' ); ?></div>
			<table class="form-table fcbkbttn_my_page_enabled">
				<tr>
					<th scope="row"><?php _e( 'Facebook ID or Username', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name='fcbkbttn_link' type='text' maxlength='250' value='<?php echo $this->options['link']; ?>' />
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Profile Button Image', 'facebook-button-plugin' ); ?>
					</th>
					<td>
						<?php if ( scandir( $this->upload_dir['basedir'] ) && is_writable( $this->upload_dir['basedir'] ) ) { ?>
							<fieldset>
								<label>
									<input type="radio" name="fcbkbttn_display_option" value="standard" <?php checked( 'standard', $this->options['display_option'] ); ?> />
									<?php _e( 'Default', 'facebook-button-plugin' ); ?>
								</label>
								<br />
								<label>
									<input type="radio" name="fcbkbttn_display_option" value="custom" <?php checked( 'custom', $this->options['display_option'] ); ?> />
									<?php _e( 'Custom image', 'facebook-button-plugin' ); ?>
								</label>
							</fieldset>
						<?php } else {
							echo __( 'To use custom image, You need to setup permissions for upload directory of your site', 'facebook-button-plugin' ) . " - " . $this->upload_dir['basedir'];
						} ?>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php _e( 'Current image', 'facebook-button-plugin' ); ?>:
						<img src="<?php echo $this->options['fb_img_link']; ?>" style="vertical-align: middle;" />
					</td>
				</tr>
				<tr id="fcbkbttn_display_option_custom">
					<th></th>
					<td>
						<input name="fcbkbttn_uploadfile" type="file" /><br />
						<span class="bws_info"><?php _e( 'Image requirements: max image width: 100px; max image height: 40px; max image size: 32Kb; image types: "jpg", "jpeg", "png".', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
			</table>
			<div class="bws_tab_sub_label fcbkbttn_share_like_block"><?php _e( 'Like&Share Buttons', 'facebook-button-plugin' ); ?></div>
			<table class="form-table">
				<tr class="fcbkbttn_like_enabled">
					<th><?php _e( 'Like Button Layout', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset class="fcbkbttn_layout_option">
							<label class="fcbkbttn_like_layout">
								<input id="fcbkbttn_standard_layout" type="radio" name="fcbkbttn_like_layout" value="standard" <?php checked( 'standard', $this->options['layout_like_option'] ); ?> />
								Standard
							</label>
							<label>
								<input id="fcbkbttn_box_count_layout" type="radio" name="fcbkbttn_like_layout" value="box_count" <?php checked( 'box_count', $this->options['layout_like_option'] ); ?> />
								Box count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_like_layout" value="button_count" <?php checked( 'button_count', $this->options['layout_like_option'] ); ?> />
								Button count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_like_layout" value="button" <?php checked( 'button', $this->options['layout_like_option'] ); ?> />
								Button
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_share_enabled">
					<th><?php _e( 'Share Button Layout', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset class="fcbkbttn_layout_option">
							<label>
								<input id="fcbkbttn_box_count_layout" type="radio" name="fcbkbttn_share_layout" <?php checked( 'box_count', $this->options['layout_share_option'] ); ?> value="box_count" />
								Box count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_share_layout" value="button_count" <?php checked( 'button_count', $this->options['layout_share_option'] ); ?> />
								Button count
							</label>
							<label>
								<input type="radio" name="fcbkbttn_share_layout" value="button" <?php checked( 'button', $this->options['layout_share_option'] ); ?> />
								Button
							</label>
							<label class="fcbkbttn_share_layout">
								<input type="radio" name="fcbkbttn_share_layout" value="icon_link" <?php checked( 'icon_link', $this->options['layout_share_option'] ); ?> />
								Icon link
							</label>
							<label class="fcbkbttn_share_layout">
								<input type="radio" name="fcbkbttn_share_layout" value="icon" <?php checked( 'icon', $this->options['layout_share_option'] ); ?> />
								Icon
							</label>
							<label class="fcbkbttn_share_layout">
								<input type="radio" name="fcbkbttn_share_layout" value="link" <?php checked( 'link', $this->options['layout_share_option'] ); ?> />
								Link
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_like_enabled">
					<th><?php _e( 'Like Button Action', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="fcbkbttn_like_action" value="like" <?php checked( 'like', $this->options['like_action'] ); ?> />
								<?php _e( 'Like', 'facebook-button-plugin' ); ?>
							</label>
							<br />
							<label>
								<input type="radio" name="fcbkbttn_like_action" value="recommend" <?php checked( 'recommend', $this->options['like_action'] ); ?> />
								<?php _e( 'Recommend', 'facebook-button-plugin' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_like_standard_layout">
					<th><?php _e( 'Friends Faces', 'facebook-button-plugin' ); ?></th>
					<td>
						<input name="fcbkbttn_faces" type='checkbox' value="1" <?php checked( $this->options['faces'] ); ?> />
						<span class="bws_info"><?php _e( 'Enable to show faces of your friends who submitted the button.', 'facebook-button-plugin' ); ?></span>
					</td>
				</tr>
				<tr class="fcbkbttn_like_standard_layout">
					<th><?php _e( 'Layout Width', 'facebook-button-plugin' ); ?></th>
					<td>
						<label>
							<input name="fcbkbttn_width" type="number" step="1" min="225" max="450" value="<?php echo $this->options['width']; ?>" />
							<?php _e( 'px', 'facebook-button-plugin' ); ?>
						</label>
					</td>
				</tr>
				<tr class="fcbkbttn_like_standard_layout">
					<th><?php _e( 'Theme', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label>
								<input type="radio" name="fcbkbttn_color_scheme" value="light" <?php checked( 'light', $this->options['color_scheme'] ) ; ?> />
								<?php _e( 'Light', 'facebook-button-plugin' ); ?>
							</label>
							<br />
							<label>
								<input type="radio" name="fcbkbttn_color_scheme" value="dark" <?php checked( 'dark', $this->options['color_scheme'] ); ?> />
								<?php _e( 'Dark', 'facebook-button-plugin' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="fcbkbttn_like_enabled">
					<th scope="row"><?php _e( 'Like Button HTML Tag', 'facebook-button-plugin' ); ?></th>
					<td>
						<fieldset>
							<label><input name='fcbkbttn_html5' type='radio' value='0' <?php checked( '0', $this->options['html5'] ); ?> /><?php echo "&lt;fb:like&gt;"; ?>
							</label><br />
							<label><input name='fcbkbttn_html5' type='radio' value='1' <?php checked( '1', $this->options['html5'] ); ?> /><?php echo "&lt;div&gt;"; ?>
							</label><br />
							<span class="bws_info"><?php printf( __( "Tag %s can be used to improve website code validation.", 'facebook-button-plugin' ), '&lt;div&gt;' ); ?></span>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc fcbkbttn_like_enabled">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'facebook-button-plugin' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr>
								<th><?php _e( 'URL to Like', 'facebook-button-plugin' ); ?></th>
								<td><input disabled="disabled" name='fcbkbttn_url_to_like' type='text' value="" /><br />
									<span class="bws_info"><?php _e( 'Leave blank to use current page URL.', 'facebook-button-plugin' ); ?></span>
								</td>
							</tr>
						</table>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
		<?php }

		/**
		* Display custom options on the 'misc' tab
		* @access public
		*/
		public function additional_misc_options() {
			do_action( 'fcbkbttn_settings_page_misc_action', $this->options );
		}

		/**
		* Display custom metabox
		* @access public
		* @param  void
		* @return array    The action results
		*/
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Facebook Button Shortcode', 'facebook-button-plugin' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Facebook button(-s) to your posts, pages, custom post types or widgets by using the following shortcode:", 'facebook-button-plugin' ); ?>
					<?php bws_shortcode_output( '[fb_button]' ); ?>
				</div>
			</div>
		<?php }

		/**
		* Display custom metabox
		* @access public
		* @param  void
		* @return array    The action results
		*/
		public function display_second_postbox() {
			/*pls */
			if ( ! $this->hide_pro_tabs ) { ?>
				<div class="postbox bws_pro_version_bloc">
					<div class="bws_table_bg"></div>
					<h3 class="hndle">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'facebook-button-plugin' ); ?>"></button>
						<?php _e( 'Facebook Buttons Preview', 'facebook-button-plugin' ); ?>
					</h3>
					<div class="inside">
						<img src='<?php echo plugins_url( 'images/preview.png', dirname( __FILE__ ) ); ?>' />
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php }
		}

		/**
		*
		*/
		public function tab_display() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Display Settings', 'facebook-button-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_pro_version_bloc">
				<div class="bws_pro_version_table_bloc">
					<div class="bws_table_bg"></div>
					<table class="form-table bws_pro_version">
						<tr>
							<td colspan="2">
								<?php _e( 'Please choose the necessary post types (or single pages) where Facebook button will be displayed:', 'facebook-button-plugin' ); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label>
									<input disabled="disabled" checked="checked" type="checkbox" name="jstree_url" value="1" />
									<?php _e( "Show URL for pages", 'facebook-button-plugin' );?>
								</label>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<img class="fcbkbttn_pro_block" src="<?php echo plugins_url( 'images/pro_screen_1.png', dirname( __FILE__ ) ); ?>" alt="<?php _e( "Example of site pages tree", 'facebook-button-plugin' ); ?>" title="<?php _e( "Example of site pages tree", 'facebook-button-plugin' ); ?>" />
							</td>
						</tr>
					</table>
				</div>
				<?php $this->bws_pro_block_links(); ?>
			</div>
		<?php }
	}
}
