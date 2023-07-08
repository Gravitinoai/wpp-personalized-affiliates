<?php

class PartnersSetting {
    function register($plugin_slug) {
        register_setting($plugin_slug, $plugin_slug . '_partners', 'array');
    }

    function sanitize_partners($partners) {
        return array_values(array_filter($partners, function($partner) {
            return !empty(trim($partner));
        }));
    }
    function html() {
        ?>
        <tr>
            <th colspan="2">
                <h2>Affiliate Partners</h2>
                <p>Describe the partner and add the affiliate link to the text.</p>
            </th>
        </tr>
        <?php

        $partners = get_option('paff_partners', ['']);
        $partners = $this->sanitize_partners($partners);
        foreach ($partners as $index => $partner) {
            ?>
            <tr valign="top">
                <th scope="row">Partner <?php echo $index + 1; ?></th>
                <td>
                    <textarea rows="3" cols="75" name="paff_partners[]"><?php echo esc_textarea($partner); ?></textarea>
                    <button type="button" class="remove-partner">Remove</button>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr valign="top">
            <th scope="row">Add Another Partner</th>
            <td><textarea rows="3" cols="75" name="paff_partners[]"></textarea></td>
        </tr>
        <tr>
            <th></th>
            <td><?php submit_button('+', 'secondary'); ?></td>
        </tr>
        <?php

        // Add jQuery script
        $this->add_script();
    }

    function add_script() {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.remove-partner').click(function() {
                $(this).prev().val('');
                $('form').find('[type="submit"]').click();
            });
        });
        </script>
        <?php
    }
}
