<?php
/*
Samsra theme.
Common header file.
*/

// HTTP header with right Charset settings
  header("Content-Type: text/html; charset=".$this->GetCharset());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->page["lang"] ?>" lang="<?php echo $this->page["lang"] ?>">
<head>
  <title><?php
// Echoes Title of the page.
  echo $this->config["wacko_name"]." : ".$this->AddSpaces($this->tag).($this->method!="show"?" (".$this->method.")":"");
?></title>
<?php
// We don't need search robots to index subordinate pages
  if ($this->method != 'show' || $this->page["latest"] == "0")
     echo "<meta name=\"robots\" content=\"noindex, nofollow\" />\n";
?>
  <meta name="keywords" content="<?php echo $this->GetKeywords(); ?>" />
  <meta name="description" content="<?php echo $this->GetDescription(); ?>" />
  <meta name="language" content="<?php echo $this->page["lang"] ?>" />
  <meta http-equiv="content-type" content="text/html; charset=<?php echo $this->GetCharset(); ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo $this->config["theme_url"] ?>css/default.css" />
  <?php if ($this->config["allow_x11colors"]) {?><link rel="stylesheet" type="text/css" href="<?php echo $this->config["base_url"] ?>themes/_common/X11colors.css" /><?php } ?>
  <link rel="shortcut icon" href="<?php echo $this->config["theme_url"] ?>icons/favicon.ico" type="image/x-icon" />
  <link rel="alternate" type="application/rss+xml" title="<?php echo $this->GetTranslation("RecentChangesRSS");?>" href="<?php echo $this->config["base_url"];?>xml/changes_<?php echo preg_replace("/[^a-zA-Z0-9]/", "", strtolower($this->config["wacko_name"]));?>.xml" />
  <link rel="alternate" type="application/rss+xml" title="<?php echo $this->GetTranslation("RecentCommentsRSS");?>" href="<?php echo $this->config["base_url"];?>xml/comments_<?php echo preg_replace("/[^a-zA-Z0-9]/", "", strtolower($this->config["wacko_name"]));?>.xml" />
  <link rel="alternate" type="application/rss+xml" title="<?php echo $this->GetTranslation("HistoryRevisionsRSS");?><?php echo $this->tag; ?>" href="<?php echo $this->href("revisions.xml");?>" />
<?php
// JS files.
// default.js contains common procedures and should be included everywhere
?>
  <script type="text/javascript" src="<?php echo $this->config["base_url"];?>js/default.js"></script>
<?php
// load swfobject with flash action (e.g. $this->config["allow_swfobject"] = 1), by default it is set off
if ($this->config["allow_swfobject"])
{
	echo "  <script type=\"text/javascript\" src=\"".$this->config["base_url"]."js/swfobject.js\"></script>\n";
}
// autocomplete.js, protoedit & wikiedit2.js contain classes for WikiEdit editor. We may include them only on method==edit pages.
if ($this->method == 'edit')
{
	echo "  <script type=\"text/javascript\" src=\"".$this->config["base_url"]."js/protoedit.js\"></script>\n";
	echo "  <script type=\"text/javascript\" src=\"".$this->config["base_url"]."js/wikiedit2.js\"></script>\n";
	echo "  <script type=\"text/javascript\" src=\"".$this->config["base_url"]."js/autocomplete.js\"></script>\n";
}
?>
	<script type="text/javascript" src="<?php echo $this->config["base_url"];?>js/captcha.js"></script>
<?php
// Doubleclick edit feature.
// Enabled only for registered users who don't swith it off (requires class=page in show handler).
if ($user = $this->GetUser())
   {
      if ($user["doubleclick_edit"] == "1")
         {
?>
  <script type="text/javascript">
   var edit = "<?php echo $this->href("edit");?>";
  </script>
<?php
         }
   }
else if($this->HasAccess("write"))
   {
?>

      <script type="text/javascript">
      var edit = "<?php echo $this->href("edit");?>";
     </script>
<?php
   }
?>
</head>

<?php
// all_init() initializes all js features:
//   * WikiEdit
//   * Doubleclick editing
//   * Smooth scrolling

?>
<body onload="all_init();">

<div class="header">
<?php
// Outputs page title
?>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
  		<td>
<!-- <h1>-->
     <span class="main"><?php echo $this->config["wacko_name"] ?>:</span>
     <span class="pagetitle"><?php echo $this->GetPagePath(); ?></span>
     <a class="Search" title="<?php echo $this->GetTranslation("SearchTitleTip")?>"
     href="<?php echo $this->config["base_url"].$this->GetTranslation("TextSearchPage").($this->config["rewrite_mode"] ? "?" : "&amp;");?>phrase=<?php echo urlencode($this->tag); ?>">...</a><br />
<!-- </h1> -->
</td><td>
<?php
/*
Samsra theme.

Commented by Roman Ivanov.
*/

// Opens Search form
echo $this->FormOpen("", $this->GetTranslation("TextSearchPage"), "get"); ?>
<div align="right">
<?php
// Searchbar
?>
  <span><?php echo $this->GetTranslation("SearchText") ?><input type="text" name="phrase" size="15" style="border: none; border-bottom: 1px solid #CCCCAA; padding: 0px; margin: 0px;" /><input  class="submitinput" type="submit" value="&raquo;" alt="<?php echo $this->GetTranslation("SearchButtonText"); ?>!" title="<?php echo $this->GetTranslation("SearchButtonText"); ?>!" /></span>
</div>
<?php

// Search form close
echo $this->FormClose();
?>
</td>
	</tr>
</table>
<?php
// Begin Login form
echo $this->FormOpen("", $this->GetTranslation("LoginPage"), "post"); ?>
      <input type="hidden" name="action" value="login" />
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <?php
// Outputs Bookmarks AKA QuickLinks
  // Main page
  echo $this->ComposeLinkToPage($this->config["root_page"]); ?>
|
<?php
  // All user's Bookmarks
  echo $this->Format($this->GetBookmarksFormatted(), "post_wacko"); ?>
|
<?php
  // Here Wacko determines what it should show: "add to Bookmarks" or "remove from Bookmarks" icon
if ($this->GetUser())
{
 if (!in_array($this->tag, $this->GetBookmarkLinks()))
 {?>
<a href="<?php echo $this->Href('', '', "addbookmark=yes")?>"><img src="<?php echo $this->config["theme_url"] ?>icons/bookmark1.gif" alt="+" title="<?php echo $this->GetTranslation("AddToBookmarks") ?>" /></a>
<?php
 } else { ?>
<a href="<?php echo $this->Href('', '', "removebookmark=yes")?>"><img src="<?php echo $this->config["theme_url"] ?>icons/bookmark2.gif" alt="-" title="<?php echo $this->GetTranslation("RemoveFromBookmarks") ?>" /></a><?php  }
} ?></td>
    <td align="right"><?php


// If user are logged, Wacko shows "You are UserName"
if ($this->GetUser()) { ?>
      <span class="nobr"><?php echo $this->GetTranslation("YouAre")." ".$this->Link($this->GetUserName()) ?></span> <small>( <span class="nobr Tune">
      <?php
      echo $this->ComposeLinkToPage($this->GetTranslation("YouArePanelLink"), "", $this->GetTranslation("YouArePanelAccount"), 0); ?>
| <a onclick="return confirm('<?php echo $this->GetTranslation("LogoutAreYouSure");?>');" href="<?php echo $this->Href("",$this->GetTranslation("LoginPage")).($this->config["rewrite_mode"] ? "?" : "&amp;");?>action=logout&amp;goback=<?php echo $this->SlimUrl($this->tag);?>"><?php echo $this->GetTranslation("LogoutLink"); ?></a></span> )</small>
      <?php
// Else Wacko shows login's controls
} else {
?>
      <span class="nobr">
      <input type="hidden" name="goback" value="<?php echo $this->SlimUrl($this->tag);?>"
/>
      <strong><?php echo $this->GetTranslation("LoginWelcome") ?>:&nbsp;</strong>
      <input
type="text" name="name" size="18" class="login" />
      &nbsp;
      <?php
echo $this->GetTranslation("LoginPassword") ?>
      :&nbsp;
      <input type="password" name="password"
class="login" size="8" />
      &nbsp;
      <input name="image" type="image"
src="<?php echo $this->config["theme_url"] ?>icons/login.gif" alt=">>>" align="top" />
      </span>
      <?php
}
// End if
?></td>
  </tr>
</table>
<?php
// Closing Login form
echo $this->FormClose();
?>
</div>
<?php
// here we show messages
if ($message = $this->GetMessage()) echo "<div class=\"info\">$message</div>";
?>