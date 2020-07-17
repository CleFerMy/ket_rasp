<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<title>RASP</title>

  </head>
  <body>

<div class="container">
	<div class="margin_top_20 thumbnail row">

<?
require_once 'simple_html_dom.php';
require_once 'simplexlsx.php';
$html =  file_get_html('http://spo-ket.ru/node/394');


$full = $html->find('div.article a');
$url = $full[1]->href;
echo '
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<strong>Открыт документ: http://spo-ket.ru'.$url.'</strong>
</div>';
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

echo '<div class="table-responsive">
	<table class="table">
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
	echo '<tr>';
    foreach($element->c as $product)
    {
    	if(!empty($product->v) || $product->v == '0'){
    		echo '<td>'.$sharedstrings->getValue($rasp_print)[(int)$product->v] .'</td>';
    	}else{
    		echo '<td></td>';
    	}
    }
    echo '</tr>';
}


	} else {
		echo SimpleXLSX::parse_error();
	}
echo '	</tbody>
	</table></div>';



?>
</div>

    <script src="https://code.jquery.com/jquery.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
