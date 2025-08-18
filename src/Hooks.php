<?php

namespace MediaWiki\Extension\Mermaid2;

use MediaWiki\Config\Config;
use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Html\Html;
use MediaWiki\MainConfigNames;
use MediaWiki\Parser\Parser;
use MediaWiki\Parser\Sanitizer;
use PPFrame;

class Hooks implements ParserFirstCallInitHook {
	private Config $mainConfig;

	public function __construct( Config $mainConfig ) {
		$this->mainConfig = $mainConfig;
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
		$parser->getOutput()->addModules( [ 'ext.Mermaid2' ] );
		// loaded later using mw.loader.getScript
		$parser->getOutput()->setJsConfigVar(
			'wgMermaid2ScriptUrl',
			$this->mainConfig->get( MainConfigNames::ExtensionAssetsPath )
				. '/Mermaid2/resources/Mermaid/mermaid.min.js'
		);
		$parser->getOutput()->setJsConfigVar(
			'wgMermaid2PanzoomScriptUrl',
			$this->mainConfig->get( MainConfigNames::ExtensionAssetsPath )
				. '/Mermaid2/resources/Panzoom/panzoom.min.js'
		);

		$attribs = Sanitizer::validateTagAttributes( $params, element: 'pre' );
		if ( isset( $attribs['class'] ) ) {
			$attribs['class'] = 'mermaid ' . $attribs['class'];
		} else {
			$attribs['class'] = 'mermaid';
		}

		// outputted HTML
		return [
			Html::element( 'pre', $attribs, trim( $text ) ),
			// make sure this won't get mangled
			'markerType' => 'nowiki'
		];
	}
}
