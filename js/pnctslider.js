/* SLIDE EXAMPLE
<div id="slider_left"></div>
<div id="slider_right"></div>
<div id="slides" style="overflow: hidden;position: relative;">
    <div id="slidescontainer" style="width: 9600px;">
        * content *
    </div> 
</div>
<script>
var slider = new PNCTSLIDER({
    $slidesframe:$('#slides'); //or supply slideWitdh if slideframe doens't have the correct size;
    $slidescontainer: $('#slidescontainer');
    pageTransition:'slide',
    autoplay:true,
    timerSpeed:1000,
    $leftbutton:$('#slider_left'),
    $rightbutton:$('#slider_right')
});
</script>
*/
/* FADE EXAMPLE
<div id="slider">
    <div>* content *</div>
    <div>* content *</div>  
</div>
<div id="sliderindicators">
    <div class="active"></div>
    <div></div>
</div>
<script>
var slider = new PNCTSLIDER({
    $slides:$('#slider div'),
    $indicators:$('#sliderindicators div'),
    pageTransition:'fade',
    autoplay:true,
    timerSpeed:7000
});
</script>
*/
function PNCTSLIDER(options){
    var self = this,i;
    
    self.options = {
        autoplay:false,
        timerSpeed: 6000,
        startslide:0,
        slideWidth:null, //alleen opgeven als de $slideFrame.width niet klopt 
        slideCount:null, //alleen opgeven als er meer dan één slide tegelijkertijd wordt weergegeven.
        slideSpeed:null,
        pageTransition:'slide', /*or 'fade';*/
        
        onBeforeAnimate:null,
        onAnimate:null
    };
    
    for (i in options){
        if (options.hasOwnProperty(i)){ self.options[i] = options[i];}
    }
    
    self.$slidesframe       = options.$slidesframe;//top div, hiding most of the slides.
    self.$slidescontainer   = options.$slidescontainer;//moving div, containg all the slides.
    self.$slides            = options.$slides;
    self.$indicators        = options.$indicators;//seperate non-moving thumbs/indicators
    self.$leftbutton        = options.$leftbutton;
    self.$rightbutton       = options.$rightbutton;
    
    if(self.options.pageTransition === 'fade' && typeof self.$slides !== 'object'){
        if (console){ console.error('De option.$slides is verplicht bij fade pageTransition');}
        return;
    }
    if(self.options.pageTransition === 'slide'){
        if(typeof self.$slidesframe !== 'object' && null == self.options.slideWidth){
            if (console){ console.error('De option.$slidesframe of option.slideWidth is verplicht bij slide pageTransition');}
            return;
        }
        if(typeof self.$slidescontainer !== 'object'){
            if (console){ console.error('De option.$slidescontainer is verplicht bij slide pageTransition');}
            return;
        }
    
        self.slideWidth     = (self.options.slideWidth?self.options.slideWidth:(self.$slidesframe.width()));    
    }
    
    if(self.options.slideCount){ self.slideCount = self.options.slideCount;}
    else if(options.pageTransition==='fade'){self.slideCount = self.$slides.length;}
    else if(options.pageTransition==='slide'){self.slideCount = Math.ceil(self.$slidescontainer.width()/self.slideWidth);}
    
    self.slidetimer         = null;
    self.hasThumbs          = false;
    self.curNo              = self.options.startslide;
    
    self.START_EV = 'click';
       
    if(typeof self.$indicators === 'object'){
        self.hasThumbs = (self.$indicators.length>0?true:false);
    }
    
    //activeer click of touches op links en rechts knoppen
    if(self.$leftbutton!==undefined && self.$rightbutton!==undefined){
        options.$leftbutton.bind(self.START_EV,function(){
            if(self.options.autoplay){ clearTimeout(self.slidetimer); }
            self.changeSlide(-1,false);
        });
        options.$rightbutton.bind(self.START_EV,function(){
            if(self.options.autoplay){ clearTimeout(self.slidetimer); }
            self.changeSlide(1,false);
        });
    }
    
    //indien autoplay starten met timeouten.
    if(self.options.autoplay){
        self.slidetimer = setTimeout(function(){self.changeSlide(0);},self.options.timerSpeed);
    } 

    //indien hovers mogelijk en autoplay dan de autoplay even pauzeren bij een hover.
    if(self.options.autoplay){
        var hoverables = [];
        if(self.hasThumbs){hoverables.push(self.$indicators);}
        if(self.$slidesframe !== undefined){ hoverables.push(self.$slidesframe); }
        if(self.$slides !== undefined){ hoverables.push(self.$slides); }
            
        $(hoverables).each(function(){
            this.hover(function(){
                clearTimeout(self.slidetimer);
            },function(){
                clearTimeout(self.slidetimer);
                self.slidetimer = setTimeout(function(){self.changeSlide(0);},self.options.timerSpeed);
            });
        });
    }

    //als er thumbs zijn click/touch registeren.
    if(self.hasThumbs){
        self.$indicators.bind(self.START_EV,function(){
            if($(this).hasClass('active')){
                return false;
            }
            var pageno = self.$indicators.index($(this));
            self.loadPage(pageno);
            
            if(self.options.autoplay){
                clearTimeout(self.slidetimer);
                self.slidetimer = setTimeout(function(){self.changeSlide(1);},self.options.timerSpeed);
            }
        });

        self.$indicators.find('.active').removeClass('active');
        self.$indicators.eq(self.options.startslide).addClass('active');
    }

    if(options.pageTransition==='slide'){
        self.$slidescontainer.css('marginLeft',(self.slideWidth*(-1*self.options.startslide)));
    }

}
PNCTSLIDER.prototype  = {
    //functie voor de automatische timer om slides te wissellen
    changeSlide: function(direction,auto){
        if(!direction){direction = 1;}
        var self = this;
        if(self.slideCount === self.curNo+direction){
            self.loadPage(0);
        } else if(self.curNo+direction < 0) {
            self.loadPage(self.slideCount-1);
        } else {
            self.loadPage(self.curNo+direction);
        }
        if(self.options.autoplay){
            clearTimeout(self.slidetimer);
            var speed = self.options.timerSpeed;
            if(!auto){
                speed = speed*2;
            }
            self.slidetimer = setTimeout(function(){self.changeSlide(1);},speed);
        }
    },

    // sluit de huidige pagina
    loadPage:function(no){
        var self = this;
        if(self.slideCount===1){ return; }
        if(no<0 || no > (self.slideCount-1)){
            no = 0;
        }
        
        self.curNo = no;
        
        if(self.hasThumbs){
            self.$indicators.removeClass('active');
            self.$indicators.eq(no).addClass('active');
        }
        
        self.animatePage();
    },

    // toon de volgende pagina
    // only used by loadPage to changeSlides & indicator
    animatePage:function(){
        var self = this,speed;
        if (self.options.onBeforeAnimate) self.options.onBeforeAnimate.call(self);
        if(self.options.pageTransition==='slide'){

            if(self.options.slideSpeed){
                speed = self.options.slideSpeed;
            } else {
                //100px brede slide duurt 400ms, 1000px brede slide duurt 1000ms.
                speed = 600/9*(self.slideWidth/100-1)+400;
                if(speed>1000){ speed = 1000; }
            }
            
            self.$slidescontainer.stop(true,false).animate({
                marginLeft:(self.slideWidth*(-1*self.curNo))
            },speed);
            
        } else if(self.options.pageTransition==='fade'){
            speed = (self.options.slideSpeed?self.options.slideSpeed:700);
            self.$slides.fadeOut(speed);
            self.$slides.eq(self.curNo).fadeIn(speed);
        } else {
            console.error('Bij een custom animatie moet je wel animatePage() overschrijven');
        }
        if (self.options.onAnimate) self.options.onAnimate.call(self,speed);
    }
};