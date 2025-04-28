<?php

namespace MediaWiki\Extension\Mermaid2;

use MediaWiki\Config\Config;
use MediaWiki\Config\ConfigFactory;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Html\Html;
use MediaWiki\MainConfigNames;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\Sanitizer;
use PPFrame;

class Hooks implements ParserFirstCallInitHook {
	private Config $mainConfig;
	private Config $extensionConfig;

	public function __construct( Config $mainConfig, ConfigFactory $configFactory ) {
		$this->mainConfig = $mainConfig;
		$this->extensionConfig = $configFactory->makeConfig( 'mermaid2' );
	}

	/**
	 * @inheritDoc
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 */
	public function onParserFirstCallInit( $parser ): void {
		$parser->setHook( 'mermaid', [ $this, 'renderTag' ] );
	}

	public function renderTag( ?string $text, array $params, Parser $parser, PPFrame $frame ): string|array {
		if ( $text === null ) {
			// nothing to render
			return '';
		}

		// add Mermaid to the page
		// ResourceLoader has issues, so we use a <script> tag
		$parser->getOutput()->addHeadItem( Html::element(
				'script',
				[ 'src' => $this->mainConfig->get( MainConfigNames::ExtensionAssetsPath )
					. '/Mermaid2/resources/Mermaid/mermaid.min.js' ]
		) );
		$parser->getOutput()->addModules( [ 'ext.Mermaid2' ] );
		$parser->getOutput()->setJsConfigVar(
			'wgMermaid2SecurityLevel',
			$this->extensionConfig->get( 'Mermaid2SecurityLevel' )
		);

		$attribs = Sanitizer::validateTagAttributes( $params, 'div' );
		if ( isset( $attribs['class'] ) ) {
			$attribs['class'] = 'mermaid ' . $attribs['class'];
		} else {
			$attribs['class'] = 'mermaid';
		}

		// outputted HTML
		return [
			Html::element( 'div', $attribs, trim( $text ) ),
			// make sure this won't get mangled
			'markerType' => 'nowiki'
		];
	}
}
