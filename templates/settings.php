<div class="wrap">
    <h2>Dynamic Content Settings</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('wp_dc_plugin-group'); ?>
        <?php @do_settings_fields('wp_dc_plugin-group'); ?>

        <table class="form-table">  
            <tr valign="top">
                <th scope="row"><label for="dc_content_type">Content Type (to retrieve)</label></th>
                <td><input type="text" name="dc_content_type" id="dc_content_type" value="<?php echo get_option('dc_content_type'); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="setting_b">Setting B</label></th>
                <td><input type="text" name="setting_b" id="setting_b" value="<?php echo get_option('setting_b'); ?>" /></td>
            </tr>
        </table>

        <?php @submit_button(); ?>
    </form>
</div>
