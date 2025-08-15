app

.filter("ReduceString", function() {

    return function(str, size) {

        if (str.length <= size) {
            return str;
        } else {
            return str.substring(0, size) + "...";
        }

    };

});