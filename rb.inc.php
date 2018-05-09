<?
class platba {
  function __construct($uid, $imap)
  {
      $overview = imap_fetch_overview($imap, $uid, 0);
      if(iconv_mime_decode($overview[0]->subject)=="Pohyb na účtě") {
        $body = $this->get_part($imap, $uid, "TEXT/HTML");
        // if HTML body is empty, try getting text body
        if ($body == "") {
            $body = $this->get_part($imap, $uid, "TEXT/PLAIN");
        }
        
        $this->getDom($body);
      }
      else {
        return false;
      }
  }
  function getDom($body) {
    $document = new DOMDocument();
    $internalErrors = libxml_use_internal_errors(true);

    $document->loadHTML(html_entity_decode ($body));
    $p = $document->getElementsByTagName('p');
    $this->message = $p;
  }
  
  function getNode($node, $hour = false) {
      for ($i = 0; $i < $this->message->length; $i++) {
      //echo $this->message->item($i)->nodeValue."<br>";
          if($this->message->item($i)->nodeValue == "Datum a čas") {
            $pole["datum"] = $this->getDate($this->message->item($i+1)->nodeValue,$hour);
          }
          if($this->message->item($i)->nodeValue == "Částka v měně účtu") {
            $pole["castka"] = $this->getPrice($this->message->item($i+1)->nodeValue);
          }
          if($this->message->item($i)->nodeValue == "Z účtu") {
            $txt = $this->message->item($i+1)->nodeValue;
            $pole["jmeno"] = substr(strstr($txt,"/"),5,strlen(strstr($txt,"/")));
            $pole["ucet"] = substr(strstr($txt,"/",true),0,strlen(strstr($txt,"/",true)));
            $pole["kodbanky"] = substr(strstr($txt,"/"),1,4);
          }
          if($this->message->item($i)->nodeValue == "Variabilní symbol") {
            $pole["var_symbol"] = $this->message->item($i+1)->nodeValue;
          }
          if($this->message->item($i)->nodeValue == "Konstantní symbol") {
            $pole["konstantni_symbol"] = $this->message->item($i+1)->nodeValue;
          }
          if($this->message->item($i)->nodeValue == "Zpráva pro příjemce") {
            $pole["zprava"] = $this->message->item($i+1)->nodeValue;
          }  
          if($this->message->item($i)->nodeValue == "Specifický symbol") {
            $pole["specificky_symbol"] = $this->message->item($i+1)->nodeValue;
          }  
      }
      return $pole[$node];
  }
  function getDate($date,$hour = false) {
    $date = str_replace(". ",".",$date);
    $d = new DateTime($date);
    if($hour == false) {
      return $d->format("Y-m-d");
    }
    else {
      return $d->format("Y-m-d H:i:s");
    }
  }
  function getPrice($price) {
      $price = str_replace("+","",$price);
      $price = str_replace(" CZK","",$price);
      $price = str_replace(",",".",$price);
      return $price;
  }
  function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false)
  {
      if (!$structure) {
          $structure = imap_fetchstructure($imap, $uid, FT_UID);
      }
      if ($structure) {
          if ($mimetype == $this->get_mime_type($structure)) {
              if (!$partNumber) {
                  $partNumber = 1;
              }
              $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
              switch ($structure->encoding) {
                  case 3:
                      return imap_base64($text);
                  case 4:
                      return imap_qprint($text);
                  default:
                      return $text;
              }
          }

          // multipart
          if ($structure->type == 1) {
              foreach ($structure->parts as $index => $subStruct) {
                  $prefix = "";
                  if ($partNumber) {
                      $prefix = $partNumber . ".";
                  }
                  $data = $this->get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                  if ($data) {
                      return $data;
                  }
              }
          }
      }
      return false;
  }

  function get_mime_type($structure)
  {
      $primaryMimetype = ["TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"];

      if ($structure->subtype) {
          return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
      }
      return "TEXT/PLAIN";
  }
}
?>
