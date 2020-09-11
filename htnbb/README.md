# htnbb
The original HTN.BB burning board from Ingmar Runge.





#### So, 28. Mai. 06 um 00:53:37 Uhr

>Bevor mich jeder einzeln per Email fragt, hier die Antwort für alle:
>
>Hi,
>
>glaube nicht, dass das htnBB ist was du suchst, denn es gibt weder eine
>vernünftige Installation, noch eine Dokumentation und am schlimmsten -
>die Administrator-Oberfläche fehlt praktisch komplett. Neue
>Foren/Kategorien anlegen, Benutzer in Gruppen packen und so weiter, das
>lässt sich alles nur über die Datenbank (phpMyAdmin oder so) machen.
>
>Als Tipp für schlanke Foren könnte ich dir noch mit auf den Weg geben:
>
>miniBB: http://www.minibb.net/
>
>aterr: http://chimaera.starglade.org/aterr/
>
>Grüße
>Ingmar



#### So, 28. Mai. 06 um 13:02:51 Uhr


>Wenn ihr so geil drauf seid, hier:
>
>http://irsoft.de/web/stuff (htnbb.7z)
>
>Ich gebe aber keinen Support! Viel Spaß also beim Zum-Laufen-Bringen

_________________________________________________
Problem:
    Fehler vom Typ 2 in common_functions.php in Zeile 117: mysqli::select_db() [function.mysqli-select-db]: invalid object or resource mysqli
	
Lösung:
    common_funktions.php editieren:
	
	1.  class mysql_crap vom Kommentar entfernen.
	2.  Von "$db = mysqli_init(); // neues MySQLi-Objekt erstellen"   bis "$db->select_db(DB_NAME);" kommentieren.
	3.  Von "$db = new mysql_crap;"  bis "mysql_select_db(DB_NAME);}"  vom Kommentar entfernen.
	
	
Problem:  Memcache macht probleme:

Lösung:
    board.php editieren:
      1. include 'core/memcache.php';     und  htnbb_memcache_delete_thread($topic_id);   kommentieren.

    browse.php editieren:   
      1. Von "include 'core/memcache.php';" bis "if(!$is_memcached) { "  kommentieren.
      2. Von "if($total_pages > 1)" bis "$memcache_data->reply_data = array_slice($memcache_data->reply_data, $start, $this->posts_per_page); }" kommentieren.

    modcp.php editieren:
      1. Von "include 'core/memcache.php';" bis "htnbb_memcache_delete_thread($topic_id);"  kommentieren.
