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
                <p>Describe the partner and add the affiliate link to the text. For example:</p>
                <div class="example-card">
                    Brilliant.org is an American for-profit company and associated community that features problems and courses in mathematics, physics, quantitative finance, and computer science. It operates via a freemium business model.
                    <br><br>
                    The best way to learn math and computer science. Guided interactive problem solving that's effective and fun. Master concepts in 15 minutes a day.
                    <br><br>
                    https://brilliant.org/?referral=your-referral-code
                </div>
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
        $this->add_styles();
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

    function add_styles() {
        ?>
        <style type="text/css">
            .example-card {
                background-color: #f9f9f9;
                border: 1px solid #ccc;
                padding: 10px;
                margin-bottom: 10px;
                width: 500px; /* Adjust the width as needed */
            }
        </style>
        <?php
    }
}
