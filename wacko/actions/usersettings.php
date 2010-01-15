<!--notypo-->
<?php

#	echo "  <script type=\"text/javascript\" src=\"".$this->GetConfigValue("root_url")."js/protoedit.js\"></script>\n";
#	echo "  <script type=\"text/javascript\" src=\"".$this->GetConfigValue("root_url")."js/wikiedit2.js\"></script>\n";
#	echo "  <script type=\"text/javascript\" src=\"".$this->GetConfigValue("root_url")."js/autocomplete.js\"></script>\n";

// reconnect securely in ssl mode
if ($this->config["ssl"] == true && $_SERVER["HTTPS"] != "on")
{
	$this->Redirect(str_replace("http://", "https://", $this->href()));
}

// email confirmation
if (isset($_GET["confirm"]) && $_GET["confirm"])
{
	if ($temp = $this->LoadSingle(
			"SELECT name, email, email_confirm ".
			"FROM ".$this->config["user_table"]." ".
			"WHERE email_confirm = '".quote($this->dblink, $_GET["confirm"])."'"))
	{
		$this->Query(
			"UPDATE ".$this->config["user_table"]." ".
			"SET email_confirm = '' ".
			"WHERE email_confirm = '".quote($this->dblink, $_GET["confirm"])."'");

		echo "<br /><br />".$this->GetTranslation("EmailConfirmed")."<br /><br />";

		// log event
		$this->Log(4, str_replace("%2", $temp["name"], str_replace("%1", $temp["email"], $this->GetTranslation("LogUserEmailActivated"))));

		unset($temp);
	}
	else
	{
		echo "<br /><br />".str_replace('%1', $this->ComposeLinkToPage('Settings', '', $this->GetTranslation("SettingsText"), 0), $this->GetTranslation("EmailNotConfirmed"))."<br /><br />";
	}
}
else if (isset($_POST["action"]) && $_POST["action"] == "logout")
{
	$this->LogoutUser();
	$this->SetMessage($this->GetTranslation("LoggedOut"));
	$this->Redirect($this->href());
}
else if ($user = $this->GetUser())
{
	$this->SetPageLang($this->userlang);

	// is user trying to update?
	if (isset($_POST["action"]) && $_POST["action"] == "update")
	{
		// no email given
		if (!$_POST["email"])
			$error .= $this->GetTranslation("SpecifyEmail")." ";
		// invalid email
		else if (!preg_match("/^[\w.-]+?\@[\w.-]+?\.[\w]+$/", $_POST["email"]))
			$error .= $this->GetTranslation("NotAEmail")." ";

		// check for errors and store
		if ($error)
		{
			$this->SetMessage($error.$this->GetTranslation("SettingsNotStored"));
		}
		else
		{
			if ($user["email"] != $_POST["email"]) $email_changed = true;

			// store if email hasn't been changed otherwise request authorization
			if (!$email_changed)
			{
				$bookmarks = str_replace("\r", "", $_POST["bookmarks"]);

				$more = $this->ComposeOptions(array(
					"theme" => $_POST["theme"],
					"autocomplete" => $_POST["autocomplete"],
					"dont_redirect" => $_POST["dont_redirect"],
					"send_watchmail" => $_POST["send_watchmail"],
					"show_files" => $_POST["show_files"],
					"allow_intercom" => $_POST["allow_intercom"],
					"hide_lastsession" => $_POST["hide_lastsession"],
					"validate_ip" => $_POST["validate_ip"],
					"noid_pubs" => $_POST["noid_pubs"],
				));

				$this->Query(
					"UPDATE ".$this->config["user_table"]." SET ".
						"real_name = '".quote($this->dblink, $_POST["real_name"])."', ".
						"email = '".quote($this->dblink, $_POST["email"])."', ".
						"doubleclickedit = '".quote($this->dblink, $_POST["doubleclickedit"])."', ".
						"show_datetime = '".quote($this->dblink, $_POST["showdatetimeinlinks"])."', ".
						"show_comments = '".quote($this->dblink, $_POST["show_comments"])."', ".
						"revisioncount = '".quote($this->dblink, $_POST["revisioncount"])."', ".
						"changescount = '".quote($this->dblink, $_POST["changescount"])."', ".
						"motto = '".quote($this->dblink, $_POST["motto"])."', ".
						"bookmarks = '".quote($this->dblink, $bookmarks)."', ".
						"show_spaces = '".quote($this->dblink, $_POST["show_spaces"])."', ".
						"typografica = '".quote($this->dblink, $_POST["typografica"])."', ".
						"lang = '".quote($this->dblink, $_POST["lang"])."', ".
						"more = '".quote($this->dblink, $more)."' ".
					"WHERE name = '".quote($this->dblink, $user["name"])."' ".
					"LIMIT 1");

				$this->SetMessage($this->GetTranslation("UserSettingsStored"));

				// log event
				$this->Log(6, str_replace("%1", $user["name"], $this->GetTranslation("LogUserSettingsUpdate")));
			}
		}
	}

	// (re)send email confirmation code
	if ((isset($_GET['resend_code']) && $_GET['resend_code'] == 1) || $email_changed === true)
	{
		if ($email = ( $_GET["resend_code"] == 1 ? $user["email"] : $_POST["email"] ))
		{
			$confirm = md5($user["password"].mt_rand().time().mt_rand().$email.mt_rand());

			$this->Query(
				"UPDATE {$this->config["user_table"]} ".
				"SET email_confirm = '".quote($this->dblink, $confirm)."' ".
				"WHERE name = '".quote($this->dblink, $user['name'])."' ".
				"LIMIT 1");

			$subject = $this->config["wacko_name"].". ".$this->GetTranslation("EmailConfirm");
			$message = $this->GetTranslation("EmailHello"). $user["name"].".\n\n".
						str_replace('%1', $this->GetConfigValue("wacko_name"),
						str_replace('%2', $user["name"],
						str_replace('%3', $this->Href().
						($this->config["rewrite_mode"] ? "?" : "&amp;")."confirm=".$confirm,
						$this->GetTranslation("EmailVerify"))))."\n\n".
						$this->GetTranslation("EmailGoodbye")."\n".
						$this->GetConfigValue("wacko_name")."\n".
						$this->GetConfigValue("base_url");
			$this->SendMail($email, $subject, $message);
		}
		else
		{
			$this->SetMessage($this->GetTranslation("SettingsCodeNotSent"));
		}
	}

	// reload user data
	if ( (isset($_REQUEST["action"]) && $_REQUEST["action"] == "update") || (isset($_GET["resend_code"]) && $_GET["resend_code"] == 1))
	{
		$this->SetUser($this->LoadUser($user["name"]), 1);
		$this->SetBookmarks(BM_USER);

		// forward
		$this->SetMessage($this->GetTranslation("UserSettingsStored",$_POST["lang"]));

		$this->Redirect($this->href());
		$user = $this->GetUser();
	}

	// user is logged in; display config form
	echo $this->FormOpen();

	$code = $this->LoadSingle(
		"SELECT email_confirm ".
		"FROM {$this->config["user_table"]} ".
		"WHERE name = '".quote($this->dblink, $user["name"])."'");
?>
<input type="hidden" name="action" value="update" />
<div id="cssformX">
<h3><?php echo $this->GetTranslation("UserSettings"); ?></h3>
<table class="form_tbl">
<tbody>
  <tr>
    <td class="form_left"><?php echo $this->GetTranslation("UserName");?></td>
    <td><strong><?php echo $user["name"];?></strong></td>
  </tr>
  <tr>
    <td class="form_left"><label for="real_name"><?php echo $this->GetTranslation("RealName");?></label></td>
    <td><input id="real_name" name="real_name" value="<?php echo htmlentities($user["real_name"]) ?>" size="40" />
    </td>
  </tr>
  <tr>
    <td class="form_left"><a href="<?php echo $this->href("", "Password")?>"><?php echo $this->GetTranslation("YouWantChangePassword");?></a></td>
    <td><input type="button" onclick="location.href='password'" value="<?php echo $this->GetTranslation("YouWantChangePassword");?>" name="_password"/></td>
  </tr>
  <tr>
    <td class="form_left"><label for="email"><?php echo $this->GetTranslation("YourEmail");?></label></td>
    <td><input id="email" name="email" value="<?php echo htmlentities($user["email"]) ?>" size="40" />&nbsp;<?php echo $user["email_confirm"] == "" ? '<img src="'.$this->GetConfigValue("root_url").'images/tick.png" alt="'.$this->GetTranslation("EmailConfirmed").'" title="'.$this->GetTranslation("EmailConfirmed").'" width="20" height="20" />' : '<img src="'.$this->GetConfigValue("root_url").'images/warning.gif" alt="'.$this->GetTranslation("EmailConfirm").'" title="'.$this->GetTranslation("EmailConfirm").'" width="16" height="16" />' ?>
<?php
		if (!$user["email"] || $code["email_confirm"])
			echo "<strong class=\"cite\"".
				$this->GetTranslation("EmailNotVerified")."</strong><br />".
				"<small>".$this->GetTranslation("EmailNotVerifiedDesc")."<strong><a href=\"?resend_code=1\">".$this->GetTranslation("HereLink")."</a></strong>.</small>";
?></td>
  </tr>
  <tr>
    <td class="form_left"><label for="motto"><?php echo $this->GetTranslation("YourMotto");?></label></td>
    <td class="form_right"><textarea id="motto" name="motto" cols="80" rows="2"><?php echo htmlspecialchars($user["motto"]) ?></textarea>
<?php /*
		<script type="text/javascript">
					wE = new WikiEdit();
					wE.init('motto','<a href="<?php echo $this->href('', $this->config['wiki_docs']); ?>" title="<?php echo $this->GetTranslation('WikiEditTitle'); ?>">WikiEdit</a>','edname-w','<?php echo $this->config['root_url']; ?>images/wikiedit/');
				</script>
*/ ?>
	</td>
  </tr>
	<tr class="lined">
		<td></td>
		<td></td>
	</tr>
	<tr>
	<td class="form_left"><label for="lang"><?php echo $this->GetTranslation("YourLanguage");?></label></td>
	<td class="form_right"><select id="lang" name="lang">
	<option value=""></option>
	<?php
	$langs = $this->AvailableLanguages();
	for ($i = 0; $i < count($langs); $i++)
	{
		echo "<option value=\"".$langs[$i]."\" ".($user["lang"] == $langs[$i] ? " selected=\"selected\"" : "").">".$langs[$i]."</option>\n";
	}
	?>
</select></td>
  </tr>
  <tr>
    <td class="form_left"><label for="theme"><?php echo $this->GetTranslation("ChooseTheme");?></label></td>
    <td class="form_right"><select id="theme" name="theme">
      <option value=""></option>
      <?php
	$themes = $this->AvailableThemes();
	for ($i = 0; $i < count($themes); $i++)
	{
		echo '<option value="'.$themes[$i].'" '.($user["options"]["theme"] == $themes[$i] ? "selected=\"selected\"" : "").'>'.$themes[$i].'</option>';
	}
	?>
    </select></td>
  </tr>
  <tr>
    <td class="form_left"><label for="bookmarks"><?php echo $this->GetTranslation("YourBookmarks");?></label></td>
    <td class="form_right"><textarea id="bookmarks" name="bookmarks" cols="40" rows="10"><?php echo htmlspecialchars($user["bookmarks"]) ?></textarea></td>
  </tr>
  <tr>
    <td class="form_left"><label for="changescount"><?php echo $this->GetTranslation("RecentChangesLimit");?></label></td>
    <td class="form_right"><input id="changescount" name="changescount"
	value="<?php echo htmlentities($user["changescount"]) ?>" size="40" /></td>
  </tr>
  <tr>
    <td class="form_left"><label for="revisioncount"><?php echo $this->GetTranslation("RevisionListLimit");?></label></td>
    <td class="form_right"><input id="revisioncount" name="revisioncount"
	value="<?php echo htmlentities($user["revisioncount"]) ?>" size="40" /></td>
  </tr>
	<tr class="lined">
		<td></td>
		<td></td>
	</tr>
  <tr>
    <td class="form_left"><?php echo $this->GetTranslation("UserSettingsOther");?></td>
    <td class="form_right"><input type="hidden" name="doubleclickedit" value="0" />
      <input
	type="checkbox" id="doubleclickedit" name="doubleclickedit" value="1"
	<?php echo $user["doubleclickedit"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="doubleclickedit"><?php echo $this->GetTranslation("DoubleclickEditing");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="autocomplete" value="0" />
      <input
	type="checkbox" id="autocomplete" name="autocomplete" value="1"
	<?php echo $user["options"]["autocomplete"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="autocomplete"><?php echo $this->GetTranslation("WikieditAutocomplete");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="showdatetimeinlinks" value="0" />
      <input
	type="checkbox" id="showdatetimeinlinks" name="showdatetimeinlinks"
	value="1"
	<?php echo $user["show_datetime"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="showdatetimeinlinks"><?php echo $this->GetTranslation("ShowDateTimeInLinks");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="show_comments" value="0" />
      <input
	type="checkbox" id="show_comments" name="show_comments" value="1"
	<?php echo $user["show_comments"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="show_comments"><?php echo $this->GetTranslation("ShowComments?");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="show_files" value="0" />
      <input
	type="checkbox" id="show_files" name="show_files" value="1"
	<?php echo $user["options"]["show_files"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="show_files"><?php echo $this->GetTranslation("ShowFiles?");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="show_spaces" value="0" />
      <input
	type="checkbox" id="show_spaces" name="show_spaces" value="1"
	<?php echo $user["show_spaces"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="show_spaces"><?php echo $this->GetTranslation("ShowSpaces");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="dont_redirect" value="0" />
      <input
	type="checkbox" id="dont_redirect" name="dont_redirect" value="1"
	<?php echo $user["options"]["dont_redirect"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="dont_redirect"><?php echo $this->GetTranslation("DontRedirect");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="send_watchmail" value="0" />
      <input
	type="checkbox" id="send_watchmail" name="send_watchmail" value="1"
	<?php echo $user["options"]["send_watchmail"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="send_watchmail"><?php echo $this->GetTranslation("SendWatchEmail");?></label></td>
  </tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right"><input type="hidden" name="allow_intercom" value="0" />
      <input
	type="checkbox" id="allow_intercom" name="allow_intercom" value="1"
	<?php echo $user["options"]["allow_intercom"] == "1" ? "checked=\"checked\"" : "" ?> />
      <label for="allow_intercom"><?php echo $this->GetTranslation("AllowIntercom");?></label></td>
  </tr>

	<tr>
		<td class="form_left">&nbsp;</td>
		<td class="form_right">
			<input type="hidden" name="validate_ip" value="0" />
			<input type="checkbox" name="validate_ip" id="validate_ip" value="1" <?php echo $user['options']['validate_ip'] == '1' ? 'checked' : '' ?> />
		<label for="validate_ip"><?php echo $this->GetTranslation('ValidateIP');?></label></td>
	</tr>
	<tr>
		<td class="form_left">&nbsp;</td>
		<td class="form_right">
			<input type="hidden" name="hide_lastsession" value="0" />
			<input type="checkbox" name="hide_lastsession" id="hide_lastsession" value="1" <?php echo $user['options']['hide_lastsession'] == '1' ? 'checked' : '' ?> />
		<label for="hide_lastsession"><?php echo $this->GetTranslation('HideLastSession');?></label></td>
	</tr>
	<tr>
		<td class="form_left">&nbsp;</td>
		<td class="form_right">
			<input type="hidden" name="noid_pubs" value="0" />
			<input type="checkbox" name="noid_pubs" id="noid_pubs" value="1" <?php echo $user['options']['noid_pubs'] == '1' ? 'checked' : '' ?> />
		<label for="noid_pubs"><?php echo $this->GetTranslation('ProfileAnonymousPub');?></label></td>
	</tr>
  <!--<tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right">
	<input type="hidden" name="typografica" value="0" /><input type="checkbox" id="typografica" name="typografica" value="1" <?php echo $user["typografica"] == "1" ? "checked=\"checked\"" : "" ?> /><label for="typografica"><?php echo $this->GetTranslation("Typografica");?></label>
	</td>
  </tr>-->
	<tr class="lined">
		<td></td>
		<td></td>
	</tr>
  <tr>
    <td class="form_left">&nbsp;</td>
    <td class="form_right">
		<input id="submit" name="submit" type="submit" value="<?php echo $this->GetTranslation("UpdateSettingsButton"); ?>" />
		&nbsp;
		<input id="button" name="button" type="button" onclick="document.location='<?php echo $this->href("", "", "action=logout"); ?>'" value="<?php echo $this->GetTranslation("LogoutButton"); ?>" />
	</td>
  </tr>
  </tbody>
</table>
</div>
<br />
	<?php
	//  echo $this->FormatTranslation("SeeListOfPages")."<br />";
	echo $this->FormClose();
}
else
{
	// user is not logged in
	echo $this->Action("login", array());
}
?>
<!--/notypo-->