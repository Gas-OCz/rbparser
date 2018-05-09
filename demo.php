<?
$mbox = imap_open ("{pop3.sitepark.cz:110/pop3/novalidate-cert}INBOX", "jmeno", "heslo");
$headers = imap_headers($mbox);
$count = count($headers);

for($x=1;$x<=$count;$x++)
{
        $p = new platba($x,$mbox);
         //var_symbol, konstantni_symbol, specificky_symbol, kodbanky, ucet, jmeno, castka,
        echo "1) ";
        echo $p->getNode("datum");
        echo "<br>";
        
        echo "2) ";
        echo $p->getNode("var_symbol");
        echo "<br>";
        
        echo "3) ";
        echo $p->getNode("konstantni_symbol");
        echo "<br>";
        
        echo "4) ";
        echo $p->getNode("specificky_symbol");
        echo "<br>";
        
        echo "5) ";
        echo $p->getNode("ucet");
        echo "<br>";
        
        echo "6) ";
        echo $p->getNode("kodbanky");
        echo "<br>";
        
        echo "7) ";
        echo $p->getNode("jmeno");
        echo "<br>";
        
        echo "8) ";
        echo $p->getNode("castka");
        echo "<br>";
        
        echo "9) ";
        echo $p->getNode("zprava");
        echo "<br>";
}
?>
