(function(){

    function checkButton () {
        var $form = $('#controls_container>form'),
            $submit = $form.find('.subRow'),
            shouldHave = $form.find('.filterRow').length > 0;

        if ($submit.length && !shouldHave) {
            $form.trigger('submit');
            return $submit.remove();
        }

        if (!$submit.length && shouldHave) {
            return $form.append("<div class=subRow><button type=submit>Aplicar filtro</button></div>");
        }

        $submit.appendTo($form);
    }
    
    function makeFilterHeader (filterID, filterName) {
        
        var $form = $('#controls_container>form');
        var $filter = $('<fieldset class="filterRow">').data('filter', filterID).appendTo($form);
        $filter.append("<legend>" + filterName + "</legend>");

        $('<a class=close>&times;</a>').appendTo($filter).click(function(){
            $(this).closest('.filterRow').remove();
            checkButton();
        });
        return $filter;

    }
    function getFilterName (filterID) {
        return $('#select_filter').find('option[value="'+filterID+'"]').text();
    }
    
    function isNumCode (filterID) {
        return $('#select_filter').find('option[value="'+filterID+'"]').attr('data-num-code');
    }

    function makeNumCodeFilter (filterID, filterName, setValue, callback) {
        cas.ajaxer({
            method: 'GET',
            sendto: '/noc/num_codes',
            sendme: {id: filterID},
            andthen: function (x) {
                
                var $filter = makeFilterHeader(filterID, filterName);

                var codes = x.data.codes;
                var $select = $('<select data-operator>').appendTo($filter);

                ['=','!='].forEach(function (operator) {
                    $('<option>').attr('value', operator).text(operator).appendTo($select);
                });

                $select = $('<select data-value>');
        
                $select.appendTo($filter);
                $('<label class=selectLabel></label>').appendTo($filter);

                codes.forEach(function (code) {
                    $('<option>').data('option', code).attr('value', code.value).text(code.name).appendTo($select);
                });

                $select.change(function () {
                    var $this = $(this),
                        $option = $this.find('option:selected'),
                        code = $option.data('option');

                    $this.next('.selectLabel').text(code.color.toUpperCase()).attr('class', 'selectLabel').addClass(code.color);
                });
                $select.trigger('change');
                if (setValue !== undefined) $select.val(setValue);
                checkButton();
                
                _.isFunction(callback) && callback();
            }
        });
    }

    function makeRangeFilter (filterID, filterName, setValue, setOperator) {
        var $filter = makeFilterHeader(filterID, filterName);
        var $select = $('<select data-operator>').appendTo($filter);

        ['=','!=','>','>=','<','<='].forEach(function(operator){
            $('<option>').attr('value', operator).text(operator).appendTo($select);
        });
        
        if (setOperator !== undefined) $select.val(setOperator);

        var $value = $('<input type=number step=0.25 required data-value />').appendTo($filter);
        if (setValue !== undefined) $value.val(setValue);

        checkButton();
    }

    function addFilter () {
        var $select = $('#select_filter'),
            filterID = $select.val(),
            $selected = $select.find('option:selected'),
            filterName = $selected.text();

        if ($selected.attr('data-num-code') !== undefined) {
            makeNumCodeFilter(filterID, filterName);
        } else {
            makeRangeFilter(filterID, filterName);
        }

    }

    function parseFilterFromURI () {
        async.eachSeries(cas.args.filter,
                
                function (filter, callback) {
                    if (isNumCode(filter[0])) {
                        makeRangeFilter(filter[0], getFilterName(filter[0]), filter[1], filter[2]);
                        callback();
                    } else {
                        makeNumCodeFilter(filter[0], getFilterName(filter[0]), filter[1], callback);
                    }
                },

                function () {
                    cas.nocMapReady = true;
                    cas.nocMapUpdate();
                }

            );
    }
    function init () {
        $('#add_filter').click(addFilter);
        
        if (cas.args.filter && _.isArray(cas.args.filter)) return parseFilterFromURI();

        cas.nocMapReady = true;
        cas.nocMapUpdate();
    }

    init();
}());