

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

    
    Version htn2src.2.1-RC2
    
    Systemanforderungen:
      PHP 4: mindestens PHP 4.2.0 (atm nicht lauff�hig unter PHP 5)
      MySQL 4.0.x
      Apache-Webserver empfohlen
      
      Wichtig f�r Installation:
      Unter Linux:
        chmod -R 0777 data
      oder
        chown -R <apache-user> data           
    
1. Lizenz
   Dieser Quellcode steht unter einer Creative Commons License:
   http://creativecommons.org/licenses/by-nc-sa/2.0/de/
   (Namensnennung-NichtKommerziell-Weitergabe unter gleichen Bedingungen 2.0 Deutschland)
   zusammengefasst auch in der Datei license_by-nc-sa_2.0_de.txt ...
   Der vollst�ndige Text kann hier abgerufen werden: http://creativecommons.org/licenses/by-nc-sa/2.0/de/legalcode
   
   Au�erdem sind Sie nicht berechtigt, den Hinweis unter "Team" oder den Link auf www.hackthenet.org zu entfernen.
   
   Die Icons im Crystal-Stylesheet stehen unter LGPL. Details siehe lizenz.txt und
   lgpl.txt im static-Verzeichnis.

2. Haftungsausschluss
   Die Autoren dieses Quelltexts k�nnen nichts garantieren und keinerlei Verantwortung
   f�r jegliche Fehler oder Sch�den die durch diesen Quelltext verursacht werden, �bernehmen.
   Wir k�nnen f�r nichts, was Ihnen, Ihrem Computer, Ihrer Katze, Ihrem Sexleben oder irgendetwas
   anderem durch die Benutzung oder Nicht-Benutzung des Quelltextes passieren kann, Verantwortung
   �bernehmen. Sie benutzen den Quelltext zu 100% zu ihrem eigenen Risiko!
   Es besteht ebenfalls kein Anspruch auf Support.
   
3. Installation
   F�hren Sie die SQL-Befehle in der Datei DATABASE.DUMP.SQL aus (z.B. mit phpMyAdmin).
   Dadurch wird eine Datenbank htn_server1 angelegt.
   Wenn Sie den Namen der DB nicht �ndern bzw. keine neuen Datenbanken anlegen k�nnen,
   benutzen sie die Datei DATABASE-TABLES.DUMP.SQL.TXT.
   (siehe zu diesem Thema auch config.php)
   Jetzt k�nnen sie sich schon mit folgenden Benutzern einloggen:
    Administrator
    TestUser
   Die Passw�rter f�r die Accounts sind jeweils ein leeres Passwortfeld. Der erste
   Account ist im "god-mode". Er kann nicht angegriffen werden. Au�erdem stehen
   von diesem Account aus Administrator-Funktionen zur Verf�gung, man kann also die Daten
   von Spielern, PCs und Clustern einsehen und �ndern.
   Weitere Accounts k�nnen sie �ber die Registrieren-Funktion hinzuf�gen!

4. Einstellungen
   Alle Einstellungs-M�glichkeiten finden Sie in der Datei config.php, welche mit jedem Text-Editor bearbeitet
   werden kann.

5. Wie man sich am besten zurechtfindet.
   Man nehme eine installiertes HackTheNet und klicke ein bisschen auf den Links rum.
   In der URL in der Adresszeile findet man einen Parameter, der page, a, action, m oder
   mode hei�t.
   Dann �ffne man die entspr. Datei und suche dort nach Wert dieses Parameters. Dann d�rfte
   man relativ schnell f�ndig werden!
   
X. Enjoy
   Trotz des schlechten Programmierstils w�nschen wir allen viel Spa� mit diesem Code!
   Das HackTheNet-Team
   
   
   
   
