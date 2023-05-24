<?php
	/**
	 * The template for the panel header area.
	 *
	 * Override this template by specifying the path where it is stored (templates_path) in your Redux config.
	 *
	 * @author 	Redux Framework
	 * @package 	ReduxFramework/Templates
     * @version:    3.5.4.18
	 */

    $tip_title  = esc_html__('Developer Mode Enabled', 'corona');

    if ($this->parent->args_class->dev_mode_forced) {
        $is_debug       = false;
        $is_localhost   = false;

        $debug_bit = '';
        if (Redux_Helpers::isWpDebug ()) {
            $is_debug = true;
            $debug_bit = esc_html__('WP_DEBUG is enabled', 'corona');
        }

        $localhost_bit = '';
        if (Redux_Helpers::isLocalHost ()) {
            $is_localhost = true;
            $localhost_bit = esc_html__('you are working in a localhost environment', 'corona');
        }

        $conjunction_bit = '';
        if ($is_localhost && $is_debug) {
            $conjunction_bit = ' ' . esc_html__('and', 'corona') . ' ';
        }

        $tip_msg    = esc_html__('Redux has enabled developer mode because', 'corona') . ' ' . $debug_bit . $conjunction_bit . $localhost_bit . '.';
    } else {
        $tip_msg    = esc_html__('If you are not a developer, your theme/plugin author shipped with developer mode enabled. Contact them directly to fix it.', 'corona');
    }

    ?>
    <div id="redux-header">
       <?php if ( ! empty( $this->parent->args['display_name'] ) ) { ?>
       <div class="display_header">

        <h2><i class="fa fa-cog" aria-hidden="true"></i><?php echo esc_html($this->parent->args['display_name']); ?>

     </h2>
         <?php if ( ! empty( $this->parent->args['display_version'] ) ) { ?>
         <span class="redux-theme-version"> <?php echo esc_attr($this->parent->args['display_version']); ?></span>
         <?php } ?>
 </div>
 <?php } ?>
 <div class="clear"></div>
</div>