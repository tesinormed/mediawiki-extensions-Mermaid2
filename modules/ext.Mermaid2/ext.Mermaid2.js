// load Mermaid and Panzoom
$.when(
	mw.loader.getScript( mw.config.get( 'wgMermaid2ScriptUrl' ) ),
	mw.loader.getScript( mw.config.get( 'wgMermaid2PanzoomScriptUrl' ) )
).then(
	// loading successful
	() => {
		const rootClasses = document.documentElement.classList;

		// determine dark mode status
		let darkMode = false;
		if ( rootClasses.contains( 'skin-theme-clientpref-night' ) ) {
			darkMode = true;
		} else if ( rootClasses.contains( 'skin-theme-clientpref-os' ) && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ) {
			darkMode = true;
		}

		// initialize Mermaid
		mermaid.initialize( {
			securityLevel: 'strict',
			startOnLoad: false, // done on wikipage.content hook
			logLevel: 'error',
			theme: darkMode ? 'dark' : 'forest'
		} );

		mw.hook( 'wikipage.content' ).add( ( $content ) => {
			mermaid.run( {
				nodes: $content.find( '.mermaid' ), // limit scope to $content
				postRenderCallback: ( id ) => {
					const element = document.getElementById( id );

					// disable if requested
					if ( element.parentElement.dataset.panzoom === 'off' ) {
						return;
					}

					// set up Panzoom
					const panzoom = Panzoom( element, {
						canvas: true,
						minScale: 1,
						maxScale: 5,
						step: 0.25
					} );
					element.parentElement.addEventListener( 'wheel', panzoom.zoomWithWheel );
				}
			} );
		} );
	},
	// loading failed
	( error ) => {
		mw.log.error( error.message );
	}
);
