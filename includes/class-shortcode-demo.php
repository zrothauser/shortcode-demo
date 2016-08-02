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
		add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
		add_filter( 'mce_buttons',          array( $this, 'add_tinymce_button' ) );
		add_action( 'mce_css',              array( $this, 'editor_styles' ) );

		// Frontend filters
		add_action( 'wp_enqueue_scripts',   array( $this, 'frontend_styles' ) );
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
			trailingslashit( $this->get_plugin_url() ) . 'css/shortcode-demo.css',
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

		$mce_css .= trailingslashit( $this->get_plugin_url() ) . 'css/shortcode-demo.css';

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
}