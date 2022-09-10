[ === main === ]

	<script>
	<!-- Begin
	function textCounter(field, countfield, maxlimit)
	{
		if (field.value.length> maxlimit)		// if too long...trim it!
			field.value = field.value.substring(0, maxlimit);
				else							// otherwise, update 'characters left' counter
			countfield.value = maxlimit - field.value.length;
	}
	// End -->
	</script>

	<table>
		<tr>
			<td> <a href="[ ' hrefinbox ' ]">[ ' _t: Inbox ' ]</a> </td>
			<td> | <a href="[ ' hrefcompose ' ]">[ ' _t: Compose ' ]</a> </td>
			<td> | <b>[ ' _t: Folder ' ]</b></td>
			<td>
				<form action="[ ' href: ' ]" method="post" name="message_folder">
					[ ' csrf: message_folder ' ]
					<select name="whichfolder">
					[= o _ =
						<option value="[ ' info ' ]"[ ' selected ' ]>[ ' info ' ]</option>
					=]
					</select>
					<button type="submit">[ ' _t: View ' ]</button>
				</form>
			</td>
			<td> | <a href="[ ' hrefsend ' ]">[ ' _t: SentItems ' ]</a> </td>
			<td> | <b>[ ' _t: Manage ' ]: </b><a href="[ ' hreffolders ' ]">[ ' _t: Folders ' ]</a> | <a href="[ ' hrefcontacts ' ]">[ ' _t: Contacts ' ]</a> </td>
			<td> | <a href="[ ' hrefusers ' ]">[ ' _t: Users ' ]</a></td>
			<td> | <a href="[ ' hrefhelp ' ]">[ ' _t: Help ' ]</a></td>
		</tr>
	</table>
	[ ' folder ' ]<br>
	[ ' forbidden ' ]
	[= a _ =
		[ ' message ' ]
	=]
	[= b _ =
		<table class="hl-line" cellpadding="2" cellspacing="3">
			<tr>
				<td colspan="5" align="center">
					[ '' pagination '' ]
				</td>
			</tr>
			<tr bgcolor=#93B2DD>
				<td width="400"><b>[ ' _t: Subject ' ]</b></td>
				<td width=""><b>[ ' _t: Date ' ]</b></td>
				<td width="100"><b>[ ' _t: Sender ' ]</b></td>
				<td width="250"><b>[ ' _t: MoveToFolder ' ]</b></td>
				<td width="80"><b>[ ' _t: Delete ' ]</b></td>
			</tr>
			[= n _ =
				<tr>
					<td>[ ' status ' ][ ' urgent ' ] <a href="[ ' hrefview ' ]">[ ' subject ' ]</a><small>[ ' replied ' ]</small></td>
					<td>[ ' time | time_formatted ' ]</td>
					<td>[ ' username ' ]<small> [<a href="[ ' hrefcontact ' ]">-></a>]</small></td>
					<td width="155">
						<form action="[ ' hrefform ' ]" method="post" name="move_folder">
							[ ' csrf: move_folder ' ]
							<select name="move2folder">
							[= o _ =
								<option value="[ ' info ' ]">[ ' info ' ]</option>
							=]
							</select>
							<button type="submit">[ ' _t: Move ' ]</button>
						</form>
					</td>
					<td>
						<a href="[ ' hrefdelete ' ]">[ ' _t: Delete ' ]</a><br>
					</td>
				</tr>
			=]
		</table>
	=]
	[= c _ =
		<br><b>[ ' _t: ComposeMessage ' ]</b><br><br>
		<table width="675">
			<tr>
				<td>
					<form action="[ ' hrefform ' ]" method="post" name="message_store">
						[ ' csrf: message_store ' ]
						<table>
							<tr>
								<td>[ ' _t: Subject ' ]:</td>
								<td><input type="text" name="subject" maxlength="65" size="30" value="" required></td>
							</tr>
							<tr>
								<td>[ ' _t: Recipient ' ]:</td>
								<td>
									<select name="to" required>
										<option value="">[ ' _t: ChooseRecipient ' ]</option>
										[= o _ =
											<option value="[ ' userid ' ]"[ ' selected ' ]>[ ' username ' ]</option>
										=]
									</select>
								</td>
							</tr>
							<tr>
								<td>[ ' _t: Message ' ]:</td>
								<td>
									<textarea rows="16" cols="45" name="message" onKeyDown="textCounter(this.form.message,this.form.remLen,2000);" onKeyUp="textCounter(this.form.message,this.form.remLen,2000);"></textarea><br>
									<input readonly type="text" name="remLen" size="4" maxlength="4" value="2000"> [ ' _t: CharactersLeft ' ]
								</td>
							</tr>
							<tr>
								<td><button type="submit">[ ' _t: Send ' ]</button></td>
								<td align="right">[ ' _t: Urgent ' ] <input type="checkbox" name="urgent" value="1"></td>
							</tr>
						</table>
					</form>
				</td>
				<td width="200">
					Um einen User zu Deiner Kontaktliste hinzuzufügen bitte <a href="[ ' hrefusers ' ]">hier</a> klicken!<br><br>
					<b>[ ' _t: ContactList ' ]:</b><br><small>([ ' _t: ClickName ' ])</small><br><br>
					[= u _ =
						<a href="[ ' hrefcompose ' ]">[ ' username ' ]</a><br>
					=]
				</td>
			</tr>
		</table>
	=]
	[= d _ =
		<br><b>[ ' _t: ReplyToMessage ' ]</b><br><br>
		<form action="[ ' hrefform ' ]" method="post" name="message_reply">
			[ ' csrf: message_reply ' ]
			<table width="400">
				<tr>
					<td>[ ' _t: Subject ' ]:</td>
					<td><input readonly type="text" name="subject" maxlength="65" size="30" value="[ ' subject ' ]" required></td>
				</tr>
				<tr>
					<td>[ ' _t: Recipient ' ]:</td>
					<td>
						<select name="to" readonly required>
							<option value="[ ' userid ' ]">[ ' username ' ]</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>[ ' _t: Message ' ]:</td>
					<td><textarea rows="16" cols="45" name="message" onKeyDown="textCounter(this.form.message,this.form.remLen,2000);" onKeyUp="textCounter(this.form.message,this.form.remLen,2000);">[ ' origmsg | pre ' ]</textarea><br>
						<input readonly type="text" name="remLen" size="4" maxlength="4" value="2000"> [ ' _t: CharactersLeft ' ]
					</td>
				</tr>
				<tr>
					<td><button type="submit">[ ' _t: Send ' ]</button></td>
					<td align="right">[ ' _t: Urgent ' ] <input type="checkbox" name="urgent" value="1"></td>
				</tr>
			</table>
		</form>
	=]
	[= e _ =
		<br><b>[ ' _t: ForwardMessage ' ]</b><br><br>
		<table width="675">
			<tr>
				<td>
					<form action="[ ' hrefform ' ]" method="post" name="message_forward">
						[ ' csrf: message_forward ' ]
						<table>
							<tr>
								<td>[ ' _t: Subject ' ]:</td>
								<td><input type="text" name="subject" maxlength="65" size="30" value="[ ' subject ' ]" required></td>
							</tr>
							<tr>
								<td>[ ' _t: Recipient ' ]:</td>
								<td>
									<select name="to" required>
										<option value="">[ ' _t: ChooseRecipient ' ]</option>
										[= o _ =
											<option value="[ ' userid ' ]"[ ' selected ' ]>[ ' username ' ]</option>
										=]
									</select>
								</td>
							</tr>
							<tr>
								<td>[ ' _t: Message ' ]:</td>
								<td>
									<textarea rows="16" cols="45" name="message" onKeyDown="textCounter(this.form.message,this.form.remLen,2500);" onKeyUp="textCounter(this.form.message,this.form.remLen,2500);">[ ' origmsg | pre ' ]</textarea><br>
									<input readonly type="text" name="remLen" size="4" maxlength="4" value="2500"> [ ' _t: CharactersLeft ' ]
								</td>
							</tr>
							<tr>
								<td><button type="submit">[ ' _t: Send ' ]</button></td>
								<td align="right">[ ' _t: Urgent ' ] <input type="checkbox" name="urgent" value="1"></td>
							</tr>
						</table>
					</form>
				</td>
				<td width="200">
					Um einen User zu Deiner Kontaktliste hinzuzufügen bitte <a href="[ ' hrefusers ' ]">hier</a> klicken!<br><br>
					<b>[ ' _t: ContactList ' ]:</b><br><small>([ ' _t: ClickName ' ])</small><br><br>
					[= u _ =
						<a href="[ ' hrefforward ' ]">[ ' username ' ]</a><br>
					=]
				</td>
			</tr>
		</table>
	=]
	[= f _ =
		[= x _ =
			<br><center><span class="cite">Ein Feld wurde nicht ausgefüllt. Es müssen alle Felder ausgefüllt sein!</span></center><br><br>
			<a href="[ ' hrefcompose ' ]">Zurück</a>
		=]
		[= e _ =
			<br><span class="cite">Die Nachricht konnte nicht versendet werden, da der eingetragende Empfänger kein registrierter Benutzer ist.</span><br><br>
			<a href="[ ' hrefcompose ' ]">Zurück</a>
		=]
		[ ' sendto ' ]
	=]
	[= g _ =
		<table class="hl-line" cellpadding="2" cellspacing="3" width="100%">
			<tr>
				<td colspan="4" align="center">
					[ '' pagination '' ]
				</td>
			</tr>
			<tr bgcolor=#93B2DD>
				<td width="400"><b>[ ' _t: Subject ' ]</b></td>
				<td width=""><b>[ ' _t: Date ' ]</b></td>
				<td width="100"><b>[ ' _t: Recipient ' ]</b></td>
				<td width="75"><b>[ ' _t: Read ' ]</b></td>
			</tr>
			[= n _ =
				<tr>
					<td><a href="[ ' hrefview2 ' ]">[ ' subject ' ]</a></td>
					<td>[ ' time | time_formatted ' ]</td>
					<td>[ ' username ' ]<small> [<a href="[ ' hrefcontact ' ]">-></a>]</small></td>
					<td width="50">[ ' status ' ]<br></td>
				</tr>
			=]
		</table>
		[ ' // <br><br>Löscht der Empfänger eine Nachricht, wird sie auch hier automatisch entfernt! ' ]
	=]
	[= h _ =
		<table class="hl-line" cellpadding="2" cellspacing="3" width="800">
			<tr>
				<td colspan="5" align="center">
					[ '' pagination '' ]
				</td>
			</tr>
			<tr bgcolor="#93B2DD">
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="400"><b> [ ' _t: Subject ' ]:</b></td>
							<td align="right"></td>
						</tr>
					</table>
				</td>
				<td width="100"><b> [ ' _t: Sender ' ]</b></td>
				<td width="250"><b> [ ' _t: MoveToFolder ' ]</b></td>
				<td width="80"><b> [ ' _t: Delete ' ]</b></td>
			</tr>
			[= n _ =
				<tr>
					<td>
						[ ' status ' ][ ' urgent ' ]<a href="[ ' hrefview ' ]">[ ' subject ' ]</a>[ ' replied ' ]<small>([ ' time | time_formatted ' ])</small>
					</td>
					<td width="125">[ ' username ' ]<small> [<a href="[ ' hrefcontact ' ]">-></a>]</small></td>
					<td>
						<form action="[ ' hrefform ' ]" method="post" name="move_folder">
							[ ' csrf: move_folder ' ]
							<select name="move2folder">
							[= o _ =
								<option value="[ ' info ' ]">[ ' info ' ]</option>
							=]
							</select>
							<button type="submit">[ ' _t: Move ' ]</button>
						</form>
					</td>
					<td>
						<a href="[ ' hrefdelete ' ]">[ ' _t: Delete ' ]</a><br>
					</td>
				</tr>
			=]
		</table>
		[ ' nomessages ' ]
	=]
	[= i _ =
		[ ' forbidden ' ]
		<table border="1" bordercolor="#666699" width="600">
			<tr>
				<td width="350"><strong> [ ' _t: Subject ' ]: </strong>[ ' subject ' ]</td>
				<td></td>
				<td><strong> Von: </strong>[ ' username ' ]<small> [<a href="[ ' hrefcontact ' ]">-></a>]</small></td>
			</tr>
			<tr>
				<td colspan="3"><strong> [ ' _t: Message ' ]:</strong><br>[ ' message ' ]</td>
			</tr>
			<tr>
				<td>
					<a href="[ ' hrefreply ' ]"> [ ' _t: Reply ' ]</a>
					/ <a href="[ ' hrefforward ' ]">[ ' _t: Forward ' ]</a>
					/ <a href="[ ' hrefdelete ' ]">[ ' _t: Delete ' ]</a>
				</td>
				<td>[ ' replied ' ]</td>
				<td><small><strong>[ ' _t: Date ' ]:</strong> [ ' time | time_formatted ' ]</small></td>
			</tr>
		</table><br>
	=]
	[= j _ =
		<table border="1" width="600">
			<tr>
				<td colspan="2"><strong>[ ' _t: Subject ' ]:</strong> [ ' subject ' ]</td>
			</tr>
			<tr>
				<td colspan="2"><strong>[ ' _t: Recipient ' ]:</strong> [ ' username ' ]<small> [<a href="[ ' hrefcontact ' ]">-></a>]</small></td>
			</tr>
			<tr>
				<td colspan="2"><strong>[ ' _t: Message ' ]: </strong>[ ' message ' ]</td>
			</tr>
			<tr>
				<td colspan="2"><small><strong>[ ' _t: Date ' ]: </strong>[ ' time | time_formatted ' ]</small></td>
			</tr>
		</table>
	=]
	[= k _ =
		<form action="[ ' hrefdelete ' ]" method="post" name="delete_message">
			[ ' csrf: delete_message ' ]
			[ ' _t: DeleteItem ' ]
			<button type="submit">[ ' _t: Delete ' ]</button>
		</form>
		[ ' message ' ]
	=]
	[= l _ =
		<br><b>[ ' _t: ContactList ' ]:</b><br><br>
		<form action="[ ' hrefform ' ]" method="post" name="edit_contacts">
			[ ' csrf: edit_contacts ' ]
			<input type="hidden" name="insert" value="1">
			<table border="1" cellspacing="0" width="70%" align="left">
				<tr>
					<td><strong>[ ' _t: ContactNames ' ]</strong></td>
					<td><strong>[ ' _t: Notes ' ]</strong></td>
					<td> </td>
				</tr>
				<tr>
					<td>
						<select name="field1_value" required>
							<option value="">[ ' _t: ChooseRecipient ' ]</option>
							[= o _ =
								<option value="[ ' userid ' ]"[ ' selected ' ]>[ ' username ' ]</option>
							=]
						</select>
					</td>
					<td>
						<input type="text" size="35" maxlength="65" name="field2_value">
					</td>
					<td colspan="2" align="center">
						<button type="submit">[ ' _t: Add ' ]</button>
					</td>
				</tr>
				[= c _ =
					<tr>
						<td><a href="[ ' hrefcompose ' ]">[ ' username ' ]</a></td>
						<td>[ ' notes ' ]</td>
						<td><a href="[ ' hrefdelete ' ]">[ ' _t: Delete ' ]</a></td>
					</tr>
				=]
			</table>
			<table>
				<td width="25"></td>
				<td width="150">
					<span class="cite">[ ' _t: ClickContact ' ]</span><br><br>
					Um einen Benutzer zu Deiner Kontaktliste hinzuzufügen bitte <a href="[ ' hrefusers ' ]">hier</a> klicken!<br><br>
				</td>
			</table>
		</form>
	=]
	[= m _ =
		<br><b>[ ' _t: FolderList ' ]:</b><br><br>
		<form action="[ ' hrefform ' ]" method="post" name="message_folders">
			[ ' csrf: message_folders ' ]
			<input type="hidden" name="insert" value="1">
			<table border="1" cellspacing='0' width="65%" align="left">
				<tr>
					<td><strong>[ ' _t: Folder ' ]</strong></td>
					<td><strong>[ ' _t: Notes ' ]</strong></td>
					<td> </td>
				</tr>
				<tr>
					<td>
						<input type="text" size="25" maxlength="65" name="field1_value"></td>
					<td>
						<input type="text" size="35" maxlength="65" name="field2_value">
					</td>
					<td colspan="2" align="center">
						<button type="submit">[ ' _t: Add ' ]</button>
					</td>
				</tr>
				[= f _ =
					<tr>
						<td><a href="[ ' hreffolder ' ]">[ ' info ' ]</a></td>
						<td>[ ' notes ' ]</td>
						<td>
							<a href="[ ' hrefdelete ' ]">[ ' _t: Delete ' ]</a>
						</td>
					</tr>
				=]
			</table>
			<table>
				<tr>
					<td width="25"></td>
					<td width="200">
						<span class="cite">[ ' _t: ClickFolder ' ]</span><br><br><b>Ordner erstellen:</b><br><br>
						Gib in das leere Feld unter <b>Ordner:</b> den Namen für den neuen Ordner ein.<br><br>In das Feld <b>Beschreibung:</b> kannst Du eine Beschreibung für den Ordner eingeben.<br><br>
						Nun noch ein Klick auf <b>Hinzufügen</b> und der neue Ordner steht Dir zur Verfügung.
					</td>
				</tr>
			</table>
		</form>
	=]
	[= n _ =
		<table width="650">
			<tr>
				<td>
					<br><b>[ ' _t: Users ' ]:</b><br><br>
					[= u _ =
						<a href="[ ' hrefcontact ' ]">[ ' username ' ]</a><br>
					=]
				</td>
			</tr>
		</table>
		<span class="cite"><br><br>[ ' _t: ClickContact2 ' ]</span><br><br>
	=]

	[ ' z help ' ]


[ == help == ]
[ ' nonstatic ' ]
<table width="100%">
	<tr>
		<td>
			<a name= anfang></a>
			<h3>Hilfe für das WackoWiki-Message-System</h3>
			<br><br>Auf dieser Seite gibt es einige Hilfestellungen zu den Funktionen des WackoWiki-Message-Systems. Die Erklärungen sind kurz gehalten, da die meisten Funktionen selbsterklärend sind.<br>Nachfolgend findest Du ein Inhaltsverzeichnis zu den hier erläuterten Funktionen:
			<br><br><br>
			<a href=#post>Posteingang</a><br>
			<a href=#verf>Verfassen</a><br>
			<a href=#vers>Postausgang</a><br>
			<a href=#verw>Verwalten</a><br>
				- <a href=#ordn>Ordner</a><br>
				- <a href=#kont>Kontaktliste</a><br>
			<a href=#benu>Benutzer</a><br><br>
			<a name= post><h3>Posteingang</h3></a><br><br>
			Im Posteing werden alle eingetroffenen Nachrichten angezeigt, sowie einige zusätzliche Info`s zu den einzelnen Nachrichten.<br><br>
			In der folgenden Darstellung siehst Du ein Beispiel, wie der Posteingangsordner aussehen kann:<br><br><br>
			<table>
				<tr bgcolor="#93B2DD">
					<td width="400">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tr><td width="100"><b> Betreff</b></td></tr>
						</table>
					</td>
					<td width="100"><b> Absender</b></td>
					<td width="250"><b> Verschieben in Ordner</b></td>
					<td width="80"><b> Löschen</b></td>
				</tr>
			</table>
			<table>
				<tr>
					<td width="400">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tr><td width="100"><span class="cite">!*</span><a href=#test>Testnachricht</a> (03Jun06 2:57 pm)</td></tr>
						</table>
					</td>
					<td width="100"><a href=#testuser>Testuser</a> [<a href=#pfeil>-></a>]</td>
					<td width="250">
						<form method=post>
							<select name="move2folder"></select>
							<button type="submit">Verschieben</button>
						</form>
					</td>
					<td width="80"><a href=#loeschen>Löschen</a></td>
				</tr>
			</table>
			<table>
				<tr>
					<td width="400">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
							<tr>
								<td width="100">
									<span class="cite">!</span><a href=#test1>Testnachricht1</a><font color= #808080><small><b> beantwortet am:</b> (01Jun06 1:02 pm)</small></font>
								</td>
							</tr>
						</table>
					</td>
					<td width="100"><a href=#testuser>Testuser16</a> [<a href=#pfeil>-></a>]</td>
					<td width="250"><right>
						<form method=post>
							<select name="move2folder"></select>
							<button type="submit">Verschieben</button>
						</form></right>
					</td>
					<td width="80"><a href=#loeschen>Löschen</a></td>
				</tr>
			</table><br>
			<a href=#anfang>Seitenanfang</a><br><br>
			<h4>Was bedeuten die Zeichen vor und hinter den Nachrichten?</h4>
			<br><br> - Das <b><span class="cite">!</span></b> vor einer Nachricht erscheint, wenn der Absender die Nachricht als <b>dringend</b> markiert hat.
			<br><br> - Das <b><span class="cite">*</span></b> vor der Nachricht zeigt an, das die Nachricht noch nicht gelesen wurde. Sobald die Nachricht gelesen wurde verschwindet<br>
			das <b><span class="cite">*</span></b> und der Absender erhält die Information, dass die Nachricht gelesen wurde.<br><br>
			- Die Daten hinter der Nachricht geben an, wann die Nachricht erhalten bzw. gesendet wurde. Ausserdem wird in einer anderen<br>
			Farbe angezeigt ob und wann eine Nachricht beantwortet wurde.<br><br>
			<h4>Wie kann ich eine Nachricht öffnen?</h4><br><br>
			Um eine Nachricht zu lesen muss man auf den <b>Betreff</b> der Nachricht klicken, die geöffnet werden soll. Anschliessend wird die Nachricht<br>
			in einem neuen Fenster dargestellt.<br><br>
			<h4>Nachrichten in andere Ordner verschieben!</h4><br><br>
		</td>
	</tr>
</table>

[= pagination =]
<nav class="pagination">[ ' text ' ]</nav>
