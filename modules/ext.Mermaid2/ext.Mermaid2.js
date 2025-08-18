$( () => {
	const rootClasses = document.documentElement.classList;
	let darkMode = false;

	if ( rootClasses.contains( 'skin-theme-clientpref-night' ) ) {
		darkMode = true;
	} else if ( window.matchMedia( '(prefers-color-scheme: dark)' ).matches &&
		rootClasses.contains( 'skin-theme-clientpref-os' )
	) {
		darkMode = true;
	}

	mermaid.initialize( {
		securityLevel: 'strict',
		startOnLoad: false,
		logLevel: 'error',
		theme: darkMode ? 'dark' : 'neutral'
	} );

	mw.hook( 'wikipage.content' ).add( ( $content ) => {
		mermaid.run( {
			nodes: $content.find( '.mermaid' )
		} );
	} );
} );
