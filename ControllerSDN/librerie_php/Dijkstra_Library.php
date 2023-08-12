
<?php

function Dijkstra($graph, $source, $destination)
{
	/*
	Complessità O(n) dove 'n' è il numero di Nodi.

	Implementazione dell'algoritmo di Dijkstra che lavora su una matrice che rappresenta la topologia della rete.
		Se graph[i][j] = k => Il percorso dal nodo i al nodo j costa k.
		Se graph[i][j] = 99999 => Il percorso dal nodo i al nodo j non esiste.
	*/

	// https://codereview.stackexchange.com/questions/75641/dijkstras-algorithm-in-php
	//the start and the end
	$a = $source;
	$b = $destination;

	//initialize the array for storing
	$S = array(); //the nearest path with its parent and weight
	$Q = array(); //the left nodes without the nearest path
	foreach (array_keys($graph) as $val) $Q[$val] = 99999;
	$Q[$a] = 0;

	//start calculating
	while (!empty($Q)) {
		$min = array_search(min($Q), $Q); //the most min weight
		if ($min == $b) break;
		foreach ($graph[$min] as $key => $val) if (!empty($Q[$key]) && $Q[$min] + $val < $Q[$key]) {
			$Q[$key] = $Q[$min] + $val;
			$S[$key] = array($min, $Q[$key]);
		}
		unset($Q[$min]);
	}

	//list the path
	$path = array();
	$pos = $b;
	while ($pos != $a) {
		$path[] = $pos;
		$pos = $S[$pos][0];
	}
	$path[] = $a;
	$path = array_reverse($path);

	//print result
	/*
	echo "<br />Sorgente: $a <br> Destinazione: $b";
	echo "<br />Lunghezza del Percorso: " . $S[$b][1];
	echo "<br />Il percorso è [" . implode(' => ', $path) . "] <br><br>";
	*/

	return $path;
}

function Print_Path($path , $source , $destination){
	echo "<br />Sorgente: $source <br> Destinazione: $destination";
	echo "<br />Il percorso e' [" . implode(' => ', $path) . "] <br><br>";
}

function SPF($graph, $source, $destination , $Not_Optional_Nodes){
	/*
	$Not_Optional_Nodes è un array che contiene i nodi da cui il 
	percorso deve passare per forza a prescindere dal costo.

	SPF() restituirà un array che contiene il percorso di costo minimo da source a destination 
	il quale passa anche per i nodi inclusi in $Not_Optional_Nodes.

	Supponiamo che $Not_Optional_Nodes = [a , b]

	SPF eseguirà:
		1) Dijkstra($graph, $source, a)
		2) Dijkstra($graph, a , b)
		3) Dijkstra($graph, b , destination)

	Quindi l'ordine con cui sono inseriti i nodi in $Not_Optional_Nodes NON DEVE ESSERE CASUALE.
	
	*/

	$Num_Not_Opt = count($Not_Optional_Nodes);

	if(
		($Num_Not_Opt == 0) || 
		(($Num_Not_Opt == 1) && ($Not_Optional_Nodes[0] == $source || $Not_Optional_Nodes[0] == $destination))
	){
		// Non ho vincoli, calcolo Dijkstra normalmente.
		return Dijkstra($graph , $source , $destination);
	}

	if($Num_Not_Opt == 1){
		$Paths = array(); // tutti i percorsi parziali che poi alla fine verranno uniti

		$Paths[0] = Dijkstra($graph , $source , $Not_Optional_Nodes[0]);
		array_pop($Paths[0]);
		$Paths[1] = Dijkstra($graph , $Not_Optional_Nodes[0] , $destination);

		return array_merge($Paths[0] , $Paths[1]);
	}

	// Caso Generico

	$Paths = array(); // tutti i percorsi parziali che poi alla fine verranno uniti

	// Primo path parziale
	$Paths[0] = Dijkstra($graph , $source , $Not_Optional_Nodes[0]);
	array_pop($Paths[0]); 

	for($i = 1 ; $i < $Num_Not_Opt ; $i++){
		$Paths[$i] = Dijkstra($graph , $Not_Optional_Nodes[$i - 1], $Not_Optional_Nodes[$i]);
		array_pop($Paths[$i]); 
		// elimino l'ultimo elemento del Path, in modo tale che al termine 
		// posso fare facilmente la congiunzione dei percorsi parziali
	}

	// Ultimo path parziale
	$Paths[$Num_Not_Opt] = Dijkstra($graph , $Not_Optional_Nodes[$Num_Not_Opt - 1] , $destination);

	$ret = $Paths[0];
	for($i = 1; $i <= ($Num_Not_Opt); $i++){
		$ret = array_merge($ret , $Paths[$i]);
	}

	return $ret;
}


?>

