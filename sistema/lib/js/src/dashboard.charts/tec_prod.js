(function(){
    
    var _id = 'tec_prod',
        _parent = 'std_column',
        cas = window.cas,
        google = window.google,
        chartFactory = cas.charts.chartFactory;
    
    function ChartConstructor(x) {
        chartFactory[_parent].call(this, x);
        if (this.owner.index !== null){
            this.plot.css('min-height', 500);
        }
        this.id = 'tec_prod';
        this.opts = {
            chart: {
                renderTo: this.plot[0],
                type: 'scatter',
                zoomType: 'xy'
            },
            title: {
                text: null
            },
            xAxis: {
                gridLineColor: '#E1E1E1',
                labels: {
                    format: '{value}%'
                },
                title: {
                    text: 'Qualidade',
                    align: 'low'
                }
            },
            credits: {
                enabled: false
            },
            yAxis: {
                startOnTick: false,
                endOnTick: false,
                title: {
                    text: 'Produção',
                    align: 'low'
                }
            },
            plotOptions: {
                scatter: {
                    cursor: 'pointer',
                    marker: {
                        radius: (dashboard.item === 'SIM') ? 8 : 4
                    }
                },
                series: {
                    events: {
                        click: function(event) {
                            if (dashboard.item !== 'SIM') {
                                if (event.point.item) {
                                    cas.args.dashboard = {
                                        dashboard: dashboard.dashboard,
                                        view: 'tecnico',
                                        ind: 'irm',
                                        item: event.point.item.area + ':' + event.point.item.tec
                                    };
                                    cas.pushArgs();
                                }
                            } else {
                                var abbr = cas.areaAbbr(event.point.series.name);
                                if (!cas.isValidArea(abbr))
                                    return true;

                                cas.args.dashboard = {
                                    dashboard: dashboard.dashboard,
                                    view: 'cidade',
                                    ind: 'irm',
                                    item: abbr
                                };
                                cas.pushArgs();
                            }
                        }
                    }
                }
            },
            legend: {
                enabled: (this.owner.index === null),
                align: 'right',
                verticalAlign: 'middle',
                layout: 'vertical'
            },
            tooltip: {
                valueSuffix: '%',
                formatter: function() {
                    if (this.series.name !== 'bkg') {
                        if (dashboard.item !== 'SIM')
                            return "<b>" + this.point.name + "</b><br>" +
                                '<b>Produção:</b> ' + this.y + " por dia<br>" + '<b>Qualidade:</b> ' +
                                this.x + "%<br>" + '<b>Visitas:</b> ' + this.point.vts + "<br>" +
                                '<b>Visitas não conformes:</b> ' + this.point.xvts + "<br>" +
                                '<b>Dias Trabalhados:</b> ' + this.point.ds + "<br>";
                        else
                            return "<b>" + this.series.name + "</b><br>" + '<b>Produção:</b> ' +
                                this.y + " por dia [média]<br>" + '<b>Qualidade:</b> ' + this.x +
                                "% [média]<br>" + '<b>Visitas:</b> ' + this.point.vts + "<br>" +
                                '<b>Visitas não conformes:</b> ' + this.point.xvts + "<br>" +
                                '<b>Número de Técnicos:</b> ' + this.point.tecs + "<br>" +
                                "<b>Média de Dias Trabalhados:</b> " + this.point.ds + "<br>";
                    } else return false;
                }
            }
        };
        return this;
    };
    
    ChartConstructor.prototype.predraw = function (response) {
        this.opts.plotOptions.scatter.marker.radius = ((dashboard.item === 'SIM') ? 8 : 4);
        if (this.owner.index !== null) {

            var ry = [response.range.y.min - ((response.range.y.max - response.range.y.min) * 0.1),
                response.range.y.max + ((response.range.y.max - response.range.y.min) * 0.1)
            ];
            var rx = [response.range.x.min - ((response.range.x.max - response.range.x.min) * 0.05),
                response.range.x.max + ((response.range.x.max - response.range.x.min) * 0.05)
            ];

            this.opts.xAxis.min = rx[0];
            this.opts.xAxis.max = rx[1];
            this.opts.yAxis.min = ry[0];
            this.opts.yAxis.max = ry[1];

            var rs = [{
                name: 'bkg',
                type: 'area',
                color: '#fff39f',
                showInLegend: false,
                fillOpacity: 0.5,
                lineWidth: 0,
                data: [{
                    y: ry[1],
                    x: rx[0]
                }, {
                    y: ry[1],
                    x: response.meta.x
                }, {
                    y: null,
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#ff6262',
                showInLegend: false,
                fillOpacity: 0.5,
                lineWidth: 0,
                data: [{
                    y: response.meta.y,
                    x: rx[0]
                }, {
                    y: response.meta.y,
                    x: response.meta.x
                }, {
                    y: null,
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#a4bfdb',
                fillOpacity: 0.5,
                showInLegend: false,
                lineWidth: 0,
                data: [{
                    y: null,
                    x: rx[0]
                }, {
                    y: ry[1],
                    x: response.meta.x
                }, {
                    y: ry[1],
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#a4dba8',
                fillOpacity: 0.5,
                showInLegend: false,
                lineWidth: 0,
                data: [{
                    y: null,
                    x: rx[0]
                }, {
                    y: response.meta.y,
                    x: response.meta.x
                }, {
                    y: response.meta.y,
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#fff39f',
                showInLegend: false,
                fillOpacity: 0.5,
                lineWidth: 0,
                data: [{
                    y: ry[0],
                    x: rx[0]
                }, {
                    y: ry[0],
                    x: response.meta.x
                }, {
                    y: null,
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#a4bfdb',
                showInLegend: false,
                fillOpacity: 0.5,
                lineWidth: 0,
                data: [{
                    y: null,
                    x: rx[0]
                }, {
                    y: ry[0],
                    x: response.meta.x
                }, {
                    y: ry[0],
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#ff6262',
                fillOpacity: 0.5,
                showInLegend: false,
                lineWidth: 0,
                data: [{
                    y: ry[0],
                    x: rx[0]
                }, {
                    y: ry[0],
                    x: response.meta.x
                }, {
                    y: null,
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }, {
                name: 'bkg',
                type: 'area',
                color: '#a4dba8',
                showInLegend: false,
                fillOpacity: 0.5,
                lineWidth: 0,
                data: [{
                    y: null,
                    x: rx[0]
                }, {
                    y: ry[0],
                    x: response.meta.x
                }, {
                    y: ry[0],
                    x: rx[1]
                }],
                marker: {
                    enabled: false,
                    states: {
                        hover: {
                            enabled: false
                        },
                        select: {
                            enabled: false
                        }
                    }
                },
                symbol: null
            }];
            response.series = rs.concat(response.series);
            return response;
        }
    };
    
    chartFactory[_id] = new cas.charts.ChartPlaceHolder(_id, _parent, ChartConstructor);
}());