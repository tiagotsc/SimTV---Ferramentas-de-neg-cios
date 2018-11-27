if (!Function.prototype.bind) {
  Function.prototype.bind = function (oThis) {
    if (typeof this !== "function") {
      // closest thing possible to the ECMAScript 5 internal IsCallable function
      throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
    }

    var aArgs = Array.prototype.slice.call(arguments, 1), 
        fToBind = this, 
        fNOP = function () {},
        fBound = function () {
          return fToBind.apply(this instanceof fNOP && oThis
                                 ? this
                                 : oThis,
                               aArgs.concat(Array.prototype.slice.call(arguments)));
        };

    fNOP.prototype = this.prototype;
    fBound.prototype = new fNOP();

    return fBound;
  };
}

(function() {
    Date.prototype.toYMD = Date_toYMD;
    function Date_toYMD() {
        var year, month, day;
        year = String(this.getFullYear());
        month = String(this.getMonth() + 1);
        if (month.length === 1) {
            month = "0" + month;
        }
        day = String(this.getDate());
        if (day.length === 1) {
            day = "0" + day;
        }
        return year + "-" + month + "-" + day;
    }
})();
function ultimateStripTags(html){
    return html.replace(/<\/?[^>]+(>|$)/g, "");
}

// $('img.photo',this).imagesLoaded(myFunction)
// execute a callback when all images have loaded.
// needed because .load() doesn't work on cached images
 
// mit license. paul irish. 2010.
// webkit fix from Oren Solomianik. thx!
 
// callback function is passed the last image to load
//   as an argument, and the collection as `this`
$.fn.imagesLoaded = function(callback){
  var elems = this.filter('img'),
      len   = elems.length;
      
  elems.bind('load',function(){
      if (--len <= 0){ callback.call(elems,this); }
  }).each(function(){
     // cached images don't fire load sometimes, so we reset src.
     if (this.complete || this.complete === undefined){
        var src = this.src;
        // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
        // data uri bypasses webkit log warning (thx doug jones)
        this.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
        this.src = src;
     }  
  }); 
 
  return this;
};

function getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1) + min);
}
function checkInput(type) {
    var input = document.createElement("input");
    input.setAttribute("type", type);
    return input.type === type;
}
function datePolyfill(z){
    z.find('input[type="date"]').not('.hasDatepicker').each(function(){
        var x = {dateFormat:'yy-mm-dd'}, 
            max  = $(this).attr('max'),
            min  = $(this).attr('min');
        if(max) x.maxDate = max;
        if(min) x.minDate = min;
        $(this).datepicker(x);
    });
}