<?php

/**
 * Shortcode Demo Plugin.
 */
class Shortcode_Demo {

	protected static $_instance = null;

	/**
	 * Initialize the class
	 */
	public static function init() {
		do_action( 'shortcode_demo_init' );

		if ( null === static::$_instance ) {
			static::$_instance = new Shortcode_Demo();
		}

		return static::$_instance;
	}

	/**
	 * Attach hooks
	 */
	public function load() {
		// Let people extend this plugin
		do_action( 'shortcode_demo_loaded' );

		// Add the shortcode
		add_shortcode( 'quote', array( $this, 'quote_shortcode' ) );

		// Admin filters
		add_filter( 'mce_external_plugins',  array( $this, 'add_tinymce_plugin' ) );
		add_filter( 'mce_buttons',           array( $this, 'add_tinymce_button' ) );
		add_action( 'mce_css',               array( $this, 'editor_styles' ) );

		add_action( 'register_shortcode_ui', array( $this, 'register_shortcode_for_shortcode_ui' ) );

		// Frontend filters
		add_action( 'wp_enqueue_scripts',   array( $this, 'frontend_styles' ) );
	}

	/**
	 * Activate the plugin
	 *
	 * @return void.
	 */
	public static function activate() {
		// First load the init scripts in case any rewrite functionality is being loaded
		self::init();

		// Rewrite rule for the index page
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 *
	 * Uninstall routines should be in uninstall.php
	 *
	 * @return void.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Return the plugin URL.
	 *
	 * @return The URL to the plugin's directory.
	 */
	protected function get_plugin_url() {
		return SHORTCODE_DEMO_URL;
	}

	/**
	 * Loads our styles on the front-end.
	 */
	public function frontend_styles() {
		wp_enqueue_style(
			'shortcode-demo',
			trailingslashit( $this->get_plugin_url() ) . 'assets/css/shortcode-demo.css',
			SHORTCODE_DEMO_VERSION
		);
	}

	/**
	 * Loads our styles in the TinyMCE editor.
	 */
	public function editor_styles( $mce_css ) {

		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		$mce_css .= trailingslashit( $this->get_plugin_url() ) . 'assets/css/shortcode-demo.css';

		return $mce_css;
	}

	/**
	 * Adds the tinymce plugin to the list of available plugins
	 *
	 * @param array $plugin_array Array of TinyMCE plugins.
	 *
	 * @return array Array of TinyMCE plugins, including this one.
	 */
	public function add_tinymce_plugin( $plugin_array ) {
		$plugin_array['quote'] = trailingslashit( self::get_plugin_url() ) . 'assets/js/quote-tinymce-plugin.js';

		return $plugin_array;
	}

	/**
	 * Add the button to tinymce
	 *
	 * @param array $buttons Buttons being filtered to be used in the TinyMCE editor.
	 *
	 * @return array Buttons being filtered to be used in the TinyMCE editor.
	 */
	public function add_tinymce_button( $buttons ) {
		array_push( $buttons, 'quote' );

		return $buttons;
	}

	/**
	 * Create the quote element with the shortcode content.
	 *
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content.
	 *
	 * @return string Markup for the quote.
	 */
	public function quote_shortcode( $atts, $content = '' ) {

		// Fill out attributes with defaults if not provided
		$atts = shortcode_atts( array(
			'citation' => false,
			'align' => 'full-width',
		), $atts );

		// Create the classes for the element
		$blockquote_class = 'content-quote';

		if ( 'full-width' === $atts['align'] ) {
			$blockquote_class .= ' -full-width';
		} else if ( 'left' === $atts['align'] ) {
			$blockquote_class .= ' -left';
		} else if ( 'right' === $atts['align'] ) {
			$blockquote_class .= ' -right';
		}

		ob_start();

		// Generate the output
		?>
		<blockquote class="<?php echo esc_attr( $blockquote_class ); ?>">
			<p><?php echo esc_html( $content ); ?></p>

			<?php if ( $atts['citation'] ) : ?>
				<cite class="citation">&mdash;&nbsp;<?php echo esc_html( $atts['citation'] ); ?></cite>
			<?php endif; ?>
		</blockquote>
		<?php

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Register the shortcode with Shortcake/Shortcode UI, if it's activated.
	 */
	public function register_shortcode_for_shortcode_ui() {

		// Return if this somehow gets called and Shortcake isn't activated
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		$args = array(
			'label' => 'Quote',
			'listItemImage' => plugins_url( '../assets/images/wordpress.png', dirname( __FILE__ ) ),
			'inner_content' => array( 'label' => 'Quote' ),
			'attrs' => array(
				array(
					'label'       => 'Citation',
					'attr'        => 'citation',
					'type'        => 'text',
					'placeholder' => 'Name',
				),
				array(
					'label'       => 'Alignment',
					'description' => 'Pull the quote to the left or right, or let it take up the full width of the content.',
					'attr'        => 'align',
					'type'        => 'select',
					'options'     => array(
						'full-width' => 'Full-Width',
						'left'       => 'Pull Left',
						'right'      => 'Pull Right',
					),
				),
			),
		);

		shortcode_ui_register_for_shortcode( 'quote', $args );
	}
}
