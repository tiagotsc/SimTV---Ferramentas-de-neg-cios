(function(){
    
    var _id = 'ass_schedules',
        _parent = 'std_list',
        cas = window.cas,

        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        var chart = new chartFactory.std_list(x);
        chart.id = 'ass_schedules';

        function makePicSlide(tb, os) {
            if (!os.pics || !os.pics.length)
                return false;
            var td = $("<td class='picstd' colspan=" + 3 + ">")
                .appendTo($("<tr class='slidetr'>").appendTo(tb));
            var ul = $("<ul class='picsul'>").appendTo(td);
            for (var i in os.pics) {
                var pic = os.pics[i];

                $("<li>")
                    .css('background',
                        "url(/media/tec_reg/72/" +
                        pic.file + ") center no-repeat white")
                    .data('x', Base64.encode(JSON.stringify(pic)))
                    .click(openImgPreview)
                    .appendTo(ul);
            }
        }

        function openImgPreview() {
            var elem = $("<div class='img-preview'>");
            var pos = $(this).offset();
            pos.top -= 200;
            pos.left -= 700;
            cas.weirdDialogSpawn(null, elem);
            var pic = JSON.parse(Base64.decode($(this).data('x')));

            elem.append("<div class='preview-header'>Enviada por <i>" +
                ((pic.user_name) ? pic.user_name : pic.user_email) + "</i><sup>em " + pic.timestamp + "</sup></div>");
            $("<div class='img-box'><img src='/media/tec_reg/480/" + pic.file + "' /></div>")
                .appendTo(elem);

            var d = $("<div class='preview-menu'>").appendTo(elem);
            $("<a target='_blank'>Tamanho Real</a>").attr('href', "media/tec_reg/orig/" + pic.file).appendTo(d);

        }
        chart.draw = function(response) {
            if(!this.owner.enabled){ return; }
            if (!$.contains(document.documentElement, this.plot[0]))
                return false;
            this.plot.html('<h4>Histórico de Visitas</h4>');
            this.response = response;
            var list = response.data.list,
                tb;

            for (var i in list) {

                tb = $("<table class='tec_schedule-tb'>").appendTo(this.plot);
                tb.append(
                    "<tr>" +
                        "<td class='ts1td' rowspan=2>"+
                            "<a target='_blank' href='agenda#"+
                                cas.hashbangify({
                                        agenda_d: list[i].agenda_d,
                                        agenda_perlist: [parseInt(list[i].per)]
                                    })+"'>" + list[i].day + 
                        "</a></td>" +
                        "<td class='ts2td'>" + list[i].real_ini + "</td>" +
                        "<td class='ts3td' rowspan=2>" + list[i].tec + "</td>" +
                    "</tr>");
                tb.append(
                    "<tr>" +
                    "<td class='ts2td'>" + list[i].real_end + "</td>" +
                    "</tr>");
                makePicSlide(tb, list[i]);
                for (var j in list[i].os) {
                    tb.append(
                        "<tr>" +
                        "<td class='ts1td'>" + list[i].os[j].os + "</td>" +
                        "<td class='ts2td'>" + list[i].os[j].svc + "</td>" +
                        "<td class='ts3td'>" + list[i].os[j].os_tipo + "</td>" +
                        "</tr>");

                    tb.append(
                        "<tr>" +
                        "<td colspan=3>" + ( list[i].os[j].causa || list[i].os[j].motivo ) + "</td>" +
                        "</tr>");
                    tb.append(
                        "<tr>" +
                        "<td colspan=3>" + list[i].os[j].obs + "</td>" +
                        "</tr>");
                }
            }

            if (this.menu) {
                //contagem
                var abc = list.length + " Visitas";
                this.menu.find('.ass_count').remove();
                $("<div class='ass_count'>").html(abc).appendTo(this.menu);
            }
            //---------------------------------------
            return this;
        };
        return chart;
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());