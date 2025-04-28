$( function () {
	mermaid.initialize( {
		securityLevel: 'strict',
		startOnLoad: false,
	} );
} );

mw.hook( 'wikipage.content' ).add( ( $content ) => {
	mermaid.run();
} );
