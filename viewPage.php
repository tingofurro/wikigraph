<?php
header('Content-Type: text/html; charset=utf-8');
include('extractHtml.php');
?>
<html lang="en" dir="ltr" class="client-nojs">
<head>
	<meta charset="UTF-8" />
		<meta name="generator" content="MediaWiki 1.25wmf14" />
		<link rel="alternate" href="android-app://org.wikipedia/http/en.m.wikipedia.org/wiki/Algebra" />
		<link rel="apple-touch-icon" href="//bits.wikimedia.org/apple-touch/wikipedia.png" />
		<link rel="shortcut icon" href="//bits.wikimedia.org/favicon/wikipedia.ico" />
		<link rel="search" type="application/opensearchdescription+xml" href="/w/opensearch_desc.php" title="Wikipedia (en)" />
		<link rel="EditURI" type="application/rsd+xml" href="//en.wikipedia.org/w/api.php?action=rsd" />
		<link rel="alternate" hreflang="x-default" href="/wiki/Algebra" />
		<link rel="copyright" href="//creativecommons.org/licenses/by-sa/3.0/" />
		<link rel="alternate" type="application/atom+xml" title="Wikipedia Atom feed" href="/w/index.php?title=Special:RecentChanges&amp;feed=atom" />
		<link rel="canonical" href="http://en.wikipedia.org/wiki/Algebra" />
		<link rel="stylesheet" href="//bits.wikimedia.org/en.wikipedia.org/load.php?debug=false&amp;lang=en&amp;modules=ext.gadget.DRN-wizard%2CReferenceTooltips%2Ccharinsert%2Cfeatured-articles-links%2CrefToolbar%2Cswitcher%2Cteahouse%7Cext.math.styles%7Cext.rtlcite%2Cwikihiero%2CwikimediaBadges%7Cext.uls.nojs%7Cext.visualEditor.viewPageTarget.noscript%7Cmediawiki.legacy.commonPrint%2Cshared%7Cmediawiki.skinning.interface%7Cmediawiki.ui.button%7Cskins.vector.styles%7Cwikibase.client.init&amp;only=styles&amp;skin=vector&amp;*" />
		<meta name="ResourceLoaderDynamicStyles" content="" />
		<link rel="stylesheet" href="//bits.wikimedia.org/en.wikipedia.org/load.php?debug=false&amp;lang=en&amp;modules=site&amp;only=styles&amp;skin=vector&amp;*" />
		<style>a:lang(ar),a:lang(kk-arab),a:lang(mzn),a:lang(ps),a:lang(ur){text-decoration:none}</style>
		<script src="//bits.wikimedia.org/en.wikipedia.org/load.php?debug=false&amp;lang=en&amp;modules=startup&amp;only=scripts&amp;skin=vector&amp;*"></script>
		<script>if(window.mw){
		mw.config.set({"wgCanonicalNamespace":"","wgCanonicalSpecialPageName":false,"wgNamespaceNumber":0,"wgPageName":"Algebra","wgTitle":"Algebra","wgCurRevisionId":643091911,"wgRevisionId":643091911,"wgArticleId":18716923,"wgIsArticle":true,"wgIsRedirect":false,"wgAction":"view","wgUserName":null,"wgUserGroups":["*"],"wgCategories":["Articles with inconsistent citation formats","Wikipedia indefinitely move-protected pages","Wikipedia indefinitely semi-protected pages","Articles containing Arabic-language text","Algebra"],"wgBreakFrames":false,"wgPageContentLanguage":"en","wgPageContentModel":"wikitext","wgSeparatorTransformTable":["",""],"wgDigitTransformTable":["",""],"wgDefaultDateFormat":"dmy","wgMonthNames":["","January","February","March","April","May","June","July","August","September","October","November","December"],"wgMonthNamesShort":["","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],"wgRelevantPageName":"Algebra","wgRelevantArticleId":18716923,"wgIsProbablyEditable":false,"wgRestrictionEdit":["autoconfirmed"],"wgRestrictionMove":["sysop"],"wgWikiEditorEnabledModules":{"toolbar":true,"dialogs":true,"hidesig":true,"preview":false,"publish":false},"wgBetaFeaturesFeatures":[],"wgMediaViewerOnClick":true,"wgMediaViewerEnabledByDefault":true,"wgVisualEditor":{"isPageWatched":false,"pageLanguageCode":"en","pageLanguageDir":"ltr","svgMaxSize":4096,"namespacesWithSubpages":{"6":0,"8":0,"1":true,"2":true,"3":true,"4":true,"5":true,"7":true,"9":true,"10":true,"11":true,"12":true,"13":true,"14":true,"15":true,"100":true,"101":true,"102":true,"103":true,"104":true,"105":true,"106":true,"107":true,"108":true,"109":true,"110":true,"111":true,"830":true,"831":true,"447":true,"2600":false,"828":true,"829":true}},"wikilove-recipient":"","wikilove-anon":0,"wgPoweredByHHVM":true,"wgULSAcceptLanguageList":["en-us"],"wgULSCurrentAutonym":"English","wgFlaggedRevsParams":{"tags":{"status":{"levels":1,"quality":2,"pristine":3}}},"wgStableRevisionId":null,"wgCategoryTreePageCategoryOptions":"{\"mode\":0,\"hideprefix\":20,\"showcount\":true,\"namespaces\":false}","wgNoticeProject":"wikipedia","wgWikibaseItemId":"Q3968"});
		}</script><script>if(window.mw){
		mw.loader.implement("user.options",function($,jQuery){mw.user.options.set({"variant":"en"});},{},{},{});mw.loader.implement("user.tokens",function($,jQuery){mw.user.tokens.set({"editToken":"+\\","patrolToken":"+\\","watchToken":"+\\"});},{},{},{});
		}</script>
		<script>if(window.mw){
		mw.loader.load(["mediawiki.page.startup","mediawiki.legacy.wikibits","mediawiki.legacy.ajax","ext.centralauth.centralautologin","mmv.head","ext.visualEditor.viewPageTarget.init","ext.uls.init","ext.uls.interface","ext.centralNotice.bannerController","skins.vector.js"]);
		}</script>
		<link rel="dns-prefetch" href="//meta.wikimedia.org" />
	</head>
	<body>
		<?php
			$id = 2;
			if(isset($_GET['id'])) {$id = mysql_real_escape_string($_GET['id']);}
			echo file_get_contents('data/'.$id.'.txt');
		?>
	</body>
</html>