<!DOCTYPE html>
<html lang="en-us">
<head>
	<meta charset="windows-1252">
	<title>W3Schools Online Web Tutorials</title>
	<meta name="viewport" content="width=device-width">

	<style type="text/css">
		.jalbum-album {
			margin-bottom: 15px;
		}
		.jalbum-album img {
			width: 35%;
			margin: 0;
			padding: 0;
			display: inline-block;
			vertical-align: top;
		}
		.jalbum-album div {
			width: 58%;
			margin: 0;
			padding: 5px 0 5px 5px;
			display: inline-block;
			vertical-align: top;
		}
		.jalbum-album div span {
			display: inline;
		}
		.jalbum-album .album-date {
			font-size: .85em;
			padding-left: 4px;
			color: rgb(68, 68, 68);
		}
		.jalbum-album .album-title {
			font-weight: 600;
		}

		/* ---- Mobile ---- */
		@media handheld, only screen and (max-width: 767px) {
			.jalbum-album img {
				width: 100%;
				display: block;
			}
			.jalbum-album {
				text-align: center;
			}
			.jalbum-album div {
				margin-top: -53px !important;
				background-color: rgba(0, 0, 0, .6);
				box-shadow: 0 -6px 10px 0 rgba(0, 0, 0, 0.6);
				width: 100%;
			}
			.jalbum-album div span {
				font-size: 1.2em;
				color: #FFF;
				text-shadow: 2px 2px 2px #000;
			}
		}
	</style>
</head>

<body>

	<div style="width: 400px; height: 100%">

		<?php
		$MAXYEAR	=  date("Y") + 1;
		$LASTYEAR	=  date("Y") - 1;
		$OUTPUT		=  "";
		$albums 	=  null;

		while ( $MAXYEAR >= $LASTYEAR ) {
			
			$URL_PREFIX =  "http://avgroenester.nl/$MAXYEAR/";
			$path     =   $URL_PREFIX ."/album.rss";
			
			if ( fileExists($path) ) {

				if ( $albums == null ) {
					$albums = getAlbums($path);
					$OUTPUT .= writeHTML($albums, $URL_PREFIX, 0);
				} else {
					$count = count($albums);
					$thisyearsalbums = getAlbums($path);
					$OUTPUT .= writeHTML($thisyearsalbums, $URL_PREFIX, $count);
					$albums = array_merge($albums, $thisyearsalbums);
				}    
			}

			if ( count($albums) >= 4 ) {
				break;
			}
			
			$MAXYEAR--;
		}

		print $OUTPUT;
		?>

	</div>
</body>

</html>



<?php

/*------- Functions START -------*/

function writeHTML($albums, $prefix, $alreadyWrittenCount) {
	
	$return = "";
		  $i = $alreadyWrittenCount; // the number of albums already written before this.
		  
		  foreach ( $albums as $album ) {
		  	$i++;
		  	
		  	if ( $i <= 4 ) {
		  		
		  		$album_info      =   explode('/album.rss" />', $album);
		  		$album_info_arr  =    explode(" ", $album_info[0], 2);
		  		$album_date      =    $album_info_arr[0];
		  		$album_name      =    $album_info_arr[1];
		  		$album_url       =   $prefix . str_replace(" ","%20", $album_info[0]);
		  		$album_thumb     =    $album_url . "/folderthumb.jpg";
		  		
		  		$return      .=  '
		  		<a href="'. $album_url .'" title="Fotoalbum: '. $album_name .'" target="_blank">
		  			<div class="jalbum-album">
		  				<img src="'. $album_thumb .'" alt="Album miniatuur" />
		  				<div>
		  					<span class="album-title">'. $album_name .'</span>
		  					<br />
		  					<span class="album-date">'. $album_date .'</span>
		  				</div>
		  			</div>
		  		</a>';
		  		
		  	}
		  	
		  }
		  
		  return $return;
		}

		function fileExists($path){
			return ( @fopen($path,"r")==true );
		}

		function getAlbums($path) {
			
			$rss           =   file_get_contents($path);
			$splittedrss     =   explode("</description>", $rss);
			
			$albums       =   explode('<jalbum:link rel="sub" href="', $splittedrss[1]);
		  array_shift($albums);   // remove first object from array
		  
		  return $albums;
		}

		/*------- Functions END -------*/

		?>
