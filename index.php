<html lang="en-us">
<head>
    <meta charset="windows-1252">
    <link href="style.css" media="all" rel="stylesheet" type="text/css" />
    <title>Jalbum fetcher</title>
    <meta content="width=device-width" name="viewport">

</head>

<body>
    <div style="width: 400px; height: 100%">
    <?php
        $MAXYEAR    =  date("Y") + 1;
        $LASTYEAR   =  date("Y") - 1;
        $OUTPUT     =  "";
        $albums     =  null;

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

    $rss            =   file_get_contents($path);
    $splittedrss    =   explode("</description>", $rss);

    $albums         =   explode('<jalbum:link rel="sub" href="', $splittedrss[1]);
      array_shift($albums);   // remove first object from array
      
      return $albums;
  }

  /*------- Functions END -------*/

?>
</body>
</html>