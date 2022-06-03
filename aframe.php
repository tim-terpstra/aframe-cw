<?php
function galleryShortCode()
{
  global $wpdb;
  if (empty($_GET["id"])) {
    $_GET["id"] = 47;
  }
  $gallery = $wpdb->get_row($wpdb->prepare("SELECT * from galleries where id=%d", array($_GET["id"])), "ARRAY_A");
  if (empty($gallery)) {
    echo "<script>
      window.location.replace('/galerij-overzicht/')
    </script>";
    return;
  }
  $images = [];
  $images = $wpdb->get_results($wpdb->prepare("SELECT * from gallerycontent gc
    inner join ArtPiecesUploads a on a.uploadid= gc.kunstid
    where gallery=%d", array($_GET["id"])), "ARRAY_A");
  /*
    op het moment is er een aframe template dus heb ik hier in de positions variable de posities geplaatst
    wanneer er meerdere templates zijn moet er een functie worden gemaakt of een include voor de nieuwe
    template die dan vanuit de positions de items in laad
  */
  $positions = [
    "0"=>[
      "x" => -8.9,
      "y" => 2,
      "z" => -5.38,
      "rotation" => 90
    ],
    "1"=>[
      "x" => -5.984,
      "y" => 2,
      "z" => -8.3,
      "rotation" => 0
    ],
    // "1"=>[
    //   "x" => -6.018,
    //   "y" => 2,
    //   "z" => 3.59,
    //   "rotation" => 180
    // ],
    "2"=>[
      "x" => -0.017,
      "y" => 2,
      "z" => -8.3,
      "rotation" => 0
    ],
     "3"=>[
      "x" => 6.2,
      "y" => 2,
      "z" => -8.3,
      "rotation" => 0
    ],
    "4"=>[
      "x" => 8.9,
      "y" => 2,
      "z" => -5.38,
      "rotation" => 270
    ],"5"=> [
      "x" => 8.9,
      "y" => 2,
      "z" => 0.606,
      "rotation" => 270
    ],"6"=> [
      "x" => 6.2,
      "y" => 2,
      "z" => 3.59,
      "rotation" => 180
    ],
    "7"=>[
      "x" => 0.017,
      "y" => 2,
      "z" => 3.59,
      "rotation" => 180
    ],
    "8"=>[
     "x" => -5.984,
     "y" => 2,
     "z" => 3.59,
     "rotation" => 180
   ],
    "9"=> [
      "x" => -8.9,
      "y" => 2,
      "z" => 0.606,
      "rotation" => 90
    ],
  ];
  foreach($images as $image){
    if($image["is18p"]=="true"){
      ?>
      <div id="is18p" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">18+ content</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
              <p>Je moet 18+ zijn om deze community te bekijken
  Je moet minstens achttien jaar oud zijn om deze inhoud te bekijken. Ben je ouder dan achttien en bereid om inhoud voor volwassenen te bekijken?</p>
            </div>
            <div class="modal-footer">
              <button id="is18p_continue" type="button" class="btn btn-primary">Doorgaan</button>
              <a role="button" class="btn btn-secondary" data-dismiss="modal" href="/galerij-overzicht/">Nee bedankt</a>
            </div>
          </div>
        </div>
      </div>
      <script>
        document.getElementById("is18p_continue").addEventListener("click",e=>{
          document.getElementById("is18p").style.display="none";
        })
      </script>
      <?php
      break;
    }
  }
?>

  <script src="https://aframe.io/releases/1.0.3/aframe.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/donmccurdy/aframe-extras@v6.1.0/dist/aframe-extras.min.js"></script>
  <style>
  /* deze styling staat alleen hier om de navigatie naar voren te halen en de footer te verstoppen op de
    3d galerie pagina
   */
    footer{
      display: none;
    }
    header{
      z-index: 1;
    }
  </style>
  <!-- joystick en physics inladen -->
<script src="/wp-content/themes/createwise-gallery/scripts/nipple.js"></script>
  <script src="//cdn.rawgit.com/donmccurdy/aframe-physics-system/v4.0.1/dist/aframe-physics-system.min.js"></script>
  <body>
    <!-- ="debug: true" kan worden gebruikt tijdens het testen -->
    <a-scene device-orientation-permission-ui="enabled: false" physics joystick vr-mode-ui="enabled: false">
      <a-entity light="type: ambient"> </a-entity>
      <a-assets>
        <?php
        $imgText = "";
        $path= substr(ABSPATH,0,-1);
        // insert images
        foreach ($images as $key => $value) {
          echo '<img id="my-image-' . $key . '" src="' . ($value["link"]) . '">';
          echo ($path.$value["link"]);
          $imgData = getimagesize($path.$value["link"]);
          $size = getImageCanvasSize($imgData[1],$imgData[0]);
          $pos= $positions[$value["location"]-1];
          if ($pos["rotation"] == 0) {
            // right side wall
            $imgText .= '<a-image height="' . $size["height"] . '" width="' . $size["width"] . '"  position="' . $pos["x"] . ' ' . $pos["y"] . ' ' . $pos["z"] . '" rotation="0 ' . $pos["rotation"] . ' 0" src="#my-image-' . $key . '" ></a-image>
                <a-box height="' . $size["height"] . '" width="' . $size["width"] . '"  rotation="0 ' . $pos["rotation"] . '" color="grey" depth=".1" height="1" width="1" position="' . $pos["x"] . ' ' . $pos["y"] . ' ' .
                ($pos["z"]-0.06) . '"></a-box>
                <a-text rotation="0 ' . $pos["rotation"] . '" color="black"  position="-8 2 0.606"  text="value:"></a-text>';
          } elseif ($pos["rotation"] == 90) {
            // top wall
            $imgText .= '
          <a-image height="' . $size["height"] . '" width="' . $size["width"] . '"  position="' . $pos["x"] . ' ' . $pos["y"] . ' ' . $pos["z"] . '" rotation="0 ' .
           $pos["rotation"] . ' 0" src="#my-image-' . $key . '" ></a-image>
                <a-box height="' . $size["height"] . '" width="' . $size["width"] . '"  rotation="0 ' . $pos["rotation"] . '" color="grey" depth=".1" height="1" width="1" position="' . ($pos["x"]-0.06) . ' ' . $pos["y"] . ' '.
                 $pos["z"] . '"></a-box>
                <a-text wrap-count="20"  rotation="0 ' . $pos["rotation"] . '" color="black"  position="-8 2 0.606"  text="value:"></a-text>';
          } elseif ($pos["rotation"] == 180) {
            // left side wall
            $imgText .= '
          <a-image height="' . $size["height"] . '" width="' . $size["width"] . '"  position="' . $pos["x"] . ' ' . $pos["y"] . ' ' . ($pos["z"]) . '" rotation="0 ' .
           $pos["rotation"] . ' 0" src="#my-image-' . $key . '" ></a-image>
                <a-box height="' . $size["height"] . '" width="' . $size["width"] . '" rotation="0 ' . $pos["rotation"] . '" color="grey" depth=".1" height="1" width="1" position="' . $pos["x"] . ' ' . $pos["y"] . ' '
                 . ($pos["z"]+0.06) . '"></a-box>
                <a-text rotation="0 ' . $pos["rotation"] . '" color="black"  position="-8 2 0.606"  text="value:"></a-text>';
          } elseif ($pos["rotation"] == 270) {
            // bottom wall
            $imgText .= '
              <a-image height="' . $size["height"] . '" width="' . $size["width"] . '"  position="' . $pos["x"] . ' ' . $pos["y"] . ' ' . $pos["z"] . '" rotation="0 ' .
              $pos["rotation"] . ' 0" src="#my-image-' . $key . '" ></a-image>
              <a-box height="' . $size["height"] . '" width="' . $size["width"] . '"  rotation="0 ' . $pos["rotation"] . '" color="grey" depth=".1" height="1" width="1" position="' . ($pos["x"]+0.06) . ' ' . $pos["y"] . ' '
               . ($pos["z"]) . '"></a-box>
              <a-text rotation="0 ' . $pos["rotation"] . '" color="black"  position="-8 2 0.606"  text="value:"></a-text>';
          }
        }
        ?>
        <img id="sky" src="https://t4.ftcdn.net/jpg/03/81/81/55/360_F_381815539_CUlqLBRjBFrFnkRNUGaF52eL5fNXSwrU.jpg">
        <img id="floor" src="/wp-content/themes/createwise-gallery/images/wood.jpg">
        <img id="wall" src="/wp-content/themes/createwise-gallery/images/wall.jpg">
        <img id="bank" src="/wp-content/themes/createwise-gallery/images/bank.jpg">

      </a-assets>
      <!-- Camera Entity -->
      <a-entity id="cameraHolder" width="0" depth="0" position="0 2 0">
        <a-entity id="camera" camera look-controls wasd-controls="acceleration: 300" kinematic-body></a-entity>
      </a-entity>
      <a-box static-body="" position="-9.26 2 -2.35" width=".5" height="4" depth="13" src="#wall" repeat="2 2" normal-map="#wall" normal-texture-repeat="2 2"></a-box>

      <a-box static-body="" position="9.26 2 -2.35" width=".5" height="4" depth="13" src="#wall" repeat="2 2" normal-map="#wall" normal-texture-repeat="2 2"></a-box>

      <a-box static-body="" position="0 2 -8.63" width="19" height="4" depth="0.5" src="#wall" repeat="3 2" normal-map="#wall" normal-texture-repeat="3 2"></a-box>

      <a-box static-body="" position="0 2 3.9" width="19" height="4" depth="0.5" src="#wall" repeat="3 2" normal-map="#wall" normal-texture-repeat="3 2"></a-box>

      <a-plane static-body="" rotation="-90 0 0" position="0 0 -2.35" width="20" height="13" src="#floor" repeat="5 3" normal-map="#floor" normal-texture-repeat="5 3"></a-plane>
      <!-- bank rechts -->
      <a-box static-body="" position="4.99 0.5 -3.04" rotation="0 90 0" width="3.9" height="0" depth="0" src="#bank" repeat="2 2" normal-map="#bank" normal-texture-repeat="2 2"></a-box>

      <a-box static-body="" position="3.49 0.5 -1.59" width="2" height="0" depth="0" src="#bank"></a-box>
      <!-- end bank rechts -->
      <!-- bank links -->
      <a-box static-body="" position="-4.98 0.5 -3.04" rotation="0 90 0" width="3.9" height="0" depth="0" src="#bank"repeat="2 2" normal-map="#bank" normal-texture-repeat="2 2"></a-box>

      <a-box static-body="" position="-3.48 0.5 -4.49" width="2" height="0" depth="0" src="#bank" ></a-box>
      <!-- end bank links -->
      <!-- insert images from string -->
      <?= $imgText ?>

    <a-sky color="#a9a9a9"></a-sky>
      <!-- <a-sky src='#sky'></a-sky> -->
    </a-scene>
  </body>
<?php
}
add_shortcode('cwGallery', 'galleryShortCode')
?>
