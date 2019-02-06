<?php 
session_start();

//ファイルのソースコード表示
if(isset($_GET["source"])) {
	if(isset($_GET["file"])) {
		$path = dirname(__FILE__)."/uploads/".basename($_GET["file"]);
		highlight_file($path);
	} else {
		highlight_file(__FILE__);
	}
	exit;
}

//アップロードしたファイルの削除
if(isset($_GET["delete"])) {
	if(isset($_GET["file"])) {
		$path = dirname(__FILE__)."/uploads/".basename($_GET["file"]);
		unlink($path);
		header("Location: http://".$_SERVER['HTTP_HOST']."/".basename(__FILE__));
	}
}
//アップロードしたファイルの一斉読み込み
$class_path = dirname(__FILE__)."/uploads/";
if(file_exists($class_path)) {
	$files = scandir($class_path);
	foreach($files as $key => $value) {
		if(!in_array($value, array('.','..'))) {
			try {
				require($class_path.$value);
			} catch(ParseError  $e) {
				echo "<div class=\"alert alert-danger\">\n";
				echo "<strong>ERROR</strong>: ".basename($e->getFile()). " : " . $e->getMessage() . "\n";
				echo "</div>";
			}
		}
	}
}

$result = "";
//アップロードしたファイルの一覧表示
if(isset($_GET["uploads"])) {
	$class_path = dirname(__FILE__)."/uploads/";
	if(file_exists($class_path)) {
		$files = scandir($class_path);
		$result .= "<ul class=\"list-group\">";
		foreach($files as $key => $value) {
			if(!in_array($value, array('.','..'))) {
				$result .= "<li class=\"list-group-item\">";
				$result .= "$value ";
				$result .= "<a href=\"?source&file=$value\">[VIEW]</a>";
				$result .= "<a href=\"?delete&file=$value\">[DELETE]</a>";
			}
		}
	}
}
//シリアライズ化
if($_POST["deserialize_data"] !== "" AND isset($_POST["s_submit"])) {
	eval($_POST["deserialize_data"]);
	$result = addslashes(serialize($pwn));
	$_SESSION["deserialize_data"] = $_POST["deserialize_data"];
}

//アンシリアライズ(デシリアライズ)化
if($_POST["serialize_data"] !== "" AND isset($_POST["de_submit"])) {
	$result = var_export(unserialize(stripslashes($_POST["serialize_data"])), true); 
	$_SESSION["serialize_data"] = $_POST["serialize_data"];
}

//ファイルのアップロード
if(isset($_POST["file_submit"])) {
	$uploaddir = dirname(__FILE__)."/uploads/";
	//$uploadfile = $uploaddir."vuln.class";
	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
	if(!file_exists($uploaddir)) {
		mkdir($uploaddir, 0777,true);
	}
	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
		$result = "ファイルアップロード 成功.\n";
	} else {
		$result = "ファイルアップロード 失敗\n";
	}

	$result .= file_get_contents($uploadfile);
}
?>
<!DOCYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
	<style>
		* {
		padding: 0;
		margin: 0;
		}
		ul li {
			list-style-type: none;
		}
		h1 {
			padding: 0.25em 0.5em;
			color: #494949;
			background: transparent;
			border-left: solid 5px #7db4e6;
			font-weight: bold;
			font-size: 1em;
		}
		.wrapper {
		padding-top: 20px;
		display: -webkit-box;
		width: 100%;
		height: 80%;
		}
		.div-layout {
		width: 50%;
		padding: 0 5px;
		box-sizing: border-box;
		}
		.div-layout textarea {
		width: 100%;
		}
		.send {
		margin-top: 5px;
		}
		.hidden {
			display: none;
		}
		.popover {
			max-width: 30% !important;
		}
		.result-btn-group {
			padding-bottom: 5px;
		}
		.btn-layout {
			margin: 5px;
		}
	</style>
</head>
<body>
<header>
	<nav class="navbar navbar-inverse bg-info">
		<div class="container-fluid">
			<div class="navber-header">
				<a class="navbar-brand text-light" href="/"><strong>PHP Serial killer</strong></a>
			</div>
		</div>
	</nav>
</header>
<div class="wrapper">
	<?php 
	if(isset($_GET["uploads"])) {
		echo $result;
		echo "</body></html>";
		exit;
	}
	?>
	<div class="div-layout">
		<form method="POST" action="" id="form1">
			<h1>シリアライズデータをデシリアライズする</h1>
			<textarea rows="8" name="serialize_data">
<?php
if(isset($_SESSION["serialize_data"])) {
	echo $_SESSION["serialize_data"];
}
?></textarea>
			<input type="submit" class="send btn btn-primary" name="de_submit" value="Deserialize">
			<hr>
			<h1>PHPのオブジェクトをシリアライズする</h1>
			PHPコードとして評価し、$pwn 変数をシリアライズした結果を出力します。<br>
			例えば以下のように記載してください。
			<pre>
class testClass{
  public $var = 10;
  public $var2 = 20;
};
$pwn=array();
$pwn[0]="test";
$pwn[1]= new testClass();
			</pre>
			<textarea rows="8" name="deserialize_data">
<?php
if(isset($_SESSION["deserialize_data"])) {
	echo $_SESSION["deserialize_data"];
}
?></textarea>
			<input type="submit" class="send btn btn-primary" name="s_submit" value="Serialize">
		</form>
	</div>
	<div class="div-layout">
		<h1>結果</h1>
		<div class="result-btn-group">
			<a href="#" id="popover-button" class="btn btn-info btn-layout" data-container="body" data-placement="right" data-trigger="focus">
			シリアライズデータの見方
			</a>
			<div id="popover-content" class="hidden popover">
				<ul class="list-group">
					<li class="list-group-item"><code>b:&lt;i&gt;;</code> : バイナリ型を表します。<code>&lt;i&gt;</code>には<code>0</code>(false)か<code>1</code>(true)が入ります。</li>
					<li class="list-group-item"><code>i:&lt;i&gt;;</code> : 整数型を表します。<code>&lt;i&gt;</code>には数値が入ります。</li>
					<li class="list-group-item"><code>d:&lt;f&gt;;</code> : 浮動小数型を表します。<code>&lt;f&gt;</code>には浮動小数が入ります。</li>
					<li class="list-group-item"><code>s:&lt;i&gt;:"&lt;s&gt;";</code> : 文字列型を表します。<code>&lt;i&gt;</code>には文字列長が入り、<code>&lt;s&gt;</code>には文字列が入ります。</li>
					<li class="list-group-item"><code>N;</code> : Nullを表します。</li>
					<li class="list-group-item">
						<code>a:&lt;i&gt;:{&lt;element&gt;}</code> : 配列を表します。
						<code>&lt;i&gt;</code>には要素数が入り、<code>&lt;element&gt;</code>には<code>0</code>もしくは<code>&lt;key&gt;&lt;value&gt;</code>ペアで入ります。
					</li>
					<li class="list-group-item">
						<code>O:&lt;i&gt;:"&lt;s&gt;":&lt;i&gt;:{&lt;properties&gt;}</code> : オブジェクトを表します。
						最初の<code>&lt;i&gt;</code>には<code>&lt;s&gt;</code>の文字列長が入り、<code>&lt;s&gt;</code>には完全修飾クラス名(namespaceを付けたもの)が入ります。
						2番目の<code>&lt;i&gt;</code>にはオブジェクトのプロパティ数が入ります。
						<code>&lt;property&gt;</code>には<code>&lt;name&gt;&lt;value&gt;</code>ペアで入ります。
					</li>
					<li class="list-group-item">
						<code>&lt;name&gt;</code>は<code>s:&lt;i&gt;:"&lt;s&gt;";</code>のように表され、
						<code>&lt;i&gt;</code>は<code>&lt;s&gt;</code>の文字列長が入ります。
						<code>&lt;s&gt;</code>はプロパティが持つアクセス権によって異なります。
						<ol>
							<li><strong>public</strong> : <code>&lt;s&gt;</code>はシンプルなnameプロパティです。</li>
							<li><strong>protected</strong> : <code>&lt;s&gt;</code>の前に<code>\0*\0</code>が付きます。<code>\0</code>は<code>NULL</code>です。</li>
							<li><strong>private</strong> : <code>&lt;s&gt;</code>の前に<code>\0[s]\0</code>が付きます。<code>[s]</code>は完全修飾のクラス名です。</li>
						</ol>
					</li>
				</ul>
			</div>
			<div id="popover-title" class="hidden">見方</div>
			<a href="#" onClick="replace()" class="btn btn-info btn-layout">バックスラッシュを取り除く</a>
			<a href="#" onClick="urlencode()" class="btn btn-info btn-layout">URLエンコード</a>
			<a href="#" onClick="urldecode()" class="btn btn-info btn-layout">URLデコード</a>
			<a href="#" onClick="htmlEntitiesDecode()" class="btn btn-info btn-layout">HTMLエンティティデコード</a>
			<a href="#" onClick="base64encode()" class="btn btn-info btn-layout">Base64エンコード</a>
			<a href="#" onClick="base64decode()" class="btn btn-info btn-layout">Base64デコード</a>
			<p>選択した文字列長:<span id="length-count"></span></p>
			<p>選択した要素数:<span id="element-count"></span></p>
		</div>
		<textarea rows="20" id="result"><?php echo $result; ?></textarea>
		<hr>
		<h1>設定</h1>
		<ul class="list-group list-group-flush">
		<li class="list-group-item">このファイルの<a href="?source">ソースコード</a></li>
		<li class="list-group-item">自分で用意したクラスに対してデシリアライズの攻撃を行いたい方は、php のクラスファイルをアップロードしてください。
			<form enctype="multipart/form-data" method="POST" action="">
			<input type="file" name="userfile">
			<input type="submit" class="send btn btn-primary" name="file_submit">
			</form>
			<a href="?uploads">アップロードしたファイルを確認する</a>
		</li>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script>
$(function () {
  $("#popover-button").popover({
	  html: true,
	  content: function() {
		  return $("#popover-content").html();
	  },
	  title: function() {
		  return $("#popover-title").html();
	  }
  });
});

(function(win, doc) {
	"use strict";
	var select1 = doc.getElementById("length-count");
	var select2 = doc.getElementById("element-count");
	win.addEventListener("mouseup", _handleMouseup, true);
	function _handleMouseup() {
	var txt = win.getSelection().toString();
		select1.innerText = txt.length;
		select2.innerText = txt.split(",").length;
	}
})(this, document);

var base64encode = function() {
	var result = document.getElementById("result");
	result.value = btoa(result.value);
}
var base64decode = function() {
	var result = document.getElementById("result");
	result.value = atob(result.value);
}

var htmlEntitiesDecode = function() {
	var entities = [
		['amp', '&'],
		['apos', '\''],
		['lt', '<'],
		['gt', '>'],
	];
	var result = document.getElementById("result");
	for ( var i=0, max=entities.length; i<max; i++ ) {
		result.value = result.value.replace(new RegExp('&quot;', 'g'), '"' ).replace(new RegExp( '&'+entities[i][0]+';', 'g' ), entities[i][1] );	
	}
}

var replace = function() {
	var result = document.getElementById("result");
	result.value = result.value.replace(/\\\"/g,'"');
	result.value = result.value.replace(/\\\'/g,'\'');
}

var urlencode = function() {
	replace();
	var result = document.getElementById("result");
	result.value = result.value.replace(/\\0/g,'\x00');
	result.value = encodeURIComponent(result.value);
}
var urldecode = function() {
	var result = document.getElementById("result");
	result.value = result.value.replace(/%00/g,'%5c0');
	result.value = decodeURIComponent(result.value);
}
</script>
</body>
</html>
