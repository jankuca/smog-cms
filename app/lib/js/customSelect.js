
;(function($){

  $.fn.extend({
  
    getByteLength: function(strIN){
        var i, cnt=0;
            
        for (i=0; i<strIN.length; i++){            
            if (escape(strIN.charAt(i)).length >= 4) cnt+=2;
            else cnt++;            
        } 
            
        return cnt;
    } 
    
  });
  
  $.fn.extend({
  
    getIndexByByte: function(strIN,limit){
        var i, cnt=0;
            
        for (i=0; i<strIN.length; i++){            
            if (escape(strIN.charAt(i)).length >= 4) cnt+=2;
            else cnt++;            
            
            if(cnt>limit)
            {
                return [true,i];
            }
        } 
            
        return false;
    } 
    
  });
  
  $.fn.extend({

    finalselect: function(options) {
        
        var settings =
	    {	
	        id:null,
		    animalSpeed:100,
            selectWidth:"194px",		   
            selectImage:"./styles/.acp/media/images/customSelect.png",
            selectText:"...",
		    zIndex: 0,    
		    viewHeight:"100px",
		    viewWidth:"300px",
		    viewMouseoverColor:"#cfdfff",//#dcdcdc
		    viewTop:"26px",//top,bottom
		    viewLeft:"0"//left,right
	    };
	    
	    
        if (typeof(options)!='undefined')
	    {
		    jQuery.extend(settings, options);
	    }
      
      	settings.selectWidth = (parseInt(settings.selectWidth) - 5)+'px';
        var tmp='<div id="'+settings.id+'-select" style="cursor:default;font-size:12px;z-index:'+settings.zIndex+';border: solid 0px #999; padding: 0; width: 180px; position: relative;">'
        tmp+='<div id="'+settings.id+'-Text" style="background: url('+settings.selectImage+') no-repeat 0 0; width: '+settings.selectWidth+'; height: 24px; color: #000; padding: 2px 0 0 5px;">';
        tmp+='<div class="textshow" style="padding: 4px 0 0 0;">'+settings.selectText+'</div><div class="valueshow" style="display:none;"></div></div><div id="'+settings.id+'-selectshow" style="overflow-y:auto; overflow-x:hidden; height:'+settings.viewHeight+';width:'+settings.viewWidth+'; display:none; position: absolute; left:'+settings.viewLeft+'; top:'+settings.viewTop+'; border: solid 1px #999; background: white;"></div></div>';
        

        
        var _handler = function() {
            // 從這裡開始
            $(this).html(tmp);
            bindArrowClick();
            bindSelectMouseover();
            bindSelectMouseleave();
            
        };
        
        
        
        var bindArrowClick=function(){
            var tmp=$('#'+settings.id+'-Text');
            $("#"+settings.id+'-Text').bind("click", function(e){            
                var obj=$('#'+settings.id+'-selectshow');
                if(obj.css('display')=='none')
                {
							obj.show();                       
                     obj.css('overflow','auto');
                  	obj.css('overflow-x','hidden');
                }
                else
                {
                    obj.hide();
                }
       
            });
        };
        
        var bindItemMouseover=function(){
        
            var inx=0;
            while($(".selectitem",$("#"+settings.id+"-selectshow")).get(inx)!=null)
            {
                var item=$(".selectitem",$("#"+settings.id+"-selectshow")).get(inx);
                
                $(item).bind("mouseover", function(e){
                  $(this).css('background-color',settings.viewMouseoverColor);
                });
                
                $(item).bind("mouseout", function(e){
                  $(this).css('background-color','#fff');
                });
                
                $(item).bind("click", function(e){
                 
                    var tmpstr=$(".thistext",$(this)).html();                     
                    var arr=$().getIndexByByte(tmpstr,24); 
                    if(arr[0]==true)
                        tmpstr=tmpstr.substring(0,arr[1])+'...';                    

                    $(".textshow",$("#"+settings.id+"-Text")).html(tmpstr);
                    document.getElementById(settings.id+'-selectshow').style.display="none";
                    
                    $(".valueshow",$("#"+settings.id+"-Text")).html($(".selectvalue",$(this)).html());
                    
                });

                inx++;
            }

        }
        
        var bindSelectMouseover=function(){
            $('#'+settings.id+'-Text').bind("mouseover",function(){
                if($.browser.msie==false)
                    $('#'+settings.id+'-Text').css("background-position","0 -26px");
            });
        }
        
        var bindSelectMouseleave=function(){
            $('#'+settings.id+'-Text').bind("mouseout",function(){
                if($.browser.msie==false)
                    $('#'+settings.id+'-Text').css("background-position","0 0px");
            });
        }
        
        this.setViewTop = function(top){
            $('#'+settings.id+'-selectshow').css('top',top+'px');
        } 
        
        this.setViewLeft = function(left){
            $('#'+settings.id+'-selectshow').css('left',left+'px');
        }     
        
        this.getLength = function(){
            return $('.selectitem',$('#'+settings.id+'-selectshow')).length;
        }   
       
       
        this.addItem = function(itemtext,itemvalue){            
            
            var itemhtml='<div class="selectitem"><div class="selecttext">'+itemtext
            +'</div><div class="selectvalue" style=" display:none;">'+itemvalue+'</div></div><div class="selectborder"><div>';
            
            $("#"+settings.id+'-selectshow').html($("#"+settings.id+'-selectshow').html()+itemhtml);           
            
            bindItemMouseover();
        };
        
        this.removeItem = function(index){
            if($('.selectitem',$('#'+settings.id+'-selectshow')).length>index)
            $($('.selectitem',$('#'+settings.id+'-selectshow')).get(index)).remove();
            if($('.selectborder',$('#'+settings.id+'-selectshow')).length>index)
            $($('.selectborder',$('#'+settings.id+'-selectshow')).get(index)).remove();
        }
        
        
        
        this.getValue = function(){
            return $('.valueshow',$('#'+settings.id+'-Text')).html();
        }
        
        this.getText = function(){
            return $('.textshow',$('#'+settings.id+'-Text')).html();
        }
        

        return this.each(_handler);     
    }

  });

})(jQuery);