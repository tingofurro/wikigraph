<?php
include('init.php');
header('Content-Type: text/html; charset=utf-8');
if(isset($_GET['lookUp'])) {
	$toks = explode(" ", mysql_real_escape_string($_GET['lookUp']));
	$where = ""; $att = array();
	if(count($toks) > 0) {
		foreach ($toks as $i => $tok) {
			array_push($att, "name LIKE '%".$tok."%'");
		}
		$where = "WHERE ".implode(" AND ", $att);
	}
	$found = array();
	$r = mysql_query("SELECT * FROM wg_page ".$where." ORDER BY id LIMIT 10");
	while($re = mysql_fetch_array($r)) {
		array_push($found, $re['id']."||".wikiToName($re['name']));
	}
	echo implode("[]", $found);
}
else {
topMenu($root);
$id = 2;
if(isset($_GET['id'])) {$id = mysql_real_escape_string($_GET['id']);}
$r = mysql_query("SELECT * FROM wg_page WHERE id='$id'");
if(!$re = mysql_fetch_array($r)) {
	$r = mysql_query("SELECT * FROM wg_page ORDER BY id LIMIT 1"); $re = mysql_fetch_array($r);
}
$cat = mysql_query("SELECT * FROM wg_category WHERE id=".$re['category']);
$listNames = array();
while($cate = mysql_fetch_array($cat)) {
	array_unshift($listNames, wikiToName($cate['name']));
	$cat = mysql_query("SELECT * FROM wg_category WHERE id=".$cate['parent']);
}
?>
<html lang="en" dir="ltr" class="client-nojs">
<head>
	<title><?php echo wikiToName($re['name']); ?></title>
	<meta charset="UTF-8" />
		<meta name="generator" content="MediaWiki 1.25wmf14" />
		<link rel="stylesheet" type="text/css" href="<?php echo $root; ?>css/viewPage.css">
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
		<div id="categoryTree">
			<?php
				foreach ($listNames as $i => $name) {
					if($i > 0) {echo ' <span style="font-size: '.(30-3*$i).'px; vertical-align: middle; padding: 10px;">&#65515;</span> ';}
					echo '<a href="tree.php?sourceName='.wikiToName($name).'"><span class="catName" style="font-size: '.(20-3*$i).'px; vertical-align: middle;">'.$name.'</span></a>';
				}
			?>
		</div>
		<input type="text" name="searchBox" id="searchBox" placeholder="Search for a page..." onkeyup="this.onchange();" onchange="changeTextFilter();" />
		<div id="responseContent"></div>
		<h1 id="firstHeading" class="firstHeading" style="padding-top: 90px;" lang="en"><span dir="auto"><?php echo wikiToName($re['name']); ?></span></h1>
		<?php echo file_get_contents('data/'.$id.'.txt'); ?>
	</body>
	<script type="text/javascript">
		var webroot = '<?php echo $root; ?>';
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $root; ?>JS/viewPage.js"></script>
</html>
<?php
}
?>