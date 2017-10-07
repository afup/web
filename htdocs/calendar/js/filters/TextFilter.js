planningPHPTourApp.filter('textFilter', [function () {
    return function (confs, textSearched) {
        if (textSearched != null) {
            var result = [];
            textSearched = textSearched.toLowerCase();
            angular.forEach(confs, function (conf) {
                if (conf.name.toLowerCase().indexOf(textSearched) != -1 || conf.detail.toLowerCase().indexOf(textSearched) != -1) {
                    result.push(conf);
                };
            });

            return result;
        }

        return confs;
    };
}]);