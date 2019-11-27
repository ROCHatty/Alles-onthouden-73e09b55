<?php
if (isset($_GET["a"])){
	$msg = strtolower($_GET["a"]);
	if (strpos($msg, "laat notitie") !== false || strpos($msg, "wat heb je onthouden over") !== false){
		$a = explode('"', $msg)[1];
		$data = json_decode($_COOKIE["data"], true);
		if (isset($data[$a])){
			echo json_encode(array("msg"=>"De notitie " . $a . ":", "a"=>$data[$a]));
			die();
		}
		else{
			echo json_encode(array("msg"=>"Deze notitie bestaat niet!", "a"=>""));
			die();
		}
	}
	elseif (strpos($msg, "laat me alle titels van notities zien") !== false || strpos($msg, "wat heb ik allemaal opgeslagen") !== false){
		$data = array_keys(json_decode($_COOKIE["data"], true));
		$str = "Je hebt notities met de volgende titels: ";
		foreach ($data as $value) {
			$str .= $value . ", ";
		}
		$str = substr($str, 0, -2);
		echo json_encode(array("msg"=>$str, "a"=>""));
		die();
	}

}
elseif (isset($_GET["makenew"])) {
	$data = json_decode($_COOKIE["data"], true);
	$data[$_GET["makenew"]] = "";
	setcookie("data", json_encode($data), time() + (365 * 24 * 3600));
}
elseif (isset($_GET["editnew"])) {
	$data = json_decode($_COOKIE["data"], true);
	$data[$_GET["editnew"]] = $_GET["m"];
	setcookie("data", json_encode($data), time() + (365 * 24 * 3600));
}
elseif (isset($_GET["chat"])){
		$c = strtolower($_GET["chat"]);
		if (strpos($c, "bereken voor mij") !== false || strpos($c, "wat is") !== false || strpos($c, "hoeveel is") !== false || strpos($c, "bereken het volgende") !== false || strpos($c, "calculate") !== false){
		$c = str_replace("bereken voor mij ", "", $c);
		$c = str_replace("wat is ", "", $c);
		$c = str_replace("hoeveel is ", "", $c);
		$c = str_replace("bereken het volgende ", "", $c);
		$c = str_replace("calculate ", "", $c);
		$c = str_replace("?", "", $c);
		$c = str_replace(" ", "", $c);
		
		if (strpos($c, "+") !== false){
			$d = explode("+", $c);
			$e = 0;
			foreach ($d as $value) {
				$e += $value;
			}
		}
		elseif (strpos($c, "-") !== false){
			$d = explode("-", $c);
			$e = $d[0];
			array_shift($d);
			foreach ($d as $value) {
				$e -= $value;
			}
		}
		elseif (strpos($c, "*") !== false){
			$d = explode("*", $c);
			$e = $d[0];
			array_shift($d);
			foreach ($d as $value) {
				$e *= $value;
			}
		}
		elseif (strpos($c, "/") !== false){
			$d = explode("/", $c);
			$e = $d[0];
			array_shift($d);
			foreach ($d as $value) {
				$e /= $value;
			}
		}
		elseif (strpos($c, "%") !== false){
			$d = explode("%", $c);
			$e = $d[0];
			array_shift($d);
			foreach ($d as $value) {
				$e %= $value;
			}
		} else {
			echo json_encode(array("success"=>false, "a"=>$c));
			die();
		}
		echo json_encode(array("success"=>true, "a"=>$e, "b"=>$c));
		die();
	}
	else{
		echo json_encode(array("success"=>false, "a"=>$c));
		die();
	}
}
elseif (isset($_GET["name"])) {
	setcookie("name", $_GET["name"], time() + 3600);
	header("Location: chat.php");
}
elseif (!isset($_COOKIE["name"])) {
?>
<h1>Wat is je naam?</h1>
<form>
	<input type="text" name="name">
	<input type="submit" name="submit">
</form>
<?php
}
else {
 ?>
 <p>
 	De volgende woorden of zinnen worden herkent als een vraag om een berekening te doen
 	<ul>
 		<li>Bereken voor mij</li>
 		<li>Wat is</li>
 		<li>Hoeveel is</li>
 		<li>Bereken het volgende</li>
 		<li>Calculate</li>
 	</ul>
 	De volgende woorden of zinnen worden herkent als een vraag om een notitie op te slaan
 	<ul>
 		<li>Onthou dit</li>
 		<li>Sla op</li>
 		<li>Maak een notitie</li>
 		<li>Maak een aantekening</li>
 		<li>Kan je dit voor me onthouden</li>
 	</ul>
 	De volgende woorden of zinnen worden herkent voor het opvragen van notities
 	<ul>
 		<li>Laat notitie "titel" zien</li>
 		<li>Wat heb je onthouden over "titel"</li>
 		<li>Laat me alle titels van notities zien</li>
 		<li>Wat heb ik allemaal opgeslagen</li>
 	</ul>
 </p>
 <p>Je praat nu met een chatbot</p>
 <textarea id="chat" style="height: 300px; width: 500px;"	 disabled></textarea><br>
 <p>Type een bericht</p>
 <input type="text" id="msg">
 <button onclick="sendMsg();">send</button>
 <script>
 	function httpGet(theUrl){
 		var xmlHttp = new XMLHttpRequest();
 		xmlHttp.open("GET", theUrl, false);
 		xmlHttp.send(null);
 		return xmlHttp.responseText;
 	}
 	var startName = "";
 	var startCycle = 0;
 	function sendMsg(){
 		var msg = document.getElementById("msg").value;
 		var chat = document.getElementById("chat").value;
 		var hours = new Date().getHours();
 		var min = new Date().getMinutes();
 		var sec = new Date().getSeconds();
 		var usr = "<?php echo $_COOKIE["name"]; ?>";
 		document.getElementById("chat").value = chat + "\n" + hours + ":" + min + ":" + sec + " - " + usr + ": " + msg;
 		document.getElementById("msg").value	 = "";
  		var o = JSON.parse(httpGet("chat.php?chat=" + encodeURIComponent(msg)));
		if (o.success) {
			document.getElementById("chat").value = document.getElementById("chat").value + "\n" + hours + ":" + min + ":" + sec + " - greuBot: " + o.b + " = " + o.a;
		}
		if (startCycle == 2){
			console.log("chat.php?editnew=" + encodeURIComponent(startName) + "&m=" + encodeURIComponent(msg));
			httpGet("chat.php?editnew=" + encodeURIComponent(startName) + "&m=" + encodeURIComponent(msg));
			startCycle = 0;
		}
		if (startCycle == 1){
			console.log("chat.php?makenew=" + encodeURIComponent(msg));
			httpGet("chat.php?makenew=" + encodeURIComponent(msg));
			startCycle = 2;
			document.getElementById("chat").value = document.getElementById("chat").value + "\n" + hours + ":" + min + ":" + sec + " - greuBot: Wat wil je in de notitie met titel " + '"' + msg + '" opslaan?';
			startName = msg;
		}
		if (msg.toLowerCase().includes("onthou dit") || msg.toLowerCase().includes("sla op") || msg.toLowerCase().includes("maak een notitie") || msg.toLowerCase().includes("maak een aantekening") || msg.toLowerCase().includes("kan je dit voor me onthouden")){
			startCycle = 1;
			document.getElementById("chat").value = document.getElementById("chat").value + "\n" + hours + ":" + min + ":" + sec + " - greuBot: Wat is de titel van je notitie?";
		}
		if (msg.toLowerCase().includes("laat notitie") || msg.toLowerCase().includes("wat heb je onthouden over") || msg.toLowerCase().includes("laat me alle titels van notities zien") || msg.toLowerCase().includes("wat heb ik allemaal opgeslagen")){
			console.log("chat.php?a=" + encodeURIComponent(msg));
			var a = JSON.parse(httpGet("chat.php?a=" + encodeURIComponent(msg)));
			document.getElementById("chat").value = document.getElementById("chat").value + "\n" + hours + ":" + min + ":" + sec + " - greuBot: " + a.msg;
			if (a.a != ""){
				document.getElementById("chat").value = document.getElementById("chat").value + "\n" + hours + ":" + min + ":" + sec + " - greuBot: " + a.a;
			}
		}
 		var a = document.getElementById("chat");
 		a.scrollTop = a.scrollHeight;
 	}
 	var input = document.getElementById("msg");
 	input.addEventListener("keyup", function(event) {
 		if (event.keyCode === 13)
 			sendMsg();
 	});
 </script>
 <?php
}
?>