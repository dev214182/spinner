@extends('layouts.single')

@section('content')
<div class="container"> 
<!-- <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div> -->
    <div class="content-wrapper">
    
      <div class="exterior">
      
        <div class="spritespin-wrapper" id="spritespin-wrapper">
          <div class="spritespin"></div>
          
          <ul id="hp-draggable" class="hp-draggable-ext">  </ul>
          <!-- <div class="icon-360"> <img src="{{URL::to('/')}}/images/360image.png"></div> -->
        </div>
      </div>
      <div class="interior">
        <div style="max-width: 1366px; margin: 0 auto; margin-bottom:16px;">
          <div id="panorama" style="z-index:0;"> </div>
        </div>
      </div>
    </div> 

    <div class="label-360">
      <div class="center-con">
        <div class="round-container">
              <div class="round-label"> Drag to view 360&deg;</div>
              <div class="round"> 
                  <div id="cta">
                      <span class="arrow primera next-arrow "></span>
                      <span class="arrow segunda next-arrow"></span>
                  </div>
              </div>
        </div>   
      </div>
    </div>

        <div class="content-action" style="z-index:9999999;">
          <div class="open-exterior active">exterior</div>
          <div class="open-interior"  >interior</div>
            <!-- <div class="divider"></div> -->
          <div class="videos img">VIDEOS</div>
          <!-- <div class="photos img">PHOTOS</div> -->
        </div>

        <div class="display-videos-thumbnails">
              <div class="video-slider slider">  
              </div>
        </div>

        <div class="carousel-photos"> </div><!-- End photos  -->
  </div> 

  <div id="myModal" class="modal fade" role="dialog">
    <span class="close cursor" onclick="closeModal()">&times;</span>
    <div class="modal-content">

      <div class="slider-content"> 

      </div> 
              
      <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
      <a class="next" onclick="plusSlides(1)">&#10095;</a>
  
      <div class="caption-container">
            <p id="caption"></p>
      </div>
    </div>
  </div>
@endsection