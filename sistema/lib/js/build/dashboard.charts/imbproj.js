(function(){var _id="imbproj",_parent="ev_timeline",cas=window.cas,google=window.google,chartFactory=cas.charts.chartFactory;function ChartConstructor(x){var chart=new chartFactory.ev_timeline(x);delete chart.opts.plotOptions.series.events.click;chart.id="imbproj";chart.orientation="down";chart.opts.tooltip={ySuffix:"%"};chart.opts.yAxis.labels={format:"{value}%"};chart.opts.title.text="Evolução IMB";return chart}chartFactory[_id]=new cas.charts.ChartPlaceHolder(_id,_parent,ChartConstructor)})();