[ === main === ]
	[ ' message ' ]
	[''' pagination ''']
	[= s =
		<form action="[ ' href: attachments ' ]" method="get" name="file_search">
			[ ' csrf: file_search ' ]
			<input type="hidden" name="files" value="[ ' filter |e attr ' ]">
			<table class="formation">
				<tr>
					<td class="label">
						<label for="search_file">[ ' _t: FileSearch ' ]:</label>
					</td>
					<td>
						<input type="search" name="phrase" id="search_file" size="40" value="[ ' phrase |e attr ' ]">
						<input type="submit" value="[ ' _t: SearchButtonText ' ]">
					</td>
				</tr>
			</table>
			<br />
		</form>
	=]
	[= mark =
		<div class="layout-box">
			<p><span>[ ' results ' ]: </span></p>
	=]
	<table class="[ ' style ' ]">
		[= r =
			<tr>
				<td class="file-">[ ' link ' ]</td>
					[= p =
						<td class="desc-">
							<strong>[ ' name ' ]</strong><br><br>
							[ ' desc ' ]<br><br>
							[ ' meta ' ]<br>
							[ ' size ' ]<br><br>
							[ ' user ' ]<br>
							[ ' dt ' ]<br><br>
							[ ' categories ' ]
						</td>
					=]
					[= g =
						<td class="desc-">[ ' desc ' ]</td>
						<td class="size-">
							<span class="size2-">[ ' meta ' ]</span>&nbsp;
						</td>
						<td class="dt-">
							<span class="dt2-">[ ' dt ' ]</span>&nbsp;
						</td>
					=]
				<td class="tool-">
					<span class="dt2- tool2-">
						<a href="[ ' info ' ]"><img src="[ ' db: theme_url ' ]icon/spacer.png" title="[ ' _t: FileViewProperties ' ]" alt="[ ' _t: FileViewProperties ' ]" class="btn-info"></a>
							[= mode =
								<a href="[ ' edit ' ]"><img src="[ ' db: theme_url ' ]icon/spacer.png" title="[ ' _t: FileEditProperties ' ]" alt="[ ' _t: FileEditProperties ' ]" class="btn-edit"></a>
								<a href="[ ' remove ' ]"><img src="[ ' db: theme_url ' ]icon/spacer.png" title="[ ' _t: FileRemove ' ]" alt="[ ' _t: FileRemove ' ]" class="btn-delete"></a>
							=]
					</span>
				</td>
			</tr>
		=]
	</table>
	[= emark _ =
		[ ' nonstatic ' ]
		</div>
	=]
	[''' pagination ''']

[= pagination =]
<nav class="pagination">[ ' text ' ]</nav>