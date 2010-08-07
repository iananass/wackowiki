<div id="page">
<?php

$is_global = "";
$message = "";
$error = "";
$registered = "";

// redirect to show method if page don't exists
if (!$this->page) $this->Redirect($this->href("show"));

// deny for comment
if ($this->page["comment_on_id"])
	$this->Redirect($this->href("", $this->GetCommentOnTag($this->page["comment_on_id"]), "show_comments=1")."#".$this->page["tag"]);

if ($user = $this->GetUser())
{
	$user = strtolower($this->GetUserName());
	$registered = true;
}
else
{
	$user = GUEST;
}

if ($registered
&&
(
($this->config["upload"] === true) || ($this->config["upload"] == "1") ||
($this->CheckACL($user,$this->config["upload"]))
)
&&
($this->HasAccess("write") && $this->HasAccess("read") || ($_POST["to"] == "global"))
)
{
	if (isset($_GET["remove"])) // show the form
	{
		if ($_GET["remove"] == "global")
			$page_id = 0;
		else
			$page_id = $this->page["page_id"];

		$what = $this->LoadAll(
			"SELECT f.user_id, u.user_name AS user, f.upload_id, f.filename, f.filesize, f.description, f.uploaded_dt ".
			"FROM ".$this->config["table_prefix"]."upload f ".
				"INNER JOIN ".$this->config["table_prefix"]."user u ON (f.user_id = u.user_id) ".
			"WHERE f.page_id = '".quote($this->dblink, $page_id)."'".
			"AND f.filename='".quote($this->dblink, $_GET["file"])."'");

		if (sizeof($what) > 0)
		{
			if ($this->IsAdmin() || (
				$page_id && (
				$this->page["owner_id"] == $this->GetUserId())) || (
				$what[0]["user_id"] == $this->GetUserId()))
			{
				echo "<strong>".$this->GetTranslation("UploadRemoveConfirm")."</strong>";
				echo $this->FormOpen("upload");
				// !!!!! place here a reference to delete files
?>
	<br />
	<ul class="upload">
		<li><?php echo $this->Link( "file:".$_GET["file"] ); ?>
			<ul>
				<li><?php echo $this->GetTimeStringFormatted($what[0]["uploaded_dt"]); ?></li>
				<li><?php echo "(".$this->binary_multiples($what[0]["filesize"], true, true, true).")"; ?></li>
				<li><?php echo $_GET["file"]; ?></li>
				<li><?php echo $what[0]["description"]; ?></li>
			</ul>
		</li>
	</ul>
	<br />
	<input type="hidden" name="remove" value="<?php echo $_GET["remove"]?>" />
	<input type="hidden" name="file" value="<?php echo $_GET["file"]?>" />
	<input
		name="submit" type="submit" value="<?php echo $this->GetTranslation("RemoveButton"); ?>" />
	&nbsp;
	<input
		type="button" value="<?php echo str_replace("\n"," ",$this->GetTranslation("EditCancelButton")); ?>"
		onclick="document.location='<?php echo addslashes($this->href(""))?>';" />
	<br />
	<br />
<?php
				echo $this->FormClose();
			}
			else
				$this->SetMessage($this->GetTranslation("UploadRemoveDenied"));
		}
		else
			print($this->GetTranslation("UploadFileNotFound"));

		echo "</div>";
		return true;

	}
	else
	if (isset($_POST["remove"])) // delete
	{
		// 1. where, existence
		if ($_POST["remove"] == "global")
			$page_id = 0;
		else
			$page_id = $this->page["page_id"];

		$what = $this->LoadAll(
			"SELECT f.user_id, u.user_name AS user, f.upload_id, f.filename, f.filesize, f.description ".
			"FROM ".$this->config["table_prefix"]."upload f ".
				"INNER JOIN ".$this->config["table_prefix"]."user u ON (f.user_id = u.user_id) ".
			"WHERE f.page_id = '".quote($this->dblink, $page_id)."'".
			"AND f.filename='".quote($this->dblink, $_POST["file"])."'");

		if (sizeof($what) > 0)
		{
			if ($this->IsAdmin() || (
				$page_id && (
				$this->page["owner_id"] == $this->GetUserId())) || (
				$what[0]["user_id"] == $this->GetUserId()))
			{
				// 2. remove from DB
				$this->Query(
					"DELETE FROM ".$this->config["table_prefix"]."upload ".
					"WHERE upload_id = '". quote($this->dblink, $what[0]["upload_id"])."'" );

				$message .= $this->GetTranslation("UploadRemovedFromDB")."<br />";

				// 3. remove from FS
				$real_filename = ($page_id
					? ($this->config["upload_path_per_page"]."/@".$page_id."@")
					: ($this->config["upload_path"]."/")).
					$what[0]["filename"];

				if (@unlink($real_filename))
					$message .= $this->GetTranslation("UploadRemovedFromFS");
				else
					$message .= "<div class=\"error\">".$this->GetTranslation("UploadRemovedFromFSError")."</div>";

				if ($message)
				{
					$this->SetMessage($message);
				}
				// log event
				$this->Log(1, str_replace("%2", $what[0]["filename"], str_replace("%1", $this->tag." ".$this->page["title"], $this->GetTranslation("LogRemovedFile", $this->config["language"]))));
			}
			else
			{
				$this->SetMessage($this->GetTranslation("UploadRemoveDenied"));
			}
		}
		else
		{
			$this->SetMessage($this->GetTranslation("UploadRemoveNotFound"));
		}

	}
	else // process upload
	{
		$user = $this->GetUser();
		$files = $this->LoadAll(
			"SELECT f.upload_id ".
			"FROM ".$this->config["table_prefix"]."upload f ".
				"INNER JOIN ".$this->config["table_prefix"]."user u ON (f.user_id = u.user_id) ".
			"WHERE u.user_name = '".quote($this->dblink, $user["user_name"])."'");

		if (!$this->config["upload_max_per_user"] || (sizeof($files) < $this->config["upload_max_per_user"]))
		{
			if (isset($_FILES["file"]["tmp_name"]) && is_uploaded_file($_FILES["file"]["tmp_name"])) // there is file
			{
				// 1. check out $data
				$_data = explode(".", $_FILES["file"]["name"] );
				$ext  = $_data[ sizeof($_data)-1 ];
				unset($_data[ sizeof($_data)-1 ]);
				$name = implode( ".", $_data );
				$name = str_replace("@", "_", $name);

				// here would be place for translit
				$name = $this->Format($name, "translit");

				// 1.5. +write @page_id@ to name
				if ($_POST["to"] != "global")
					$name = "@".$this->page["page_id"]."@".$name;
				else
					$is_global = 1;

				if ($is_global)
				{
					$dir = $this->config["upload_path"]."/";
					$banned = explode("|", $this->config["upload_banned_exts"]);
					if (in_array(strtolower($ext), $banned))
						$ext = $ext.".txt";
				}
				else
				$dir = $this->config["upload_path_per_page"]."/";

				$_name = $name;
				$count = 1;
				while (file_exists($dir.$name.".".$ext))
				{
					if ($name === $_name)
						$name = $_name.$count;
					else
						$name = $_name.(++$count);
				}

				$result_name	= $name.".".$ext;
				$file_size		= $_FILES["file"]['size'];

				// 1.6. check filesize, if asked
				$maxfilesize = $this->config["upload_max_size"];

				if (isset($_POST["maxsize"]))
					if ($maxfilesize > 1 * $_POST["maxsize"])
						$maxfilesize = 1 * $_POST["maxsize"];

				if ($file_size < $maxfilesize * 1024)
				{

					// 1.7. check is image, if asked
					$forbid = 0;
					$size = array(0, 0);
					$src = $_FILES["file"]["tmp_name"];
					$size = @getimagesize($src);

					if ($this->config["upload_images_only"])
					{
						if ($size[0] == 0)
							$forbid = 1;
					}
					if (!$forbid)
					{
						// 3. save to permanent location
						move_uploaded_file($_FILES["file"]['tmp_name'],
						$dir.$result_name);
						chmod( $dir.$result_name, 0644 );

						if ($is_global)
							$small_name = $result_name;
						else
						{
							$small_name = explode("@", $result_name);
							$small_name = $small_name[ sizeof($small_name) -1 ];
						}

						$file_size_kb	= ceil($file_size / 1024);
						$uploaded_dt	= date("Y-m-d H:i:s");

						$description = substr(quote($this->dblink, $_POST["description"]),0,250);
						$description = rtrim( $description, "\\" );

						// Make HTML in the description redundant ;�)
						$description = $this->Format($description, "preformat");
						$description = $this->Format($description, "safehtml");
						$description = htmlentities($description,ENT_COMPAT,$this->GetCharset());

						// 5. insert line into DB
						$this->Query("INSERT INTO ".$this->config["table_prefix"]."upload SET ".
							"page_id		= '".quote($this->dblink, $is_global ? "0" : $this->page["page_id"])."', ".
							"user_id		= '".quote($this->dblink, $user["user_id"])."',".
							"filename		= '".quote($this->dblink, $small_name)."', ".
							"description	= '".quote($this->dblink, $description)."', ".
							"filesize		= '".quote($this->dblink, $file_size)."',".
							"picture_w		= '".quote($this->dblink, $size[0])."',".
							"picture_h		= '".quote($this->dblink, $size[1])."',".
							"file_ext		= '".quote($this->dblink, substr($ext,0,10))."',".
							"uploaded_dt	= '".quote($this->dblink, $uploaded_dt)."' ");

						// 4. output link to file
						// !!!!! write after providing filelink syntax
						$this->SetMessage("<strong>".$this->GetTranslation("UploadDone")."</strong>");

						// log event
						if ($is_global)
						{
							$this->Log(4, str_replace("%3", $file_size_kb, str_replace("%2", $small_name, $this->GetTranslation("LogFileUploadedGlobal", $this->config["language"]))));
						}
						else
						{
							$this->Log(4, str_replace("%3", $file_size_kb, str_replace("%2", $small_name, str_replace("%1", $this->page["tag"]." ".$this->page["title"], $this->GetTranslation("LogFileUploadedLocal", $this->config["language"])))));
						}
						?>
	<br />
	<ul class="upload">
		<li><?php echo $this->Link("file:".$small_name); ?>
			<ul>
				<li><?php echo $this->GetTimeStringFormatted($uploaded_dt); ?></li>
				<li><?php echo "(".$file_size_kb." ".$this->GetTranslation("UploadKB").")"; ?></li>
				<li><?php echo $small_name; ?></li>
				<li><?php echo $description; ?></li>
			</ul>
		</li>
	</ul>
	<br />
<?php

					}
					else //forbid
						$error = $this->GetTranslation("UploadNotAPicture");

				}
				else //maxsize
					$error = $this->GetTranslation("UploadMaxSizeReached");

			} //!is_uploaded_file
			else
			{
				if (isset($_FILES["file"]['error']) && ($_FILES["file"]['error'] == UPLOAD_ERR_INI_SIZE || $_FILES["file"]['error'] == UPLOAD_ERR_FORM_SIZE))
					$error = $this->GetTranslation("UploadMaxSizeReached");
				else if (isset($_FILES["file"]['error']) && ($_FILES["file"]['error'] == UPLOAD_ERR_PARTIAL || $_FILES["file"]['error'] == UPLOAD_ERR_NO_FILE))
					$error = $this->GetTranslation("UploadNoFile");
				else
					$error = "";
			}
		}
		else
			$error = $this->GetTranslation("UploadMaxFileCount");
	}
	if ($error)
	{
		$this->SetMessage("<div class=\"error\">".$error."</div>");
	}
	echo $this->Action("upload", array())."<br />";

// if (!$error) echo "<br /><hr />".$this->Action("upload", array())."<hr /><br />";
}
else
{
	$this->SetMessage($this->GetTranslation("UploadForbidden"));
}
// show uploaded files for current page
if ($this->HasAccess("read"))
{
	echo $this->Action("files", array())."<br />";
}
if (!$this->config["revisions_hide_cancel"])
	echo "<input type=\"button\" value=\"".$this->GetTranslation("CancelDifferencesButton")."\" onclick=\"document.location='".addslashes($this->href(""))."';\" />\n";
?>
</div>