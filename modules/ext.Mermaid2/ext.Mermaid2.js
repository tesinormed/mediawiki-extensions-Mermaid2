$( function () {
	mermaid.initialize( {
		securityLevel: mw.config.get( 'wgMermaid2SecurityLevel' ),
		theme: mw.config.get( 'wgMermaid2DefaultTheme' ),
		startOnLoad: false
	} );
} );

mw.hook( 'wikipage.content' ).add( ( $content ) => {
	mermaid.run();
} );
