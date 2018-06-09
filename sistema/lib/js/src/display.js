cas.controller = function(){
    function hideHits(){
        $('#hits_display').prepend('<div class="muteme"></div>');
    }
    function showHits(){
        $('#hits_display').children('.muteme').remove();
    }
    var myclock,clocker,uptime;
    $('#foot').append("<div id='monclock'></div>");
    function setUpClock()
    {
        // The colors of the dials:
        uptime = 1;
        var tmp;
        tmp = $('<div>').addClass('grey clock').html(
                '<div class="display"></div>'+
                '<div class="front left"></div>'+
                '<div class="rotate left">'+
                        '<div class="bg left"></div>'+
                '</div>'+
                '<div class="rotate right">'+
                        '<div class="bg right"></div>'+
                '</div>'
        );

        // Appending to the container:
        $('#monclock').html(tmp);

        // Assigning some of the elements as variables for speed:
        tmp.rotateLeft = tmp.find('.rotate.left');
        tmp.rotateRight = tmp.find('.rotate.right');
        tmp.display = tmp.find('.display');

        // Adding the dial as a global variable. Will be available as gVars.colorName
        myclock = tmp;

        // Setting up a interval, executed every 1000 milliseconds:
        clearInterval(clocker);
        clocker = setInterval(function(){
            animation(myclock, uptime, 10);
            if(uptime < 9)
                uptime++;
            else{
                uptime = 1;
            }
        },1000);
    }
    function animation(clock, current, total)
    {
            // Calculating the current angle:
            var angle = (360/total)*(current+1);

            var element;

            if(current==0)
            {
                    // Hiding the right half of the background:
                    clock.rotateRight.hide();

                    // Resetting the rotation of the left part:
                    rotateElement(clock.rotateLeft,0);
            }

            if(angle<=180)
            {
                    // The left part is rotated, and the right is currently hidden:
                    element = clock.rotateLeft;
            }
            else
            {
                    // The first part of the rotation has completed, so we start rotating the right part:
                    clock.rotateRight.show();
                    clock.rotateLeft.show();

                    rotateElement(clock.rotateLeft,180);

                    element = clock.rotateRight;
                    angle = angle-180;
            }

            rotateElement(element,angle);
            clock.display.html(current<10?'0'+current:current);
    }

    function rotateElement(element,angle)
    {
            // Rotating the element, depending on the browser:
            var rotate = 'rotate('+angle+'deg)';

            if(element.css('MozTransform')!=undefined)
                    element.css('MozTransform',rotate);

            else if(element.css('WebkitTransform')!=undefined)
                    element.css('WebkitTransform',rotate);

            // A version for internet explorer using filters, works but is a bit buggy (no surprise here):
            else if(element.css("filter")!=undefined)
            {
                    var cos = Math.cos(Math.PI * 2 / 360 * angle);
                    var sin = Math.sin(Math.PI * 2 / 360 * angle);

                    element.css("filter","progid:DXImageTransform.Microsoft.Matrix(M11="+cos+",M12=-"+sin+",M21="+sin+",M22="+cos+",SizingMethod='auto expand',FilterType='nearest neighbor')");

                    element.css("left",-Math.floor((element.width()-200)/2));
                    element.css("top",-Math.floor((element.height()-200)/2));
            }

    }
    $('.perhits_tv').html('0');
    $('.perhits_cm').html('0');
    var refreshYET = true;
    function refreshHits(){
        if(refreshYET){
            setUpClock();
            cas.ajaxer({
                method: 'GET',
                sendto: 'display/hits',
                before: function(){
                    hideHits();
                    refreshYET = false;
                },
                error:function(data){
                    showHits();
                    refreshYET = true;
                },
                complete:function(data){
                    showHits();
                    refreshYET = true;
                },
                andthen: refreshHits_
            });
        }
    }
    function refreshHits_(x){
        var data = x.data;
        $('.perhits_tv').css('background-color','rgba(100,100,100,0.2)');
        $('.perhits_cm').css('background-color','rgba(100,100,100,0.2)');
        $('.perhits_tv').html('0');
        $('.perhits_cm').html('0');
        for(var i in data.d.tv){
            $('#per_'+data.d.tv[i].per+'>.perhits_tv').html(data.d.tv[i].hits);
            if (data.d.tv[i].hits < 50)
                $('#per_'+data.d.tv[i].per+'>.perhits_tv').css('background-color','rgba(0,255,0,0.3)');
            else if(data.d.tv[i].hits < 100)
                $('#per_'+data.d.tv[i].per+'>.perhits_tv').css('background-color','rgba(255,255,0,0.5)');
            else if(data.d.tv[i].hits >= 100)
                $('#per_'+data.d.tv[i].per+'>.perhits_tv').css('background-color','rgba(255,0,0,0.5)');

        }
        for(var i in data.d.cm){
            $('#per_'+data.d.cm[i].per+'>.perhits_cm').html(data.d.cm[i].hits);
            if (data.d.cm[i].hits < 50)
                $('#per_'+data.d.cm[i].per+'>.perhits_cm').css('background-color','rgba(0,255,0,0.3)');
            else if(data.d.cm[i].hits < 100)
                $('#per_'+data.d.cm[i].per+'>.perhits_cm').css('background-color','rgba(255,255,0,0.5)');
            else if(data.d.cm[i].hits >= 100)
                $('#per_'+data.d.cm[i].per+'>.perhits_cm').css('background-color','rgba(255,0,0,0.5)');
        }
    }
    $('#hits_display').height($('#hits_display').children('span:first').find('.perlist').height()+10);
    var hits_thread = false;
    refreshHits();
    hits_thread = setInterval(refreshHits, 10000);
    var noauto = false;
    $(window).resize(function(){
        clearTimeout(noauto);
        noauto = setTimeout(function(){
            bestSize();
        },700);
    });
    function bestSize(){
        var reserved =
            $('#hits_display').height() //hits
            + $('#head-wrapper').height() //header
            + $('#foot').height() //footer
            + ( $('#h5me').height() ) //h5
            + 10 + 10; //margin
        var free = Math.max(100,($(window).height() - reserved));
        $('#ddd').height(free);
    }
    bestSize();

    $('#ddd').nocDisplay({link:true,scroller:true,header:true});
};