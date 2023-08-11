
<?php

function Dijkstra($graph, $source, $destination,  $NodiObbligati)
{
	// https://codereview.stackexchange.com/questions/75641/dijkstras-algorithm-in-php
	//the start and the end
	$a = $source;
	$b = $destination;

	/*
	$dim_graph = count($graph[0]);
	$dim_NO = count($NodiObbligati);

	$graph_backup = $graph;

	for ($j = 0; $j < $dim_NO; $j++) {
		for ($i = 0; $i < $dim_graph; $i++) {
			$graph[$NodiObbligati[$j]][$i] = -99;
			$graph[$i][$NodiObbligati[$j]] = -99;
		}
	}
	*/


	//initialize the array for storing
	$S = array(); //the nearest path with its parent and weight
	$Q = array(); //the left nodes without the nearest path
	foreach (array_keys($graph) as $val) $Q[$val] = 99999;
	$Q[$a] = 0;

	//$AlreadySeen = array();
	//$count = 0;
	//$AlreadySeen[$count] = $a;
	//start calculating
	while (!empty($Q)) {

		$min = array_search(min($Q), $Q); //the most min weight

		if ($min == $b) break;

		foreach ($graph[$min] as $key => $val) {
			if (!empty($Q[$key]) && $Q[$min] + $val < $Q[$key]) {
				$Q[$key] = $Q[$min] + $val;
				$S[$key] = array($min, $Q[$key]);
			}
		}

		unset($Q[$min]);
	}

	/*
	if (!array_key_exists($b, $S)) {
		echo "Found no way.";
		return;
	}
	*/


	//list the path
	$path = array();
	$pos = $b;
	while ($pos != $a) {
		$path[] = $pos;
		$pos = $S[$pos][0];
	}
	$path[] = $a;
	$path = array_reverse($path);

	echo "<br />From $a to $b";
	//echo "<br />The length is ".$S[$b][1];
	echo "<br />Path is " . implode('->', $path) . "<br>";

	return $path;
}


function Dijkstra2($graph, $source, $destination,  $NodiObbligati)
{
	$MAX_VALUE = 99999;
	/*
	if($source == $destination){
		$ret = array(1 => $source);
		return $ret;
	}
	*/

	$n = count($graph[0]); // Numero dei Nodi

	$N_vis = array();
	$N = array(); // Vettori dei Nodi da estrarre
	$K = array(); // vettore delle Etichette
	$P = array(); // Vettore dei Predecessori

	//Inizializzazione dei Vettori
	for ($i = 0; $i < $n; $i++) {
		$N[$i] = $i;
		if ($i == $source) {
			$K[$i] = 0;
			$P[$i] = 1;
		} else {
			$K[$i] = $MAX_VALUE;
			$P[$i] = -1;
		}
	}

	while (true) {
		//Estrazione del Nodo con Etichetta Minima.
		$nodo_estratto = EstraiIndiceConValoreMinimo($K);

		// Calcolo la "Stella uscente"
		// ossia i nodi raggiungibili con archi che partono dal nodo estratto.
		$Fs = CostruisciStellaUscente($nodo_estratto, $graph[$nodo_estratto]);

		$Dim_Fs = count($Fs);

		$K_nodo = $K[$nodo_estratto];

		for ($i = 0; $i < $Dim_Fs; $i++) {
			$K_j = $K[$Fs[$i]]; // Valore dell'etichetta del nodo $Fs[$i]
			$C_i_j = $graph[$nodo_estratto][$Fs[$i]];

			if($K_j > $K_nodo + $C_i_j){
				// Aggiornamento
				$K[$Fs[$i]] = $K_nodo + $C_i_j;
				$P[$Fs[$i]] = $nodo_estratto;
			}
		}

		break;
	}
}

function EstraiIndiceConValoreMinimo($arr)
{
	$min = $arr[0];
	$ret = 0;
	$n = count($arr);

	if ($n == 1) return 0;

	for ($i = 1; $i < $n; $i++) {
		if($arr[$i] < 0){
			continue;
		}
		if ($arr[$i] < $min) {
			$min = $arr[$i];
			$ret = $i;
		}
	}

	return $ret;
}

function CostruisciStellaUscente($Indice_Nodo_Sorgente, $riga_nodo_scelto)
{
	$n = count($riga_nodo_scelto);

	$ret = array();
	for ($i = 0; $i < $n; $i++) {
		if ($riga_nodo_scelto[$i] != '-' && $i != $Indice_Nodo_Sorgente) {
			array_push($ret, $i);
		}
	}

	return $ret;
}

// array_search

// array_push --> Inserimento in coda

?>

