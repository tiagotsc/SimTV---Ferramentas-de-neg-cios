(function(){var _id="per_mon_rank",_parent="std_column",cas=window.cas,google=window.google,chartFactory=cas.charts.chartFactory;function ChartConstructor(x){var chart=new chartFactory.std_column(x);chart.id="per_mon_rank";chart.orientation="down";chart.opts.title.text="Volume de Alarmes";chart.opts.plotOptions.series.events.click=function(event){cas.args.dashboard={dashboard:dashboard.dashboard,view:"cidade",ind:"mon",item:event.point.category};cas.pushArgs()};return chart}chartFactory[_id]=new cas.charts.ChartPlaceHolder(_id,_parent,ChartConstructor)})();