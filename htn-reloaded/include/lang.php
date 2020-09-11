<?php

class lang
{
    public function includeLang($SelLanguage, $filename, $ext = '.lng')
    {
        global $lang;

        include ("./lang/". $SelLanguage ."/". $filename . $ext);
    }
}
?>