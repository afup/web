// Script classique (defer) : TableFilter est exposé par assets/tablefilter/tablefilter.js.
// Les URL des CSS, versionnées par AssetMapper, sont passées par les attributs data du <script>.
var tableFilterStyles = document.currentScript.dataset;

$('table.afup-tab-filterable').each(function() {

    var tfConfig = {
        auto_filter: false,
        filters_row_index: 1,
        headers_row_index: 0,
        themes: [{
            name: 'transparent',
            path: tableFilterStyles.theme
        }],
        alternate_rows: false,
        clear_filter_text: "- Filtre -",
        rows_counter: {
            text: "Lignes : "
        },
        help_instructions: false,
        btn_reset: false,
        loader: false,
        status_bar: false,
        cell_parser: {
            parse: function(o, cell, colIndex){
                /* Si on a un select, un form dans la cellule, on ne la renvoie pas */
                var selectsInCell = $('select', cell);
                if (selectsInCell.length > 0) {
                    return $('option:selected', selectsInCell).text();
                }

                return $(cell).text()
            }
        },
    };

    var colNumber = 0;
    $('thead th', this).each(function () {
        var tfType = $(this).data('tf-filter-type');
        if (typeof tfType !== 'undefined') {
            tfConfig['col_' + colNumber] = tfType;
        } else {
            tfConfig['col_' + colNumber] = 'none';
        }

        if ((typeof tfConfig.cell_parser.cols) === 'undefined') {
            tfConfig.cell_parser.cols = [];
        }

        tfConfig.cell_parser.cols.push(colNumber);

        colNumber++;
    });

    var tf = new window.TableFilter(this, tfConfig);
    // TableFilter 0.7.2 ignore l'option `stylesheet` (getStylesheetPath() lit this.config,
    // qui est une méthode) : on surcharge le getter pour charger la feuille versionnée.
    tf.getStylesheetPath = function () {
        return tableFilterStyles.stylesheet;
    };
    tf.init();
});
