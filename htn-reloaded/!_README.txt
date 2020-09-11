

    +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    ++                                                                     ++
    ++     HH     HH  TTTTTTTTTTTTT  NNN      NN                           ++
    ++     HH     HH       TT        NNNNN    NN                           ++
    ++     HH     HH       TT        NN  NN   NN               2222222     ++
    ++     HH HHH HH       TT        NN   NN  NN   vv      vv       22     ++
    ++     HH     HH       TT        NN    NN NN    vv    vv    222222     ++
    ++     HH     HH       TT        NN     NNNN     vv  vv     22         ++
    ++     HH     HH       TT        NN      NNN      vvvv      222222     ++
    ++                                                                     ++
    ++   H A C K T H E N E T  V E R S I O N  2/2.1  [ Q U E L L C O D E ]  ++
    ++                                                                     ++
    +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


    >>> Bei Fragen besuchen Sie das Quelltext-Forum auf www.hackthenet.org <<<
    >>>        Anfragen per Email oder PM werden nicht beantwortet!        <<<

    
    Version htn Reload 2.1

      Systemanforderungen:
      PHP 5.2: mindestens PHP 5.2.0
      MySQL 4.0.x
    
      Apache-Webserver empfohlen
      
      Wichtig für Installation:
      Unter Linux:
        chmod -R 0777 data
      oder
        chown -R <apache-user> data           
    
1. Lizenz
   Dieser Quellcode steht unter einer Creative Commons License:
   http://creativecommons.org/licenses/by-nc-sa/2.0/de/
   (Namensnennung-NichtKommerziell-Weitergabe unter gleichen Bedingungen 2.0 Deutschland)
   zusammengefasst auch in der Datei license_by-nc-sa_2.0_de.txt ...
   Der vollständige Text kann hier abgerufen werden: http://creativecommons.org/licenses/by-nc-sa/2.0/de/legalcode
   
   Außerdem sind Sie nicht berechtigt, den Hinweis unter "Team" oder den Link auf www.hackthenet.org zu entfernen.
   
   Die Icons im Crystal-Stylesheet stehen unter LGPL. Details siehe lizenz.txt und
   lgpl.txt im static-Verzeichnis.

2. Haftungsausschluss
   Die Autoren dieses Quelltexts können nichts garantieren und keinerlei Verantwortung
   für jegliche Fehler oder Schäden die durch diesen Quelltext verursacht werden, übernehmen.
   Wir können für nichts, was Ihnen, Ihrem Computer, Ihrer Katze, Ihrem Sexleben oder irgendetwas
   anderem durch die Benutzung oder Nicht-Benutzung des Quelltextes passieren kann, Verantwortung
   übernehmen. Sie benutzen den Quelltext zu 100% zu ihrem eigenen Risiko!
   Es besteht ebenfalls kein Anspruch auf Support.
   
3. Installation
   Führen Sie die SQL-Befehle in der Datei DATABASE.DUMP.SQL aus (z.B. mit phpMyAdmin).
   Dadurch wird eine Datenbank htn angelegt.
   Wenn Sie den Namen der DB nicht ändern bzw. keine neuen Datenbanken anlegen können,
   benutzen sie die Datei DATABASE-TABLES.DUMP.SQL.TXT.
   (siehe zu diesem Thema auch config.php)
   Jetzt können sie sich schon mit folgenden Benutzern einloggen:
    Administrator
    TestUser
   Die Passwörter für die Accounts sind jeweils ein leeres Passwortfeld. Der erste
   Account ist im "god-mode". Er kann nicht angegriffen werden. Außerdem stehen
   von diesem Account aus Administrator-Funktionen zur Verfügung, man kann also die Daten
   von Spielern, PCs und Clustern einsehen und ändern.
   Weitere Accounts können sie über die Registrieren-Funktion hinzufügen!

4. Einstellungen
   Alle Einstellungs-Möglichkeiten finden Sie in der Datei config.php, welche mit jedem Text-Editor bearbeitet
   werden kann.

5. Wie man sich am besten zurechtfindet.
   Man nehme eine installiertes HackTheNet und klicke ein bisschen auf den Links rum.
   In der URL in der Adresszeile findet man einen Parameter, der page, a, action, m oder
   mode heißt.
   Dann öffne man die entspr. Datei und suche dort nach Wert dieses Parameters. Dann dürfte
   man relativ schnell fündig werden!

6  Config anpassung der Zugangsdaten für die Datenbank
   Betroffen sind die Zeillen 13 bis 22
   $db_username = 'benutzer';  benutzer hier mit dem Benutzernamen der Datenbank ersetzen
   $db_password = 'password';  password mit dem password der Datenbank ersetzen
   
X. Enjoy
   Trotz des schlechten Programmierstils wünschen wir allen viel Spaß mit diesem Code!
   Das HackTheNet-Team
   
   

   
