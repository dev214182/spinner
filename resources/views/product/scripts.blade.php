<script type="text/javascript">
var slideIndex = 1;
 function openModal() { 
    document.getElementById("myModal").style.display = "block"; 
    
      showSlides(slideIndex)
    
  }

  function closeModal() {
    document.getElementById("myModal").style.display = "none";
  }

  $('body').on('click','.photos',function(e){
      e.preventDefault();
      hideVideo();
      openModal();
  });

  function showVideo(){ 
    $('.display-videos-thumbnails').show();
  }

  function hideVideo(){
    $('.display-videos-thumbnails').hide();
  }

  function plusSlides(n) {
    showSlides(slideIndex += n);
  }
  
  function currentSlide(n) {
    showSlides(slideIndex = n);
  }

  function showSlides(n) {
     var i;
    var slides = document.getElementsByClassName("mySlides");
    
    var captionText = document.getElementById("caption");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
     
    slides[slideIndex-1].style.display = "block"; 
    
  }  
 
  </script>
  <script type="text/javascript">
  var dt = "<?php echo date("dHis") ?>";
 
  var browsers = ["Opera", "Edg", "Chrome", "Safari", "Firefox", "MSIE", "Trident"];
var userbrowser, useragent = navigator.userAgent;
for (var i = 0; i < browsers.length; i++) {
    if( useragent.indexOf(browsers[i]) > -1 ) {
        userbrowser = browsers[i];
        break;
    }
};
  
switch(userbrowser) {
    case 'MSIE':
        userbrowser = 'IE-Browser';
        break;
  
    case 'Trident':
        userbrowser = 'IE-Browser';
        break;
  
    case 'Edg':
        userbrowser = 'Microsoft Edge';
        break;
}

    var api;
    var sls = '{{ $slug }}';
      $(function () {
        var hpEdit = [];  
 
        function getUnique(arr, comp) {
            // store the comparison  values in array
            var unique = arr.map(function (e) {
              return e[comp];
            }) // store the indexes of the unique objects
            .map(function (e, i, final) {
              return final.indexOf(e) === i && i;
            }) // eliminate the false indexes & return unique objects
            .filter(function (e) {
              return arr[e];
            }).map(function (e) {
              return arr[e];
            });
            return unique;
          }
        
        var datazz = base_url+ '/api/product/'+sls;
        
        var dataApi = $.get(datazz, function(data) {
           
                    if (data.dataItems.length < 1) {
                       
                        return false;
                    }
                    return data;
                }, 'json');

        dataApi.always(function(data) {
        
            if (data.dataItems == false) { $("body").remove(); return false; }
            if (data) { 
              
              $("head").append('<meta property="og:image" content="'+ base_url+ '/storage/uploads/'+ data.dataItems[0].company_id +'/'+ data.dataItems[0].items[0].media_file.path +'">');

                var imgs = []; 
                var panoramicImg = [];
                var panoramicSettings = [];
               
                var conf_hotspots = [];  
                var blk = 'none'; 
            
                var intHps = [];
                var cnts = 1;
                var cntsss = 1;
                var hpLabel = '';  
               
                let hpSlider = ''; 
                var xx = 1; 
                let hpThumbnailSlider = '';
               
                if(data.videos.length > 0){
                  $(".videos.img").show();
                }

                $('body').on('click','.videos', function(e){
                  e.preventDefault();   
                
                      if(xx == 1){ 
                      var vds = '';
                          $.each(data.videos, function(i,o){   
                              vds +=  '<div> <video preload="metadata"  width="220" height="140" controls>'+
                                              '<source src="'+o.video_path+'" type="video/mp4">'+
                                              'Your browser does not support the video tag.'+
                                            '</video>'+
                                        '</div>';
                          }); 
                        
                        $(".video-slider").html( vds );

                          setTimeout( function() {
                              $('.video-slider').slick({
                                  infinite: true,
                                  slidesToShow: 4,
                                  slidesToScroll: 4
                              });
                          }, 500);
                      }
                      xx++;

                      showVideo();
                }); 

                
              $.each(data.hpItems, function(i,o){   
                  
                  var hpContents =  JSON.parse(o.content);
                   if(o.hotspot_for == "exterior"){
                     hpLabel +=    '<li id="'+o.id+'" data-ids="'+o.id+'" class="cd-single-point">';  
                     hpLabel +=       '<a class="cd-img-replace" data-ids="'+ cnts++ +'" href="#">More</a>'; 
                     hpLabel +=    '</li>';
                   }

                   hpSlider += '<div class="mySlides text-center" id="'+o.id+'">';
                  
                   if(hpContents && hpContents.image){ 
                    hpThumbnailSlider += '<div class="thumbnail-slider thumbnail-point"><a href="#" data-ids="'+ cntsss++ +'"><img  width="100%" height="auto" src="'+hpContents.image+'" style="width:100%" alt="'+o.title+'" /></a></div>';
                    hpSlider +=       '<img  width="100%" height="auto" src="'+hpContents.image+'" style="width:100%" alt="'+o.title+'" />';
                  }
                  hpSlider += '<div class="hp-contents">';
                 
                   if(o.title){
                   
                     hpSlider +=       '<h2 class="text-uppercase" >'+o.title+'</h2>';
                   }
                   if(hpContents && hpContents.description){
                     hpSlider +=       '<p> '+hpContents.description+' </p>';
                   }
                  
                   if(hpContents && hpContents.cta_url){
                     if(hpContents.cta_new_tab){
                       var target = "_blank";
                     }else{
                       var target = "_parent";
                     } 

                     hpSlider +=      '<a href="'+hpContents.cta_url+'" class="c-color" target="'+target+'">'+hpContents.cta_label+'</a>';
                   }
                   hpSlider += '</div>';
                   hpSlider += '</div>';
           }); 

 
                $.each(data.dataItems, function(i, o) {  
              
                    var items = o.items;  
                    
                    Object.keys(items).map(function (ii) {  
                            if(items[ii].item_type == "panorama"){
                              panoramicImg[ii] = '/storage/uploads/'+o.user.company_id+'/'+items[ii].media_file.path; 
                              panoramicSettings[ii] = items[ii].media_file.interior_settings; 
                            } else{
                              conf_hotspots[ii] = [];      
                              conf_hotspots[ii]['hotspot_setting'] = [];
                              
                              if(userbrowser == 'Safari' || userbrowser == 'IE-Browser'){
                                console.log("ddddddddddddd");
                                var fileItem = items[ii].media_file.path.split(".");
                                fileItem = fileItem[0]; 
                                imgs[ii] = '/storage/uploads/'+o.user.company_id+'/original/'+fileItem+'.jpg?v='+dt;
                              }else{ 
                                imgs[ii] = '/storage/uploads/'+o.user.company_id+'/'+items[ii].media_file.path+'?v='+dt;
                              }
                            }
                            if(items[ii].hotspot_setting){
                                    Object.keys(items[ii].hotspot_setting).map(function (iii) {  
                                                // INTERIOR HOTSPOTS SETTINGS
                                          if(items[ii].item_type == "panorama"){
                                               
                                                var parseData = JSON.parse(items[ii].hotspot_setting[iii].hotspot_settings); 
                                                var pitch = parseData.pitch;
                                                var yaw = parseData.yaw;
                                                intHps[iii] = { pitch: pitch, yaw: yaw, display: "block", ids: cnts++, id: items[ii].hotspot_setting[iii].hotspot_id, cssClass: "custom-hotspot" };
                                                
                                          }else{
                                            // EXTERIOR HOTSPOTS SETTINGS
                                          
                                                
                                                var parseData = JSON.parse(items[ii].hotspot_setting[iii].hotspot_settings);
                                              
                                                var left = parseData.left;
                                                var top = parseData.top;
                                                conf_hotspots[ii]['hotspot_setting'][iii] = { hpID: items[ii].hotspot_setting[iii].hotspot_id, left: left+"%", top: top+"%", display: "block" };
                                          }
                                    });
                            }
                    });   
                    
                });   
                
                // REMOVE EMPTY ARRAY
                var panoramicImg = panoramicImg.filter(function (el) {
                                return el != null;
                              });
                
                var panoramicSettings = panoramicSettings.filter(function (el) {
                                return el != null;
                              });
                 
                var imgs = imgs.filter(function (el) {
                  return el != null;
                });
               
                var conf_hotspots = conf_hotspots.filter(function (el) {
                  return el != null; 
                });
                
                var intHps = intHps.filter(function (el) {
                  return el != null;
                });     
                
                imgs.forEach(function(img){
                    new Image().src = img; 
                    // caches images, avoiding white flash between background replacements
                });  
                
                let imagesArray = imgs;   
                let imgCnt = imagesArray.length;
                
                init360(); 
                function init360(){
                   
                  api = $(".spritespin").spritespin({
                    source: imagesArray,
                    frame: 0,
                    frames: imgCnt,
                    framesX: 4,
                    loading: false,
                    // width: 1366,
                    // height: 768,
                     width: 1200,
                    height: 800,
                    sense: -1,
                    renderer: "canvas",
                    responsive: true,
                    animate: false,
                    preloadCount: 6,
                    frameTime: 300,
                    plugins: [
                    "drag",
                    "360",
                    ],
                    onFrameChanged: function (e, data) {
                        hideVideo(); 
                        $('#hp-draggable li').hide();
                        
                        if(conf_hotspots){
                            Object.keys(conf_hotspots).map(function (i) { 
                             
                              if (conf_hotspots[data.frame] && conf_hotspots[data.frame].hotspot_setting.length > 0) {  
                                 
                                Object.keys(conf_hotspots[data.frame].hotspot_setting).map(function (ii) {    
                                    $('#'+conf_hotspots[data.frame].hotspot_setting[ii].hpID).css({ 
                                        top: conf_hotspots[data.frame].hotspot_setting[ii].top,
                                        left: conf_hotspots[data.frame].hotspot_setting[ii].left,
                                        display: conf_hotspots[data.frame].hotspot_setting[ii].display,
                                    });
                                
                                }); 
                              }
                            }); 
                        }
                        
                         if($("div").hasClass("label-360")){
                            $(".label-360").fadeOut("slow", function(e){
                              $(this).remove();
                            });
                         }
                    },
                    onInit: function (e) { 
                      $('#hp-draggable li').hide(); 
                    },
                    onLoad: function (e, data) {
                      if(conf_hotspots){
                            Object.keys(conf_hotspots).map(function (i) { 
                             
                              if (conf_hotspots[data.frame] && conf_hotspots[data.frame].hotspot_setting.length > 0) {  
                                 
                                Object.keys(conf_hotspots[data.frame].hotspot_setting).map(function (ii) {   
                                    $('#'+conf_hotspots[data.frame].hotspot_setting[ii].hpID).css({ 
                                        top: conf_hotspots[data.frame].hotspot_setting[ii].top,
                                        left: conf_hotspots[data.frame].hotspot_setting[ii].left,
                                        display: conf_hotspots[data.frame].hotspot_setting[ii].display,
                                    });
                                
                                }); 
                              }
                            }); 
                        }

                        
                       
                    },
                    onComplete: function(){
                      if(panoramicImg.length > 0){
                        $(".content-action").attr("style","display:flex");

                        $(".open-exterior").show();
                      
                      }
                      
                      //setTimeout(() => {
                        $("#loading-wrapper").remove();
                     // }, 2000);
                      $(".center-con").show();
                      $(".icon-360").show();
                    }
                }).spritespin("api");  
                
              }
              
              
              $('.carousel-photos').html(hpThumbnailSlider);
            $('.slider-content').html(hpSlider);
            $('#hp-draggable').html(hpLabel);  
               $('.carousel-photos').slick({
                  infinite: true, 
                  slidesToShow: 5,
                  slidesToScroll: 4,
                  responsive: [
                              {
                                breakpoint: 1024,
                                settings: {
                                  slidesToShow: 3,
                                  slidesToScroll: 3,
                                  infinite: true, 
                                }
                              }, 
                            ]
                });
              
                if(data.hpItems.length > 0){
                  $(".photos.img").show();
                } 

                      /**
                      * HotSpot
                      * */
                      //open interest point description
                      $(".cd-single-point, .thumbnail-point")
                        .children("a")
                        .on("click", function (e) {
                            e.preventDefault();
                            var rw = $(this).attr('data-ids');
                            hideVideo();
                            openModal();currentSlide(rw); 

                            if($("div").hasClass("label-360")){
                                $(".label-360").fadeOut("slow", function(e){
                                  $(this).remove();
                                });
                            }
                      }); 
                      
                      
                      $("div").on("click",".custom-hotspot", function(e){
                            e.preventDefault();
                            var rw = $(this).attr('data-ids');
                              hideVideo();
                              openModal();
                              currentSlide(rw);  
                      });

                      // Tab
                      var $interior = $(".interior");
                      var $exterior = $(".exterior");
                      var $interiorBtn = $(".open-interior");
                      var $exteriorBtn = $(".open-exterior");

                      if(panoramicImg.length > 0){
                        $interiorBtn.show();
                            var hfov, pitch, yaw;
                          if(panoramicSettings.length > 0){
                              intSettings = JSON.parse(panoramicSettings);
                                hfov = intSettings.hfov;
                                pitch = intSettings.pitch;
                                yaw = intSettings.yaw;
                          }else{
                                hfov = 80;
                                pitch = -16.834687202204037;
                                yaw = -36.30724382948786;
                          }
                        }else{
                          
                        }
                      $interior.hide();
                      var x = 1;
                      $interiorBtn.on("click", function (e) {
                        e.preventDefault();
                        hideVideo(); 
                         
                            if(x == 1){
                              console.log(x);
                                      pannellum.viewer("panorama", {
                                        //  hotSpotDebug: true,
                                        default: {
                                          firstScene: "default_scene_start",
                                          // author: "Moikzz",
                                          autoLoad: true,
                                          sceneFadeDuration: 1000,
                                        },
                                        scenes: {
                                          "default_scene_start": {  
                                            hfov: hfov,
                                            pitch: pitch,
                                            yaw: yaw, 
                                            type: "equirectangular",
                                            panorama: panoramicImg, 
                                            hotSpots: intHps, // dynamically add hotspots 
                                          
                                          },
                                        },
                                      }); 
                                      x++;   
                              } 

                        $exteriorBtn.removeClass("active");
                        $interiorBtn.addClass("active");
                        $interior.show();
                        $exterior.hide(); 

                        if($("div").hasClass("label-360")){
                            $(".label-360").fadeOut("slow", function(e){
                              $(this).remove();
                            });
                         }
                      });
                      $exteriorBtn.on("click", function () {
                        hideVideo();
                        init360();
                        $exteriorBtn.addClass("active");
                        $interiorBtn.removeClass("active");
                        $interior.hide();
                        $exterior.show(); 

                      });
            }
        });  
        

        $("body").on("click",".content-wrapper", function(e){
          e.preventDefault();
          hideVideo();
        });
      });
  </script>