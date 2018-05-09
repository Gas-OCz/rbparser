# rb parser (raiffeisenbank nová verze bankovnictví 2018)

Parser pro emaily z RB. Slouží k rozparsování e-mailů z Internet Bankingu RB, pro automatizované zpracování přijatých plateb

Návod k použití:

Stačí přidat soubor třídy na čtení e-mailu a parsování dat a inicializovat je.

  require_once('rb.ini.php');
  
  $mbox = new imap('{imap.mujserver.cz}', 'muj@email.cz', 'mojeHesloKEmailu');
  $headers = imap_headers($mbox);
  $count = count($headers);
  
  for($x=1;$x<=$count;$x++) {
    $p = new platba($x,$mbox);
      //var_symbol, konstantni_symbol, specificky_symbol, kodbanky, ucet, jmeno, castka,
      echo $p->getNode("datum");
      echo "<br>";
  }
  
Třída platby předpokládá imap spojení a id zprávy, třída načte obsah do DOM documentu, funkce getNode("parametr") rozparsuje zprávu a vrátí hodnotu dle zadaní. Třida nekontroluje odesílatele zprávy, ale kontroluje očekáváný předmět posílaný RB bankou.

Pokud budou jakékoliv připomínky, či nápady na zlepšení, rád je do kódu přidám.
Doufám, že pomůže.
Za použití této knihovny a případně způsobené chyby nenesu zodpovědnost.
Gas-O (SitePark s.r.o.)
