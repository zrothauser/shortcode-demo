( function() {
	tinymce.create('tinymce.plugins.Quote', {

		/**
		 * Initialize the TinyMCE plugin
		 */
		init : function( editor, url ) {
			editor.addButton( 'quote', {
				title : 'Add a quote with citation',

				// Quote by Matthew R. Miller from the Noun Project
				image : url + '/../images/quote.svg',

				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert quote',

						body: [ {
							type: 'textbox',
							name: 'quote',
							label: 'Quote'
						},
						{
							type: 'textbox',
							name: 'citation',
							label: 'Citation'
						},
						{
							type: 'listbox',
			                name: 'alignment',
			                label: 'Alignment',
			                values: [
			                	{
			                        value: 'left',
			                        text: 'Left'
			                    },
			                    {
			                        value: 'right',
			                        text: 'Right'
			                    },
			                    {
			                        value: 'full-width',
			                        text: 'Full Width'
			                    }
			                ]
			            } ],

						onsubmit: function( e ) {
							shortcode = '[quote citation="' + e.data.citation + '" align="' + e.data.alignment + '"]' + e.data.quote + '[/quote]';
							editor.execCommand( 'mceInsertContent', 0, shortcode );
						}
					});
				}
			} );
		}
	} );

	// Register the plugin
	tinymce.PluginManager.add( 'quote', tinymce.plugins.Quote );
} )();