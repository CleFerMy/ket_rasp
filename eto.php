<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> <!-- FAVICON -->
	<title>RASP</title>

  </head>
  <body>

<?
require_once 'simple_html_dom.php';
require_once 'simplexlsx.php';

$html =  file_get_html('http://spo-ket.ru/node/394');


$full = $html->find('div.article a');
$name_url = $full[count($full)-4]->plaintext;
$url = $full[count($full)-4]->href;
$url_full = 'http://spo-ket.ru'.$url;

$rasp_data = file_get_contents($url_full);
$rasp_print = SimpleXLSX::parse($rasp_data, true);

	if ( $rasp_print ) {
		$rasp_refl = new ReflectionObject($rasp_print);

		$sharedstrings = $rasp_refl->getProperty('sharedstrings');
		$sharedstrings->setAccessible(true);

		$sheets = $rasp_refl->getProperty('sheets');
		$sheets->setAccessible(true);

		$spans = explode(':',trim($sheets->getValue($rasp_print)[1]->sheetData->row[0]->attributes()->spans));

$msg[] = '
	<table>
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>';

    foreach($sheets->getValue($rasp_print)[1]->sheetData->row as $element)
    {
      $echo = array();
    	$echo[] = '<tr>';
        foreach($element->c as $product)
        {
        	if(!empty($product->v) || $product->v == '0'){
        		$echo[] = '<td>'.$sharedstrings->getValue($rasp_print)[(int)$product->v] .'</td>';
        	}else{
        		$echo[] = '<td></td>';
        	}
        }
        $echo[] = '</tr>';
        $echo = implode('', $echo);
        if($echo == '<tr><td></td></tr>' || $echo == '<tr><td></td><td></td></tr>' || $echo == '<tr><td></td></tr><td></td>' || $echo == '<tr><td></td><td></td><td></td><td></td></tr>'){

        }else{
          $msg[] = $echo;
        }
    }


	} else {
		$msg[] = SimpleXLSX::parse_error();
	}
$msg[] =  '	</tbody>
	</table><br><br>';
$kett = array();
$kettt = array();
$ket =  str_get_html(implode('',$msg));
$ket_f = $ket->find('table tbody', 0)->find('tr');
for($x=0; $x<count($ket_f); $x++){
  if($ket_f[$x]->find('td', 0)->plaintext == '4-1 это'){
    if(empty($ket_f[$x]->find('td',1)->plaintext)) $ket_f[$x]->find('td',2)->plaintext = '?';
    if(!empty($ket_f[$x]->find('td',3)->plaintext)) $ket_f[$x]->find('td',3)->plaintext = ' ['.$ket_f[$x]->find('td',3)->plaintext.']';
    $kettt[] = $ket_f[$x]->find('td',1)->plaintext.' - '.$ket_f[$x]->find('td',2)->plaintext.$ket_f[$x]->find('td',3)->plaintext;
    for($z=$x+1; $z<count($ket_f); $z++){
      if(empty($ket_f[$z]->find('td', 0)->plaintext) && !empty($ket_f[$z]->find('td', 1)->plaintext)){
        if(empty($ket_f[$z]->find('td',1)->plaintext)) $ket_f[$z]->find('td',2)->plaintext = '?';
        if(!empty($ket_f[$z]->find('td',3)->plaintext)) $ket_f[$z]->find('td',3)->plaintext = ' ['.$ket_f[$z]->find('td',3)->plaintext.']';
        $kettt[] = $ket_f[$z]->find('td',1)->plaintext.' - '.$ket_f[$z]->find('td',2)->plaintext.$ket_f[$z]->find('td',3)->plaintext;
      }else{
        break;
      }
    }
    $kett[] = implode('<br>', $kettt);
  }
}
echo $name_url.'<br>';
echo implode('<br>', $kett);
?>
</body>
</html>
