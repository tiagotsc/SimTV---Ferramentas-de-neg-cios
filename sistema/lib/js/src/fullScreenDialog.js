(function($) {
    var dumbshit = [];
    jQuery.fn.extend({
        fullScreenDialog: function(options){
            var defaults = {
                close:false,
                buttons: [],
                title: '',
                onClose:false,
                before:false,
                content:''
            },o = {};
            
            if (typeof options === 'object') {
                o = $.extend(defaults, options);
            } else {
                o = 'close';
            }
            return this.each(function(){
                var root = $(this), container = false,content = false,header = false,footer=false,id=0;
                if(typeof root.attr('fSCDID') === 'undefined'){
                    id = dumbshit.length;
                    root.attr('fSCDID',id);
                }else{
                    id = root.attr('fSCDID');
                }

                if(o === 'close'){
                    close();
                    return this;
                }
                root.show();
                dumbshit[id] =
                    {
                    onClose: o.onClose
                    };
                $(window).on('resize',resizeUS);
                $(document).on('keydown',keyClose);
                boot();

                function resizeUS(){
                    setTimeout(function(){
                    if(content){
                        content.css('height',$(container).height() 
                                - ((header)?30:0) - ((footer)?30:0) );
                    }
                    },1000);
                }
                function keyClose(e){
                    if(e.keyCode === 27){
                        close();
                    }
                }
                function close(){
                    $(window).unbind('resize',resizeUS);
                    $(document).unbind('keydown',keyClose);
                        if(typeof dumbshit[id] !== 'undefined' 
                                && typeof dumbshit[id].onClose === 'function'){
                        dumbshit[id].onClose();
                        dumbshit[id] = false;
                    }
                    root.removeAttr('fSCDID');
                    root.empty();
                    root.hide();
                    root.removeClass('fullScreenDialog');
                    if($('.fullScreenDialog').length < 1)
                        cas.scrollplease();
                    root = false;
                }
                function boot(){
                    cas.scrollkilla();
                    if(typeof o.before === 'function')
                        o.before();
                    root.addClass('fullScreenDialog');
                    container = $("<div></div>");
                    root.html(container);
                    container.addClass('fSCDContainer');
                    container.html("");
                    if(o.title){
                    header =
                        $(
                            "<div class='fSCDHeader'>"+
                            o.title +
                            "</div>"
                        );
                    header.appendTo(container);
                    }
                    var closebt = $("<a class='fSCDClose'></a>");

                    closebt.appendTo(container);
                    closebt.on('click',function(){
                        close();
                    });

                    if(o.buttons && o.buttons.length > 0){
                    footer =
                        $(
                        "<div class='fSCDFooter'></div>"
                        );
                    footer.appendTo(container);
                    for(var i in o.buttons){
                        var thisbt =
                            $(
                            "<button>"+o.buttons[i].title+"</button>"
                            );
                        thisbt.appendTo(footer);
                        thisbt.on('click',o.buttons[i].action);
                    }
                    }
                    content = $("<div class='fSCDContent'></div>");
                    content.appendTo(container);
                    if(header){
                    content.css('top',30);
                    }
                    content.css('height',$(container).height() - ((header)?30:0) - ((footer)?30:0) );
                    $(o.content).appendTo(content);

                    if(typeof o.after === 'function')
                    o.after();
                }

                return this;
            });
        }
    });

    jQuery.fn.extend({
        fullScreenDialog: jQuery.fn.fullScreenDialog
    });

})(jQuery);