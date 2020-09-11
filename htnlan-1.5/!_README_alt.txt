 _    _ _______ _   _       _               _   _   
| |  | |__   __| \ | |     | |        /\   | \ | | 
| |__| |  | |  |  \| |     | |       /  \  |  \| | 
|  __  |  | |  | . ` |     | |      / /\ \ | . ` |
| |  | |  | |  | |\  |  _  | |____ / ____ \| |\  |
|_|  |_|  |_|  |_| \_| (_) |______/_/    \_\_| \_|
				      by Schnitzel

Um die HTN.LAN Version auf eurem Server laufen zu lassen 
müsst ihr zuerst die 'DATABASE.DUMP.SQL' z.B. in
PHPmyAdmin starten.

========================================================================

Danach müsst ihr in der 'config.php' diese Daten anpassen:

 - $database_prefix='htn_server';
   Die Datenbank auf dem ihr die Daten eingelesen habt, ohne 
   Zahl! Standartmässig ist die Datenbank 'htn_server1', also
   'htn_server' eintragen 

 - $db_use_this_values=true;
   Wenn ihr bei eurem MySQL Server ein Username / Passwort eingegeben 
   habt, müsst ihr hier true eingeben, ist es Login-Frei muss hier
   'false' rein. 

 - $db_host='localhost';
   Hier könnt ihr den MySQL Server eintragen, standartmässig
   'localhost' 

 - $db_username='root';
   Wenn ihr bei $db_use_this_values 'true' eingegeben habt,
   dann könnt ihr hier den Username eingeben.

 - $db_password='';
   Wenn ihr bei $db_use_this_values 'true' eingegeben habt,
   dann könnt ihr hier das Passwort für den Username eingeben.

========================================================================

Nun hat es in der HTN.LAN Version in der config.php eine spezielle
Varable: 'define('geschwindigkeit',10,false);' 
Hier könnt ihr die Geschwindigkeits-Zahl einstellen:

'1'   - normale/originale HTN Geschwindigkeit
'10'  - 10x schneller als original
'100' - 100x schneller als original

========================================================================

Speziell in dieser Version habe ich die 'fillcountry' Funktion von erazorlll
eingefügt, damit kann man die Einzelnen Subnetze mit 0-, 1024- oder 
Zufalls-Punkte PC's füllen oder Herrenlose PC's löschen. Die Funktion 
findet man als Administrator 'stat <= 100' im Menü unter dem Menüpunkt: 
'Subnetze füllen / leeren'. 

========================================================================

Update von 1.1.9b oder tiefer:

Wenn ihr von der Version 1.1.9b oder einer tieferen updatet, dann müsst
ihr im PHPmyAdmin die update.sql anzeigen (fixt einen Fehler in der DB)

========================================================================

Viel Spass bei der HTN.LAN Version!

========================================================================

Changelog 1.2.2:
- Anzahl Sekunden in denen kein Geld überwiesen und der Clusterbeitrag 
  nicht erhöht werden kann, nach der neuen Runde, eingefügt 
  (config.php, game.htn, cluster.htn)
- PC Suche und Notiz verbessern (game.htn)


Changelog 1.2.1:
- MaxPlayers bei Cluster auf 32 erhöht (ingame.php)
- PC's nach Geld sortieren (game.htn)
- Neue Angriffsformel (battle.htn, distrattack.htn, ingame.php)
- layout.php auf neue Version angepasst (layout.php)

Changelog 1.2.0:
- Beschränkung bei Geldüberweisung entfernt
- Bei einem HJ wird das Attribut "lmupd" neu reingeschrieben (so haben die 
  PC's nicht so viel Geld)
- Notizblok eingefügt
- Sortierung der PC's wird gespeichert
- PC Suche eingefügt (PC's können nach bestimmen Kriterien gesucht werden)
- Angriffsstärke bei einem Hijack vermindert (SDK zählt nicht mehr)
- Fehler bei abook.htn gefixt
- Erweiterte Statistiken eingefügt
- Member können sich aus dem Cluster selber geld schicken
- Der Server kann über die Datei /data/serverstop.txt für eine bestimmte Zeit
  (muss per UnixTimestamp eingegeben werden) gesperrt werden.

****WICHTIG****
- Wenn ihr von der Version 1.1.9b oder einer tieferen updatet, dann müsst
  ihr im PHPmyAdmin die update.sql auführen!!

Changelog 1.1.9b:
- Riesen-Fehler mit der config.php gefixt (min_user_points war nicht definiert)

Changelog 1.1.9:
- Fehler bei DistributeAttack gelöst

Changelog 1.1.8:
- Der Server kann nun auf eine bestimmte Zeit gesperrt werden 
  (Zeit kann in der /data/newround.txt per UnixTimeStamp angeben werden,
  Administratoren können sich trozdem einloggen!) 
  (gres.php, startseiten.php, login.htn)
- neue Fillcountry Version eingefühgt, gibt jetzt keine Fehler mehr

Changelog 1.1.7:
- Bei Passwort Änderung wird kein Mail mehr verschickt
- Bei Accountlöschung wird auch kein Mail verschickt
- Angriffsformel auf DistributeAttack erweitert
- Passwort zusenden deaktiviert
- Neue DATABASE.DUMP.SQL eingefüght (Countrys gefixt)
- Update.SQL für Updates von alten DB's

Changelog 1.1.6b:
- Fehler in der neuen Angriffsformel gefixt

Changelog 1.1.6:
- Neue Version von Fillcountry 0.4 eingefügt
- Neue Angriffformel nachzulesen: http://www.htn-lan.com/forum/thread.php?threadid=99
- Startgeld der neuen Pcs eingeführt, kann in der config.php eingegeben werden.
- Berechnungsfehler-hänger gefixt

Changelog 1.1.5:
- Cokkie-Sicherheitleck in login.htn geändert
- Einige kleine echo Fehler gefixt

Changelog 1.1.4:
- Fehler in fillcountry mit last-menu-update-Timestamp gefixt
- Fehler bei HTN nur alle 2 Tage gefixt 

Changelog 1.1.3:
- Fehler bei zu grossen Speed mit Money gefixt
- Kapazität des BB * Geschwindigkeit erhöht

Changelog 1.1.2:

- Fehler bei der Geschwindigkeit beim Ausbau von IDS gefixt (ingame.php)
- Fehler bei HJ ohne Cluster gefixt  (battle.htn)

Changelog 1.1.1:

- Fehler dass jeder die Subnetze füllen kann gefixt.

========================================================================

Bei Fragen oder Problemen:

ICQ:	77501874
Mail:	michael@x-page.ch
MSN:	schnitzel@comireel.ch