<?php

/**
     今日のスタート　生徒用プログラム
     蓮花のＡＩ相談室　2021.9.22 by Hiroyuki.Nakamoto
**/

  require_once '../config.php';
  $a_user= array();
  start_session_https();

/**

  if( !isset($_SESSION['a_user']) )
   { header('Location: login.php');
   }
**/


  $meta= '今日のスタート メインメニュー';

/** debug **
  if( $_SERVER['REQUEST_METHOD']== 'POST' )
   { echo 'POST start'; dump( $_POST );
   }
  else
   { echo 'GET start'; dump( $_GET );
   }
**/

// 日付変数の初期化
  $date = getdate();
  $this_year= $year= sprintf('%d',$date['year']);
  $mon= sprintf('%d',$date['mon']);
  $day= sprintf('%d',$date['mday']);
  $self_url= './index.php';
  $DBdir= DB_DIR.'/pdata';
  $campus_fname= '';
  $xmlF= '';
  $week= array( "日","月","火","水","木","金","土" );
  $wday= $week[$date['wday']];
  $now_date= ['year'=>$year, 'mon'=>$mon, 'day'=>$day, 'wday'=>$wday];
  
  /**/
  $a_user['id']= 'test';
  $a_user['group']= 'h00';
  $a_user['dir']= $DBdir.'/'.$a_user['group'].'/'.$a_user['id'];
  $a_user['sname']= 'j1';
  $a_user['sgrade']= '1';
  $a_user['class']= '2';
  $a_user['ano']= '10';
  $a_user['user_agent']= 'pc';

  $_SESSION['a_user'] = $a_user;
  

/**/


  if( $_SERVER['REQUEST_METHOD']== 'GET' )
   { $campus_fname= isset($_GET['fn'])? $_GET['fn']: 'today_main';
   }
  else
   { $campus_fname= $_POST['fn'];
   }

// Campus ファイル読み込み
  $user_agent= $a_user['user_agent'];
  if( $a_user['user_agent']== 'pc' )  $sfn=  MASTER.'/'.$campus_fname.'.xml';    // ＰＣ　タブレット用
  else                                $sfn=  MASTER.'/'.$campus_fname.'_s.xml';  // スマートフォン用
  $xmlF = simplexml_load_file( $sfn );


// ここから


//日付を表示
function dspDate( $now_date )
{ global $a_user;
  if( $a_user['user_agent']== 'pc' )
   { echo '<div style="position:absolute; top:140px; left:380px; font-size:24px;">'.$now_date['year'].'年'.$now_date['mon'].'月'.$now_date['day'].'日（'.$now_date['wday'].'）</div>';
   }
  else 
   { echo '<div style="position:absolute; top:80px; left:110px; font-size:16px;">'.$now_date['year'].'年'.$now_date['mon'].'月'.$now_date['day'].'日（'.$now_date['wday'].'）</div>';
   }
}
 

/**
  デバッグ用　キャンパスの指定エリアを表示
**/
function disp_area()
{ global $xmlF;

 foreach( $xmlF->menus as $menu )
  { if( $menu['id']== '0' ) continue;
    $dc= explode( ',',$menu->coords );
    $width= $dc[2]-$dc[0];
    $height=$dc[3]-$dc[1];
    echo '<div style="position:absolute; top:'.$dc[1].'px; left:'.$dc[0].'px; width:'.$width.'px; height:'.$height.'px; border:solid 1px; color:red;">領域'.$menu['id'].'</div>';
  }

}


/**
  メニュー選択のためキャンパスのエリアを指定
**/
function dspMenuArea( $xmlF )
{ global $a_user;
  foreach($xmlF->menus as $menu)		//タグメニューの表示
   { if( $menu['id']== '0' ) continue;

     if( $menu->filename!= '' ) $fname= '&fn='.$menu->filename.'&sent='.$menu['sentiment'];
     else                      $fname= '';
     $format= ' <area shape="rect" coords="%s" href="%s?%s%s&init=start" title="%s" alt="%s" />'."\n";
     printf( $format,$menu->coords,$menu->link.'.php',SID,$fname,$menu->title,$menu->alt);
   }
  
  if( $a_user['user_agent']== 'pc' )
   { echo '<div style="position:absolute; top:370px; left:360px; font-size:22px;"><ruby>今日<rt>きょう</rt></ruby>の<ruby>気分<rt>きぶん</rt></ruby>をえらんでね！</div>';
   }
  else
   { echo '<div style="position:absolute; top:120px; left:95px; font-size:16px;"><ruby>今日<rt>きょう</rt></ruby>の<ruby>気分<rt>きぶん</rt></ruby>をえらんでね！</div>';
   }
}


//当日の豆知識を取得
function getMame( $sname,$mon,$day )
{
  $target= DB_DIR.'/mame/'.$sname.'/mame.xml';
  $xml = simplexml_load_file($target);
  foreach( $xml->rows as $row )
   { if( $row['id']== $mon )
      { foreach( $row->cols as $col )
         { if( $col['id']== $day )
            { $mame= $col;
              break 2;
            }
         }
      }
   }
  return $mame;
}

//豆知識を表示
function dspMame()
{ global $a_user,$mon,$day;
  $sname= $a_user['sname'];
  if( $a_user['user_agent']== 'pc' )
   { $t_top= '200px';
     $t_left= '100px';
     $t_width= '800px';
     $r_height= '80';
     $w_fontsize= '22px';
   }
  else
   {
     $t_top= '430px';
     $t_left= '30px';
     $t_width= '315px';
     $r_height= '200';
     $w_fontsize= '14px';
   }
  
  echo '<div style="position:absolute; top:'.$t_top.'; left:'.$t_left.';">';
  echo '<table border="1" width="'.$t_width.'">';
  echo '<tr align="center" bgcolor="white" style="font-size:'.$w_fontsize.';"><td><ruby>今日<rt>きょう</rt></ruby>の<ruby>豆知識<rt>まめちしき</rt></ruby></td></tr>';
  $mame= getMame( $sname,$mon,$day );
  echo '<tr align="center" valign="center" height="'.$r_height.'px" bgcolor="white" style="font-size:'.$w_fontsize.';"><td>'.$mame.'</td></tr>';
  echo '</table>';
  echo '</div>';
}


?>

<!DOCTYPE html>
<html lang="ja" dir="ltr" itemscope itemtype="http://schema.org/Article">
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>今日のスタート</title>
	<meta name="description" content="<?=$meta?>">
	<meta name="keywords" content="葛城市">
	<?
	 if( $user_agent== 'phone' )
	   echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
	?>
	<meta http-equiv="imagetoolbar" content="no" />
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<link href="./Rsymphony.css" type="text/css" rel="stylesheet">
  <meta charset="utf-8">
  
<script>
let width =100    // We will scale the photo width to this
let height = 0     // This will be computed based on the input stream

let streaming = false

let video = null
let canvas = null
let photo = null
//let startbutton = null
let constrains = { video: true, audio: true }
let recorder = null
let record_data = []

/**
 * ユーザーのデバイスによるカメラ表示を開始し、
 * 各ボタンの挙動を設定する
 *
 */
function startup() {
  video = document.getElementById('video')
  canvas = document.getElementById('canvas')
  photo = document.getElementById('photo')
  /**
  *startbutton = document.getElementById('startbutton')
  **/
  
  stopbutton  = document.getElementById('stopbutton')
  videoStart()

  video.addEventListener('canplay', function(ev){
    if (!streaming) {
      height = video.videoHeight / (video.videoWidth/width)

      video.setAttribute('width', width)
      video.setAttribute('height', height)
      streaming = true
    }
  }, false)

  startRecorder()





  
  // 「start」ボタンをとる挙動を定義
 /**
 startbutton.addEventListener('click', function(ev){
    recorder.start()
    ev.preventDefault()
  }, false);
**/

  stopbutton.addEventListener('click', function(ev) {
    recorder.stop();
    
    window.setTimeout(function(){
console.log(record_data)
    var blob = new Blob(record_data, { type: 'video/webm' })
    var url = window.URL.createObjectURL(blob)
    var a = document.createElement('a')
    document.body.appendChild(a)
    a.style = 'display:none'
    a.href = url;
    a.download = 'test.webm'
    while( isExistFile('test.webm')) sleep(5000);
   a.click()
    window.URL.revokeObjectURL(url)
    }, 5000);
 
  })

 

}



function isExistFile(file) {
  try {
    fs.access(file);
    return true
  } catch(err) {
    if(err.code === 'ENOENT') return false
  }
}

function sleep(waitMsec) {
  var startMsec = new Date();
  while (new Date() - startMsec < waitMsec);
}
/**
 * カメラ操作を開始する
 */
function videoStart() {
  streaming = false
  console.log(streaming)
  navigator.mediaDevices.getUserMedia(constrains)
  .then(function(stream) {
      video.srcObject = stream
      video.play()
  })
  .catch(function(err) {
      console.log("An error occured! " + err)
  })
}

function startRecorder() {
  navigator.mediaDevices.getUserMedia(constrains)
  .then(function (stream) {
    recorder = new MediaRecorder(stream)
    recorder.ondataavailable = function (e) {
      var testvideo = document.getElementById('test')
      testvideo.setAttribute('controls', '')
      testvideo.setAttribute('width', width)
      testvideo.setAttribute('height', height)
      var outputdata = window.URL.createObjectURL(e.data)
      record_data.push(e.data)
      testvideo.src = outputdata
    }
  })
}

 


</script>
</head>

    

<body>


 <button id="stopbutton">stop!!</button><br>



    <div class="camera">
        <video id="video">Video stream not available.</video>
    </div><br>
    
   
   
    <div>
      <video id="test"></video>
    </div>

<a name="top">
<center>
<div style="position:relative; width:<?=$a_user['screen_width']?>;margin: 16px 0px; padding:0px;">

<?
  echo '  <img src="./fig/'.$xmlF->campus.'" usemap="#LSmap" width="100%" border="0">'."\n";
//  disp_area();
  echo '<map name="LSmap">';
  dspMenuArea( $xmlF );
  dspDate($now_date);
  dspMame();
  echo '</map>'."\n";
?>


<div class="footer">
<address><font size="2" face="Helvetica, Geneva, Arial, SunSans-Regular, sans-serif"><?=$this_year?> 葛城市 こども・若者サポートセンター</font></address>
</div>

</div>
</center>




<script>

startup()

window.addEventListener('load', function(ev){
    MediaRecorder.start()
    ev.preventDefault()
  }, false);




</script>
</body>
</html>

