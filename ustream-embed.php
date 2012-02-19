<?php
/**
 * ustream embed extension -
 * this enables your mediawiki to include ustream.tv video clips.
 * Usage: on a wiki page, &lt;ustream-embed recorded=&quot;&lt;number&gt;&quot; [highlight=&quot;number&quot;] [width=&quot;&lt;number&gt;&quot; height=&quot;&lt;number&gt;&quot;] /&gt; will insert the ustream view
 * (http://www.ustream.tv/embed/recorded/number/highlight/number) on your page.
 *
 * @file
 * @ingroup Extensions
 * @author Haruo Kinoshita, underthetree.jp
 * @copyright © 2012 Haruo Kinoshita
 * @license BSD License
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionCredits['parserhook'][] = array(
	'path'           => __FILE__,
	'name'           => 'ustream-embed',
	'author'         => 'Haruo Kinoshita',
	'url'            => '',
	'descriptionmsg' => 'ustream-embed-desc',
);
$wgExtensionMessagesFiles['ustream-embed'] =
    (dirname(__FILE__) . '/ustream-embed.i18n.php');

$wgUstreamEmbedURL = null; // 'http://www.ustream.tv/embed'
$wgUstreamEmbedDefaultWidth = 0; // 608
$wgUstreamEmbedDefaultHeight = 0; // 368

$wgHooks['ParserFirstCallInit'][] = 'wfUstreamEmbedSetHook';

function wfUstreamEmbedSetHook( $parser ) {
	$parser->setHook( 'ustream-embed', 'wfRenderUstreamEmbed' );
	return true;
}

function wfRenderUstreamEmbed( $name, $argv, $parser ) {
    global $wgUstreamEmbedURL, $wgUstreamEmbedDefaultWidth, $wgUstreamEmbedDefaultHeight;
    $errors = array();
    $url = @$wgUstreamEmbedURL;
    if (! $url) {
        $url = 'http://www.ustream.tv/embed';
    }
    foreach (array('recorded', 'highlight') as $a) {
        if ( @$argv[$a] ) {
            $url .= '/' . $a . '/' . htmlspecialchars($argv[$a]);
        } elseif ($a == 'recorded') {
            $errors[] = 'parameter "recorded" must be not null.';
        }
    }

    $width = $wgUstreamEmbedDefaultWidth;
    if (! $width || $width < -1) {
        $width = 608;
    }
    $height = $wgUstreamEmbedDefaultHeight;
    if (! $height || $height < -1) {
        $height = 368;
    }
    $tagAttr = array('src' => $url, 'width' => $width, 'height' => $height,
                     'scrolling' => 'no', 'frameborder' => 0,
                     'style' => 'border: 0px none transparent;',);
    foreach (array('width', 'height') as $a) {
        if ( @$argv[$a] ) {
            $tagAttr[$a] = htmlspecialchars($argv[$a]);
        }
    }

    if (! count($errors)) {
        $output = Html::rawElement( 'iframe', $tagAttr, '' );
    } else {
        Html::rawElement( 'div', array( 'class' => 'error' ), wfMsgForContent( 'ustream-embed-error', htmlspecialchars(implode( ', ', $errors ))));
    }

	return $output;
}
